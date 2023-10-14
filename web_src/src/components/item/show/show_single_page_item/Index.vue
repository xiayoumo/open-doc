<template>
  <el-main class="hello single-main-box"  @scroll.native="get_scroll" >
<!--    <Header> </Header>-->
      <!-- 默认页面仪表盘 -->
      <el-container v-if="isShowDashboard" class="right-side" id="right-side">
        <ShowDashboard :item_info="item_info"></ShowDashboard>
      </el-container>
      <!--      单页面展示内容-->
      <div v-if="!isShowDashboard" id="header"></div>
    <div v-if="!isShowDashboard"
           v-loading="pageLoading"
           element-loading-background="rgba(0, 0, 0, 0.2)"
           :class="(isMobileDevice?'container-mobile':'container')+' doc-container'"
           id="doc-container">

         <div class="doc-title-box">
            <span id="doc-title-span" class="dn"></span>
            <h2 id="doc-title">{{page_title}}</h2>

            <div class="tool-bar pull-right">

                <el-button type="text"   @click="share_item">{{$t('share')}}</el-button>
                <el-button type="text" @click="edit_page"  v-if="item_info.ItemPermn && item_info.is_archived < 1" >{{$t('edit')}}</el-button>
                  &nbsp;&nbsp;&nbsp;
                <el-dropdown>
                  <span class="el-dropdown-link">
                    {{$t('item')}}<i class="el-icon-arrow-down el-icon--right"></i>
                  </span>
                  <el-dropdown-menu slot="dropdown">
                    <router-link :to="'/item/export/'+item_info.item_id"  v-if="item_info.ItemPermn"><el-dropdown-item>{{$t('export')}}</el-dropdown-item></router-link>
                    <router-link :to="'/item/setting/'+item_info.item_id"  v-if="item_info.ItemCreator"><el-dropdown-item>{{$t('item_setting')}}</el-dropdown-item></router-link>
                    <router-link to="/item/index"><el-dropdown-item >{{$t('goback')}}</el-dropdown-item></router-link>
                  </el-dropdown-menu>
                  </el-dropdown-menu>
                </el-dropdown>
            </div>

        </div>
        <div id="doc-body" >

          <div id="page_md_content" >
            <Editormd :content="content" pageType="single" :scrollBoxClass="scrollBoxClass" v-if="content"  ref="child" @doGoEdit="do_go_edit" type="html"></Editormd>
          </div>
        </div>

      </div>
      <el-dialog
        :title="$t('share')"
        :visible.sync="dialogVisible"
        :width="isMobileDevice?'40vh':'60vh'"
        :modal="false"
        class="text-center"
        >

        <p>{{$t('item_address')}} :  <code >{{share_item_link}}</code></p>
        <p><a href="javascript:;" class="home-phone-butt" v-clipboard:copyhttplist="copyText" v-clipboard:success="onCopy">{{$t('copy_link')}}</a></p>
            <p style="border-bottom: 1px solid #eee;"><img   id="" style="width:114px;height:114px;" :src="qr_item_link"> </p>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click="dialogVisible = false">{{$t('confirm')}}</el-button>
        </span>
      </el-dialog>
      <BackToTop></BackToTop>
      <Footer> </Footer>

  </el-main>
</template>

<style scoped lang="scss">
@import '~@/components/common/base.scss';
  .right-side{
    //margin-left:300px;
    background: $theme-grey-color;
    margin-bottom: 30px;
    //overflow-y: auto;
    //height: 94vh;
    //@include scroll-bar-box;
  }
  .single-main-box{
    padding-top: 15px;
    overflow-y: auto;
    height: 100vh;
    @include scroll-bar-box;
  }
  #page_md_content{
      padding: 10px 10px 90px 10px;
      overflow: hidden;
      font-size: 11pt;
      line-height: 1.7;
      color: $theme-light-black-color;
  }
  .container{
    margin-left: 15%;
  }
  .container-mobile{
    margin-left: auto;
    margin-right: auto;
  }
  .doc-container {
      position: static;
      -webkit-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      -moz-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      -ms-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      -o-box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      box-shadow: 0px 1px 6px $theme-btn-other-hover-color;
      background-color: #fff;
      border-bottom: 1px solid $theme-input-color;
      width: 52%;
      min-height: 500px;
      padding: 20px;
      margin-bottom: 80px;
      border-radius: 10px;
      margin-top: 60px;
  }

  #header{
    height: 80px;
  }

  #doc-body{
    width: 90%;
    margin: 0 auto;
    background-color: #fff;
  }

  .doc-title-box{
      height: auto;
      margin: 0px 10% 0px 10%;
      width: auto;
      //border-bottom: 1px solid $theme-border-color;
      padding-bottom: 10px;
      width: 90%;
      margin: 10px auto;
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
    border: 1px solid #e1e1e8;
  }
  .hljs{
    background-color: $theme-grey-color;
  }
  .tool-bar{
    margin-top: -20px;
  }
  .editormd-html-preview, .editormd-preview-container{
    padding: 0px;
    font-size: 16px;
    border-radius: 8px;
  }

</style>

<script>
import Editormd from '@/components/common/Editormd'
import BackToTop from '@/components/common/BackToTop'
import ShowDashboard from '@/components/item/show/Dashboard'
import store from '@/store'

export default {
  props:{
    item_info:'',
  },
  data () {
    return {
      menu:'',
      content:"",
      page_title:'',
      page_id:'',
      dialogVisible:false,
      share_item_link:'',
      qr_item_link:'',
      copyText:'',
      isMobileDevice:false,
      pageLoading:false,
      goEdit:false,//前往编辑
      scrollBoxClass:'.single-main-box',
    };
  },
  created() {
  },
  components:{
    Editormd,
    BackToTop,
    ShowDashboard
  },
  methods:{
    do_go_edit(){
      this.goEdit = true;
    },
    get_scroll(){
      const ele = document.querySelector(this.scrollBoxClass);
      let isBottom = ele.scrollTop+ele.clientHeight-ele.scrollHeight;
      if(isBottom < 0){
        this.$refs.child.onScroll(ele.scrollTop);
      }else{
        this.$refs.child.toScrollBottom();
      }
    },
    get_page_content(page_id){
        var that = this ;
        that.pageLoading = true;
        var url = DocConfig.server+'/api/page/info';
        if (! page_id) {
          page_id = that.page_id;
        };
        var params = new URLSearchParams();
        params.append('page_id',  page_id);
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              //that.$message.success("加载成功");
              setTimeout(function() {
                that.page_title = response.data.data.page_title ;
                that.content = response.data.data.page_content ;
                that.pageLoading = false;
              }, 300);
            }else{
              that.$alert(response.data.error_message);
            }

          })
          .catch(function (error) {
            console.log(error);
          });
    },

    edit_page(){
      var page_id = this.page_id > 0 ? this.page_id : 0 ;
      var url = '/page/edit/'+this.item_info.item_id+'/'+page_id;
      this.$router.push({path:url}) ;
    },
    share_item(){
      this.share_item_link =  this.getRootPath()+"/item-show/"+this.item_info.item_code;
      this.qr_item_link = DocConfig.server +'/api/common/qrcode&size=3&url='+encodeURIComponent(this.share_item_link);
      this.dialogVisible = true;
      this.copyText = this.item_info.item_name+"  -- OpenDoc \r\n"+ this.share_item_link;
    },
    AdaptToMobile(){
      var doc_container = document.getElementById('doc-container') ;
      doc_container.style.width = '100%';
      doc_container.style.padding = '5px';
      var header = document.getElementById('header') ;
      header.style.height = '10px';
    },
    onCopy(){
      this.$message({message:this.$t("copy_success")});
    }
  },
  computed: {
    isShowDashboard() {
      let isShowDashboardData = this.$route.path.indexOf('item-count-show')!==-1 && this.item_info.is_login;
      store.dispatch('SetIsShowDashboard', isShowDashboardData);
      return isShowDashboardData;
    }
  },

  mounted () {
    this.menu = this.item_info.menu ;
    this.page_id = this.menu.pages.page_id ;
    this.get_page_content();

    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice=true;
      this.$nextTick(() => {
        this.AdaptToMobile();
      });
    }
  }
}
</script>
