import 'es6-promise/auto'
import Vue from 'vue'
import axios from 'axios'
import router from './router'
import './plugins/vuetify'
import App from './App.vue'

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
Vue.prototype.$domain = 'https://quickeval.no'

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
