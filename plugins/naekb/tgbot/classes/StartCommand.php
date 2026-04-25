<?php
namespace NAEkb\TgBot\Classes;

use NAEkb\TgBot\Models\TgTokens;

class StartCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected string $name = 'start';

    /** @inheritdoc */
    protected string $pattern = '{token}';

    /** @inheritdoc */
    protected function main() {
        $token = $this->argument('token');
        if (!empty($token) && TgTokens::where('token', $token)->exists()) {
            $token = TgTokens::where('token', $token)->first();
            return $this->triggerCommand($token->command);
        }

        return $this->triggerCommand('page');
    }
}
