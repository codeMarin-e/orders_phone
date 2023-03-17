<?php

use App\Http\Controllers\Admin\OrdersPhoneController;
use App\Models\Cart;

Route::group([
    'controller' => OrdersPhoneController::class,
    'middleware' => ['auth:admin', 'can:order_phone_view,'.Cart::class],
    'as' => 'orders_phone.', //naming prefix
    'prefix' => 'orders_phone', //for routes
], function() {
    Route::get('', 'index')->name('index');
    Route::post('add', 'addProduct')->name('add_product');
    Route::post('', 'process')->name('process')->middleware('can:order_phone_create,'.Cart::class);
    Route::patch('change/{chCartProduct}', 'changeProduct')->name('change_product');
    Route::get('deliveries', 'deliveries')->name('deliveries');
    Route::get('payments', 'payments')->name('payments');
    Route::patch('payment', 'changePayment')->name('change_payment');
    Route::patch('delivery', 'changeDelivery')->name('change_delivery');
    Route::delete('remove/{chCartProduct}', 'removeProduct')->name('remove_product');
    Route::delete('clear', 'clear')->name('clear');
    Route::get('catalog', 'showCatalog')->name('show_catalog');
    Route::get('catalog/{chCategory}', 'catalog')->name('catalog');
    Route::get('product/{chProduct}', 'product')->name('product');
    Route::get('autocomplete/{type}', 'autocomplete')->name('autocomplete');

    // @HOOK_ROUTES
});
