import bearer from '@websanova/vue-auth/drivers/auth/bearer.js'
import axios from '@websanova/vue-auth/drivers/http/axios.1.x.js'
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x.js'

// Auth base configuration some of this options
// can be override in method calls
const config = {
  auth: bearer,
  http: axios,
  router: router,
  tokenDefaultName: 'quickeval-vue-laravel',
  tokenStore: ['localStorage'],
  rolesVar: 'role', // used to determine which user model field is used to define the user’s role
  registerData: {
    url: 'auth/register',
    method: 'POST',
    redirect: '/login'
  },
  loginData: {
    url: 'auth/login',
    method: 'POST',
    redirect: '',
    fetchUser: true
  },
  logoutData: {
    url: 'auth/logout',
    method: 'POST',
    redirect: '/',
    makeRequest: true
  },
  fetchData: {
    url: 'auth/user',
    method: 'GET',
    enabled: true
  },
  refreshData: {
    url: 'auth/refresh',
    method: 'GET',
    enabled: true,
    interval: 30
  }
}

export default config
