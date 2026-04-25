<?php

namespace NaEkb\Groups\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static Online()
 * @method static static OnlyOnline()
 * @method static static Offline()
 */
final class MeetingDayOnline extends Enum implements LocalizedEnum
{
    const Online        = 1;
    const OnlyOnline    = 2;
    const Offline       = 3;

    public static function getLocalizationKey(): string
    {
        return 'naekb.groups::enums.' . static::class;
    }
}
