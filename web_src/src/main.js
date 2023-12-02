// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import '../static/icon/iconfont.css';
import "../static/css/custom-element.css";
//在min.js中引入插件
import 'default-passive-events'; // 解决事件报错问题https://blog.csdn.net/tonetwo/article/details/129384680

import Header from '@/components/common/Header'
import Footer from '@/components/common/Footer'
import util from '@/js/util.js'
import axios from '@/js/http'

import i18n from '@/lang'
import 'url-search-params-polyfill'
import "babel-polyfill";
import VueClipboard from 'vue-clipboard2';

Vue.use(util);
Vue.config.productionTip = false
Vue.component('Header', Header);
Vue.component('Footer', Footer);
Vue.use(ElementUI)
Vue.use(VueClipboard)

// 将axios挂载到prototype上，在组件中可以直接使用this.axios访问
Vue.prototype.axios = axios;


/* eslint-disable no-new */

new Vue({
  el: '#app',
  router,
  i18n,
  template: '<App/>',
  components: { App }
})

