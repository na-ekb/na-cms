<?php

Route::prefix('tgbot')->group(function() {
    Route::get('set-webhook', function() {
        dd(123);
    })->name('tgbot.set-webhook');


    Route::post('/webhook/{token}', 'TgBotController@handle')->name('tgbot.webhook');
    Route::get('/webapp/{command}/{action}/{token}', 'TgBotController@webApp')->name('tgbot.webapp');
});

