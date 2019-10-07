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

/* eslint-disable */
export default new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    /* home */
    {
      path: '/',
      component: loadView('Index')
    },

    /* observer */
    {
      path: '/observer',
      component: loadView('observer/Index')
    },

    /* scientist */
    {
      path: '/scientist',
      component: loadView('scientist/Index'),
      children: [
        { path: 'dashboard',                   component: loadView('scientist/Dashboard')             },
        { path: 'experiments',                 component: loadView('scientist/experiment/List')       },
        { path: 'experiments/view/:id',        component: loadView('scientist/experiment/Experiment') },
        { path: 'experiments/create',          component: loadView('scientist/experiment/Create')     },
        { path: 'experiments/edit/:id',        component: loadView('scientist/experiment/Edit')       },
        { path: 'image-sets/view/:id',         component: loadView('scientist/image-set/ImageSet')    },
        { path: 'image-sets/create',           component: loadView('scientist/image-set/List')        },
        { path: 'image-sets/:id/file-upload',  component: loadView('scientist/image-set/CreateFile')  }
      ]
    },

    /* admin */
    {
      path: '/admin',
      component: loadView('admin/Index'),
      children: [
        { path: 'scientist-role-requests',  component: loadView('admin/ScientistRoleRequest') }
      ]
    },

    /* experiment */
    {
      path: '/experiment/:id',
      component: loadView('observer/experiment/Experiment'),
      name: 'Experiment Mode'
    },

    {
      path: '/experiment/category/:id',
      component: loadView('observer/experiment/CategoryJudgment'),
      name: 'Category Experiment Mode'
    },

    {
      path: '/experiment/triplet/:id',
      component: loadView('observer/experiment/TripletComparison'),
      name: 'Triplet Comparison Experiment Mode'
    },

    /* catch all non-existing routes */
    {
      path: '*',
      component: loadView('NotFound')
    }
  ]
})
