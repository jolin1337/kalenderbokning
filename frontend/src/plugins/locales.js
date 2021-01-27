export default {
    install(Vue, options) {
        Vue.prototype.$locale = window.defaultLocale
    }
}