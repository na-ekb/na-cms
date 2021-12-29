<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static Regular()
 * @method static static First()
 * @method static static Second()
 * @method static static Third()
 * @method static static Fourth()
 * @method static static Last()
 */
final class MeetingDayWeekdaysType extends Enum implements LocalizedEnum
{
    const Regular   = 1;
    const First     = 2;
    const Second    = 3;
    const Third     = 4;
    const Fourth    = 5;
    const Last      = 6;
}
