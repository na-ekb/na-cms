<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;

use Laraning\NovaTimeField\TimeField;

use NovaComponents\TextAutoComplete\TextAutoComplete;

use App\Models\MeetingDayFormat;
use App\Models\MeetingDay;
use App\Models\Setting;
use App\Enums\MeetingDayWeekdaysType;
use App\Enums\Weekdays;
use App\Enums\MeetingDayOnline;

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
                ->default(MeetingDayWeekdaysType::Regular())
                ->required()
                ->rules([
                    'required',
                    Rule::in(MeetingDayWeekdaysType::getValues()),
                ])
                ->size('w-1/2'),

            Select::make(__('admin/resources/days.fields.day'), 'day')
                ->options(Weekdays::asSelectArray())
                ->required()
                ->rules([
                    'required',
                    Rule::in(Weekdays::getValues()),
                ])
                ->size('w-1/2'),

            TimeField::make(__('admin/resources/days.fields.time'), 'time')
                ->withTimezoneAdjustments()
                ->required()
                ->rules(['required', 'date_format:H:i'])
                ->size('w-1/2'),

            Number::make(__('admin/resources/days.fields.duration'), 'duration')
                ->placeholder(60)
                ->default(Setting::getValueForKey('meetings_duration'))
                ->required()
                ->rules(['required', 'integer'])
                ->size('w-1/2'),

            Select::make(__('admin/resources/days.fields.online'), 'online')
                ->options(MeetingDayOnline::asSelectArray())
                ->required()
                ->rules([
                    'required',
                    Rule::in(MeetingDayOnline::getValues()),
                ])
                ->default(MeetingDayOnline::OnlyOnline)
                ->size('w-1/3'),

            Select::make(__('admin/resources/days.fields.format'), 'format')
                ->options(
                    MeetingDayFormat::all()
                        ->mapWithKeys(function ($item, $key) {
                            return [$item->id => "{$item->title} — {$item->description}"];
                        })->toArray()
                )
                ->default(MeetingDayFormat::first()->id ?? null)
                ->required()
                ->rules([
                    'required',
                    Rule::in(MeetingDayFormat::all()->pluck('id')->toArray()),
                ])
                ->size('w-1/3'),

            TextAutoComplete::make(__('admin/resources/days.fields.format_second'), 'format_second')
                ->items(
                    MeetingDay::all()
                        ->pluck('format_second')
                        ->filter()
                        ->values()
                        ->toArray()
                )
                ->translatable()
                ->size('w-1/3'),
        ];
    }
}
