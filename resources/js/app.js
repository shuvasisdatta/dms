/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// admin-lte
require('admin-lte');

window.Vue = require('vue');

// vForm
import { Form, HasError, AlertError } from 'vform'
window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

// window.access_token = localStorage.getItem('access_token') ? localStorage.getItem('access_token') : ''
// window.isAuthenticated = localStorage.getItem('isAuthenticated') ? localStorage.getItem('isAuthenticated') : false
// window.user = localStorage.getItem('user')? JSON.parse(localStorage.getItem('user')) : ''
// window.userRole = localStorage.getItem('userRole')? localStorage.getItem('userRole') : ''


import axios from 'axios'
window.axios = axios
// axios.defaults.headers.common['Authorization'] = "Bearer " + window.access_token


// import router
import router from './router'


// VueProgressBar 
import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '2px'
})

// sweetAlart2
import Swal from 'sweetalert2'
window.Swal = Swal;

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
})

window.toast = toast;


Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

// Filters
Vue.filter('capitalize', function (value) {
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
})

Vue.filter('substr', function (value, start, end) {
    if (!value) return ''
    value = value.substr(start, end)
    return value
})

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data() {
        return {
            isAuthenticated: localStorage.getItem('isAuthenticated') || false,
            access_token: localStorage.getItem('access_token') || null,
            user: localStorage.getItem('user') || null,
            userRole: localStorage.getItem('userRole')  || null
        }
    },
    computed: {
    },
    created() {
        this.$on('onLogin', (response) => {
            toast.fire({
                title: 'Logging In',
                type: 'success'
            });
            // console.log('login' + response)
            this.access_token = response.access_token
            this.user = response.user
            this.userRole = response.user.role.name
            this.isAuthenticated = true
            localStorage.setItem('access_token', this.access_token)
            localStorage.setItem('user', JSON.stringify(this.user))
            localStorage.setItem('userRole', this.userRole)
            localStorage.setItem('isAuthenticated', this.isAuthenticated)
            
            router.push(this.$route.query.redirect || '/documents')
        })
        this.$on('onLogout', () => {
            toast.fire({
                title: 'Logging Out',
                type: 'success'
            });
            // console.log('logout')
            this.access_token = null
            this.user = null
            this.userRole = null
            this.isAuthenticated = false
            localStorage.removeItem('access_token')
            localStorage.removeItem('user')
            localStorage.removeItem('userRole')
            localStorage.removeItem('isAuthenticated')
            router.push('/login')
        })
    },

});






// devtools enable
Vue.config.devtools = true
