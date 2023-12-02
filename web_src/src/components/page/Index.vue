<template>
<!--  被分享后用户进入的页面-->
  <div class="hello">
    <Header> </Header>
    <el-main class="hello share-main-box"  @scroll.native="get_scroll" id="doc-container" >
      <div id="header"></div>
      <div @scroll="get_scroll" class="container doc-container" id="doc-container">
         <div class="doc-title-box">
            <span id="doc-title-span" class="dn"></span>
            <h2 v-if="page_use!='excel'" id="doc-title">{{page_title}}</h2>
        </div>
        <div id="doc-body" >

          <div id="page_md_content" >
            <Editormd v-if="page_use=='api'&&content" :key="page_id" :content="content" :scrollBoxClass="scrollBoxClass" ref="child"  type="html"></Editormd>
            <Tinymce v-if="page_use=='doc'&&content" :key="page_id" :tinymceContent="content" :scrollBoxClass="scrollBoxClass" ref="child" type="html" ></Tinymce>
            <Luckysheet v-if="page_use=='excel'&&content" :key="page_id" :sheetTitle="page_title"  :sheetContent="content" :scrollBoxClass="scrollBoxClass" ref="child" type="html"></Luckysheet>
            <el-empty v-if="content==''" :description="$t('empty_data')" :image-size="250"  class="edit-model-box-empty"></el-empty>
          </div>
        </div>

      </div>
    </el-main>
      <BackToTop></BackToTop>
    <Footer> </Footer>
    <div class=""></div>
  </div>
</template>

<style scoped  lang="scss">
@import '~@/components/common/base.scss';
  .share-main-box{
    padding-top: 15px;
    overflow-y: auto;
    height: 100vh;
    @include scroll-bar-box;
  }
  #page_md_content{
      //padding: 10px 10px 90px 10px;
      overflow: hidden;
      font-size: 11pt;
      line-height: 1.7;
      color: $theme-light-black-color;
      //padding-bottom: 90px;
  }

  .doc-container {
      position: static;
      //-webkit-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      //-moz-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      //-ms-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      //-o-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      //box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      //background-color: #fff;
      //border-bottom: 1px solid $theme-btn-other-hover-color;
      margin-bottom: 20px;
      width: 90%;
      min-height: 500px;
      margin-left: auto;
      margin-right: auto;
      padding: 20px;
  }

  #header{
    height: 80px;
  }

  #doc-body{
    width: 90%;
    margin: 0 auto;
    //margin-top: 25px;
    //background-color: #fff;
  }

  .doc-title-box{
      height: auto;
      margin: 0px 10% 0px 10%;
      width: auto;
      padding-bottom: 10px;
      width: 90%;
      margin: 10px auto;
  }
  #doc-title{
    margin: 0px;
  }
  #footer{
      margin: 0 auto;
      width: 180px;
      font-size: 8px;
      color: $theme-words-color;
  }

  pre ol{
    list-style: none;
  }

  .markdown-body pre {
    background-color: $theme-grey-color;
    border: 1px solid $theme-input-color;
  }
  .hljs{
    background-color: $theme-grey-color;
  }
</style>


<script>
import Editormd from '@/components/common/Editormd'
import Tinymce from '@/components/common/Tinymce'
import Luckysheet from '@/components/common/Luckysheet'
import BackToTop from '@/components/common/BackToTop'
import pageHelper from '@/js/page-helper'

export default {
  data () {
    return {
      currentDate: new Date(),
      itemList:{},
      content:"",
      page_title:'',
      scrollBoxClass:'.share-main-box',
      page_id:0,
      page_use:'',
    };
  },
  components:{
    Editormd,
    Tinymce,
    Luckysheet,
    BackToTop
  },
  methods:{
    get_scroll(){
      if(!this.isMobileDevice&&this.page_use!='excel'){
        const ele = document.querySelector(this.scrollBoxClass);
        let isBottom = ele.scrollTop+ele.clientHeight-ele.scrollHeight;
        if(isBottom < 0){
          this.$refs.child.onScroll(ele.scrollTop);
        }else{
          this.$refs.child.toScrollBottom();
        }
      }
    },
    async get_page_content(){
        var that = this ;
        var page_id = that.$route.params.page_id ;
        let response = await pageHelper.getPage(page_id);
        if (response.error_code === 0 ) {
          that.content = response.data.page_content ;
          that.page_title = response.data.page_title ;
          that.page_id = response.data.page_id ;
          that.page_use = response.data.page_use ;
        }
        else if (response.error_code === 10307 || response.error_code === 10303 ) {
          //需要输入密码
          that.$router.replace({
              path: '/item/password/0',
              query: {page_id: page_id,redirect: that.$router.currentRoute.fullPath}
          });
        }
        else{
          that.$alert(response.error_message);
        }
    },
    AdaptToMobile(){
      var doc_container = document.getElementById('doc-container') ;
      doc_container.style.width = '95%';
      doc_container.style.padding = '5px';
      var header = document.getElementById('header') ;
      header.style.height = '10px';
    }

  },
  mounted () {
    this.get_page_content();
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.$nextTick(() => {
        this.AdaptToMobile();
      });
    }
    /*给body添加类，设置背景色*/
    document.getElementsByTagName("body")[0].className="grey-bg";

  },
  beforeDestroy(){
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>
