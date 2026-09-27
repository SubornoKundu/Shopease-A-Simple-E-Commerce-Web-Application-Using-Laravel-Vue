<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storefront Settings
    |--------------------------------------------------------------------------
    |
    | Small, store-wide settings that several controllers/views need
    | (checkout total calculation, price formatting) but that don't
    | belong to any single Eloquent model.
    |
    */

    // Flat shipping fee added to every order at checkout.
    'shipping_fee' => (float) env('SHOP_SHIPPING_FEE', 60),

    'currency' => env('SHOP_CURRENCY', 'BDT'),

    'currency_symbol' => env('SHOP_CURRENCY_SYMBOL', '৳'),

];
