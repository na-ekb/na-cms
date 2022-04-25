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
use NovaComponents\NovaDependencyContainer\HasDependencies;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Models\MeetingDayRegular as MeetingDayRegularModel;
use App\Models\MeetingDayFormat;
use App\Models\MeetingDay;
use App\Models\Setting;
use App\Enums\MeetingDayWeekdaysType;
use App\Enums\Weekdays;

class MeetingDayRegular extends Resource
{
    use PermissionsBasedAuthTrait, HasDependencies;

    /**
     * {@inheritdoc}
     */
    public static $model = MeetingDayRegularModel::class;

    /**
     * {@inheritdoc}
     */
    public static $title = 'title';

    /**
     * {@inheritdoc}
     */
    public static $displayInNavigation = false;

    /**
     * {@inheritdoc}
     */
    public static $search = [
        'title',
        'city',
        'location'
    ];

    /**
     * Abilities by permissions
     *
     * @var array
     */
    public static $permissionsForAbilities = [
        'viewAny'           => 'meeting.view',
        'view'              => 'meeting.view',
        'create'            => 'meeting.create',
        'update'            => 'meeting.update',
        'delete'            => 'meeting.delete',
        'restore'           => 'meeting.restore',
        'forceDelete'       => 'meeting.forceDelete',
        'addMeeting'        => 'meeting.addAttributes',
        'attachMeeting'     => 'meeting.attachAttributes',
        'detachMeeting'     => 'meeting.detachAttributes',
    ];

    /**
     * {@inheritdoc}
     */
    public static function label()
    {
        return __('admin/resources/meetings.fields.days');
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
                ->size('w-1/2'),

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
