<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Lhilton\TextAutoComplete\TextAutoComplete;
use Laraning\NovaTimeField\TimeField;
use Techouse\IntlDateTime\IntlDateTime as DateTime;
use Carbon\Carbon;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Models\MeetingDayOneTime as MeetingDayOneTimeModel;
use App\Models\MeetingDayFormat;
use App\Models\MeetingDay;
use NovaComponents\NovaDependencyContainer\HasDependencies;

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
                ->hideUserTimeZone()
                ->minDate(Carbon::now())
                ->size('w-1/3'),

            TimeField::make(__('admin/resources/days.fields.time'), 'time')
                ->withTimezoneAdjustments()
                ->size('w-1/3'),

            Number::make(__('admin/resources/days.fields.duration'), 'duration')
                ->placeholder(60)
                ->size('w-1/3'),

            Select::make(__('admin/resources/days.fields.format'), 'format')
                ->options(
                    MeetingDayFormat::all()
                        ->mapWithKeys(function ($item, $key) {
                            return [$item->id => "{$item->title} — {$item->description}"];
                        })->toArray()
                )->size('w-1/2'),

            TextAutoComplete::make(__('admin/resources/days.fields.format_second'), 'format_second')
                ->items(
                    MeetingDay::all()
                        ->pluck('format_second')
                        ->filter()
                        ->values()
                        ->toArray()
                )->translatable()
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
