import Vue from 'vue'
import Router from 'vue-router'
import Index from '@/components/Index'
import UserLogin from '@/components/user/Login'
import UserSetting from '@/components/user/Setting'
import UserRegister from '@/components/user/Register'
import UserResetPassword from '@/components/user/ResetPassword'
import ResetPasswordByUrl from '@/components/user/ResetPasswordByUrl'
import ItemIndex from '@/components/item/Index'
import ItemAdd from '@/components/item/Add'
import ItemPassword from '@/components/item/Password'
import ItemShow from '@/components/item/show/Index'
import ItemExport from '@/components/item/export/Index'
import ItemSetting from '@/components/item/setting/Index'
import PageIndex from '@/components/page/Index'
import PageEdit from '@/components/page/edit/Index'
import PageDiff from '@/components/page/Diff'
import Catalog from '@/components/catalog/Index'
import Notice from '@/components/notice/Index'
import Admin from '@/components/admin/Index'
import Team from '@/components/Team/Index'
import TeamMember from '@/components/Team/Member'
import TeamItem from '@/components/Team/Item'
import NotFoundComponent from '@/components/common/404'

Vue.use(Router)
// 解决Uncaught (in promise) NavigationDuplicated: Avoided redundant navigation to current location 问题
const originalPush = Router.prototype.push
Router.prototype.push = function push(location) {
  return originalPush.call(this, location).catch(err => err)
}
Router.prototype.replace = function push(location) {
  return originalPush.call(this, location).catch(err => err)
}
export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'defaultIndex',
      component: (resolve) => require(['@/components/Index'],resolve)
    },
    { path: '/404', name: 'NotFound', component: (resolve) => require(['@/components/common/404'],resolve) },
    { path: '*', redirect: '/404' },
    {
      path: '/show/index',
      name: 'Index',
      component: (resolve) => require(['@/components/Index'],resolve)
    },
    {
      path: '/user/login',
      name: 'UserLogin',
      component: (resolve) => require(['@/components/user/Login'],resolve)
    },
    {
      path: '/user/setting',
      name: 'UserSetting',
      component: (resolve) => require(['@/components/user/Setting'],resolve)
    },
    {
      path: '/user/register',
      name: 'UserRegister',
      component: (resolve) => require(['@/components/user/Register'],resolve)
    },
    {
      path: '/user/resetPassword',
      name: 'UserResetPassword',
      component: (resolve) => require(['@/components/user/ResetPassword'],resolve)
    },
    {
      path: '/user/ResetPasswordByUrl',
      name: 'ResetPasswordByUrl',
      component: (resolve) => require(['@/components/user/ResetPasswordByUrl'],resolve)
    },
    {
      path: '/item/index',
      name: 'ItemIndex',
      component: (resolve) => require(['@/components/item/Index'],resolve)
    },
    {
      path: '/item/add',
      name: 'ItemAdd',
      component: (resolve) => require(['@/components/item/Add'],resolve)
    },
    {
      path: '/item/password/:item_id',
      name: 'ItemPassword',
      component: (resolve) => require(['@/components/item/Password'],resolve)
    },
    {
      path: '/item-show/:item_id',
      name: 'ItemShow',
      component: (resolve) => require(['@/components/item/show/Index'],resolve)
    },
    {
      path: '/item-count-show/:item_id',
      name: 'ItemShow',
      component: (resolve) => require(['@/components/item/show/Index'],resolve)
    },
    {
      path: '/item/export/:item_id',
      name: 'ItemExport',
      component: (resolve) => require(['@/components/item/export/Index'],resolve)
    },
    {
      path: '/item/setting/:item_id',
      name: 'ItemSetting',
      component: (resolve) => require(['@/components/item/setting/Index'],resolve)
    },
    {
      path: '/page/:page_id',
      name: 'PageIndex',
      component: (resolve) => require(['@/components/page/Index'],resolve)
    },
    {
      path: '/page/edit/:item_id/:page_id',
      name: 'PageEdit',
      component: (resolve) => require(['@/components/page/edit/Index'],resolve)
    },
    {
      path: '/page/diff/:page_id/:page_history_id',
      name: 'PageDiff',
      component: (resolve) => require(['@/components/page/Diff'],resolve)
    },
    {
      path: '/catalog/:item_id',
      name: 'Catalog',
      component: (resolve) => require(['@/components/catalog/Index'],resolve)
    },
    {
      path: '/notice/index',
      name: 'Notice',
      component: (resolve) => require(['@/components/notice/Index'],resolve)
    },
    {
      path: '/admin/index',
      name: 'Admin',
      component: (resolve) => require(['@/components/admin/Index'],resolve)
    },
    {
      path: '/team/index',
      name: 'Team',
      component: (resolve) => require(['@/components/Team/Index'],resolve)
    },
    {
      path: '/team/member/:team_id',
      name: 'TeamMember',
      component: (resolve) => require(['@/components/Team/Member'],resolve)
    },
    {
      path: '/team/item/:team_id',
      name: 'TeamItem',
      component: (resolve) => require(['@/components/Team/Item'],resolve)
    },
  ]
})

// 导航守卫用于捕获404错误
// Router.beforeEach((to, from, next) => {
//   // 如果目标路由不存在，重定向到404页面
//   if (to.matched.length === 0) {
//     next('/404');
//     console.log('404')
//   } else {
//     next();
//   }
// })
