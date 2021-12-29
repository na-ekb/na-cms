<?php

namespace App\Nova\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasPermissions;

class PermissionBooleanGroup extends BooleanGroup
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null, $labelAttribute = null)
    {
        $permissionClass = app(PermissionRegistrar::class)->getPermissionClass();

        parent::__construct(
            $name,
            $attribute,
            $resolveCallback ?? static function (Collection $permissions) {
                return $permissions->mapWithKeys(function ($permission) {
                    return [$permission->name => true];
                });
            }
        );

        $options = $permissionClass::get()->mapWithKeys(function($value) {
            $langIndex = explode('.', $value->name);
            if ($this->showOnIndex == true && $this->showOnDetail == true) {
                return [
                    $value->name => __("admin/permissions/roles.permissions-{$langIndex[0]}")
                        . ': ' .
                        __("admin/permissions/permissions.{$langIndex[1]}")
                ];
            } else {
                return [
                    $value->name => __("admin/permissions/permissions.{$langIndex[1]}")
                ];
            }
        })->toArray();

        $this->options($options);
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        return data_get($resource, 'permissions');
    }

    /**
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param HasPermissions $model
     * @param string $attribute
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (Str::startsWith($requestAttribute, 'permissions.')) {
            $allPermissions = [];
            collect($request->all())->filter(function ($value, $key) {
                return Str::startsWith($key, 'permissions');
            })->map(function ($value) use (&$allPermissions) {
                $value = json_decode($value, true);
                foreach ($value as $permission => $isChecked) {
                    if ($isChecked) {
                        $allPermissions[] = $permission;
                    }
                }
                return true;
            });
        } else {
            if (! $request->exists($requestAttribute)) {
                return;
            }

            $allPermissions = collect(json_decode($request[$requestAttribute], true))
                ->filter(static function (bool $value) {
                    return $value;
                })
                ->keys()
                ->toArray();
        }

        $model->syncPermissions($allPermissions);
    }

    /**
     * Filter options for some group
     *
     * @param string $group
     * @return self
     */
    public function filter(string $group) :self {
        $permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
        $options = $permissionClass::get()
            ->filter(function ($value, $key) use ($group) {
                return Str::startsWith($value->name, "{$group}.");
            })->mapWithKeys(function($value) {
                $langIndex = explode('.', $value->name);
                if ($this->showOnIndex == true && $this->showOnDetail == true) {
                    return [
                        $value->name => __("admin/permissions/roles.permissions-{$langIndex[0]}")
                                        . ': ' .
                                        __("admin/permissions/permissions.{$langIndex[1]}")
                    ];
                } else {
                    return [
                        $value->name => __("admin/permissions/permissions.{$langIndex[1]}")
                    ];
                }
            })->toArray();
        $this->options($options);
        return $this;
    }
}
