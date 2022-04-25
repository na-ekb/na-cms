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

use NovaComponents\NovaDependencyContainer\HasDependencies;
use NovaComponents\TextAutoComplete\TextAutoComplete;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Models\MeetingDayOneTime as MeetingDayOneTimeModel;
use App\Models\MeetingDayFormat;
use App\Models\MeetingDay;


class MeetingDayOneTime extends MeetingDayRegular
{
    use PermissionsBasedAuthTrait, HasDependencies;

    /**
     * {@inheritdoc}
     */
    public static $model = MeetingDayOneTimeModel::class;

    /**
     * {@inheritdoc}
     */
    public static function label()
    {
        return __('admin/resources/meetings.fields.days-one-time');
    }

    /**
     * {@inheritdoc}
     */
    public static function singularLabel()
    {
        return __('admin/resources/meetings.group');
    }

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
                ])->size('w-1/2'),

            TextAutoComplete::make(__('admin/resources/days.fields.format_second'), 'format_second')
                ->items(
                    MeetingDay::all()
                        ->pluck('format_second')
                        ->filter()
                        ->values()
                        ->toArray()
                )
                ->translatable()
                ->size('w-1/2'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(Request $request)
    {
        return [];
    }
}
