<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Exports\EconomyPaymentExport;
use App\Helpers\ValidationDefaults;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \Excel;

class EconomyPaymentController extends Controller {

    const PAGINATE_ITEMS = 50;

    /**
     * Community economy payments index.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $payments = $economy
            ->payments()
            ->latest()
            ->with('user')
            ->paginate(self::PAGINATE_ITEMS);

        return view('community.economy.payment.index')
            ->with('economy', $economy)
            ->with('payments', $payments);
    }

    /**
     * Community economy payments export.
     *
     * @return Response
     */
    public function export($communityId, $economyId) {
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        $firstDate = (new Carbon($economy->payments()->min('payment.created_at')))
            ->toDateString();
        $lastDate = today()->toDateString();

        return view('community.economy.payment.export')
            ->with('economy', $economy)
            ->with('firstDate', $firstDate)
            ->with('lastDate', $lastDate);
    }

    /**
     * Community economy payments export.
     *
     * @return Response
     */
    public function doExport(Request $request, $communityId, $economyId) {
        // Validate
        $this->validate($request, [
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|' . ValidationDefaults::exportTypes(),
        ]);

        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        $headers = is_checked($request->input('headers'));
        $fromDate = $request->input('date_from');
        $toDate = $request->input('date_to');
        $format = $request->input('format');
        $fileName = 'barapp-payments.' . collect(config('bar.spreadsheet_export_types'))->firstWhere('type', $format)['extension'];

        return Excel::download(
            new EconomyPaymentExport($headers, $economy->id, $fromDate, $toDate),
            $fileName,
            $format,
        );
    }
}
