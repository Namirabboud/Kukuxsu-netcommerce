<?php
Route::get('netcommerce-redirect-payment/{id}', 'Kukuxsu\Netcommerce\NetCommerceController@redirectPayment')->name('netcommerce.payment.redirect');
Route::post('netcommerce-payment-response', 'Kukuxsu\Netcommerce\NetCommerceController@paymentResponse')->name('netcommerce.payment.response');
Route::get('payment-mobile-response', function(){
    echo 'Payment Response';
})->name('payment-mobile-response');
