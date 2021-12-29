<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use Laravel\Nova\Filters\BooleanFilter;

use Spatie\Permission\Models\Role;

class UserRole extends BooleanFilter
{
    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return __('admin/filters.user-role');
    }

    /**
     * {@inheritdoc}
     */
    public function default()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, $query, $value)
    {
        if ($value == true) {
            return $query;
        }

        return $query->role($value);
    }

    /**
     * {@inheritdoc}
     */
    public function options(Request $request)
    {
        return Role::all()->mapWithKeys(function($model) {
            $name = implode(preg_split('/(?=[A-Z])/', Str::singular(Str::ucfirst($model->name))));
            return [$name => $model->name];
        })->toArray();
    }
}
