<?php namespace NAEkb\Pages\Components;

use Cms\Classes\ComponentBase;
use Http;
use Input;
use NAEkb\Pages\Models\TurnstileSettings;
use NAEkb\TgBot\models\TgSettings;
use October\Rain\Exception\ValidationException;
use October\Rain\Support\Facades\Flash;
use Request;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class Feedback extends ComponentBase
{
    public const SENDER_TG = 'tg';
    public const SENDER_SMTP = 'smtp';

    public function componentDetails()
    {
        return [
            'name' => 'naekb.pages::lang.snippet.feedback_name',
            'description' => 'naekb.pages::lang.snippet.feedback_desc',
            'icon' => 'icon-book',
            'snippetAjax' => true
        ];
    }

    public function defineProperties()
    {
        return [
            'sender' => [
                'title' => 'naekb.pages::lang.snippet.sender',
                'description' => 'naekb.pages::lang.snippet.sender_desc',
                'type' => 'set',
                'items' => [
                    static::SENDER_TG => __('naekb.pages::lang.snippet.tg'),
                    static::SENDER_SMTP => __('naekb.pages::lang.snippet.smtp'),
                ]
            ],
        ];
    }

    public function onSend()
    {

        /*
        $token = Input::get('cf-turnstile-response');
        $ip = Request::ip();

        try {
            $secret = TurnstileSettings::get('secret');
            $resp = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip
            ]);
        } catch (\Throwable $e) {
            report($e);
            return 'errorHttpCaptcha';
        }

        $cloudFlareResp = $resp->json();
        if (!$cloudFlareResp['success']) {
            Flash::error(__('naekb.pages::lang.errors.captcha'));
        }*/

        $name = Input::get('name');
        $email = Input::get('email');
        $msg = Input::get('msg');
        if (empty($msg)) {
            throw new ValidationException(['msg' => __('naekb.pages::lang.errors.empty_msg')]);
        }

        /*
        $sender = $this->property('sender') ?? [];
        if (empty($sender)) {
            return 'errorSender';
        }
        if (in_array(static::SENDER_TG, $sender)) {}
        if (in_array(static::SENDER_SMTP, $sender)) {}
        */

        try {
            $apiKey = TgSettings::get('bot_secret');
            $chatId = TgSettings::get('chat_id');
            $telegram = new Api($apiKey);
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => __('naekb.tgbot::commands.notification.feedback', [
                    'name' => $name ?? __('naekb.tgbot::commands.notification.noname'),
                    'email' => $email ?? __('naekb.tgbot::commands.notification.noemail'),
                    'msg' => $msg
                ]),
                'disable_web_page_preview' => true
            ]);
        } catch (\Throwable $e) {
            report($e);
            Flash::error(__('naekb.pages::lang.errors.http_tg'));
        }


        Flash::success(__('naekb.pages::lang.success.feedback'));
    }
}
