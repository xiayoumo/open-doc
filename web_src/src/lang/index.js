import Vue from 'vue'
import VueI18n from 'vue-i18n'
Vue.use(VueI18n)

import zh from './zh'
import en from './en'

const messages = {
  'zh': zh,
  'en': en,
}

const i18n = new VueI18n({
  // 初始语言找本地储存的lang，默认为浏览器语言
  locale:  localStorage.lang  || 'zh',
  fallbackLocale: 'zh-CN',
  messages,
})

export default i18n
