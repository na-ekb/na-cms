<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

class Module extends Model
{
    use HasTranslations;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'title', 'realname', 'description'
    ];

    /**
     * The attributes that are translatable
     *
     * @var string[]
     */
    public $translatable = [
        'title', 'description'
    ];

    /**
     * Is module active
     *
     * @return bool
     */
    public function getEnabledAttribute() {
        $model = \Module::find($this->realname ?? '');
        return empty($model) ? false : $model->isEnabled();
    }
}
