<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bar properties.
    |--------------------------------------------------------------------------
    */

    /**
     * Multiple quick buy actions within this number of seconds will be merged
     * into a single transaction if possible.
     */
    'quick_buy_merge_timeout' => 60 * 2.5,

    /**
     * The number of seconds to show recent product transactions for on bar
     * pages.
     *
     * This is done for social control, to show other users for a short period
     * of time what is bought.
     */
    'bar_recent_product_transaction_period' => 60 * 60,
];
