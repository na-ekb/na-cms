<?php

namespace NaEkb\Groups\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Attributes\Description;
use Illuminate\Support\Facades\Lang;

/**
 * @method static static Monday()
 * @method static static Tuesday()
 * @method static static Wednesday()
 * @method static static Thursday()
 * @method static static Friday()
 * @method static static Saturday()
 * @method static static Sunday()
 */
final class MeetingFormats extends Enum implements LocalizedEnum
{
    const Open      = 1;
    const Closed    = 2;
    const Business  = 3;
    const Committee = 4;

    public static function getLocalizationKey(): string
    {
        return 'naekb.groups::enums.' . static::class;
    }

    public static function getLocalizationDescriptionKey(): string
    {
        return 'naekb.groups::enumsDesc.' . static::class;
    }

    public static function getLocalized(mixed $value): array
    {
        return [
            'name' => static::getLocalizedName($value),
            'description' => static::getLocalizedDescription($value)
        ];
    }

    protected static function getLocalizedName(mixed $value): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationKey() . '.' . $value;
            if (Lang::has($localizedStringKey)) {
                return __($localizedStringKey);
            }
        }

        return null;
    }

    protected static function getLocalizedDescription(mixed $value): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationDescriptionKey() . '.' . $value;
            if (Lang::has($localizedStringKey)) {
                return __($localizedStringKey);
            }
        }

        return null;
    }

    public static function asSelectArrayNames(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $value) {
            $selectArray[$value] = static::getLocalizedName($value);
        }

        return $selectArray;
    }
}
