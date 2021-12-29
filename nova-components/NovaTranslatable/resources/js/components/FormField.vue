<template>
    <div class="translatable-field" ref="main">
        <div class="locale-tabs w-100 absolute" ref="tabs">
            <locale-tabs
                :locales="locales"
                :active-locale="activeLocale"
                :display-type="field.translatable.display_type"
                :errors="errors"
                :error-attributes="errorAttributes"
                @tabClick="setActiveLocale"
                @doubleClick="setAllLocale"
            />
        </div>
        <div v-for="locale in locales" :key="locale.key">
            <component
                v-show="locale.key === activeLocale"
                :is="'form-' + field.translatable.original_component"
                :field="fields[locale.key]"
                :resource-name="resourceName"
                :errors="errors"
                :class="{ 'remove-bottom-border': removeBottomBorder() }"
                ref="input"
                :show-help-text="showHelpText"
            />
        </div>
    </div>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import TranslatableField from '../mixins/TranslatableField';
import LocaleTabs from './LocaleTabs';

export default {
  components: { LocaleTabs },
  mixins: [HandlesValidationErrors, FormField, TranslatableField],
  props: ['field', 'resourceId', 'resourceName'],
  methods: {
    setInitialValue() {
      // Do nothing
    },

    isKeyAnArray(key) {
      const ARR_REGEX = () => /\[\d+\]$/g;
      return !!key.match(ARR_REGEX());
    },

    getKeyAndValue(rawKey, locale, formData) {
      const ARR_REGEX = () => /\[\d+\]$/g;
      const LOC_LEN = locale.key.length + 1;

      let key = rawKey;

      // Remove '.en' ending from key
      if (key.slice(-LOC_LEN) === `.${locale.key}`) key = key.slice(0, -LOC_LEN);

      // Is key is an array, we need to remove the '.en' part from '.en[0]'
      const isArray = !!key.match(ARR_REGEX());
      if (isArray) {
        const result = ARR_REGEX().exec(key);
        key = `${key.slice(0, result.index - LOC_LEN)}${key.slice(result.index)}`;
      }

      if (isArray) {
        const result = ARR_REGEX().exec(key);
        return [`${key.slice(0, result.index)}[${locale.key}]${key.slice(result.index)}`, formData.get(rawKey)];
      } else {
        return [`${key}[${locale.key}]`, formData.get(rawKey)];
      }
    },

    fill(formData) {
      try {
        if (this.isFlexible && this.isFile)
          return alert('Sorry, nova-translatable File and Image fields inside Flexible currently do not work.');

        const data = {};
        const originalAttribute = this.field.translatable.original_attribute;

        for (const locale of this.locales) {
          const tempFormData = new FormData();
          const field = this.fields[locale.key];
          field.fill(tempFormData);

          const formDataKeys = Array.from(tempFormData.keys());
          for (const rawKey of formDataKeys) {
            const [key, value] = this.getKeyAndValue(rawKey, locale, tempFormData);

            if ((this.isFlexible && key.endsWith(originalAttribute + `[${locale.key}]`)) || this.isSimpleRepeatable) {
              if (this.isKeyAnArray(rawKey)) {
                if (!data[locale.key]) data[locale.key] = [];
                data[locale.key].push(value);
              } else {
                data[locale.key] = value;
              }
            } else {
              formData.append(key, value);
            }
          }
        }

        if (this.isFlexible || this.isSimpleRepeatable) formData.append(originalAttribute, JSON.stringify(data));
        return;
      } catch (e) {
        console.error(e);
      }
    },
  },
  mounted() {
      let classes = [];
      if (Array.isArray(this.$refs.input)) {
          for (let i = 0; i < this.$refs.input.length; i++) {
              let el = this.$refs.input[i].$el;
              if (!classes.includes(el.classList.value)) {
                  classes.push(el.classList.value);
              }
              el.classList.value = 'w-full';
          }
      } else {
          classes.push(this.$refs.input.$el.classList.value);
          this.$refs.input.$el.classList.value = 'w-full';
      }
      this.$refs.main.classList.value = this.$refs.main.classList.value + ' ' + classes.join(' ');
      this.$refs.tabs.classList.value = this.$refs.tabs.classList.value + ' ' + classes.join(' ');
  },
  computed: {
    errorAttributes() {
      const locales = this.locales;
      const errorAttributes = {};
      for (const locale of locales) {
        errorAttributes[locale.key] = `${this.field.attribute}.${locale.key}`;
      }
      return errorAttributes;
    },
  },
};
</script>
