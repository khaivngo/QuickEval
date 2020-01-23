import Vue from 'vue'
import axios from 'axios'
import router from './router'
import './plugins/vuetify'
import App from './App.vue'

// if (process.env.NODE_ENV === 'development') {
Vue.prototype.$DOMAIN         = 'https://quickeval.no'
Vue.prototype.$UPLOADS_FOLDER = 'http://127.0.0.1/QuickEval/public/storage/'
Vue.prototype.$API_URL        = 'http://127.0.0.1/QuickEval/public/api'
axios.defaults.baseURL        = Vue.prototype.$API_URL
// } else {
// Vue.prototype.$DOMAIN         = 'https://quickeval.no'
// Vue.prototype.$UPLOADS_FOLDER = 'https://quickeval.no/uploads/public/'
// Vue.prototype.$API_URL        = 'https://quickeval.no/api'
// axios.defaults.baseURL        = Vue.prototype.$API_URL
// }

if (localStorage.access_token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
}

Vue.prototype.$axios = axios

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
