<?php

namespace Modules\TgBot\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Modules\TgBot\Tests\Data\Commands;

class CommandsTest extends TestCase
{
    public function testStart()
    {
        $this->message();
    }

    public function testGroups()
    {
        $first = [0, 1];
        $second = [
            'today', 'tomorrow', 'after',
            '1', '2', '3', '4', '5', '6', '0'
        ];
        foreach ($first as $fArg) {
            foreach ($second as $sArg) {
                $this->message("/groups  {$fArg} {$sArg}");
                sleep(1);
            }
        }
    }

    public function testGeo()
    {
        $first = [0, 1];
        foreach ($first as $fArg) {
            $this->message("/geo  {$fArg}");
            sleep(1);
            $this->geo();
            sleep(1);
        }
    }


    private function message(string $text = '/start') {
        if (mb_strpos($text, '  ')) {
            $length = explode('  ', $text);
            $length = array_shift($length);
            $length = mb_strlen($length);
        }

        $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache'
        ])->postJson(
            route('tgbot.webhook', [
                'token' => config('telegram.webhook_secret_token')
            ]),
            Commands::message($text, $length ?? 6)
        )->assertOk();
    }
    
    private function geo() {
        $this->withHeaders([
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache'
        ])->postJson(
            route('tgbot.webhook', [
                'token' => config('telegram.webhook_secret_token')
            ]),
            Commands::geo()
        )->assertOk();
    }
}
