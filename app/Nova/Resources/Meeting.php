<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;

use NovaComponents\DaDataSuggestion\DaDataSuggestion;
use NovaComponents\NovaDependencyContainer\HasDependencies;
use NovaComponents\NovaDependencyContainer\NovaDependencyContainer;
use NovaComponents\NovaNestedForm\NestedForm;
use NovaComponents\NovaTranslatable\HandlesTranslatable;
use NovaComponents\TextAutoComplete\TextAutoComplete;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Models\Meeting as MeetingModel;
use App\Models\Setting;
use App\Enums\MeetingsType;
use App\Enums\MeetingDayWeekdaysType;
use App\Enums\Weekdays;

class Meeting extends Resource
{
    use PermissionsBasedAuthTrait, HasDependencies, HandlesTranslatable;

    /**
     * {@inheritdoc}
     */
    public static $model = MeetingModel::class;

    /**
     * {@inheritdoc}
     */
    public static $title = 'title';

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

        'addMeetingDay'     => 'meeting.addAttributes',
        'attachMeetingDay'  => 'meeting.attachAttributes',
        'detachMeetingDay'  => 'meeting.detachAttributes',

        'addMeetingDayRegular'     => 'meeting.addAttributes',
        'attachMeetingDayRegular'  => 'meeting.attachAttributes',
        'detachMeetingDayRegular'  => 'meeting.detachAttributes',

        'addMeetingDayOneTime'     => 'meeting.addAttributes',
        'attachMeetingDayOneTime'  => 'meeting.attachAttributes',
        'detachMeetingDayOneTime'  => 'meeting.detachAttributes',
    ];

    /**
     * {@inheritdoc}
     */
    public static function group()
    {
        return __('admin/resources/groups.groups');
    }

    /**
     * {@inheritdoc}
     */
    public static function label()
    {
        return __('admin/resources/meetings.groups');
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
        $meetingDayWeekdays = json_encode(MeetingDayWeekdaysType::asSelectArray());
        $weekDays           = json_encode(Weekdays::asSelectArray());
        $newDayRegular      = __('admin/resources/meetings.new.regular');
        $newDayOneTime      = __('admin/resources/meetings.new.one-time');
        $languages          = Setting::getValueForKey('languages');
        $primaryLang        = is_array($languages) ? array_shift($languages) : $languages;

        $meetingDayRegularHeading = "
                    let fn = function(child) {
                        let fieldDayType = child.fields.find(
                            (field) => field.originalAttribute === 'day_type'
                        );
                        
                        let fieldWeekDay = child.fields.find(
                            (field) => field.originalAttribute === 'day'
                        );
                        if (typeof fieldWeekDay == 'undefined' || fieldWeekDay.value == null) {
                            return '{$newDayRegular} ({{INDEX}})';
                        }
                        
                        let weekDay = JSON.parse('{$weekDays}')[fieldWeekDay.value];
                        
                        return JSON.parse('{$meetingDayWeekdays}')[fieldDayType.value] + ' ' +
                        weekDay[0].toLowerCase() + weekDay.substring(1);
                    };
                    fn(this.child);
                ";

        $meetingDayOneTimeHeading = "
                    let fn = function(child) {
                        let fieldDate = child.fields.find(
                            (field) => field.originalAttribute === 'date'
                        );
                        if (typeof fieldDate == 'undefined' || fieldDate.value == null) {
                            return '{$newDayOneTime} ({{INDEX}})';
                        }
                        
                        return moment(fieldDate.value).format('DD.MM.YY');
                    };
                    fn(this.child);
                ";

        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make(__('admin/resources/meetings.fields.city'), 'city')
                ->onlyOnIndex()
                ->sortable(),

            Text::make(__('admin/resources/meetings.fields.title'), 'title')
                ->translatable()
                ->required()
                ->rules(['max:50'])
                ->rulesFor($primaryLang, [
                    'required'
                ])
                ->size('w-full'),

            Text::make(__('admin/resources/meetings.fields.type'), function() {
                return MeetingsType::fromValue($this->type)->description;
            })->showOnDetail()->showOnIndex(),

            Select::make(__('admin/resources/meetings.fields.type'), 'type')
                ->options(MeetingsType::asSelectArray())
                ->rules(
                    'required',
                    Rule::in(MeetingsType::getValues()),
                )
                ->hideFromDetail()
                ->hideFromIndex()
                ->size('w-full'),

            NovaDependencyContainer::make([

                 DaDataSuggestion::make('?????????? ????????????','address-helper')
                    ->city('city')
                    ->geoLat('lat')
                    ->geoLon('long')
                    ->cityDistrictWithType('location')
                    ->streetWithHouse('address')
                    ->flatWithType('address_description')
                    ->size('w-full')
                    ->fillUsing(function() {
                        return null;
                    }),

                TextAutoComplete::make(
                        __('admin/resources/meetings.fields.city'), 'city'
                    )
                    ->items(MeetingModel::getAllUnique('city'))->translatable('w-1/2')
                    ->required()
                    ->rulesFor($primaryLang, [
                        'required'
                    ])
                    ->size('w-1/2'),

                Text::make(__('admin/resources/meetings.fields.address'), 'address')
                    ->translatable('w-1/2')
                    ->required()
                    ->rulesFor($primaryLang, [
                        'required'
                    ])
                    ->size('w-1/2'),

                TextAutoComplete::make(
                        __('admin/resources/meetings.fields.address_description'), 'address_description'
                    )
                    ->items(MeetingModel::getAllUnique('address_description'))->translatable('w-1/2')
                    ->help(__('admin/resources/meetings.fields.address_description_help'))
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.location'), 'location')
                    ->items(MeetingModel::getAllUnique('location'))
                    ->translatable('w-1/2')
                    ->help(__('admin/resources/meetings.fields.location_help'))
                    ->size('w-1/2'),

                Text::make(__('admin/resources/meetings.fields.lat'), 'lat')
                    ->size('w-1/2'),

                Text::make(__('admin/resources/meetings.fields.long'), 'long')
                    ->size('w-1/2'),

            ])->dependsOn('type', MeetingsType::Live)->size('w-full'),

            NovaDependencyContainer::make([

                DaDataSuggestion::make('?????????? ????????????','address-helper')
                    ->city('city')
                    ->geoLat('lat')
                    ->geoLon('long')
                    ->cityDistrictWithType('location')
                    ->streetWithHouse('address')
                    ->flatWithType('address_description')
                    ->size('w-full')
                    ->fillUsing(function() {
                        return null;
                    }),

                TextAutoComplete::make(
                        __('admin/resources/meetings.fields.city'), 'city'
                    )
                    ->items(MeetingModel::getAllUnique('city'))
                    ->translatable('w-1/2')
                    ->required()
                    ->size('w-1/2'),

                Text::make(__('admin/resources/meetings.fields.address'), 'address')
                    ->required()
                    ->translatable('w-1/2')
                    ->size('w-1/2'),

                TextAutoComplete::make(
                    __('admin/resources/meetings.fields.address_description'), 'address_description'
                    )
                    ->items(MeetingModel::getAllUnique('address_description'))
                    ->translatable('w-1/2')
                    ->help(__('admin/resources/meetings.fields.address_description_help'))
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.location'), 'location')
                    ->items(MeetingModel::getAllUnique('location'))
                    ->translatable('w-1/2')
                    ->help(__('admin/resources/meetings.fields.location_help'))
                    ->size('w-1/2'),

                Text::make(__('admin/resources/meetings.fields.lat'), 'lat')
                    ->size('w-1/2'),

                Text::make(__('admin/resources/meetings.fields.long'), 'long')
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.link'), 'link')
                    ->items(MeetingModel::getAllUnique('link'))
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.link_text'), 'link_text')
                    ->help(__('admin/resources/meetings.fields.link_text_help'))
                    ->items(MeetingModel::getAllUnique('link_text'))
                    ->translatable()
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.online'), 'online')
                    ->help(__('admin/resources/meetings.fields.online_help'))
                    ->items(MeetingModel::getAllUnique('online'))
                    ->translatable()
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.password'), 'password')
                    ->items(MeetingModel::getAllUnique('password'))
                    ->size('w-1/2'),

            ])
            ->dependsOn('type', MeetingsType::LiveAndStream)
            ->size('w-full'),

            NovaDependencyContainer::make([

                TextAutoComplete::make(__('admin/resources/meetings.fields.link'), 'link')
                    ->items(MeetingModel::getAllUnique('link'))
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.link_text'), 'link_text')
                    ->help(__('admin/resources/meetings.fields.link_text_help'))
                    ->items(MeetingModel::getAllUnique('link_text'))
                    ->translatable()
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.online'), 'online')
                    ->help(__('admin/resources/meetings.fields.online_help'))
                    ->items(MeetingModel::getAllUnique('online'))
                    ->translatable()
                    ->size('w-1/2'),

                TextAutoComplete::make(__('admin/resources/meetings.fields.password'), 'password')
                    ->items(MeetingModel::getAllUnique('password'))
                    ->size('w-1/2'),

            ])->dependsOn('type', MeetingsType::OnlyStream)->size('w-full'),

            Trix::make(__('admin/resources/meetings.fields.description'), 'description')
                ->help(__('admin/resources/meetings.fields.description_help'))
                ->translatable()
                ->size('w-full'),

            NestedForm::make('MeetingDayRegular')
                ->heading($meetingDayRegularHeading)->displayIf(function ($nestedForm, $request) {
                    return [
                        [ 'attribute' => 'type', 'isNot' => (string) MeetingsType::LiveAndStream, 'isNotEmpty' => true ]
                    ];
                })->title(__('admin/resources/meetings.fields.days'))
                ->open(false),
            NestedForm::make('MeetingDayOneTime')
                ->heading($meetingDayOneTimeHeading)->displayIf(function ($nestedForm, $request) {
                    return [
                        [ 'attribute' => 'type', 'isNot' => (string) MeetingsType::LiveAndStream, 'isNotEmpty' => true ]
                    ];
                })->title(__('admin/resources/meetings.fields.days-one-time'))
                ->open(false),
            NestedForm::make('MeetingDayRegularOnline')
                ->heading($meetingDayRegularHeading)->displayIf(function ($nestedForm, $request) {
                    return [
                        [ 'attribute' => 'type', 'is' => (string) MeetingsType::LiveAndStream ]
                    ];
                })->title(__('admin/resources/meetings.fields.days-one-time'))
                ->open(false),
            NestedForm::make('MeetingDayOneTimeOnline')
                ->heading($meetingDayOneTimeHeading)->displayIf(function ($nestedForm, $request) {
                    return [
                        [ 'attribute' => 'type', 'is' => (string) MeetingsType::LiveAndStream ]
                    ];
                })->title(__('admin/resources/meetings.fields.days-one-time'))
                ->open(false),


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
