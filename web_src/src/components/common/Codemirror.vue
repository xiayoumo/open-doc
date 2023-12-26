<template>
  <Codemirror
      :autofocus="true"
      :placeholder="emptyPlaceholder"
      :indent-with-tab="true"
      :tabSize="2"
      class="codemirror-box"
      :extensions="extensions"
      :modelValue="nowEditorContent"
      @change="codemirrorChange($event)"
  />

<!--  <Codemirror ref="codeEditor" v-model="nowEditorContent" :options="codemirrorOptions"></Codemirror>-->
</template>

<script>
import { Codemirror } from 'vue-codemirror';// 参考文档：https://blog.csdn.net/Dandrose/article/details/130247393

// import "codemirror/lib/codemirror.css";// 参考文档 https://blog.csdn.net/LoveZJC96/article/details/131513036
// import "codemirror/theme/mbo.css";// 主题参考 https://github.surmon.me/vue-codemirror
// import 'codemirror/addon/display/placeholder.js';// 官方文档 https://codemirror.net/5/doc/manual.html#addon_placeholder
//
// require("codemirror/mode/javascript/javascript"); // 这里引入的模式的js，根据设置的mode引入，一定要引入！！

import { javascript } from '@codemirror/lang-javascript'
import { oneDark } from '@codemirror/theme-one-dark'

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
    Codemirror
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
      extensions:[],
      // codemirrorOptions: {  //vue2
      //   line: true,
      //   placeholder: this.emptyPlaceholder,
      //   lineNumbers: true, // 显示行号
      //   mode: 'text/javascript', // 语法model
      //   gutters: ['CodeMirror-lint-markers'], // 语法检查器
      //   theme: "mbo",
      //   lint: true, // 开启语法检查
      // }
    }
  },
  // computed: {
  //   Codemirror() {
  //     return this.$refs.codeEditor.codemirror
  //   }
  // },
  mounted() {
    this.extensions = [javascript(), oneDark];
    // 初始化
    // this.initCodeEditor()
  },
  methods: {
    // log(type,data){
    //   console.info(type+':',data);
    // },
    //父组件调用清空的方法
    resetData(){
      this.nowEditorContent = '';
      //this.Codemirror.setValue('');
    },
    codemirrorChange(codeData){
      this.nowEditorContent = codeData;
      if (this.$emit) {
        // 通过监听Input事件可以拿到动态改变的code值
        this.$emit('inputData', this.nowEditorContent)
      }
    },
    // 初始化
    // initCodeEditor() {
    //   // let theme = 'ambiance'//设置主题，不设置的会使用默认主题
    //   this.Codemirror.setValue(this.editorContent);
    //   // 支持双向绑定
    //   this.Codemirror.on('change', (coder) => {
    //     this.nowEditorContent = coder.getValue()
    //     if (this.$emit) {
    //       // 通过监听Input事件可以拿到动态改变的code值
    //       this.$emit('inputData', this.nowEditorContent)
    //     }
    //   })
    // },
  },
}
</script>

<style lang="scss">
@import '~@/assets/base.scss';

.codemirror-box .cm-content{
  height: 55vh;
}
</style>
