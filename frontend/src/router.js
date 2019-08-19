import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

/**
 * Load components async with dynamic import.
 * @see: babel-plugin-syntax-dynamic-import
 *
 * @usage: route level code-splitting,
 * this generates a separate chunk (about.[hash].js) for this route,
 * which is lazy-loaded when the route is visited.
 */
function loadView (view) {
  return () => import(`@/views/${view}.vue`) /* webpackChunkName: "view-[request]" */
}

export default new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      name: 'Home',
      path: '/',
      component: loadView('Home'),
      meta: {
        // requiresVisitor: true
      }
    },
    {
      name: 'Observer Mode',
      path: '/observer',
      component: loadView('Observer')
    },
    {
      path: '/scientist',
      component: loadView('Scientist'),
      meta: {
        // requiresScientist: true
        // auth: {
        //   roles: 2,
        //   redirect: { name: 'login' },
        //   forbiddenRedirect: '/403'
        // }
      },
      // these will be rendered inside Scientist's <router-view>
      children: [
        // loaded by default
        {
          name: 'Scientist Mode',
          path: '',
          component: loadView('scientist/Dashboard')
        },
        {
          path: 'dashboard',
          component: loadView('scientist/Dashboard')
        },
        {
          path: 'experiment/create',
          component: loadView('scientist/experiment/Create')
        },
        {
          path: 'experiment/edit/:id',
          component: loadView('scientist/experiment/Edit')
        },
        {
          path: 'experiment/experiments',
          component: loadView('scientist/experiment/List')
        },
        {
          path: 'experiment/experiments/:id',
          component: loadView('scientist/experiment/Experiment')
        },
        {
          path: 'image-sets/create',
          component: loadView('scientist/experiment/Uploader')
        },
        {
          path: 'image-sets/set/:id',
          component: loadView('scientist/PictureSet')
        }
      ]
    },
    {
      path: '/admin',
      component: loadView('admin/Admin'),
      meta: {
        // requiresAdmin: true
        // redirect: { name: 'login' },
        // forbiddenRedirect: '/403'
      },
      children: [
        // loaded by default
        {
          name: 'admin.dashboard',
          path: '',
          component: loadView('admin/Dashboard')
        },
        {
          // name: 'admin.dashboard',
          path: 'dashboard',
          component: loadView('admin/Dashboard')
        },
        {
          name: 'admin.authorize',
          path: 'authorize',
          component: loadView('admin/Authorize')
        }
      ]
    },
    {
      name: 'Experiment Mode',
      path: '/experiment/:id',
      component: loadView('observer/Experiment')
    },
    {
      path: '*',
      component: loadView('NotFound')
    }
  ]
})
