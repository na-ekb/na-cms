<?php

use NAEkb\VkBot\Classes\VkCallbackHandler;

Route::post('vk-webhook/{token}', [VkCallbackHandler::class, 'handle'])->name('vk.callback');
