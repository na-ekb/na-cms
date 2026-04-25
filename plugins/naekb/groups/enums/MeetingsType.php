<?php

namespace NaEkb\Groups\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static Live()
 * @method static static LiveAndStream()
 * @method static static OnlyStream()
 */
final class MeetingsType extends Enum implements LocalizedEnum
{
    const Live          = 0;
    const LiveAndStream = 1;
    const OnlyStream    = 2;

    public static function getLocalizationKey(): string
    {
        return 'naekb.groups::enums.' . static::class;
    }
}
