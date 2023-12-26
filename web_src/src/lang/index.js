import { createI18n } from 'vue-i18n';

import ZH from './zh.js';
import EN from './en.js';
const messages = {
  zh: { ...ZH  },
  en: { ...EN  },
};

const i18n = createI18n({
  // 初始语言找本地储存的lang，默认为浏览器语言
  legacy: false,
  globalInjection: true,
  locale: localStorage.lang  || 'zh',
  messages
});
export default i18n;
