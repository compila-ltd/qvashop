<?php

return [

    // Current Node operator API
    'base_url' => env('LIGHTNING_URL', ''),
    'key' => env('LIGHTNING_KEY', ''),
    'secret' => env('LIGHTNING_SECRET', ''),

    // USDTTRC wallet for forwarding payments
    'usdt_address' => env('USDT_ADDRESS', ''),

    // LN Tax for rebalance
    'tax' => env('LN_TAX', 1)
];
