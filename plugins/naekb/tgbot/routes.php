<?php

use NAEkb\TgBot\Classes\TgCallbackHandler;

Route::post('tg-webhook/{token}', [TgCallbackHandler::class, 'handle'])->name('tg.callback');
