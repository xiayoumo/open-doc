// router.js
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'defaultIndex',
    component: () => import('@/view/Index')
  },
  {
    path: '/show/index',
    redirect: '/'
  },
  // {
  //   path: '*',
  //   redirect: '/404',
  //   children: [
  //     {
  //       path: '/404', // 这里要加上/404,和上面的redirect对应,必须加上/
  //       name: '404',
  //       component: () => import('@/components/404.vue'),
  //     },
  //   ],
  // },
  {
    path: '/item/index',
    name: 'ItemIndex',
    component: () => import('@/view/item/Index')
  },
  {
    path: '/user/login',
    name: 'UserLogin',
    component:  () => import('@/view/user/Login')
  },
  {
    path: '/user/setting',
    name: 'UserSetting',
    component: () => import('@/view/user/Setting')
  },
  {
    path: '/user/register',
    name: 'UserRegister',
    component: () => import('@/view/user/Register')
  },
  {
    path: '/user/resetPassword',
    name: 'UserResetPassword',
    component: () => import('@/view/user/ResetPassword')
  },
  {
    path: '/user/ResetPasswordByUrl',
    name: 'ResetPasswordByUrl',
    component: () => import('@/view/user/ResetPasswordByUrl')
  },
  {
    path: '/item/index',
    name: 'ItemIndex',
    component: () => import('@/view/item/Index')
  },
  {
    path: '/item/add',
    name: 'ItemAdd',
    component: () => import('@/view/item/Add')
  },
  {
    path: '/item/password/:item_id',
    name: 'ItemPassword',
    component: () => import('@/view/item/Password')
  },
  {
    path: '/item-show/:item_id',
    name: 'ItemShow',
    component: () => import('@/view/item/show/Index')
  },
  {
    path: '/item-count-show/:item_id',
    name: 'ItemCountShow',
    component: () => import('@/view/item/show/Index')
  },
  {
    path: '/item/export/:item_id',
    name: 'ItemExport',
    component: () => import('@/view/item/export/Index')
  },
  {
    path: '/item/setting/:item_id',
    name: 'ItemSetting',
    component: () => import('@/view/item/setting/Index')
  },
  {
    path: '/page/:page_id',
    name: 'PageIndex',
    component: () => import('@/view/page/Index')
  },
  {
    path: '/page/edit/:item_id/:page_id',
    name: 'PageEdit',
    component: () => import('@/view/page/edit/Index')
  },
  {
    path: '/page/diff/:page_id/:page_history_id',
    name: 'PageDiff',
    component: () => import('@/view/page/Diff')
  },
  {
    path: '/catalog/:item_id',
    name: 'Catalog',
    component: () => import('@/view/catalog/Index')
  },
  {
    path: '/notice/index',
    name: 'Notice',
    component: () => import('@/view/notice/Index')
  },
  {
    path: '/admin/index',
    name: 'Admin',
    component: () => import('@/view/admin/Index')
  },
  {
    path: '/team/index',
    name: 'Team',
    component: () => import('@/view/team/Index')
  },
  {
    path: '/team/member/:team_id',
    name: 'TeamMember',
    component: () => import('@/view/team/Member')
  },
  {
    path: '/team/item/:team_id',
    name: 'TeamItem',
    component: () => import('@/view/team/Item')
  },
  { path: '/404', name: 'NotFound', component: () => import('@/components/404.vue') },
  {  path: '/:catchAll(.*)', redirect: '/404' },
]

const router = createRouter({
  history: createWebHistory(),  // history模式
  routes,  // 路由配置
})


export default router