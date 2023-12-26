import { createApp } from 'vue'
import App from './App.vue'

import router from '@/router'
import store from '@/store'
import ElementPlus from 'element-plus'
import 'element-plus/theme-chalk/index.css'
import '../public/static/icon/iconfont.css';
import '../public/static/css/custom-element.css';

import Header from '@/components/common/Header'
import Footer from '@/components/common/Footer'
import util from '@/js/util.js'
import axios from '@/js/http'

import i18n from '@/lang'
// import 'url-search-params-polyfill'
// import "babel-polyfill";

import 'default-passive-events' // 解决事件报错问题https://blog.csdn.net/tonetwo/article/details/129384680
import '@/js/browserPatch.js'

// 如果您正在使用CDN引入，请删除下面一行。
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import DirectiveExtensions from '@/directive' //自定义指令集（比如：vue-clipboard3）


const app = createApp(App);
app.config.globalProperties.$axios = axios;// 将axios挂载到prototype上，在组件中可以直接使用this.axios访问
app.component('Header', Header)
    .component('Footer', Footer)
    .use(util)
    .use(ElementPlus)
    .use(store)
    .use(i18n)
    .use(router)
    .use(DirectiveExtensions)
    .mount('#app');
// 引入icon
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
}
