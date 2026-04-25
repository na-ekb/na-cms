<?php

namespace NaEkb\Groups\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;
use Illuminate\Support\Facades\Lang;

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
    const Regular   = 0;
    const First     = 1;
    const Second    = 2;
    const Third     = 3;
    const Fourth    = 4;
    const Last      = 5;

    public static function getLocalizationKey(): string
    {
        return 'naekb.groups::enums.' . static::class;
    }

    protected static function getLocalizedDescriptionWeekday(mixed $value, int $weekday): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationKey() . '_display.' . $value;
            if (Lang::has($localizedStringKey)) {
                $num = match ($weekday) {
                    0 => 2,
                    1 => 0,
                    2 => 0,
                    3 => 1,
                    4 => 0,
                    5 => 1,
                    6 => 1,
                    7 => 2
                };
                $weekday = mb_strtolower(Weekdays::fromValue($weekday)->description);
                return trans_choice($localizedStringKey, $num, ['weekday' => $weekday]);
            }
        }

        return null;
    }

    public static function asSelectArrayLocalized(int $weekday): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $value) {
            $selectArray[$value] = static::getLocalizedDescriptionWeekday($value, $weekday);
        }

        return $selectArray;
    }
}
