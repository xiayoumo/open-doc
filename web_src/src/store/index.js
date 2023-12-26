import Vue from 'vue'
import { createStore } from 'vuex'
import base from './modules/base'
import getters from './getters'

// 引入示例 import store from '@/store';
// 调用示例 store.getters.nowHeadTitle; store.getters.userInfo
// 设置示例 store.dispatch('SetNowHeadTitle', '内容');

export default createStore({
  modules: {
    base,
  },
  getters
})
