<?php

Route::prefix('tgbot')->group(function() {
    Route::get('set-webhook', function() {
        dd(123);
    })->name('tgbot.set-webhook');
});

