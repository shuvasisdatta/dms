import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const router = new Router({
    mode: 'history',
    routes: [
        {
            path: '/',
            redirect: '/documents'
        },
        {
            path: '/login',
            component: require('./components/Login.vue').default,
            meta: {
                title: 'Login',
            }
        },
        {
            path: '/dashboard',
            component: require('./components/Dashboard.vue').default,
            meta: {
                title: 'Dashboard',
                requiresAuth: true,
            }
        }, 
        {
            path: '/users',
            component: require('./components/Users.vue').default,
            meta: {
                title: 'Users',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/roles',
            component: require('./components/Roles.vue').default,
            meta: {
                title: 'Roles',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/categories',
            component: require('./components/Categories.vue').default,
            meta: {
                title: 'Categories',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/lockers',
            component: require('./components/Lockers.vue').default,
            meta: {
                title: 'Lockers',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/departments',
            component: require('./components/Departments.vue').default,
            meta: {
                title: 'Departments',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/plants',
            component: require('./components/Plants.vue').default,
            meta: {
                title: 'Plants',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/equipments',
            component: require('./components/Equipments.vue').default,
            meta: {
                title: 'Equipments',
                requiresAuth: true,
                adminAuth: true
            }
        }, {
            path: '/documents',
            component: require('./components/Documents.vue').default,
            meta: {
                'title': 'Documents',
                requiresAuth: true
            }
        },
        {
            path: '/contact',
            component: require('./components/Contact.vue').default,
            meta: {
                title: 'Contact',
            }
        }, 
        {
            path: '*',
            component: require('./components/helper/404.vue').default
        }
    ]
});

import axios from 'axios'
// window.axios = axios


// routing guard for authentication
router.beforeEach(async(to, from, next) => {
    await Vue.nextTick()
    axios.defaults.headers.common['Authorization'] = "Bearer " + router.app.access_token
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if(!router.app.isAuthenticated || !router.app.access_token) {
            next({
                path: '/login',
                query: {
                    redirect: to.fullPath
                }
            })
        } else if (to.matched.some(record => record.meta.adminAuth)) {
            if(router.app.userRole === 'Admin') {
                next()
            } else {
                next('/404')
            }
        } else {
            next()
        }
    } else {
        // if the url is login and user is already authenticated then route to root url else pass the next()
        if(to.path === '/login' && (router.app.isAuthenticated || router.app.access_token)) {
            next('/')
        }
        next()
    }
    
})

// App Title change
router.afterEach((to, from) => {
    Vue.nextTick(() => {
        document.title = to.meta.title ? to.meta.title : '';
    });
})

export default router;