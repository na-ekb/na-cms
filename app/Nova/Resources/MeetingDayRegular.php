<?php

namespace App\Nova\Resources;

use App\Models\MeetingDay;
use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Lhilton\TextAutoComplete\TextAutoComplete;
use OptimistDigital\NovaSimpleRepeatable\SimpleRepeatable;
use Laraning\NovaTimeField\TimeField;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Models\MeetingDayRegular as MeetingDayRegularModel;
use App\Models\MeetingDayFormat;
use App\Nova\Resources\Meeting;
use App\Enums\MeetingDayWeekdaysType;
use App\Enums\Weekdays;
use NovaComponents\NovaDependencyContainer\HasDependencies;

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
