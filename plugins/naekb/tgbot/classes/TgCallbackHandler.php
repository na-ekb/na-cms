<?php

namespace NAEkb\TgBot\Classes;

use Illuminate\Routing\Controller;
use Telegram\Bot\Api;

use NAEkb\TgBot\Models\TgSettings;
use NAEkb\TgBot\Models\TgState;

class TgCallbackHandler extends Controller
{
    /**
     * Receive update from bot api.
     *
     * @param string $token
     * @return string
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws \Throwable
     */
    public function handle(string $token) :string
    {
        try {
            $this->checkToken($token);

            $apiKey = TgSettings::get('bot_secret');
            $telegram = new Api($apiKey);
            $telegram->addCommands([
                new StartCommand(),
                new PageCommand(),
                new ContactsCommand()
            ]);

            $update = $telegram->getWebhookUpdate();

            // Служебные обновления (my_chat_member при блокировке бота,
            // poll_answer и т.п.) могут содержать chat, но не являются командами.
            // Подтверждаем их без попытки отправить пользователю typing/сообщение.
            $isCallback = $update->has('callback_query');
            if (!$update->has('message') && !$isCallback) {
                return 'OK';
            }

            $userId = $update->getChat()->get('id');
            if (empty($userId)) {
                return 'OK';
            }

            $state = TgState::firstOrCreate(['user_id' => $userId], ['state' => 'start']);

            if ($isCallback) {
                try {
                    $telegram->answerCallbackQuery(['callback_query_id' => $update->callbackQuery->get('id')]);
                } catch (\Throwable) { }

                if ($state->state !== $update->callbackQuery->get('data')) {
                    $state->update([
                        'state' => $update->callbackQuery->get('data'),
                        'prev' => $state->state
                    ]);
                }

                $command = $this->getCallbackCommand($update->callbackQuery->get('data'));
                $telegram->triggerCommand($command, $update);
            } else {
                // ToDo: обрабатывать только сообщения
                $telegram->triggerCommand('start', $update);
            }

            return 'OK';
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check webhook secret token
     *
     * @param string $token
     * @return void
     */
    private function checkToken(string $token): void
    {
        if ($token !== TgSettings::get('webhook_token')) {
            abort(401);
        }
    }

    private function getCallbackCommand(string $command)
    {
        return explode(' ', trim($command))[0];
    }
}
