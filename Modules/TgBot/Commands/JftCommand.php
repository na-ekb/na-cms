<?php
namespace Modules\TgBot\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

use Modules\Site\Entities\Jft;
use Modules\Site\Jobs\Jft as JftJob;

class JftCommand extends AbstractCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'jft';

    /**
     * @inheritdoc
     */
    public function main(array $arguments = [])
    {
        if (!Jft::today()->exists()) {
            try {
                JftJob::dispatchSync();
            } catch (\Throwable $e) {
                Log::error("Jft sync error {$e->getMessage()}");
            }
        }
        $jft = Jft::today()->first();

        $text = "{$this->description}:" . PHP_EOL . PHP_EOL;
        $text .= "<b>{$jft->header}</b>" . PHP_EOL . PHP_EOL;
        $text .= "<i>{$jft->quote}</i>" . PHP_EOL;
        $text .= "<b>{$jft->from}</b>";

        $this->reply($text, [
            [
                Keyboard::button([
                    'text'  => __('tgbot::commands.jft.more'),
                    'url'   => config('Site.site_jft_link', 'https://na-russia.org/eg')
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.main'),
                    'callback_data' => 'start'
                ])
            ]
        ]);
    }
}
