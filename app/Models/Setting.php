<?php

namespace App\Models;

use Illuminate\Support\Collection as BaseCollection;
use OptimistDigital\NovaSettings\Models\Settings;
use OptimistDigital\NovaSettings\NovaSettings;

class Setting extends Settings
{
    public function getValueAttribute()
    {
        $value = $this->attributes['value'] ?? null;
        $casts = NovaSettings::getCasts();
        $castType = $casts[$this->key] ?? null;

        if (!$castType) return $value;

        if (class_exists($castType)) {
            $value = $this->fromJson($value, true);
            if (is_array($value)) {
                foreach ($value as &$item) {
                    $item = $this->getCastModel($castType, $item)->toArray();
                }
            } else {
                $value = $this->getCastModel($castType, $value)->toArray();
            }
            return $value;
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $casts[$this->key], 2)[1]);
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimestamp($value);
        }

        return $value;
    }

    public function setValueAttribute($value)
    {
        $casts = NovaSettings::getCasts();
        $castType = $casts[$this->key] ?? null;

        if (class_exists($castType)) {
            if (count($value) != count($value, COUNT_RECURSIVE)) {
                foreach ($value as &$item) {
                    $model = $this->setCastModel($castType, $item);
                    $item = $model->id;
                }
                $castType::whereNotIn('id', $value)->delete();
            } else {
                $model = $this->setCastModel($castType, $value);
                $value = $model->id;
                $castType::whereNot('id', $value)->delete();
            }
        }
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    protected function setCastModel($castModel, $attributes) {
        $id = $attributes['id'] ?? null;
        if (isset($attributes['id'])) {
            unset($attributes['id']);
        }
        return $castModel::updateOrCreate(
            ['id' => $id],
            $attributes
        );
    }

    protected function getCastModel($castModel, $id)
    {
        return $castModel::find($id);
    }

    public static function getValueForKey($key)
    {
        $setting = static::where('key', $key)->get()->first();
        return isset($setting) ? $setting->value : null;
    }
}
