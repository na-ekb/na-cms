<?php
namespace Modules\TgBot\Commands;

use Illuminate\Support\Collection;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
Use Carbon\Carbon;

use App\Helpers\Geo;
use Modules\TgBot\Entities\TgMeeting;
use Modules\TgBot\Helpers\GroupFormatter;

class GeoCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'geo';

    /** @var string Command Argument Pattern */
    protected $pattern = 'geo{region?}';

    /**
     * @var string Command Description
     */
    protected $description;

    public function __construct() {
        $this->description = __('tgbot::commands.geo.description');
    }

    /**
     * @inheritdoc
     */
    public function handle(array $arguments = [])
    {
        $arguments['region'] = $arguments['region'] ?? 0;
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $message = $this->getUpdate()->getMessage();
        if ($message->has('location')) {
            $groups = TgMeeting::where('location', '!=', 'онлайн')
                ->where(function($q) use ($arguments) {
                    if (!empty(config('primary.primary_city'))) {
                        if ((int) $arguments['region'] == 1) {
                            return $q->where('region', '!=', config('primary.primary_city'));
                        }
                        return $q->where('region', config('primary.primary_city'));
                    }
                    return $q;
                })
                ->where('day', Carbon::now()->dayOfWeek)
                ->get();

            if ($groups->count() > 0) {
                $closest = Geo::getClosest(
                    array_values($message->getLocation()->toArray()),
                    $groups->map(function ($item) {
                        return [$item->latitude, $item->longitude];
                    })->toArray()
                );
                $text = 'Ближайшие собрания сегодня: ' . PHP_EOL . PHP_EOL;
                
                
                TgMeeting::where([
                    'latitude'  => $closest->latitude,
                    'longitude' => $closest->longitude,
                    'day'       => Carbon::now()->dayOfWeek
                ])->withCasts([
                    'time' => 'datetime',
                    'end_time' => 'datetime'
                ])->get()->each(function($meeting) use (&$text) {
                    $text .= GroupFormatter::format($meeting);
                });
            } else {
                $text = __('tgbot::commands.geo.not_found');
            }
        } else {
            $text = $this->description;
        }
        
        $this->replyWithMessage([
            'text'          => $text,
            'reply_markup'  => new Keyboard([
                'inline_keyboard' => [
                    [
                        Keyboard::button([
                            'text'          => __('tgbot::commands.back'),
                            'callback_data' => "groups  {$arguments['region']}"
                        ]),
                        Keyboard::button([
                            'text'          => __('tgbot::commands.main'),
                            'callback_data' => 'start'
                        ])
                    ]
                ]
            ]),
            'parse_mode'    => 'HTML',
            'disable_web_page_preview' => 1
        ]);

        return true;
    }
}
