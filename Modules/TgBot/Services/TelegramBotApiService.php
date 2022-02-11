<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

use Carbon\Carbon;
use Telegram\Bot\BotsManager;

class TelegramBotApiService {
    protected $client;

    public function __construct() {
        $this->client = app(BotsManager::class);
        //$telegram->addCommand(Telegram\Bot\Commands\HelpCommand::class);

    }
}
