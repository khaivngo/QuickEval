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
      name: 'home',
      component: loadView('Index')
    },

    /* observer */
    { path: '/observer',     component: loadView('observer/Index') },
    { path: '/observer/:id', component: loadView('observer/Index'), name: 'referral' },

    /* scientist */
    {
      path: '/scientist',
      component: loadView('scientist/Index'),
      children: [
        { path: '',          component: loadView('scientist/Dashboard') },
        { path: 'dashboard', component: loadView('scientist/Dashboard') },
        {
          path: 'experiments',
          component: loadView('scientist/experiment/Index'),
          children: [
            { path: 'create',   component: loadView('scientist/experiment/Create') },
            { path: 'view/:id', component: loadView('scientist/experiment/View') },
            { path: 'edit/:id', component: loadView('scientist/experiment/Create') }
          ]
        },
        {
          path: 'image-sets',
          component: loadView('scientist/image-set/Index'),
          children: [
            // { path: 'create',   component: loadView('scientist/image-set/Create') },
            { path: 'view/:id', component: loadView('scientist/image-set/View') }
            // { path: 'edit/:id', component: loadView('scientist/image-set/Edit') }
          ]
        }
        // { path: 'experiments/view/:id',        component: loadView('scientist/experiment/results/Index') },
        // { path: 'experiments/create',          component: loadView('scientist/experiment/Create')        },
        // { path: 'experiments/edit/:id',        component: loadView('scientist/experiment/Edit')          },
        // { path: 'image-sets/view/:id',         component: loadView('scientist/image-set/ImageSet')       },
        // { path: 'image-sets/create',           component: loadView('scientist/image-set/List')           },
        // { path: 'image-sets/:id/file-upload',  component: loadView('scientist/image-set/CreateFile')     }
      ]
    },

    /* admin */
    {
      path: '/admin',
      component: loadView('admin/Index'),
      children: [
        { path: '',                         component: loadView('admin/ScientistRoleRequest') },
        { path: 'dashboard',                component: loadView('admin/Dashboard') },
        { path: 'scientist-role-requests',  component: loadView('admin/ScientistRoleRequest') }
      ]
    },

    /* user */
    {
      path: '/user/profile',
      component: loadView('Profile')
    },

    /* experiment */
    { path: '/experiment/1/:id', component: loadView('observer/experiment/PairedComparison'),   name: 'Paired Comparison' },
    { path: '/experiment/2/:id', component: loadView('observer/experiment/RankOrder'),          name: 'Rank Order' },
    { path: '/experiment/3/:id', component: loadView('observer/experiment/CategoryJudgement'),  name: 'Category Judgement' },
    { path: '/experiment/4/:id', component: loadView('observer/experiment/ArtifactMarking'),    name: 'Artifact Marking' },
    { path: '/experiment/5/:id', component: loadView('observer/experiment/TripletComparison'),  name: 'Triplet Comparison' },

    /* catch all non-existing routes */
    {
      path: '*',
      component: loadView('NotFound')
    }
  ]
})
