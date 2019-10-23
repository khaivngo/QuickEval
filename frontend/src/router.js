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
    { path: '/observer',     component: loadView('observer/Index') },
    { path: '/observer/:id', component: loadView('observer/Index') },

    /* scientist */
    {
      path: '/scientist',
      component: loadView('scientist/Index'),
      children: [
        { path: '',                            component: loadView('scientist/Dashboard'),                meta: { transitionName: 'slide-left' } },
        { path: 'dashboard',                   component: loadView('scientist/Dashboard'),                meta: { transitionName: 'slide-left' } },
        { path: 'experiments',                 component: loadView('scientist/experiment/List'),          meta: { transitionName: 'slide-left' } },
        { path: 'experiments/view/:id',        component: loadView('scientist/experiment/results/Index'), meta: { transitionName: 'slide-right' } },
        { path: 'experiments/create',          component: loadView('scientist/experiment/Create'),        meta: { transitionName: 'slide-right' } },
        { path: 'experiments/edit/:id',        component: loadView('scientist/experiment/Edit'),          meta: { transitionName: 'slide-right' } },
        { path: 'image-sets/view/:id',         component: loadView('scientist/image-set/ImageSet'),       meta: { transitionName: 'slide-right' } },
        { path: 'image-sets/create',           component: loadView('scientist/image-set/List'),           meta: { transitionName: 'slide-left' } },
        { path: 'image-sets/:id/file-upload',  component: loadView('scientist/image-set/CreateFile'),     meta: { transitionName: 'slide-right' } }
      ]
    },

    /* admin */
    {
      path: '/admin',
      component: loadView('admin/Index'),
      children: [
        { path: '',                         component: loadView('admin/ScientistRoleRequest') },
        { path: 'scientist-role-requests',  component: loadView('admin/ScientistRoleRequest') }
      ]
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
