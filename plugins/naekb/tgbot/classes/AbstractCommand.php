<?php
namespace NAEkb\TgBot\Classes;

use Illuminate\Support\Str;

use Telegram\Bot\Objects\Update;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Keyboard\InlineKeyboardMarkup;

use NAEkb\TgBot\Models\TgState;

abstract class AbstractCommand extends Command
{
    /** @var string Default action method name */
    protected string $defaultAction = 'main';

    /** @var string Command Argument Pattern */
    protected string $pattern = '{action}';

    /** @var TgState|null Current user state */
    protected ?TgState $state;

    /** @var int User id */
    protected int $userId;

    /**
     * @inheritdoc
     */
    public function handle(): bool
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $action = $this->argument('action', $this->defaultAction);
        $this->{$action}();
        return true;
    }


    protected function reply(?string $msg = '', ?array $keyboard = [], ?bool $back = true, ?bool $main = true, ?string $parseMode = 'HTML'): void
    {
        if ($back) {
            $state = TgState::where('user_id', $this->userId)->first();

            if ($main) {
                $keyboard[] = [
                    Keyboard::inlineButton([
                        'text'          => __('naekb.tgbot::commands.back'),
                        'callback_data' => $state->prev
                    ]),
                    Keyboard::inlineButton([
                        'text'          => __('naekb.tgbot::commands.main'),
                        'callback_data' => 'start'
                    ])
                ];
            } else {
                $keyboard[] = [
                    Keyboard::inlineButton([
                        'text'          => __('naekb.tgbot::commands.back'),
                        'callback_data' => $state->prev
                    ])
                ];
            }
        } elseif ($main) {
            $keyboard[] = [
                Keyboard::inlineButton([
                    'text'          => __('naekb.tgbot::commands.main'),
                    'callback_data' => 'start'
                ])
            ];
        }

        $this->replyWithMessage([
            'text'          => $msg ?? $this->description,
            'reply_markup'  => new Keyboard([
                'inline_keyboard' => $keyboard
            ]),
            'parse_mode'    => 'HTML'
        ]);
    }

    protected function isMember()
    {
        return false;
    }

    /**
     * Process Inbound Command.
     */
    public function make(Api $telegram, Update $update, array $entity): mixed
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->entity = $entity;
        $this->arguments = $this->parseCallbackCommandArguments();
        $this->userId = $this->getUpdate()->getMessage()->getChat()->getId();
        return $this->handle();
    }

    /**
     * Parse Command Arguments.
     */
    protected function parseCallbackCommandArguments(): array
    {

        if (!$this->update->has('callback_query') || $this->pattern === '') {
            return [];
        }

        // Generate the regex needed to search for this pattern
        [$pattern, $arguments] = $this->makeRegexPattern();

        preg_match("%{$pattern}%ixmu", $this->update->callbackQuery->get('data'), $matches, PREG_UNMATCHED_AS_NULL);
        return $this->formatMatches($matches, $arguments);
    }

    protected function formatMatches(array $matches, array $arguments): array
    {
        $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        return array_merge(array_fill_keys($arguments, null), $matches);
    }

    protected function makeRegexPattern(): array
    {
        preg_match_all(
            pattern: '#\{\s*(?<name>\w+)\s*(?::\s*(?<pattern>\S+)\s*)?}#ixmu',
            subject: $this->pattern,
            matches: $matches,
            flags: PREG_SET_ORDER
        );

        $patterns = collect($matches)
            ->mapWithKeys(function ($match): array {
                $pattern = $match['pattern'] ?? '[^ ]++';

                return [
                    $match['name'] => "(?<{$match['name']}>{$pattern})?",
                ];
            })
            ->filter();

        $commandName = ($this->aliases === []) ? $this->name : implode('|', [$this->name, ...$this->aliases]);

        return [
            sprintf('%s%s%s', "(?:{$commandName})", '(?:\@[\w]*bot\b)?\s+', $patterns->implode('\s*')),
            $patterns->keys()->all(),
        ];
    }

    abstract protected function main();
}
