<template>
  <el-row >
    <el-col :span="showMarkdownNav&&pageType=='many'?16:24">
      <div :id="id" class="main-editor" >
        <link href="../../../static/editor.md/css/editormd.css" rel="stylesheet">
        <textarea ref="editorMd" v-show="false" v-html="content" ></textarea>
        <!-- 放大图片 -->
        <BigImg v-if="showImg" @clickit="showImg = false" :imgSrc="imgSrc"></BigImg>
      </div>
    </el-col>
    <el-col v-if="showMarkdownNav" :span="8">
      <el-card class="markdown-nav-box latest-activity-card">
        <div class="latest-activity-card__badge">{{$t('quick_navigation')}}</div>
        <el-timeline hide-timestamp class="markdown-nav-timelie">
          <el-timeline-item
            v-for="(activity, index) in markdownNavData"
            :class="activity.showType!='root'?'timeline-item-sub-title':''"
            :key="index">
            <span :name="'nav-timelie-item-'+index" @click="scrollIntoViewByMarkdownNav(activity.headId,index)" >{{activity.content}}</span>
          </el-timeline-item>
        </el-timeline>
      </el-card>
    </el-col>
  </el-row>
</template>

<style lang="scss">
@import '~@/components/common/base.scss';


.timeline-item-sub-title{
  padding-left: 20px;
}
.table-auto-overflow-x{
  overflow-x: auto;
}
.scroll-into-view{
  color:$theme-right-msg-color !important;
}
.markdown-nav-timelie{
  margin-top: 2rem !important;
  padding-left: 5px;
  width: 100%;
  max-width: 300px;
  min-width: 200px;
  min-height: 425px;
  height: 48vh;
  overflow-y: auto;
  @include scroll-bar-box;
}
.markdown-nav-timelie .el-timeline-item {
  padding-bottom: 5px;
}
.markdown-nav-timelie .el-timeline-item__wrapper {
  //padding-left: 18px;
}
.el-timeline-item__wrapper .el-timeline-item__content{
  cursor: pointer;
  color: $theme-color;
  font-weight: bold;
  &:hover,&:focus,&:visited{
    color:$theme-right-msg-color !important;
  }
}
.el-timeline-item__node--normal {
  //left: -2px;
}
.el-timeline-item__content .is-active{
  color:$theme-right-msg-color !important;
  font-weight: bold;
}

.markdown-nav-box{
  margin-left: 0px;
  top: 155px;
  position: fixed;
  left: 70%;
  height: 60vh;
  background-color: #fff;
}
.latest-activity-card{
  overflow: visible !important;
  //height: 236px;
  background: #fff;
  box-shadow: 8px 8px 20px 0 rgba(55,99,170,.1);
  //position: relative;
  background-size: 100vh;
  background-repeat: no-repeat;
  background-position: right -92px bottom -115px;
  border: 2px solid #fff;
  z-index: 0;
  padding: 22px;
  box-sizing: border-box;
  transition: all .3s ease-in-out;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  //background-image: url('../../../static/images/nav-bg.png');
  background-position: right -41px bottom -146px;
}
.doc-container .latest-activity-card:before {
  content: "";
  z-index: -1;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  border-radius: 4px;
  background: linear-gradient(96.75deg,#fafcff 2.24%,hsla(0,0%,100%,.06) 103.12%);
}

.latest-activity-card__badge:before {
  content: "";
  width: 14px;
  height: 26px;
  position: absolute;
  background-size: auto 100%;
  top: 0;
  left: -8px;
  background-image: url(../../../static/images/nav-head-badge-corner.svg);
}
.latest-activity-card__badge {
  position: absolute;
  right: -2px;
  top: -6px;
  height: 26px;
  background: $theme-color;
  border-radius: 0 6px 0 6px;
  font-weight: bold;
  font-size: 14px;
  line-height: 18px;
  color:$theme-grey-color;
  text-align: center;
  box-sizing: border-box;
  padding: 4px 10px;
}

.markdown-nav-box .el-divider{
  margin-top: 10px;
}
.quick-navigation-title{
  font-weight: bold;
  font-size: 14px;
  color: $theme-color;
}
.message-box{
  @include message-box;
}
</style>
<script>
import BigImg from '@/components/common/BigImg'

if (typeof window !== 'undefined') {
  var $s = require('scriptjs');
}
export default {
  computed: {
    showMarkdownNav() {
      return this.markdownNavData.length>0 && this.type=='html' && !this.isMobileDevice;
    },
  },
  name: 'Editor',
  props: {
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
    editorConfig: { // 参考官方：https://pandao.github.io/editor.md/examples/index.html
      type: Object,
      default() {
        return {
                path: '../../../static/editor.md/lib/',
                height: '70vh',
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
                    this.$emit("finishLoad",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据
                    console.log('onload');
                }
          };
      },
    },

  },
  components:{
    BigImg
  },
  created() {

  },
  // markdown-nav
  data() {
    return {
      isScrollByNav:false,//是否便捷目录点击的滚动
      offsetTopByHtmlHead:false,// 是否由加粗实现的定位滚动（默认是h1等）
      offsetTopDefault:209,
      offsetTopArray:[],
      markdownNavData: [{
        content: '活动按期开始',
        showType: 'root'
      }, {
        content: '通过审核',
        showType: 'sub'
      }],
      instance: null,
      showImg:false,
      imgSrc: '',
      backgroundColorByTableTheadTr:'rgb(21, 24, 34)', //background-color #409eff
      colorByTableTheadTr:'rgb(245, 245, 245)', //color #fff
      colorByCode:'rgb(6,192,95)', //color #fff
      backgroundColorByCode:'#384548', //color #fff
      isMobileDevice:false,
      doubleTitleData:{name:'',navId:'',headId:''},// 重复出现的标题名称
    };
  },
  mounted() {
    //加载依赖""
    $s([`${this.editorPath}/../jquery.min.js`,
    	`${this.editorPath}/lib/raphael.min.js`,
    	`${this.editorPath}/lib/flowchart.min.js`,

    	],()=>{
          $s([
            `${this.editorPath}/../xss.min.js`,
            `${this.editorPath}/lib/marked.min.js`,
            `${this.editorPath}/lib/prettify.min.js`,
            `${this.editorPath}/lib/underscore.min.js`,
            `${this.editorPath}/lib/sequence-diagram.min.js`,
            `${this.editorPath}/lib/jquery.flowchart.min.js`,
          ], () => {

            $s(`${this.editorPath}/editormd.js`, () => {
              this.initEditor();
            });

            $s(`${this.editorPath}/../highlight/highlight.min.js`, () => {
              hljs.initHighlightingOnLoad();
            });
        });
    });


  },
  methods: {
    setContentTitleSelected(headId=''){
      $('.scroll-into-view').removeClass('scroll-into-view');
      $("#"+headId).addClass('scroll-into-view');
    },
    setNowMarkdownNav(navNowIndex){
      $('.el-timeline-item__node').css('background-color','#E4E7ED');
      let className = 'nav-timelie-item-' + navNowIndex;
      $('.el-timeline-item__content span').removeClass('is-active');
      let obj= document.querySelector("span[name='"+className+"']");
      $(obj).addClass('is-active');
      $(obj).parent().parent().prev().css('background-color','rgb(170, 0, 10)');
    },
    toScrollBottom(){// 父页面滚动到底部调用
      if(this.offsetTopArray.length>0 && !this.isScrollByNav) {// 当为按钮触发不进行快捷导航的设置监听
        let nowIndex = this.markdownNavData.length - 1;
        this.setNowMarkdownNav(nowIndex);
        this.setContentTitleSelected(this.markdownNavData[nowIndex].headId);
      }
    },
    onScroll(currentScrollTop){// 父页面滚动调用
      if(!this.isScrollByNav){// 当为按钮触发不进行快捷导航的设置监听
        let that = this;
        if(that.offsetTopArray.length>0){
          let offsetTopArr = that.offsetTopArray;
          // 定义当前点亮的导航下标
          let addNum = that.offsetTopDefault + 200;
          currentScrollTop = Math.ceil(currentScrollTop) + addNum;// 小数进一,当为点击快速导航滚动时，不需要加距离高度
          for (let n = 0; n < offsetTopArr.length; n++) {
            // 如果 scrollTop 大于等于第 n 个元素的 offsetTop 则说明 n-1 的内容已经完全不可见
            // 那么此时导航索引就应该是 n 了
            if (currentScrollTop >= offsetTopArr[n].offsetTop) {
              if (n < (offsetTopArr.length - 1) && currentScrollTop < offsetTopArr[n + 1].offsetTop) {
                that.setNowMarkdownNav(n);
                that.setContentTitleSelected(offsetTopArr[n].headId);
                break;
              }
            }
          }
        }
      }
    },
    getOffsetTopArray(){// 获取当前全部标题的对于页面顶部的距离
      // 所有锚点元素的 offsetTop
      const offsetTopArr = []
      let navContents = document.querySelectorAll("a.reference-link");
      if(navContents.length == 0){
        navContents = document.querySelectorAll("strong");
        if(navContents.length > 0){
          navContents.forEach((item,i) => {
            let temp={
              headId:item.id,
              offsetTop:Math.floor($(item).offset().top),
              name:item.innerText.replace(/<.*?>/g,""),
            }
            offsetTopArr.push(temp)
          })
        }
      }else{
        navContents.forEach((item,i) => {
          let temp={
            headId:item.parentNode.id,
            offsetTop:Math.floor($(item).offset().top),
            name:item.name.replace(/<.*?>/g,""),
          }
          offsetTopArr.push(temp)
        })
      }
      return offsetTopArr;
    },
    scrollIntoViewByMarkdownNav(headId='',cIndex=0){
      this.scrollIntoViewByClick(headId);
      this.setNowMarkdownNav(cIndex);
    },
    scrollIntoViewBySelector(headId=''){
      var that = this;
      that.isScrollByNav = true;
     // document.getElementById(headId).scrollIntoView({block:'center',behavior:'smooth'});
      document.querySelector("#"+headId).scrollIntoView({block:'center',behavior:'smooth'});
      setTimeout(function (){
        that.$set(that, 'isScrollByNav', false)
      }, 800)
    },
    scrollIntoViewByClick(headId=''){
      this.setContentTitleSelected(headId);
      this.scrollIntoViewBySelector(headId);
    },
    initEditor() {
      var that = this;
      this.$nextTick((editorMD = window.editormd) => {
        if (editorMD) {

          if (this.type == 'editor'){
            this.instance = editorMD(this.id, this.editorConfig);
            //草稿
            //this.draft(); 鉴于草稿功能未完善。先停掉。
            //window.addEventListener('beforeunload', e => this.beforeunloadHandler(e));
          } else {
            this.instance = editorMD.markdownToHTML(this.id, this.editorConfig);
            //获取便捷目录
            // 获取便捷菜单内容
            that.markdownNavData = [];
            let maxHeadTag = '';
            let nodeHeadType = {};
            let headHtmlData = document.querySelectorAll(".reference-link");
            if(headHtmlData.length == 0){ // strong 等
              headHtmlData = document.querySelectorAll("strong");
              if(headHtmlData.length>0){
                  [].forEach.call(headHtmlData, function(e,i){
                    let tempName = e.innerHTML.replace(/<.*?>/g,"");
                    let headId = 'strong-'+i;
                    $(e).attr('id',headId);
                    let tempData = {
                      headId:headId,
                      content:tempName,
                      showType:'root'
                    };
                    that.markdownNavData.unshift(tempData);
                  });
              }
            }else{// h1 等标题
              let nameDoubleCheck = [];
              [].forEach.call(headHtmlData, function(e,i){
                let tempName = e.name.replace(/<.*?>/g,"");
                let headData = e.parentNode.id.split('-');
                if(nameDoubleCheck.indexOf(tempName)!==-1){
                  that.doubleTitleData.name = tempName;
                  that.doubleTitleData.navId = i;
                  that.doubleTitleData.headId = e.parentNode.id;
                }
                nodeHeadType[tempName] = headData[0];// 登记类型
                if(maxHeadTag==''){
                  maxHeadTag = headData[0];
                }else{
                  if(headData[0] < maxHeadTag){
                    maxHeadTag = headData[0];
                  }
                }
                nameDoubleCheck.push(tempName);// 重复标题登记检查
              });
              [].forEach.call(headHtmlData, function(e){
                let tempName = e.name.replace(/<.*?>/g,"");
                let tempData = {
                  headId:e.parentNode.id,
                  content:tempName,
                  showType:'root'
                };
                if(nodeHeadType[tempName] > maxHeadTag){ // 当不为一级标题时，需要约进
                  tempData.showType = 'sub';
                }
                that.markdownNavData.unshift(tempData);
              });
            }
            that.markdownNavData.reverse()
          }
          this.deal_with_content();
          //获取页面元素的高度
          that.offsetTopArray = this.getOffsetTopArray()

          //监听toc 或 tocm 菜单点击事件
          $(".toc-menu-span-btn").click(function(){
            let clickText = $(this).text();
            that.markdownNavData.some((navData,n) =>{
              if(navData.content== clickText){
                that.scrollIntoViewByMarkdownNav(navData.headId,n);
                return true;
              }
            });
          });
          // 提示是否重复
          if(this.doubleTitleData.navId > ''){
            let warningMsg = this.$t("double_title_tips")+'：'+this.doubleTitleData.name;
            this.$alert(warningMsg, '温馨提示', {
              confirmButtonText: '前往编辑',
              customClass:'message-box',
              callback: action => {
                this.$emit("doGoEdit",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据
              }
            });
            //滚动到重复的地方
            this.scrollIntoViewByMarkdownNav(this.doubleTitleData.headId,this.doubleTitleData.navId);
          }else{
            this.setNowMarkdownNav(0); // 默认显示第一个菜单
          }
        }
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
    for (var i = 1; i < 999; i++){
      window.clearInterval(i);
    };
    // 必须移除监听器，不然当该vue组件被销毁了，监听器还在就会出错
    window.removeEventListener('scroll', this.onScroll)
    //window.removeEventListener('beforeunload', e => this.beforeunloadHandler(e))
  },
};
</script>
