<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static Online()
 * @method static static OnlyOnline()
 * @method static static Offline()
 */
final class MeetingDayOnline extends Enum implements LocalizedEnum
{
    const Online        = 0;
    const OnlyOnline    = 1;
    const Offline       = 2;
}
