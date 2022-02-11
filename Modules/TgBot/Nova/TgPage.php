<?php

namespace Modules\TgBot\Nova;

use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Hidden;

use Laravel\Nova\Http\Requests\NovaRequest;
use NovaAttachMany\AttachMany;

use App\Nova\Resources\Resource;

class TgPage extends Resource
{
    /**
     * {@inheritdoc}
     */
    public static $model = \Modules\TgBot\Entities\TgPage::class;

    /**
     * {@inheritdoc}
     */
    public static $title = 'title';

    /**
     * {@inheritdoc}
     */
    public static $search = [
        'id',
    ];

    /**
     * {@inheritdoc}
     */
    public static function group()
    {
        return __('tgbot::admin/resources/groups.tgbot');
    }

    /**
     * {@inheritdoc}
     */
    public static function label()
    {
        return __('site::admin/resources/pages.pages');
    }

    /**
     * {@inheritdoc}
     */
    public static function singularLabel()
    {
        return __('site::admin/resources/pages.page');
    }

    /**
     * {@inheritdoc}
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('hidden', 0)->when(empty($request->get('orderBy')), function($q) {
            $q->getQuery()->orders = [];
            return $q->orderBy('id');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')
                ->exceptOnForms()
                ->sortable()
                ->size('w-full'),
            Text::make(__('tgbot::admin/resources/pages.fields.title'), 'title')
                ->translatable()
                ->sortable()
                ->rules(['max:100'])
                ->size('w-1/3'),
            Number::make(__('tgbot::admin/resources/pages.fields.order'), 'order')
                ->sortable()
                ->rules(['max:100'])
                ->size('w-1/3'),
            Boolean::make(__('tgbot::admin/resources/pages.fields.active'), 'active')
                ->sortable()
                ->size('w-1/3'),
            AttachMany::make(__('tgbot::admin/resources/pages.fields.parents'), 'parents', self::class)
                ->nullable()
                ->size('w-1/2'),
            AttachMany::make(__('tgbot::admin/resources/pages.fields.childrens'), 'childrens', self::class)
                ->nullable()
                ->size('w-1/2'),
            Trix::make(__('tgbot::admin/resources/pages.fields.content'), 'content')
                ->translatable()
                ->hideFromIndex()
                ->size('w-full')
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
