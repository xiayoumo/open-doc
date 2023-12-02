<template>
  <codemirror ref="codeEditor" v-model="nowEditorContent" :options="codemirrorOptions"></codemirror>
</template>

<script>
import { codemirror } from 'vue-codemirror';// 参考文档 https://blog.csdn.net/LoveZJC96/article/details/131513036

import "codemirror/lib/codemirror.css";
import "codemirror/theme/mbo.css";// 主题参考 https://github.surmon.me/vue-codemirror
import 'codemirror/addon/display/placeholder.js';// 官方文档 https://codemirror.net/5/doc/manual.html#addon_placeholder

require("codemirror/mode/javascript/javascript"); // 这里引入的模式的js，根据设置的mode引入，一定要引入！！


export default {
  name: 'in-coder',
  props: {
    editorContent: {
      type: String,
      default: ''
    },
    emptyPlaceholder: {
      type: String,
      default: '示例：\n{\n "xingming": "李四",\n "nianling": "20",\n "sex": "女",\n "kong": "",\n "null": null\n }'
    },
  },
  components: {
    codemirror
  },
  watch: {
    editorContent(newVal) {
      //父组件传过来的值
      this.nowEditorContent = this.editorContent
    },
  },
  data() {
    return {
      nowEditorContent: '',
      editor: null,
      content: '',
      codemirrorOptions: {
        // autorefresh: true,
        // tabSize: 4,
        // mode: 'text/x-properties',
        // theme: 'ayu-mirage',
         line: true,
        // viewportMargin: Infinity, //处理高度自适应时搭配使用
        // highlightDifferences: true,
        // autofocus: false,
        // indentUnit: 2,
        // smartIndent: true,
        // readOnly: true, // 只读
        // showCursorWhenSelecting: true,
        // firstLineNumber: 1
        placeholder: this.emptyPlaceholder,
        lineNumbers: true, // 显示行号
        mode: 'text/javascript', // 语法model
        gutters: ['CodeMirror-lint-markers'], // 语法检查器
        theme: "mbo",
        lint: true, // 开启语法检查
      }
    }
  },
  computed: {
    codemirror() {
      return this.$refs.codeEditor.codemirror
    }
  },
  mounted() {
    // 初始化
    this.initCodeEditor()
  },
  methods: {
    //父组件调用清空的方法
    resetData(){
      this.codemirror.setValue('');
    },
    // 初始化
    initCodeEditor() {
      // let theme = 'ambiance'//设置主题，不设置的会使用默认主题
      this.codemirror.setValue(this.editorContent);
      // 支持双向绑定
      this.codemirror.on('change', (coder) => {
        this.nowEditorContent = coder.getValue()
        if (this.$emit) {
          // 通过监听Input事件可以拿到动态改变的code值
          this.$emit('input', this.nowEditorContent)
        }
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@/components/common/base.scss';

</style>
