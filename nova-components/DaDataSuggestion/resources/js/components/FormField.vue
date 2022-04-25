<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <div
                v-click-outside="hideSuggestions"
                class="suggestion-wrapper clearfix">
                <input
                    :id="field.name"
                    type="text"
                    class="w-full form-control form-input form-input-bordered"
                    :class="errorClasses"
                    :placeholder="field.name"
                    v-model="value"
                    @focus="active=true"
                />
                <suggestion-list
                    v-show="active"
                    v-model="value"
                    v-on:select="onSelectSuggestion"
                ></suggestion-list>
            </div>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'
    import SuggestionList from './SuggestionList';
    import vClickOutside from 'v-click-outside'

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        components: {
            SuggestionList
        },

        directives: {
            clickOutside: vClickOutside.directive
        },

        data: function () {
            return {
                active: false
            }
        },

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || ''
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || '')
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },

            hideSuggestions(){
                this.active = false;
            },

            changeValue(field, ru, en) {
                if (document.getElementById(`${field}.en`) != null && ru != null) {
                    let el = document.getElementById(`${field}.en`);
                    if (el.nodeName !== 'INPUT') {
                        let inputs = el.getElementsByTagName('input');
                        if (inputs.length > 0) {
                            inputs[0].value = en;
                        }
                    }
                    Nova.$emit(`${field}.en-value`, en);
                }
                if (document.getElementById(`${field}.ru`) != null && en != null) {
                    let el = document.getElementById(`${field}.ru`);
                    if (el.nodeName !== 'INPUT') {
                      let inputs = el.getElementsByTagName('input');
                      if (inputs.length > 0) {
                        inputs[0].value = ru;
                      }
                    }
                    Nova.$emit(`${field}.ru-value`, ru);
                }

                if (document.getElementById(`${field}`) != null && ru != null) {
                    Nova.$emit(`${field}-value`, ru);
                }
            },

            onSelectSuggestion(suggestion, suggestions){
                let suggestionLang = {
                    en: null,
                    ru: null
                };
                for(let i = 0; i < suggestions.en.length; i++) {
                    if(suggestions.en[i].data.kladr_id == suggestion.data.kladr_id) {
                        suggestionLang.en = suggestions.en[i]
                    }
                }
                for(let i = 0; i < suggestions.ru.length; i++) {
                    if(suggestions.ru[i].data.kladr_id == suggestion.data.kladr_id) {
                        suggestionLang.ru = suggestions.ru[i]
                    }
                }

                this.hideSuggestions();
                this.value = suggestion.unrestricted_value;

                for(let key in this.field) {
                    const fields = [
                        'postalCode', 'country', 'federalDistrict', 'regionType',
                        'regionTypeFull', 'region', 'areaType', 'areaTypeFull',
                        'area', 'cityWithType', 'cityType', 'cityTypeFull',
                        'city', 'cityDistrictWithType', 'cityDistrictType', 'cityDistrictTypeFull',
                        'cityDistrict', 'settlementWithType', 'settlementType', 'settlementTypeFull',
                        'settlement', 'streetType', 'streetTypeFull', 'street',
                        'houseType', 'houseTypeFull', 'house', 'blockType',
                        'blockTypeFull', 'block', 'flatType', 'flatTypeFull',
                        'flat', 'geoLat', 'geoLon', 'streetWithType', 'streetWithHouse', 'flatWithType', 'metro'
                    ];
                    if(fields.includes(key) && this.field[key] !== undefined) {
                        let kkey = key.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
                        if (key === 'streetWithHouse') {
                          let ru = `${suggestionLang.ru.data['street_with_type']}, ${suggestionLang.ru.data['house']}`;
                          if (suggestionLang.ru.data['block_type_full'] != null) {
                            ru += ` ${suggestionLang.ru.data['block_type_full']} ${suggestionLang.ru.data['block']}`;
                          }
                          let en = `${suggestionLang.en.data['street_with_type']}, ${suggestionLang.en.data['house']}`;
                          if (suggestionLang.en.data['block_type_full'] != null) {
                            en += ` ${suggestionLang.en.data['block_type_full']} ${suggestionLang.en.data['block']}`;
                          }
                          this.changeValue(this.field[key], ru, en);
                        } else if (key === 'flatWithType') {
                          let ru = null;
                          let en = null;
                          if (suggestionLang.ru.data['block_type_full'] != null) {
                            ru = `${suggestionLang.ru.data['flat_type_full']} ${suggestionLang.ru.data['flat']}`;
                          }
                          if (suggestionLang.en.data['block_type_full'] != null) {
                            en = `${suggestionLang.en.data['flat_type_full']} ${suggestionLang.en.data['flat']}`;
                          }
                          this.changeValue(this.field[key], ru, en);
                        } else {
                          this.changeValue(
                              this.field[key],
                              suggestionLang.ru.data[kkey],
                              suggestionLang.en.data[kkey]
                          );
                        }
                    }
                }
            }
        },
        mounted() {
            this.popupItem = this.$el
        }
    }
</script>
