import Vue from 'vue'
import NProgress from 'nprogress'
import VueRouter from 'vue-router'
import DataTable from 'laravel-vue-datatable';
import moment from 'moment-timezone';
import vuetify from './vuetify'
import routes from './routes'
import App from '../../screens/App'
import RequestMixin from './mixins/RequestMixin'
import HelperMixin from './mixins/HelperMixin'
import store from './store';

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

moment.locale('pt-BR')
moment.tz.setDefault('America/Sao_Paulo')

Vue.use(VueRouter)
Vue.use(DataTable)

Vue.mixin(RequestMixin)
Vue.mixin(HelperMixin)

const router = new VueRouter({
    mode: 'history',
    base: Dashboard.path,
    routes
})

NProgress.configure({
    showSpinner: false,
    easing: 'ease',
    speed: 300,
})

router.beforeEach((to, from, next) => {
    NProgress.start()
    next()
})

const app = new Vue({
    el: '#dashboard',
    vuetify,
    router,
    store,
    components: { App }
})

// Give the store access to the root Vue instance
store.$app = app
