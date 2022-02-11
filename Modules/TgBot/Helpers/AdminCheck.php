<?php

namespace Modules\TgBot\Helpers;

use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\TgBot\Entities\TgAdmin;

class AdminCheck
{
    public static function isAdmin($channel_id, int $user_id) {
        /*
        if (TgAdmin::where('tg_id', $user_id)->exists()) {
            return true;
        }
        */

        if (!empty(config('TgBot.tg_channel'))) {
            $admins = Telegram::getChatAdministrators([
                'chat_id' => $channel_id
            ]);

            foreach ($admins as $admin) {
                if ($admin->getUser()->getId() == $user_id) {
                    return true;
                }
            }

            if (!empty(config('TgBot.tg_channel_admins'))) {
                foreach (config('TgBot.tg_channel_admins') as $admin) {
                    if ($admin == $user_id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
