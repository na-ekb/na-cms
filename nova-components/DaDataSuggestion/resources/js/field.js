Nova.booting((Vue, router, store) => {
  Vue.component('index-dadata-suggestion', require('./components/IndexField'))
  Vue.component('detail-dadata-suggestion', require('./components/DetailField'))
  Vue.component('form-dadata-suggestion', require('./components/FormField'))
})