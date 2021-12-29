<?php

namespace App\Nova\Resources;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;

abstract class Resource extends NovaResource
{
    /**
     * {@inheritdoc}
     */
    public static $polling = true;

    /**
     * {@inheritdoc}
     */
    public static function group()
    {
        return __('admin/resources/groups.other');
    }

    /**
     * {@inheritdoc}
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * {@inheritdoc}
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }
}
