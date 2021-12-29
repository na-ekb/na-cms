<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\Place as PlaceField;

class Place extends PlaceField {
    /** {@inheritdoc} */
    public $component = 'place-field-locales';
}