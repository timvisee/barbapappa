<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\EconomyCurrency;
use BarPay\Models\Service as PayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class PaymentServiceController extends Controller {

    /**
     * Payment service index page.
     * This shows information about payment services in an economy.
     *
     * @return Response
     */
    public function index(Request $request, $communityId, $economyId) {
        // Get the user, community, find the payment services
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $services = $economy->paymentServices;

        return view('community.economy.paymentservice.index')
            ->with('economy', $economy)
            ->with('services', $services);
    }

    /**
     * Payment service creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId, $economyId) {
        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $serviceable = $request->query('serviceable');
        $choose = empty($serviceable);

        // Validate serviceable type input
        $request->validate([
            'serviceable' => Rule::in(PayService::SERVICEABLES),
        ]);

        // List the currencies that can be used
        $currencies = $economy->currencies;

        return view('community.economy.paymentservice.create' . ($choose ? 'Choose' : ''))
            ->with('economy', $economy)
            ->with('serviceable', $serviceable)
            ->with('currencies', $currencies);
    }

    /**
     * Payment service create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $serviceable_type = $request->input('serviceable');

        // Validate service and serviceable fields
        $request->validate([
            'serviceable' => ['required', Rule::in(PayService::SERVICEABLES)],
            'currency' => array_merge(['required'], ValidationDefaults::economyCurrency($economy, false)),
        ]);
        ($serviceable_type::CONTROLLER)::validateCreate($request);

        // Find the selected economy currency, get it's currency ID
        $currencyId = EconomyCurrency::findOrFail($request->input('currency'))->currency_id;

        // Create the payment service in a transaction
        DB::transaction(function() use($request, $economy, $serviceable_type, $currencyId) {
            // Create the service
            $service = $economy->paymentServices()->create([
                'serviceable_id' => 0,
                'serviceable_type' => '',
                'enabled' => is_checked($request->input('enabled')),
                'deposit' => is_checked($request->input('deposit')),
                'withdraw' => is_checked($request->input('withdraw')),
                'currency_id' => $currencyId,
            ]);

            // Create serviceable
            $serviceable = ($serviceable_type::CONTROLLER)::create($request, $service);
        });

        // Redirect to services index
        return redirect()
            ->route('community.economy.payservice.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ])
            ->with('success', __('pages.paymentService.created'));
    }

    /**
     * Show a payment service.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $serviceId) {
        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $service = $economy->paymentServices()->withTrashed()->findOrFail($serviceId);
        $serviceable = $service->serviceable;

        return view('community.economy.paymentservice.show')
            ->with('economy', $economy)
            ->with('service', $service)
            ->with('serviceable', $serviceable);
    }

    /**
     * Edit a payment service.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $serviceId) {
        // TODO: with trashed?

        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $service = $economy->paymentServices()->withTrashed()->findOrFail($serviceId);
        $serviceable = $service->serviceable;

        // List the currencies that can be used
        $currencies = $economy->currencies;

        return view('community.economy.paymentservice.edit')
            ->with('economy', $economy)
            ->with('service', $service)
            ->with('serviceable', $serviceable)
            ->with('currencies', $currencies);
    }

    /**
     * Payment service update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $serviceId) {
        // TODO: with trashed?

        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $service = $economy->paymentServices()->withTrashed()->findOrFail($serviceId);
        $serviceable = $service->serviceable;

        // Validate service and serviceable fields
        $request->validate([
            'currency' => array_merge(['required'], ValidationDefaults::economyCurrency($economy, false)),
        ]);
        ($serviceable::CONTROLLER)::validateCreate($request);

        // Find the selected economy currency, get it's currency ID
        // TODO: did we get Currency id from form? Should be economycurren?
        $currencyId = EconomyCurrency::findOrFail($request->input('currency'))->currency_id;

        // Change service and serviceable properties and sync prices in transaction
        DB::transaction(function() use($request, $service, $serviceable, $currencyId) {
            // Update service properties
            $service->currency_id = $currencyId;
            $service->enabled = is_checked($request->input('enabled'));
            $service->deposit = is_checked($request->input('deposit'));
            $service->withdraw = is_checked($request->input('withdraw'));
            $service->save();

            // Update serviceable
            $serviceable = ($serviceable::CONTROLLER)::edit($request, $service, $serviceable);
        });

        // Redirect the user to the payment service page
        return redirect()
            ->route('community.economy.payservice.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'serviceId' => $service->id,
            ])
            ->with('success', __('pages.paymentService.changed'));
    }

    // /**
    //  * Page for confirming restoring a product.
    //  *
    //  * @return Response
    //  */
    // public function restore($communityId, $economyId, $productId) {
    //     // Get the user, community, find the product
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $product = $economy->products()->withTrashed()->findOrFail($productId);

    //     // If already restored, redirect to the product
    //     if(!$product->trashed())
    //         return redirect()
    //             ->route('community.economy.product.show', [
    //                 'communityId' => $community->human_id,
    //                 'economyId' => $economy->id,
    //                 'productId' => $product->id,
    //             ])
    //             ->with('success', __('pages.products.restored'));

    //     return view('community.economy.product.restore')
    //         ->with('economy', $economy)
    //         ->with('product', $product);
    // }

    // /**
    //  * Restore a product.
    //  *
    //  * @return Response
    //  */
    // public function doRestore($communityId, $economyId, $productId) {
    //     // TODO: delete trashed, and allow trashing?

    //     // Get the user, community, find the product
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $product = $economy->products()->withTrashed()->findOrFail($productId);

    //     // Restore the product
    //     $product->restore();

    //     // Redirect to the product index
    //     return redirect()
    //         ->route('community.economy.product.show', [
    //             'communityId' => $community->human_id,
    //             'economyId' => $economy->id,
    //             'productId' => $product->id,
    //         ])
    //         ->with('success', __('pages.products.restored'));
    // }

    /**
     * Page for confirming the deletion of the payment service.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $serviceId) {
        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $service = $economy->paymentServices()->withTrashed()->findOrFail($serviceId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // product

        return view('community.economy.paymentservice.delete')
            ->with('economy', $economy)
            ->with('service', $service);
    }

    /**
     * Delete a payment service.
     *
     * @return Response
     */
    public function doDelete(Request $request, $communityId, $economyId, $serviceId) {
        // Get the user, community, find the payment service
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $service = $economy->paymentServices()->withTrashed()->findOrFail($serviceId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // product

        // Soft delete
        $service->delete();

        // Redirect to the payment service index
        return redirect()
            ->route('community.economy.payservice.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ])
            ->with('success', __('pages.paymentService.deleted'));
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
