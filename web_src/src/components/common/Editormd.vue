<template>
  <div v-loading="editormdLoading" element-loading-background="transparent">
    <el-row  :gutter="20">
      <el-col :span="showMarkdownNav?18:24" >
        <div v-if="type=='html'||type=='editor'" :id="id" class="main-editor">
          <el-empty v-if="type=='html'&&content.trim().length==0" :description="$t('empty_data')" :image-size="300"  class="edit-model-box-empty"></el-empty>
          <link href="../../../static/editor.md/css/editormd.css" rel="stylesheet">
          <textarea ref="editorMd" v-show="false" v-html="content" ></textarea>
          <!-- 放大图片 -->
          <BigImg v-if="showImg" @clickit="showImg = false" :imgSrc="imgSrc"></BigImg>
        </div>
      </el-col>
      <el-col v-if="showMarkdownNav" :span="6">
        <Quicknav ref="QuickNav" @doGoEdit="goEdit"></Quicknav>
      </el-col>
    </el-row>
<!--    仅供渲染markdownToHtml-->
    <div id="show-editormd-view">
      <textarea style="display:none;" name="show-editormd-markdown-doc">###Hello world!</textarea>
    </div>
  </div>

</template>

<style lang="scss">
@import '~@/components/common/base.scss';

.message-box{
  @include message-box;
}
.CodeMirror-lines{
  min-height: 63vh !important;
  margin-top: 1rem;
  border-top: 1px dotted $theme-icon-color;
  border-bottom: 1px dotted $theme-icon-color;
  margin-bottom: .5rem;
}
</style>
<script>
import BigImg from '@/components/common/BigImg'
import Quicknav from '@/components/common/QuickNav'

if (typeof window !== 'undefined') {
  var $s = require('scriptjs');
}
export default {
  computed: {
    showMarkdownNav() {
      return  this.type=='html' && !this.isMobileDevice;
    },
  },
  name: 'Editor',
  watch: {
    '$i18n.locale'(newValue) {//
      this.initEditor();
    }
  },
  props: {
    pageId:{
      type: String,
      default: '0'
    },
    pageType:{
      type: String,
      default: 'many'
    },// many or single
    scrollBoxClass:'',
    width: '',
    content:{
      type: String,
      default: '###初始化成功'
    },
    type: {
      type:String,
      default: 'editor'
    },
    id: {
      type: String,
      default: 'editor-md'
    },
    editorPath: {
      type: String,
      default: '../../../static/editor.md',
    },
    jqueryMinPath: {
      type: String,
      default: '../../../static/jquery.min.js',
    },
    editorConfig: { // 参考官方：https://pandao.github.io/editor.md/examples/index.html
      type: Object,
      default() {
        return {
               // language:'en-US',// 'zh-CN','en-US'
                path: '../../../static/editor.md/lib/',
                height: '69vh',
                emoji:true,
                tocm:true,
                taskList        : true,
                tex             : true,  // 默认不解析 Tex 科学公式语言 (TeX/LaTeX)
                flowChart       : true,  // 默认不解析
                sequenceDiagram : true,  // 默认不解析
                syncScrolling: "single",
                htmlDecode: 'style,script,iframe|filterXSS',
                imageUpload: true,
                imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp", "JPG", "JPEG", "GIF", "PNG", "BMP", "WEBP"],
                imageUploadURL: DocConfig.server+"/api/page/uploadImg",
                toolbarIcons : function() {
                      return [
                        "undo", "redo", "|",
                        "bold", "del", "italic", "quote", "ucwords", "uppercase", "lowercase", "|",
                        "h1", "h2", "h3", "h4", "h5", "h6", "|",
                        "tocmAdd","tocAdd", "list-ul", "list-ol", "hr", "|",
                        "link", "reference-link", "image", "code", "preformatted-text", "code-block", "table", "datetime", "emoji", "html-entities", "pagebreak", "|",
                        "goto-line", "watch", "preview", "fullscreen", "clear", "search", "|",
                        // "help", "info"
                        "help"
                      ];
                },
                // 自定义工具栏按钮的事件处理
                toolbarHandlers : {
                  /**
                   * @param {Object}      cm         CodeMirror对象
                   * @param {Object}      icon       图标按钮jQuery元素对象
                   * @param {Object}      cursor     CodeMirror的光标对象，可获取光标所在行和位置
                   * @param {String}      selection  编辑器选中的文本
                   */
                  tocmAdd : function(cm, icon, cursor, selection) {
                    //var cursor    = cm.getCursor();     //获取当前光标对象，同cursor参数
                    //var selection = cm.getSelection();  //获取当前选中的文本，同selection参数
                    // 替换选中文本，如果没有选中文本，则直接插入
                    cm.replaceSelection("[TOCM]" + selection);
                    // 如果当前没有选中的文本，将光标移到要输入的位置
                    if(selection === "") {
                      cm.setCursor(cursor.line, cursor.ch + 1);
                    }
                  },
                  tocAdd : function(cm, icon, cursor, selection) {
                    // 替换选中文本，如果没有选中文本，则直接插入
                    cm.replaceSelection("[TOC]" + selection);
                    // 如果当前没有选中的文本，将光标移到要输入的位置
                    if(selection === "") {
                      cm.setCursor(cursor.line, cursor.ch + 1);
                    }
                  },
                },
                lang : {
                  toolbar: {
                    tocmAdd: "生成目录树",
                    tocAdd: "生成目录列表",
                  }
                },
                toolbarIconsClass : {
                  tocmAdd : "fa-sitemap",  // 指定一个FontAawsome的图标类
                  tocAdd : "fa-reorder",  // 指定一个FontAawsome的图标类
                },
                onload: () => { // 只在编辑状态被调用，展示状态不执行
                  this.editormdFinishLoad();
                    //this.$emit("finishLoad",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据
                    console.log('onload');
                }
          };
      },
    },

  },
  components:{
    BigImg,
    Quicknav
  },
  // markdown-nav
  data() {
    return {
      editormdLoading:true,
      markdownNavData: [],
      instance: null,
      showImg:false,
      imgSrc: '',
      backgroundColorByTableTheadTr:'rgb(21, 24, 34)', //background-color #409eff
      colorByTableTheadTr:'rgb(245, 245, 245)', //color #fff
      colorByCode:'rgb(6,192,95)', //color #fff
      backgroundColorByCode:'#384548', //color #fff
      isMobileDevice:false,
      languageZhCn:{},
    };
  },
  mounted() {
    if (typeof window.editormd !== 'undefined') { // 防止重复加载jq
      this.initEditor();
      hljs.initHighlightingOnLoad();
    }else{
      //加载依赖""
      $s([
        `${this.jqueryMinPath}`,
        `${this.editorPath}/lib/raphael.min.js`,
        `${this.editorPath}/lib/flowchart.min.js`
      ],()=>{
        $s([
          `${this.editorPath}/../xss.min.js`,
          `${this.editorPath}/lib/marked.min.js`,
          `${this.editorPath}/lib/prettify.min.js`,
          `${this.editorPath}/lib/underscore.min.js`,
          `${this.editorPath}/lib/sequence-diagram.min.js`,
          `${this.editorPath}/lib/jquery.flowchart.min.js`
        ], () => {
          $s([`${this.editorPath}/editormd.js`], () => {
            this.initEditor();
          });
          $s(`${this.editorPath}/../highlight/highlight.min.js`, () => {
            hljs.initHighlightingOnLoad();
          });
        });
      });
    }
  },
  methods: {
    editormdFinishLoad(){// 当子组件加载完成后插入数据
      // this.isEditormdFinishLoad = true;
      if(this.content>''){
        this.initContentToEditormd();
      }
    },
    initContentToEditormd(){
      // this.insertValue(this.content ,1) ;
      //如果长度大于3000,则关闭预览
      if (this.content.length > 3000) {
        this.editor_unwatch();//关闭预览
        if (!sessionStorage.getItem("page_id_unwatch_"+this.pageId) ){
          this.$alert("检测到本页面内容比较多，OpenDoc暂时关闭了html实时预览功能，以防止过多内容造成页面卡顿。你可以在编辑栏中找到预览按钮进行手动打开。");
          sessionStorage.setItem("page_id_unwatch_"+this.pageId,1)
        }
      }else{
        this.editor_watch();// 开启预览
      }
      this.editormdLoading = false;
    },
    markdownDataToHtml(markdownData){// 获取文件之间的翻译
      var newHtml = window.editormd.markdownToHTML("show-editormd-view", {
        markdown: markdownData,
        htmlDecode: "style,script,iframe",  // you can filter tags decode
        tocm: true,    // Using [TOCM]
        tocContainer: "#custom-toc-container", // 自定义 ToC 容器层
        emoji: true,
        taskList: true,
        tex: true,  // 默认不解析
        flowChart: true,  // 默认不解析
        sequenceDiagram: true,  // 默认不解析
      });
      return $(newHtml).html();
    },
    goEdit(data){
      this.$emit("doGoEdit",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据
    },
    toScrollBottom(){// 父页面滚动到底部调用
      this.$refs.QuickNav.toScrollBottom();
    },
    onScroll(currentScrollTop){// 父页面滚动调用
      this.$refs.QuickNav.onScroll(currentScrollTop);
    },
    initEditor() {
      var that = this;
      this.$nextTick((editorMD = window.editormd) => {
        if (editorMD) {
          if (this.type == 'editor'){// 编辑
            this.instance = editorMD(this.id, this.editorConfig);
            //草稿
            //this.draft(); 鉴于草稿功能未完善。先停掉。
            //window.addEventListener('beforeunload', e => this.beforeunloadHandler(e));
            // 设置多语言
            this.setLanguage(editorMD,this.$i18n.locale);// locale zh/en
          } else {// 查看
            this.instance = editorMD.markdownToHTML(this.id, this.editorConfig);
            this.editormdLoading = false;
            //获取便捷目录
            // 获取便捷菜单内容
            this.$refs.QuickNav.setQuickNavLoading(true);
            this.markdownNavData = that.$refs.QuickNav.getMarkdownNavData();
            this.$refs.QuickNav.initScrollNav();
            this.$refs.QuickNav.setQuickNavLoading(false);
          }
          this.deal_with_content();

        }
      });
    },
    setLanguage(editorMD,lang='zh'){ // zh/en
        lang = lang=='en'?'en':'zh-cn';// zh-cn(默认)/zh-tw/en
        var languageFilePath  = this.editorPath+"/languages/"+lang;
        editorMD.loadScript(languageFilePath, function() {
          editorMD.lang = editorMD.defaults.lang;
          // 只重建涉及语言包的部分，如工具栏、弹出对话框等
          // if(isRecreate){
          //   editorMD.recreate();
          // }
        });
    },
    //插入数据到编辑器中。插入到光标处
    insertValue(insertContent){
      this.instance.insertValue(this.html_decode(insertContent));
    },

    getMarkdown(){
      return this.instance.getMarkdown();
    },
    editor_unwatch(){
      return this.instance.unwatch();
    },

    editor_watch(){
      return this.instance.watch();
    },
    clear(){
      return this.instance.clear();
    },

    //草稿
    draft(){
      var that = this ;
        //定时保存文本内容到localStorage
        setInterval(()=>{
            localStorage.page_content= that.getMarkdown() ;
        }, 60000);

      //检测是否有定时保存的内容
      var page_content = localStorage.page_content ;
      if (page_content && page_content.length > 0) {
        localStorage.removeItem("page_content");
        that.$confirm(that.$t('draft_tips'),'',{
          showClose:false
        }
        ).then(()=>{
            that.clear() ;
            that.insertValue(page_content) ;
            localStorage.removeItem("page_content");
          }).catch(()=>{
            localStorage.removeItem("page_content");
          });
      };

    },
    //关闭前提示
    beforeunloadHandler(e){
        e = e || window.event;

        // 兼容IE8和Firefox 4之前的版本
        if (e) {
          e.returnValue = '提示';
        }

        // Chrome, Safari, Firefox 4+, Opera 12+ , IE 9+
        return '提示';
    },

    //对内容做些定制化改造
    deal_with_content(){
      var that = this ;
        //当表格列数过长时将自动出现滚动条
        $.each($("#"+this.id+' table'), function() {
            $(this).prop('outerHTML', '<div class="table-auto-overflow-x">'+$(this).prop('outerHTML')+'</div>');
        });

        //超链接都在新窗口打开
        $("#"+this.id+' a[href^="http"]').each(function() {
          $(this).attr('target', '_blank');

        });

        //对表格进行一些改造
        $("#"+this.id+" table tbody tr").each(function(){
          var tr_this = $(this) ;
          var td1 =  tr_this.find("td").eq(1).html() ;
          var td2 =  tr_this.find("td").eq(2).html() ;
          if(td1 =="object" || td1 =="array[object]" || td2 =="object" || td2 =="array[object]"){
            tr_this.css({"background-color":"#F8F8F8"});
          }else{
            tr_this.css("background-color","#fff");
          }
          //设置表格hover
          tr_this.hover(function(){
              tr_this.css("background-color","#F8F8F8");
          },function(){
            if(td1 =="object" || td1 =="array[object]" || td2 =="object" || td2 =="array[object]"){
              tr_this.css({"background-color":"#F8F8F8"});
            }else{
              tr_this.css("background-color","#fff");
            }
          });

        });

        //获取内容总长度
        var contentWidth = $("#"+this.id+" p").width() ;
        contentWidth = contentWidth ? contentWidth : 722;
        //表格列 的宽度
        $("#"+this.id+" table").each(function(i){
          var $v =$(this).get(0) ;//原生dom对象
          var num = $v.rows.item(0).cells.length ; //表格的列数
          var colWidth = Math.floor(contentWidth/num) -2 ;
          if (num <= 5) {
            $(this).find("th").css("width",colWidth.toString()+"px");
          }
        });

        //图片点击放大
        $("#"+this.id+" img").click(function(){
          var  img_url = $(this).attr("src");
          that.showImg = true;
　　　　　　// 获取当前图片地址
          that.imgSrc = img_url;
        });
        $("#"+this.id+" img").css("cursor",'pointer') ;
        //表格头颜色
        $("#"+this.id+" table thead tr").css("background-color",this.backgroundColorByTableTheadTr) ;
        $("#"+this.id+" table thead tr").css("color",this.colorByTableTheadTr) ;
        $("#"+this.id+" table thead tr").css("font-size",'15px') ;
        $("#"+this.id+" table tbody tr").css("font-size",'14px') ;

        //代码块美化
        $("#"+this.id+" .linenums").css("padding-left","5px") ;
        $("#"+this.id+" .linenums li").css("list-style-type","none") ;
        $("#"+this.id+" .linenums li").css("background-color",this.backgroundColorByCode) ;
        $("#"+this.id+" pre").css("background-color",this.backgroundColorByCode) ;
        $("#"+this.id+" pre").css("border","1px solid #e1e1e8") ;
        $("#"+this.id+" pre").css("border-radius","8px") ;

        $("#"+this.id+" code").css("color",this.colorByCode);
        $("#"+this.id+" code").css("padding",'10px');
        $("#"+this.id+" code").css("border",'none');

        //界面自适应手机
        if( this.isMobile() ||  window.screen.width< 1000) {
          $("#" + this.id).removeClass("editormd-html-preview").addClass("editormd-html-preview-mobile");
          this.isMobileDevice = true;
        }

        //为超链接加上target='_blank'属性
        $("#"+this.id+" a").each(function() {
          $(this).attr('target', '_blank');
        });


    },
    //转义
    html_decode(str){
      var s = "";
      if (str.length == 0) return "";
      s = str.replace(/&gt;/g, "&");
      s = s.replace(/&lt;/g, "<");
      s = s.replace(/&gt;/g, ">");
      s = s.replace(/&nbsp;/g, " ");
      s = s.replace(/&#39;/g, "\'");
      s = s.replace(/&quot;/g, "\"");
      //s = s.replace(/<br>/g, "\n");
      return s;
    }
  },
  beforeDestroy() {
    //清理所有定时器
    let vm = this
    if (vm.timer != null) {
      window.clearInterval(vm.timer)
      vm.timer = null
    }

    // for (var i = 1; i < 999; i++){
    //   window.clearInterval(i);
    // };
    // 必须移除监听器，不然当该vue组件被销毁了，监听器还在就会出错
    window.removeEventListener('scroll', this.onScroll);
    //window.removeEventListener('beforeunload', e => this.beforeunloadHandler(e))
    // delete window.editormd;
    // delete window.jquery;
  },
};
</script>
