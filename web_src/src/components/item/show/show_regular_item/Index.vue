<template xmlns="http://www.w3.org/1999/html">
  <div >
<!--      阅读模式      -->
      <el-container v-if="!isEditLeftMenu">
        <el-aside class="el-aside" id="left-side" width="300px">
            <LeftMenu v-if="item_info" @setEditLeftMenu="setEditLeftMenu" :get_page_content="get_page_content" :item_info="item_info" :search_item="search_item" ></LeftMenu>
        </el-aside>

        <!-- 默认页面仪表盘 -->
        <el-main v-if="isShowDashboard" class="right-side is-vertical" id="right-side">
          <ShowDashboard :item_info="item_info"></ShowDashboard>
        </el-main>

        <el-main
          @scroll.native="get_scroll"
          v-loading="pageLoading"
          element-loading-body="true"
          element-loading-background="rgba(0, 0, 0, 0.2)"
          v-if="!isShowDashboard"
          class="right-side is-vertical"
          id="right-side">
          <el-header class="page-head-box">
            <div class="header-right">
              <!-- 登录的事情下 -->
<!--              <router-link class="header-right-goback-btn" v-if="item_info.is_login" to="/item/index" >{{$t('goback')}} </router-link>-->
              <el-dropdown  v-if="item_info.is_login&&!isMobileDevice" @command="dropdown_callback">
                <span class="el-dropdown-link">
                  {{$t('item')}}<i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item :command="share_item">{{$t('share')}}</el-dropdown-item>
                  <router-link :to="'/item/export/'+item_info.item_id" v-if="item_info.ItemPermn"><el-dropdown-item>{{$t('export')}}</el-dropdown-item></router-link>
                  <router-link :to="'/item/setting/'+item_info.item_id"  v-if="item_info.ItemCreator&&!isMobileDevice"><el-dropdown-item>{{$t('item_setting')}}</el-dropdown-item></router-link>
                </el-dropdown-menu>
              </el-dropdown>

              <!-- 非登录的情况下 -->

              <div v-if="!item_info.is_login">
                  <router-link to="/user/login">{{$t('login_or_register')}}</router-link>
                  &nbsp;&nbsp;&nbsp;&nbsp;
                  <a :href="homeUrl+'/show/index'" target="_blank">{{$t('about_opendoc')}}</a>
              </div>
            </div>
          </el-header>

          <el-main v-show="page_id" :class="isMobileDevice?'page_content_main_mobile':'page_content_main'" id="page_content_main">
                  <div v-if="page_info.page_use!='excel'" :class="isMobileDevice?'doc-title-box-mobile':'doc-title-box'">
                    <span id="doc-title-span" class="dn"></span>
                    <transition name="el-fade-in-linear">
                      <div v-show="page_id" class="transition-box">
                        <h2 id="doc-title">{{page_title}}</h2>
                      </div>
                    </transition>
                  </div>
                  <Editormd  ref="child" v-if="page_id&&page_info.page_use=='api'" pageType="many" :scrollBoxClass="scrollBoxClass" @doGoEdit="do_go_edit" :content="content" type="html"></Editormd>
                  <Tinymce ref="child" v-if="page_id&&page_info.page_use=='doc'" pageType="many" :scrollBoxClass="scrollBoxClass" @doGoEdit="do_go_edit" :tinymceContent="content" type="html"></Tinymce>
                  <Luckysheet ref="child" v-if="page_id&&page_info.page_use=='excel'" pageType="many" :scrollBoxClass="scrollBoxClass" @savePage="save_page" :sheetTitle="page_title"  :sheetContent="content" type="html"></Luckysheet>
                  <el-empty v-if="content==''" :description="$t('empty_data')" :image-size="250"  class="edit-model-box-empty"></el-empty>
          </el-main>
          <div class="page-bar" v-show="show_page_bar && item_info.ItemPermn && item_info.is_archived < 1 && !isShowDashboard " >
            <PageBar v-if="page_id" :goEdit="goEdit" :page_id="page_id" :item_id='item_info.item_id' :item_info='item_info'  :page_info="page_info"></PageBar>
          </div>

          <el-dialog :title="$t('share_page')" :visible.sync="dialogShareItemVisible" :width="isMobileDevice?'40vh':'60vh'" :modal="false" class="text-center">
            <p><img id="" class="qrcode-img" :src="qr_item_link"> </p>
            <p>
              <el-button type="text" class="copy-link-btn" v-clipboard:copyhttplist="copyText" v-clipboard:success="onCopy"><span class="iconfont icon-fuzhi"></span> {{$t('copy_link')}}</el-button>
              <code >{{share_item_link}}</code>
            </p>
            <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="dialogShareItemVisible = false">{{$t('confirm')}}</el-button>
          </span>
          </el-dialog>
        </el-main>
      </el-container>
<!--      编辑模式      -->
      <el-container v-if="isEditLeftMenu">
        <el-aside class="el-aside" id="left-side" width="300px">
          <LeftMenuEdit v-if="item_info" @setEditLeftMenu="setEditLeftMenu" @editPage="editPage" :item_info="item_info"></LeftMenuEdit>
        </el-aside>
        <el-main class="edit-model-box">
          <EditPage v-if="editPageId>0"  :key="editPageId" :now_page_id="editPageId" :is_children="true"></EditPage>
          <el-empty v-if="editPageId==0" :description="$t('doc_edit_area')" :image-size="250"  class="edit-model-box-empty"></el-empty>
        </el-main>

      </el-container>
  </div>
</template>

<script>
  import Editormd from '@/components/common/Editormd'
  import Tinymce from '@/components/common/Tinymce'
  import Luckysheet from '@/components/common/Luckysheet'
  import EditPage from '@/components/page/edit/Index'
  import LeftMenu from '@/components/item/show/show_regular_item/LeftMenu'
  import LeftMenuEdit from '@/components/item/show/show_regular_item/LeftMenuEdit'
  import PageBar from '@/components/item/show/show_regular_item/PageBar'
  import ShowDashboard from '@/components/item/show/Dashboard'
  import store from '@/store'
  import pageHelper from '@/js/page-helper'

  export default {
    props:{
      item_info:'',
      search_item:'',// 父级方法
      get_item_menu:'',// 父级方法
    },
    components:{
      Editormd,
      Tinymce,
      Luckysheet,
      LeftMenu,
      LeftMenuEdit,
      PageBar,
      EditPage,
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
      "isEditLeftMenu"(newValue,oldValue){
        if(!newValue){// 关闭编辑模式时
          this.search_item('');
        }
      }
    },
    data() {
      return {
        editPageId:0,
        isEditLeftMenu:false,
        homeUrl:DocConfig.homeUrl,
        content:"###正在加载...",
        page_id:'',
        page_title:'',
        dialogShareItemVisible:false,
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
      editPage(pageId){
        this.$forceUpdate();// 必须强制更新
        this.$emit('setNowPageId',pageId);// 设置父页面的
        this.editPageId = pageId;
      },
      setEditLeftMenu(data){
        this.isEditLeftMenu = data;
      },
      do_go_edit(){
        this.goEdit = true;
      },
      get_scroll(){
        if(!this.isMobileDevice&&this.page_info.page_use!='excel') {
          const ele = document.querySelector(this.scrollBoxClass);
          let isBottom = ele.scrollTop + ele.clientHeight - ele.scrollHeight;
          if (isBottom < 0) {
            this.$refs.child.onScroll(ele.scrollTop);
          } else {
            this.$refs.child.toScrollBottom();
          }
        }
      },
      async save_page(page_content=''){
        var that = this ;
        that.pageLoading = true;
        let res = await pageHelper.savePage(this.page_info.cat_id,this.page_info.item_id,this.page_id,page_content,this.page_title,this.page_info.s_number,this.page_info.page_use)
        if(res.error_code==0){
          that.get_page_content(this.page_id);
        }
        that.pageLoading = false;
      },
      //获取页面内容
      async get_page_content(page_id){
          this.now_page_id = page_id;
          if (page_id <= 0 ) {return;};

          //根据屏幕宽度进行响应(应对移动设备的访问)
          if( this.isMobile() ||  window.screen.width< 1000){
            this.$nextTick(() => {
              this.AdaptToMobile();
            });
          }
          var that = this ;
          that.$forceUpdate();// 必须强制更新
          that.pageLoading = true;
          let response = await pageHelper.getPage(page_id);
          if (response.error_code === 0 ) {
              that.page_info = response.data ;
              that.page_title = response.data.page_title ;
              that.content = response.data.page_content ;
              pageHelper.scrollPageById('right-side','top',that);
              //切换变量让它重新加载、渲染子组件
              that.page_id = 0 ;
              that.$nextTick(() => {
                that.page_id = page_id ;
                that.pageLoading = false;
              });
          }else{
            that.$alert(response.error_message);
          }
      },
      dropdown_callback(data){
        if (data) {
          data();
        };
      },
      share_item(){
        this.share_item_link =  this.getRootPath()+"/item-show/"+this.item_info.item_id  ;
        this.qr_item_link = DocConfig.server +'/api/common/qrcode&size=3&url='+encodeURIComponent(this.share_item_link);
        this.dialogShareItemVisible = true;
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
          // element.style.marginLeft = '300px';
          element.style.display = 'none' ;
          // var element = document.getElementById('page_content_main') ;
          // element.style.width = '800px' ;
      },
      hide_menu(){
          var element = document.getElementById('left-side') ;
          element.style.display = 'none';
          var element = document.getElementById('right-side') ;
          element.style.display = 'block' ;
          element.style.marginLeft = '0px';
          // var element = document.getElementById('page_content_main') ;
          // element.style.width = '100%' ;
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

  .copy-link-btn{
    margin-right: 20px;
  }
  .qrcode-img{
    width:114px;height:114px;
  }
  .edit-model-box{
    background: $theme-grey-color;
    height: 100vh;
    //margin-left: 300px;
  }
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
    line-height: 60px;
    text-align: right;
    font-size: 12px;
    right: 6%;
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
    background-color: $theme-grey-color;
    border-right: solid 1px $theme-border-color;
    margin-top: 60px;
    max-width: 300px;
    width: 100% !important;
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
    top: 120px;
    right: 20px;
    width: 100px;
  }

  .page_content_main{

    //width: 70%;
    //margin-left: 15%;
    width: 95%;
    margin-left: 1%;
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
    padding: 5px;
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
