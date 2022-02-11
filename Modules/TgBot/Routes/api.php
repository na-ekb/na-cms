<?php

Route::prefix('tgbot')->group(function() {
    /*
    Route::get('/test', function () {
       return morphos\Russian\GeographicalNamesInflection::getCase('Самара', morphos\Russian\Cases::LOCATIVE);
    });
    */
    Route::post('/webhook/{token}', 'TgBotController@handle')->name('tgbot.webhook');
});
