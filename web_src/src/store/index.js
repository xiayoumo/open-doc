import Vue from 'vue'
import Vuex from 'vuex'
import base from './modules/base'
import getters from './getters'
Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    base,
  },
  getters
})

export default store

// 引入示例 import store from '@/store';
// 调用示例 store.getters.nowHeadTitle; store.getters.userInfo
// 设置示例 store.dispatch('SetNowHeadTitle', '内容');
