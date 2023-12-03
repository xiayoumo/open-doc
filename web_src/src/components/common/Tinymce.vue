<template>
  <el-row class="tinymce-box" :gutter="20">
    <el-col :span="showMarkdownNav?18:24">
<!--      编辑页面-->
      <div  v-if="type=='editor'" v-loading="tinymceLoading" element-loading-background="transparent" >
        <TinymceEditor v-if="type=='editor'" :id="editViewId" v-model="tinymceHtml" :init="init"></TinymceEditor>
      </div>
<!--      查看页面-->
      <div :id="htmlViewId" v-if="type=='html'&&tinymceHtml.trim().length>0" :class="isMobileDevice?'tinymce-html-preview-mobile':'tinymce-html-preview'" v-html="tinymceHtml"></div>
      <el-empty v-if="type=='html'&&tinymceHtml.trim().length==0"  :description="$t('empty_data')" :image-size="300"  class="tinymce-html-preview edit-model-box-empty"></el-empty>
    </el-col>
    <el-col v-if="showMarkdownNav" :span="6">
<!--      快速导航-->
      <Quicknav ref="QuickNav"  @doGoEdit="goEdit"></Quicknav>
    </el-col>
  </el-row>
</template>

<script>
import Quicknav from '@/components/common/QuickNav';
import httpToolHelper from '@/js/http-tool-helper';
import md5 from 'js-md5';
import tinymce from 'tinymce/tinymce';
import "tinymce/themes/silver/theme";
// 如果还是不好使的话就改成
// 引入富文本编辑器主题的js和css
import 'tinymce/themes/silver/theme.min.js'
import 'tinymce/skins/ui/oxide/skin.min.css'
// 扩展插件
import 'tinymce/plugins/code';
import 'tinymce/plugins/table';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/advlist';// 高级列表
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/image';
import 'tinymce/plugins/imagetools';
import 'tinymce/plugins/importcss';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/print'; // 打印
import 'tinymce/plugins/preview'; // 预览
// import 'tinymce/plugins/toc'; // 目录
import 'tinymce/plugins/link'; // 超链接插件
// import 'tinymce/plugins/autosave' //自动存稿
import 'tinymce/plugins/charmap' //特殊字符
import 'tinymce/plugins/codesample' //代码示例
import 'tinymce/plugins/searchreplace' //查找替换
import 'tinymce/plugins/template' //内容模板
import 'tinymce/plugins/hr';
import 'tinymce/plugins/imagetools';
import 'tinymce/plugins/autoresize';
import TinymceEditor from '@tinymce/tinymce-vue'

if (typeof window !== 'undefined') {
  var $s = require('scriptjs');
}
export default {
  name: "tinymce",
  computed: {
    showMarkdownNav() {
      return this.type=='html' && !this.isMobileDevice;
    },
  },
  props: {
    editViewId:{
      type:String,
      default: 'tinymceEditView'
    },
    type:{
      type:String,
      default: 'editor'
    },
    pageType:{
      type: String,
      default: 'many'
    },// many or single
    tinymceContent: {
      type: String,
      default: ''
    },
    jqueryMinPath: {
      type: String,
      default: '../../../static/jquery.min.js',
    },
    htmlViewId:{
      type:String,
      default: 'tinymceHtmlView'
    },
    defaultLanguage:{
      type:String,
      default: 'zh-Hans'
    },
    defaultLanguageUrl:{
      type:String,
      default: '../../../static/tinymce/plugins/langs/zh-Hans.json',// 必须放在static/tinymce/plugins/langs ，满足插件tpImportword的调用
    },
  },
  watch:{
    '$i18n.locale'(newValue) {
      tinymce.remove() //关键
      this.initData();// 重新渲染
    },
  },
  components:{
    TinymceEditor,
    Quicknav
  },
  mounted(){
    this.initData();
  },
  data () {
    return {
      tinymceLoading:true,
      markdownNavData: [],
      isMobileDevice:false,
      tinymceHtml: '请输入',
      init: {// 配置参考：https://www.tiny.cloud/docs/tinymce/6/use-tinymce-inline/
        language:this.defaultLanguage,
        language_url:this.defaultLanguageUrl,// 必须放在static/tinymce/plugins/langs ，满足插件tpImportword的调用
        selector: "#"+this.editViewId,
        skin_url: "../../../static/tinymce/skins/ui/oxide-opendoc",
        height: '70vh',
        content_css: [
          '../../../static/tinymce/innerLayout.css'
        ],
        external_plugins: {
          'tpimportword': '../../../static/tinymce/plugins/tpImportword_plugin.min.js' // words的导入插件 指导文档https://tinymce-plugin.gitee.io/packages/tp-importword/quickStart.html
        },
        default_link_target: "_blank",
        menubar: true, //顶部菜单栏显示
        plugins: "tpimportword searchreplace lists image imagetools importcss code table wordcount paste preview print link charmap codesample",
        toolbar: "tpimportword bold italic underline strikethrough  | fontselect | fontsizeselect | formatselect  | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | removeformat| undo redo | link unlink image media insertdatetime table  hr pagebreak codesample | code fullscreen preview",
        branding: false,
        automatic_uploads: true,
        paste_retain_style_properties: "all",
        paste_word_valid_elements: "*[*]", //word需要它
        paste_convert_word_fake_lists: true, // 插入word文档需要该属性
        paste_webkit_styles: "all",
        paste_merge_formats: true,
        nonbreaking_force_tab: false,
        paste_auto_cleanup_on_paste: false,
        end_container_on_empty_block: true,
        powerpaste_word_import: 'merge',
        powerpaste_html_import: 'merge',
        powerpaste_allow_local_images: true,
        paste_data_images: true,    // 允许word粘贴
        // 截图  粘贴的图片  需要走上传方法不然就是个文件流   （该方法工具栏里的图片上传 是两个方法。 分开写，此方法为粘贴图片时使用）
        // urlconverter_callback: function(url, node, on_save, name) {
        //   if (node === 'img' && url.startsWith('blob:') || url.startsWith('data:')) {
        //      console.log('urlConverter:', url, 'node',node, on_save, name)
        //      tinymce.activeEditor && tinymce.activeEditor.uploadImages()
        //   }
        //   //  console.log(node)
        //    console.log(url)
        //   return url;
        // },
        // 此处为图片上传处理函数 该方法为工具栏上传图片  需要走后台上传接口  与上面的方法区分开
        images_upload_handler: function (blobInfo, success, failure) {
          // base64转本地上传
          // 这个函数主要处理word中的图片，并自动完成上传；
          // 自己定义的一个函数；在回调中，记得调用success函数，传入上传好的图片地址；
          // blobInfo.blob() 得到图片的file对象；
          httpToolHelper.execUpload(blobInfo.blob()).then(res => {
            if (res.data.success == 1) {
              success(res.data.url);
            }
          })
        },
        init_instance_callback: this.finishTinymceLoading
      },
      tinymceHtmlViewConfig:{
        selector: '#'+this.htmlViewId,
        inline: true,// 不使用iframe 渲染页面
        readonly: true,
        menubar: false,
        height:'69rem',
        plugins: ['link', 'lists', 'table', 'image', 'codesample','preview'],
        statusbar: false,
        branding: false,
        toolbar: false,
        skin_url: "../../../static/tinymce/skins/ui/oxide-opendoc",
        content_css: [
          '../../../static/tinymce/innerLayout.css'
        ],
        setup: this.startTinymceLoading,// 开始渲染
        onpageload:this.onpageloadByTinymce,
        // init_instance_callback: this.finishTinymceLoading // 结束渲染
      }
    }
  },
  methods:{
    onpageloadByTinymce(){
      this.finishTinymceLoading()
    },
    startTinymceLoading(editor){
      this.tinymceLoading = false;
    },
    finishTinymceLoading(editor){
      this.tinymceLoading = false;
    },
    initQuickNavByInput(htmlId){
      $(htmlId).find(":header").each(function() {
        let headTitle = $(this).text();
        if(headTitle>''){
          // 确认是否已包含跳转a标签
          if($(this).find("a.reference-link").length==0){
            let idString = $(this).prop('tagName').toLowerCase() + '-' + md5(headTitle);
            $(this).attr('id', idString);
            $(this).prepend('<a name="'+headTitle+'" class="reference-link"></a>');
          }
        }
      });
    },
    initQuickNavByTpImportword(htmlId){
      let tpHrArray = $(htmlId).find("p[class^=tp-importword_]");
      let tpHrNumberArray = [];
      tpHrArray.each(function() {
        let num = parseInt($(this).attr('class').substring(14));
        if(typeof num === "number"){
          if(!tpHrNumberArray.includes(num)){
            tpHrNumberArray.push(num);
          }
        }
      });
      let tpHrMaxThreeArray =  tpHrNumberArray.sort((a,b)=>{return a-b}).slice(0,3);
      tpHrArray.each(function() {
        let headTitle = $(this).text().trim();
        if(headTitle>'') {
          let num = parseInt($(this).attr('class').substring(14));
          if (typeof num === "number") {
            if (tpHrMaxThreeArray.includes(num)) {// 当为前三小的标题时
              if ($(this).children().find("a.reference-link").length == 0) {
                let idString = 'h'+ num + '-' + md5(headTitle);
                $(this).attr('id', idString);
                $(this).prepend('<a name="' + headTitle + '" class="reference-link"></a>');
              }
            }
          }
        }
      });
    },
    //插入数据到编辑器中。插入到光标处
    insertValue(data) {
      // 获取富文本编辑器
      var ed = tinymce.get(this.editViewId);
      var range = ed.selection.getRng();

      // 创建要插入的内容
      var divNode = ed.getDoc().createElement('div');
      const realStr = data;
      divNode.innerHTML = realStr;
      range.insertNode(divNode);
    },
    getTinymceContent(){
      return tinymce.get(this.editViewId).getContent();
    },
    clear () {
      tinymce.get(this.editViewId).setContent('');
    },
    initData(){
      let language = this.defaultLanguage;
      let language_url = this.defaultLanguageUrl;
      if(this.$i18n.locale=='en'){
        language = 'en';
        language_url = '';
      }
      if (typeof tinymce === 'undefined') { // 防止重复加载jq
        tinymce.init({}) // 特别注意这个空对象的存在，如果这个初始化空对象不存在依旧会报错
      }
      this.tinymceHtml = this.tinymceContent;
      if(this.type=='editor'){ // 编辑
          this.init.language_url = language_url;
          this.init.language = language;
          tinymce.init(this.init);
          tinymce.util.I18n.setCode(language);// https://www.tiny.cloud/docs/tinymce/6/apis/tinymce.util.i18n/#setCode
      }else{ // 查看
          // 初始化展示
          this.tinymceHtmlViewConfig.language_url = language_url;
          this.tinymceHtmlViewConfig.language = language;
          tinymce.init(this.tinymceHtmlViewConfig);
          //加载依赖""
        if(this.showMarkdownNav) {
          if (typeof window.$ !== 'undefined') { // 防止重复加载jq
            this.initShowHtmlQuickNav();
          } else {
            $s([`${this.jqueryMinPath}`],()=>{
                this.initShowHtmlQuickNav();
            });
          }
        }
      }
      //界面自适应手机
      if( this.isMobile() ||  window.screen.width< 1000) {
        //$("#" + this.id).removeClass("editormd-html-preview").addClass("editormd-html-preview-mobile");
        this.isMobileDevice = true;
      }
    },
    initShowHtmlQuickNav(){
      this.$refs.QuickNav.setQuickNavLoading(true);
      // 初始化快速导航需要的标题格式（与Editormd编辑器保持一致）
      // tpImportword 导入
      this.initQuickNavByTpImportword('#'+this.htmlViewId);
      // 页面添加模式
      this.initQuickNavByInput('#'+this.htmlViewId);
      // 获取便捷菜单内容
      this.markdownNavData = this.$refs.QuickNav.getMarkdownNavData();
      this.$refs.QuickNav.initScrollNav();
      this.quickNavLoading = false;
      this.$refs.QuickNav.setQuickNavLoading(false);
    },
    goEdit(data){
      this.$emit("doGoEdit",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据
    },
    toScrollBottom(){// 父页面滚动到底部调用
      this.$refs.QuickNav.toScrollBottom();
    },
    onScroll(currentScrollTop){// 父页面滚动调用
      this.$refs.QuickNav.onScroll(currentScrollTop);
    }
  },
  beforeDestroy () {
    tinymce.remove();
  }
}
</script>

<style lang="scss">
@import '~@/components/common/base.scss';
#tinymce{
  background: $theme-grey-color;
}
.tinymce-html-preview table{
  width: 100%;
  border-collapse: collapse;
}
.tinymce-html-preview{
  table, table td, table th, table caption {
    padding: 6px 13px;
    border: 1px solid $theme-btn-other-hover-color;
  }
}

.tox-collection__item-label{
  h1,h2,h3,h4,h5,h6,h7,p,pre{
    background: $theme-color !important;
  }
}

/*标题行*/
.tinymce-html-preview table th,.tinymce-html-preview thead tr,.tinymce-html-preview thead tr span
{
  background: $theme-color;
  color:$theme-grey-color ;
}
</style>
<style scoped lang="scss">
@import '~@/components/common/base.scss';

.tinymce-html-preview {
  padding: 2rem 3rem;
  text-align: left;
  font-size: 14px;
  line-height: 1.6;
  overflow: auto;
  //width: 100%;
  background-color: #fff;
  min-height: 70vh;
  border-radius: 10px;
  margin-top: 10px;
}

.tinymce-html-preview-mobile {
  padding: 5px;
  text-align: left;
  font-size: 14px;
  line-height: 1.6;
  overflow: auto;
  //width: 100%;
  background-color: #fff;
  min-height: 70vh;
  border-radius: 10px;
  margin-top: 10px;
}


</style>
