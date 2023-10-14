<template xmlns="http://www.w3.org/1999/html">
  <div >
<!--    <Header> </Header>-->

      <el-container>

        <el-aside class="el-aside" id="left-side">
            <LeftMenu :get_page_content="get_page_content" :item_info="item_info" :search_item="search_item" v-if="item_info" ></LeftMenu>
        </el-aside>

        <!-- 默认页面仪表盘 -->
        <el-container v-if="isShowDashboard" class="right-side is-vertical" id="right-side">
          <ShowDashboard :item_info="item_info"></ShowDashboard>
        </el-container>

        <el-container
          @scroll.native="get_scroll"
          v-loading="pageLoading"
          element-loading-body="true"
          element-loading-background="rgba(0, 0, 0, 0.2)"
          v-if="!isShowDashboard" class="right-side is-vertical" id="right-side">
          <el-header class="page-head-box">

            <div class="header-right">
              <!-- 登录的事情下 -->
               <router-link class="header-right-goback-btn" v-if="item_info.is_login" to="/item/index" >{{$t('goback')}} </router-link>
              <el-dropdown @command="dropdown_callback" v-if="item_info.is_login">
                <span class="el-dropdown-link">
                  {{$t('item')}}<i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item :command="share_item">{{$t('share')}}</el-dropdown-item>
                  <router-link :to="'/item/export/'+item_info.item_id" v-if="item_info.ItemPermn"><el-dropdown-item>{{$t('export')}}</el-dropdown-item></router-link>
                  <router-link :to="'/item/setting/'+item_info.item_id"  v-if="item_info.ItemCreator"><el-dropdown-item>{{$t('item_setting')}}</el-dropdown-item></router-link>
                </el-dropdown-menu>
              </el-dropdown>

              <!-- 非登录的情况下 -->

              <div v-if="!item_info.is_login">
                  <router-link to="/user/login">{{$t('login_or_register')}}</router-link>
                  &nbsp;&nbsp;&nbsp;&nbsp;
                  <a href="http://open-doc.docker-sky.cn/help" target="_blank">{{$t('about_opendoc')}}</a>
              </div>

            </div>


          </el-header>

            <el-main
              v-show="page_id" :class="isMobileDevice?'page_content_main_mobile':'page_content_main'" id="page_content_main">
                    <div :class="isMobileDevice?'doc-title-box-mobile':'doc-title-box'">
                      <span id="doc-title-span" class="dn"></span>
                      <transition name="el-fade-in-linear">
                        <div v-show="page_id" class="transition-box">
                          <h2 id="doc-title">{{page_title}}</h2>
                        </div>
                      </transition>
                    </div>
                    <Editormd ref="child" v-if="page_id" pageType="many" :scrollBoxClass="scrollBoxClass" @doGoEdit="do_go_edit" v-bind:content="content" type="html"></Editormd>
            </el-main>

        </el-container>

        <div class="page-bar" v-show="show_page_bar && item_info.ItemPermn && item_info.is_archived < 1 && !isShowDashboard " >
          <PageBar v-if="page_id" :goEdit="goEdit" :page_id="page_id" :item_id='item_info.item_id' :item_info='item_info'  :page_info="page_info"></PageBar>
        </div>
      </el-container>
      <BackToTop  > </BackToTop>
      <el-dialog
        title="分享项目"
        :visible.sync="dialogVisible"
        :width="isMobileDevice?'40vh':'60vh'"
        :modal="false"
        class="text-center"
        >
          <p>项目地址：<code >{{share_item_link}}</code></p>
          <p><a href="javascript:;" class="home-phone-butt" v-clipboard:copyhttplist="copyText" v-clipboard:success="onCopy">{{$t('copy_link')}}</a></p>
              <p style="border-bottom: 1px solid #eee;"><img id="" style="width:114px;height:114px;" :src="qr_item_link"> </p>
          <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="dialogVisible = false">{{$t('confirm')}}</el-button>
          </span>
      </el-dialog>
      <Footer> </Footer>

  </div>
</template>

<script>
  import Editormd from '@/components/common/Editormd'
  import BackToTop from '@/components/common/BackToTop'
  import LeftMenu from '@/components/item/show/show_regular_item/LeftMenu'
  import PageBar from '@/components/item/show/show_regular_item/PageBar'
  import ShowDashboard from '@/components/item/show/Dashboard'
  import store from '@/store'
  import pageHelper from '@/js/page-helper.js'

  export default {
    props:{
      item_info:'',
      search_item:''
    },
    components:{
      Editormd,
      LeftMenu,
      PageBar,
      BackToTop,
      ShowDashboard
    },
    watch: {
      //   监听属性对象，newValue为新的值，也就是改变后的值
      "showLeftMenu"(newValue, oldValue) {
        if (newValue) {
          this.show_menu();
        }else{
          this.hide_menu();
        }
      },
    },
    data() {
      return {
        content:"###正在加载...",
        page_id:'',
        page_title:'',
        dialogVisible:false,
        share_item_link:'',
        qr_item_link:'',
        page_info:'',
        show_page_bar:true,
        copyText:"",
        now_page_id:0,
        isMobileDevice:false,
        pageLoading:false,
        goEdit:false,//前往编辑
        scrollBoxClass:'.right-side',
      }
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
      //获取页面内容
      get_page_content(page_id){
          this.now_page_id = page_id;
          if (page_id <= 0 ) {return;};

          //根据屏幕宽度进行响应(应对移动设备的访问)
          if( this.isMobile() ||  window.screen.width< 1000){
            this.$nextTick(() => {
              this.AdaptToMobile();
            });
          }
          var that = this ;
          that.pageLoading = true;
          var url = DocConfig.server+'/api/page/info';
          //var loading = that.$loading({target:".page_content_main",fullscreen:false});
          var params = new URLSearchParams();
          params.append('page_id',  page_id);
          that.axios.post(url, params)
            .then(function (response) {
              //loading.close();
              if (response.data.error_code === 0 ) {

                that.page_info = response.data.data ;
                setTimeout(function() {
                  that.page_title = response.data.data.page_title ;
                  that.content = response.data.data.page_content ;
                  pageHelper.scrollPageById('right-side','top',that);
                  //切换变量让它重新加载、渲染子组件
                  that.page_id = 0 ;
                  that.$nextTick(() => {
                    that.page_id = page_id ;
                    that.pageLoading = false;

                  });
                }, 300);

              }else{
                that.$alert(response.data.error_message);
              }
            });
      },
      dropdown_callback(data){
        if (data) {
          data();
        };
      },
      share_item(){
        this.share_item_link =  this.getRootPath()+"/show-item/"+this.item_info.item_id  ;
        this.qr_item_link = DocConfig.server +'/api/common/qrcode&size=3&url='+encodeURIComponent(this.share_item_link);
        this.dialogVisible = true;
        this.copyText = this.item_info.item_name+"  -- OpenDoc \r\n"+ this.share_item_link;
      },
      //根据屏幕宽度进行响应(应对移动设备的访问)
      AdaptToMobile(){
        store.dispatch('SetShowHeadMenu', true);
        store.dispatch('SetShowLeftMenu', false);
        this.hide_menu();
        this.show_page_bar = false;
      },
      show_menu(){
          var element = document.getElementById('left-side') ;
          element.style.display = 'block' ;
          var element = document.getElementById('right-side') ;
          element.style.marginLeft = '300px';
          element.style.display = 'none' ;
          var element = document.getElementById('page_content_main') ;
          element.style.width = '800px' ;
      },
      hide_menu(){
          var element = document.getElementById('left-side') ;
          element.style.display = 'none';
          var element = document.getElementById('right-side') ;
          element.style.display = 'block' ;
          element.style.marginLeft = '0px';
          var element = document.getElementById('page_content_main') ;
          element.style.width = '100%' ;
      },
      // switch_menu(){
      //   var element = document.getElementById('left-side') ;
      //   if (element.style.display == 'none') {
      //     this.show_menu();
      //   }else{
      //     this.hide_menu();
      //   }
      // },
      onCopy(){
        this.$message(this.$t("copy_success"));
      },
    },
    computed: {
      showLeftMenu(){
        return store.getters.showLeftMenu;
      },
      isShowDashboard() {
        this.now_page_id = this.$route.query.page_id ? this.$route.query.page_id : 0  ;
        let isShowDashboardData = (this.$route.path.indexOf('item-count-show')!==-1||this.now_page_id==0) && this.item_info.is_login;
        store.dispatch('SetIsShowDashboard', isShowDashboardData);
        return isShowDashboardData;
      }
    },
    mounted () {
      window.addEventListener('resize', this.resizePage)
      //根据屏幕宽度进行响应(应对移动设备的访问)
      if( this.isMobile() ||  window.screen.width< 1000){
        this.isMobileDevice = true;
        this.$nextTick(() => {
          this.AdaptToMobile();
        });
      }

    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
  .header-right-goback-btn{
    margin: 0px 10px;
  }
  .page-head-box{
    padding-top: 70px;
    width: 150px;
    height: 0px !important;
  }
  .header-right{
    color: $theme-light-black-color;
    line-height: 40px;
    text-align: right;
    font-size: 12px;
    right: 10px;
    position: absolute;
    /*border: 1px solid #eee;*/
  }
  .header-right .el-dropdown-link{
    margin-right: 20px;
  }
  .el-aside {
    color: $theme-light-black-color;
    position:fixed;
    height: calc(100% - 20px);
    background-color: rgb(250, 250, 250);
    border-right: solid 1px $theme-border-color;
    margin-top: 60px;;
  }
  .el-aside::-webkit-scrollbar-thumb{
    /*滚动条里面小方块*/
    border-radius: 10px;
    height: 20px;
    background-color: $theme-btn-other-hover-color;
  }
  .el-aside::-webkit-scrollbar{
    /*滚动条整体样式*/
    width: 7px; /*高宽分别对应横竖滚动条的尺寸*/
  }
  .page-bar{
    color: $theme-light-black-color;
    position:fixed;
    top: 100px;
    right: 10px;
    width: 100px;
  }

  .page_content_main{
    //width:auto;
    width: 70%;
    //margin: 0 auto ;
    margin-left: 15%;
    overflow: visible;
    //min-height: 86vh;
    margin-bottom: 40px;
    //padding-top: 50px;
  }
  .page_content_main_mobile{
    margin: 0 auto;
    overflow: visible;
    margin-bottom: 40px;
    width: 100%;
  }
  .editormd-html-preview-mobile{
    width: 100%;
    font-size: 16px;
    border-radius: 8px;
    //padding: 5px;
    background: $theme-grey-color;
  }
  .doc-title-box-mobile{
    height: auto;
    margin: 60px 5px 0px 5px;
    width: auto;
    //padding-bottom: 10px;
  }
  .right-side{
    margin-left:300px;
    background: $theme-grey-color;
    overflow-y: auto;
    height: 94vh;
    @include scroll-bar-box;
  }

  .doc-title-box{
      height: auto;
      //margin: 0px 20px 0px 20px;
      width: auto;
      //border-bottom: 1px solid $theme-border-color;
      //padding-bottom: 10px;
  }
  .editormd-html-preview{
    width: 100vh;
    font-size: 16px;
    border-radius: 8px;
    margin-bottom: 60px;
    max-width: 95%;
    min-height: 70vh;
  }


</style>
