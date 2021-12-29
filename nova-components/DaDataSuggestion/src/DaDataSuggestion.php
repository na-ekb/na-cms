<?php
namespace NovaComponents\DaDataSuggestion;

use Laravel\Nova\Fields\Field;

class DaDataSuggestion extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'dadata-suggestion';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
    }

    public function postalCode(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function country(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function federalDistrict(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function regionType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function regionTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function region(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function areaType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function areaTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function area(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityWithType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function city(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityDistrictWithType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityDistrictType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityDistrictTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function cityDistrict(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function settlementWithType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function settlementType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function settlementTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function settlement(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function streetType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function streetTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function street(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function streetWithType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function houseType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function houseTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function house(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function blockType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function blockTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function block(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function flatType(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function flatTypeFull(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function flat(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function geoLat(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    public function geoLon(string $field){
        return $this->withMeta([__FUNCTION__ => $field]);
    }
}
