<?php namespace NAEkb\TgBot\Widgets;

use Flash;
use Redirect;
use Backend\Classes\FormWidgetBase;
use Illuminate\Support\Str;

use Telegram\Bot\Api;

use NAEkb\TgBot\Models\TgSettings;

class SetWebHook extends FormWidgetBase
{
    protected $defaultAlias = 'tg-connect';

    public function render()
    {
        return $this->makePartial('settings_toolbar');
    }

    public function onSetWebHook()
    {
        $apiKey = TgSettings::get('bot_secret');
        if (empty($apiKey)) {
            Flash::error(__('naekb.tgbot::lang.settings.failed_key'));
            return Redirect::to(url()->current());
        }

        $telegram = new Api($apiKey);
        $telegram->deleteWebhook();
        $response = $telegram->setWebhook([
            'url' => route('tg.callback', [
                'token' => TgSettings::get('webhook_token')
            ])
        ]);

        if (!$response) {
            Flash::error(__('naekb.tgbot::lang.settings.failed'));
        } else {
            Flash::success(__('naekb.vkbot::lang.settings.success'));
        }

        return Redirect::to(url()->current());
    }
}
