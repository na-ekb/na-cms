<?php namespace NAEkb\VkBot\Classes;

use NAEkb\VKBot\Models\CleanDate;
use NAEkb\VKBot\Models\State;

class GroupRemoveCommand extends AbstractCommand
{
    /** @var string Default action method name */
    protected string $name = 'groupRemove';

    protected function main()
    {
        CleanDate::where('user_id', $this->userId)->delete();
        State::where('user_id', $this->userId)->delete();

        return 'ok';
    }
}
