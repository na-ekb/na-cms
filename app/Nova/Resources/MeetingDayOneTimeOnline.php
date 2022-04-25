<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;

use Laraning\NovaTimeField\TimeField;
use Techouse\IntlDateTime\IntlDateTime as DateTime;
use Carbon\Carbon;

use NovaComponents\TextAutoComplete\TextAutoComplete;

use App\Models\MeetingDayFormat;
use App\Models\MeetingDay;
use App\Enums\MeetingDayOnline;

class MeetingDayOneTimeOnline extends MeetingDayOneTime
{
    /**
     * {@inheritdoc}
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('meeting'),

            DateTime::make(__('admin/resources/days.fields.date'), 'date')
                ->placeholder('23.08.2021')
                ->required()
                ->rules(['required', 'date_format:Y-m-d H:i:s O'])
                ->hideUserTimeZone()
                ->minDate(Carbon::now())
                ->size('w-1/3'),

            TimeField::make(__('admin/resources/days.fields.time'), 'time')
                ->withTimezoneAdjustments()
                ->required()
                ->rules(['required', 'date_format:H:i'])
                ->size('w-1/3'),

            Number::make(__('admin/resources/days.fields.duration'), 'duration')
                ->placeholder(60)
                ->required()
                ->rules(['required', 'integer'])
                ->size('w-1/3'),

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
