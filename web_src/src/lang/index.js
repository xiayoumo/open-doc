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
  locale: 'zh',
  fallbackLocale: 'zh-CN',
  messages,
})

export default i18n
