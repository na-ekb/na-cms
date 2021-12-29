
Nova.booting((Vue, router) => {

    const registeredViews =  JSON.parse('{"detail":{"route":"detail","component":"Detail","name":"meeting-detail-view"}}')
    Object.keys(registeredViews).forEach(function(key) {
        Vue.component(registeredViews[key]['name'], require('./views/' + registeredViews[key]['component']))
    })
})
