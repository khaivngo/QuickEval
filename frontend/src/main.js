import Vue from 'vue'
import axios from 'axios'
import router from './router'
import './plugins/vuetify'
import App from './App.vue'

Vue.prototype.$domain = 'https://quickeval.no'
Vue.prototype.$UPLOADS_FOLDER = 'http://127.0.0.1/QuickEval/storage/app/'
Vue.prototype.$API_URL = 'http://127.0.0.1/QuickEval/public/api'

axios.defaults.baseURL = 'http://127.0.0.1/QuickEval/public/api'

if (localStorage.access_token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
}
// localStorage.getItem('access_token') || null,

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (!localStorage.access_token) {
      next({ path: '/' })
    } else {
      next()
    }
  } else if (to.matched.some(record => record.meta.requiresScientist)) {
    if (!localStorage.access_token) {
      next({ path: '/' })
    } else {
      next()
    }
  } else if (to.matched.some(record => record.meta.requiresAdmin)) {
    if (!localStorage.access_token) {
      next({ path: '/' })
    } else {
      next()
    }
  } else {
    next() // make sure to always call next()!
  }
})

Vue.prototype.$axios = axios

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')

// defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// axios.defaults.baseURL = 'http://127.0.0.1:8080/api'
// axios.defaults.headers.common['Authorization'] = AUTH_TOKEN
// axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded'
// let token = document.head.querySelector('meta[name="csrf-token"]')

// if (token) {
//   axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
//   axios.defaults.headers.common['Authorization'] = token.content
// } else {
//   console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
// }

// axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api`
