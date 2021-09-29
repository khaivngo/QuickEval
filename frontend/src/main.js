import Vue from 'vue'
import axios from 'axios'
import router from './router'
import vuetify from './plugins/vuetify'
import App from './App.vue'

if (process.env.NODE_ENV === 'development') {
  Vue.prototype.$DOMAIN         = 'https://quickeval.no'
  Vue.prototype.$UPLOADS_FOLDER = 'http://127.0.0.1/QuickEval/public/storage/'
  Vue.prototype.$API_URL        = 'http://127.0.0.1/QuickEval/public/api'
  axios.defaults.baseURL        = Vue.prototype.$API_URL
} else {
  Vue.prototype.$DOMAIN         = 'https://quickeval.no'
  Vue.prototype.$UPLOADS_FOLDER = 'https://quickeval.no/uploads/public/'
  Vue.prototype.$API_URL        = 'https://quickeval.no/api'
  axios.defaults.baseURL        = Vue.prototype.$API_URL
  // local production testing:
  // Vue.prototype.$DOMAIN         = 'https://quickeval.no'
  // Vue.prototype.$UPLOADS_FOLDER = 'http://127.0.0.1/QuickEval/public/storage/'
  // Vue.prototype.$API_URL        = 'http://127.0.0.1/QuickEval/public/api'
  // axios.defaults.baseURL        = Vue.prototype.$API_URL
}

if (localStorage.access_token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.access_token
}

Vue.prototype.videoFormats = ['m4p', 'webm', '3g2', '3gp', 'aaf', 'asf', 'avchd', 'avi', 'drc', 'flv', 'm2v', 'm3u8', 'm4v', 'mkv', 'mng', 'mov', 'mp2', 'mp4', 'mpe', 'mpeg', 'mpg', 'mpv', 'mxf', 'nsv', 'ogg', 'ogv', 'qt', 'rm', 'rmvb', 'roq', 'svi', 'vob', 'wmv', 'yuv']
Vue.prototype.imageFormats = ['jpg', 'jpeg', 'jpe', 'jif', 'jfif', 'jfi', 'png', 'gif', 'webp', 'tiff', 'tif', 'psd', 'raw', 'arw', 'cr2', 'nrw', 'k25', 'bmp', 'dib', 'heif', 'heic', 'ind', 'indd', 'indt', 'jp2', 'j2k', 'jpf', 'jpx', 'jpm', 'mj2', 'svg', 'svgz', 'ai', 'eps', 'pdf']

Vue.prototype.$axios = axios

Vue.config.productionTip = false

new Vue({
  router,
  vuetify,
  render: h => h(App)
}).$mount('#app')
