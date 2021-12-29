<?php

namespace App\Nova\Resources;

use App\Enums\MeetingDayOnline;
use App\Models\MeetingDay;
use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Lhilton\TextAutoComplete\TextAutoComplete;
use Laraning\NovaTimeField\TimeField;

use App\Models\MeetingDayFormat;
use App\Enums\MeetingDayWeekdaysType;
use App\Enums\Weekdays;

class MeetingDayRegularOnline extends MeetingDayRegular
{
    /**
     * {@inheritdoc}
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('meeting'),

            Select::make(__('admin/resources/days.fields.type'), 'day_type')
                ->options(MeetingDayWeekdaysType::asSelectArray())
                ->size('w-1/2'),

            Select::make(__('admin/resources/days.fields.day'), 'day')
                ->options(Weekdays::asSelectArray())
                ->size('w-1/2'),

            TimeField::make(__('admin/resources/days.fields.time'), 'time')
                ->withTimezoneAdjustments()
                ->size('w-1/2'),

            Number::make(__('admin/resources/days.fields.duration'), 'duration')
                ->placeholder(60)
                ->size('w-1/2'),

            Select::make(__('admin/resources/days.fields.online'), 'online')
                ->options(MeetingDayOnline::asSelectArray())
                ->default(function() { return MeetingDayOnline::Online; })
                ->size('w-1/3'),

            Select::make(__('admin/resources/days.fields.format'), 'format')
                ->options(
                    MeetingDayFormat::all()
                        ->mapWithKeys(function ($item, $key) {
                            return [$item->id => "{$item->title} — {$item->description}"];
                        })->toArray()
                )->size('w-1/3'),

            TextAutoComplete::make(__('admin/resources/days.fields.format_second'), 'format_second')
                ->items(
                    MeetingDay::all()
                        ->pluck('format_second')
                        ->filter()
                        ->values()
                        ->toArray()
                )->translatable()
                ->size('w-1/3'),
        ];
    }
}
