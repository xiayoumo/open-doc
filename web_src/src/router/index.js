import Vue from 'vue'
import Router from 'vue-router'
// import Index from '@/components/Index'
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
      // component: Index
      component: ItemIndex
    },
    { path: '/404', name: 'NotFound', component: NotFoundComponent },
    { path: '*', redirect: '/404' },
    {
      path: '/show/index',
      name: 'Index',
      component: Index
    },
    {
      path: '/user/login',
      name: 'UserLogin',
      component: UserLogin
    },
    {
      path: '/user/setting',
      name: 'UserSetting',
      component: UserSetting
    },
    {
      path: '/user/register',
      name: 'UserRegister',
      component: UserRegister
    },
    {
      path: '/user/resetPassword',
      name: 'UserResetPassword',
      component: UserResetPassword
    },
    {
      path: '/user/ResetPasswordByUrl',
      name: 'ResetPasswordByUrl',
      component: ResetPasswordByUrl
    },
    {
      path: '/item/index',
      name: 'ItemIndex',
      component: ItemIndex
    },
    {
      path: '/item/add',
      name: 'ItemAdd',
      component: ItemAdd
    },
    {
      path: '/item/password/:item_id',
      name: 'ItemPassword',
      component: ItemPassword
    },
    {
      path: '/item-show/:item_id',
      name: 'ItemShow',
      component: ItemShow
    },
    {
      path: '/item-count-show/:item_id',
      name: 'ItemShow',
      component: ItemShow
    },
    {
      path: '/item/export/:item_id',
      name: 'ItemExport',
      component: ItemExport
    },
    {
      path: '/item/setting/:item_id',
      name: 'ItemSetting',
      component: ItemSetting
    },
    {
      path: '/page/:page_id',
      name: 'PageIndex',
      component: PageIndex
    },
    {
      path: '/page/edit/:item_id/:page_id',
      name: 'PageEdit',
      component: PageEdit
    },
    {
      path: '/page/diff/:page_id/:page_history_id',
      name: 'PageDiff',
      component: PageDiff
    },
    {
      path: '/catalog/:item_id',
      name: 'Catalog',
      component: Catalog
    },
    {
      path: '/notice/index',
      name: 'Notice',
      component: Notice
    },
    {
      path: '/admin/index',
      name: 'Admin',
      component: Admin
    },
    {
      path: '/team/index',
      name: 'Team',
      component: Team
    },
    {
      path: '/team/member/:team_id',
      name: 'TeamMember',
      component: TeamMember
    },
    {
      path: '/team/item/:team_id',
      name: 'TeamItem',
      component: TeamItem
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
