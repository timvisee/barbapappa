<?php

namespace App\Http\Controllers\Callbacks;

use App\Jobs\ProcessBunqAccountEvents;
use App\Jobs\ProcessBunqBunqMeTabEvent;
use App\Models\BunqAccount;
use bunq\Model\Generated\Endpoint\BunqMeTabApiObject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use bunq\Model\Generated\Object\NotificationUrlObject;

class BunqController extends Controller {

    /**
     * bunq callback index.
     *
     * @return Response
     */
    public function index(Request $request) {
        \Log::debug('bunq callback: callback received');

        // Get the JSON body content as string, return if no data is given
        $json = $request->getContent();
        if(empty(trim($json)))
            return 'OK';

        // Get the NotificationUrlObject section to parse
        $json = json_encode(json_decode($json, true)['NotificationUrl']);

        // Create the NotificationUrl object
        $notification = NotificationUrlObject::createFromJsonString($json);
        $category = $notification->getCategory();
        $object = $notification->getObject();

        // Only handle specific types
        if($category != 'PAYMENT' && $category != 'BUNQME_TAB') {
            \Log::debug('bunq callback: unhandled callback type: ' . $category);
            return 'OK';
        }

        // Handle the notification, update bunq events for its monetary account
        if(($payment = $object->getPayment()) != null) {
            \Log::debug('bunq callback: processing regular payment');
            Self::processEventsForAccount($payment->getMonetaryAccountId(), 0);
        } else if(($bunqMeTab = $object->getBunqMeTab()) != null) {
            \Log::debug('bunq callback: processing bunq me tab payment');
            Self::processBunqMeTab($bunqMeTab);
        } else if (($bunqMeTab = $object->getBunqMeTabResultInquiry()) != null || ($bunqMeTab = $object->getBunqMeTabResultResponse()) != null) {
            \Log::debug('bunq callback: processing bunq me tab payment inquiry');
            if(($payment = $bunqMeTab->getPayment()) != null)
                if(($id = $payment->getMonetaryAccountId()) != null)
                    Self::processEventsForAccount($id, 1);

            // Schedule bunq me tab processing as fallback in case events are incomplete
            Self::processBunqMeTab($bunqMeTab, now()->addSeconds(15));
        } else if($object->getPaymentBatch() != null) {
            // ignore payment batch
            \Log::debug('bunq callback: ignoring payment batch');
        } else {
            throw new \Exception('Unhandled notification type');
        }

        return 'OK';
    }

    /**
     * Process all new events for the given monetary account ID through bunq.
     *
     * If no linked bunq account is found for the given monetary account ID it
     * is ignored. Notifications are commonly received for monetary accounts not
     * under control by Barbapappa.
     *
     * @param int $monetaryAccountId The ID of the monetary account.
     * @param int [$retryCountIfNone=0] Retry the job for this many times after a
     *      few seconds if no new events were found while running it the first
     *      time. This is useful for some events that may be collected with a
     *      slight delay on bunqs end.
     */
    // TODO: differentiate by production/sandbox account IDs
    private static function processEventsForAccount(int $monetaryAccountId, int $retryCountIfNone = 0) {
        // Find the account, skip if it is not linked
        // TODO: handle deleted accounts here as well?
        $account = BunqAccount::where('monetary_account_id', $monetaryAccountId)->first();
        if($account == null)
            return;

        // Dispatch a job for processing all new events on this monetary account
        ProcessBunqAccountEvents::dispatch($account, $retryCountIfNone);
    }

    /**
     * Process a specific bunq me tab. Check of payment and process it if found.
     *
     * If no linked bunq account is found for the given monetary account ID it
     * is ignored. Notifications are commonly received for monetary accounts not
     * under control by Barbapappa.
     *
     * @param BunqMeTabApiObject $bunqMeTab The bunq me tab to process.
     * @param Carbon|null $delay Optional delay for processing the bunq me tab,
     *      used as fallback in case of delayed events on bunqs end.
     */
    // TODO: differentiate by production/sandbox account IDs
    private static function processBunqMeTab(BunqMeTabApiObject $bunqMeTab, $delay = null) {
        $monetaryAccountId = $bunqMeTab->getMonetaryAccountId();
        if($monetaryAccountId == null)
            return;

        // Find the account, skip if it is not linked
        // TODO: handle deleted accounts here as well?
        $account = BunqAccount::where('monetary_account_id', $monetaryAccountId)->first();
        if($account == null)
            return;

        // Dispatch a job for processing events on the bunq me tab
        $job = ProcessBunqBunqMeTabEvent::dispatch($account, $bunqMeTab);
        if($delay != null)
            $job->delay($delay);
    }
}
