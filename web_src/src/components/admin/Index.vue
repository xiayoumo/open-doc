<template>

  <div class="hello">


    <el-container>
      <el-header>
      <div class="header_title">OpenDoc</div>
      <div v-if="!isMobileDevice" class="user-msg-box">
        当前账号：<span class="user-msg-name">{{ nowUsername }}</span>
      </div>
      <router-link class="goback" to="/item/index">{{$t('goback')}}</router-link>
    </el-header>
      <el-container>
        <el-aside :width="isMobileDevice?'120px':'151px'">

        <el-menu
          default-active="1"
          class="el-menu-vertical-demo"
          text-color="#fff"
          @select="select_menu"
          active-text-color="#ffd04b">

          <el-menu-item index="1">
            <i class="el-icon-info"></i>
            <span slot="title">{{$t('user_manage')}}</span>
          </el-menu-item>
          <el-menu-item index="2">
            <i class="el-icon-tickets"></i>
            <span slot="title">{{$t('item_manage')}}</span>
          </el-menu-item>
          <el-menu-item index="3">
            <i class="el-icon-tickets"></i>
            <span slot="title">{{$t('web_setting')}}</span>
          </el-menu-item>
        </el-menu>
        <div v-if="isMobileDevice" class="user-msg-box-mobile">
          <p>当前账号：</p>
          <p class="user-msg-name">{{ nowUsername }}</p>
        </div>
      </el-aside>
        <span> </span>
        <el-container
          v-loading="pageLoading"
          element-loading-background="transparent"
        >

                <el-main :class="isMobileDevice?'content-box-mobile':'content-box'">
                  <el-collapse-transition>
                  <User v-if="open_menu_index == 1 " @closeLoading="closeLoading"> </User>
                  <Item v-if="open_menu_index == 2 " @closeLoading="closeLoading"> </Item>
                  <Setting v-if="open_menu_index == 3 " @closeLoading="closeLoading"> </Setting>
                  </el-collapse-transition>
                </el-main>

          <el-footer>
            <!-- something -->
        </el-footer>
        </el-container>
      </el-container>
    </el-container>
    </div>
  </div>
</template>
<style lang="scss">
@import '~@/components/common/base.scss';
  .hello{
    background: $theme-grey-color;
    //min-height: 80vh;
    height:100vh;
    overflow-y: auto;
    @include scroll-bar-box;
  }
  .el-pager li.active {
    color: $theme-color !important;
  }
  .el-table tr {
    background-color: $theme-grey-color;
  }
  .el-table th.el-table__cell{
    background-color: $theme-color;
  }
  .el-pagination button.disabled {
    background-color: $theme-grey-color !important;
  }
  .el-dialog, .el-pager li {
    background: $theme-grey-color !important;
  }
  .el-pagination button{
    background-color: $theme-grey-color !important;
  }
  .el-button--text {
    color: $theme-color;
    &:hover,&:focus{
      color: $theme-right-msg-color;
    }
  }
  .el-input .el-input__inner{
      @include input-box;
  }
  .el-button--default,.el-button--medium{
      @include yes-btn;
  }
  .el-button--primary{
    @include yes-full-btn;
  }
  .el-dialog__close{
    @include close-btn;
  }
  .el-switch.is-checked .el-switch__core {
    border-color:$theme-right-msg-color;
    background-color: $theme-right-msg-color;
  }
  .el-message-box {
    background-color: $theme-secondary-color;
    border: 1px solid $theme-secondary-color;
  }
  .el-message-box__message{
    color: $theme-fourth-color;
    text-align: center;
  }
</style>

<style scoped lang="scss">
@import '~@/components/common/base.scss';

  .user-msg-box-mobile{
    color: $theme-btn-other-color;
    line-height: 14px;
    margin-top: 10rem;
  }
  .user-msg-box{
    color: $theme-btn-other-color;
    float: right;
    margin-right: 1%;
  }
  .user-msg-name{
    color: $theme-btn-other-hover-color;
    font-size: 14px;
    font-weight: bold;
  }
  .el-header {
    background-color: $theme-color;
    color: $theme-light-black-color;
    text-align: center;
    line-height: 60px;
    border-bottom:1px solid $theme-btn-other-hover-color;
    padding-left: 0px;
  }

   .el-footer {
    color: $theme-light-black-color;
    text-align: center;
    line-height: 60px;
  }


  .el-aside {
    background-color: $theme-color;
    color: $theme-light-black-color;
    text-align: center;
    line-height: 200px;
    height: calc(100% - 60px);
    position: absolute;
  }
  .el-aside::-webkit-scrollbar-thumb{
    /*滚动条里面小方块*/
    border-radius: 10px;
    height: 20px;
    background-color: rgb(210, 210,210);
  }
  .el-aside::-webkit-scrollbar{
    /*滚动条整体样式*/
    width: 7px; /*高宽分别对应横竖滚动条的尺寸*/
  }
  .el-menu{
    border-right: 0px;
    background: $theme-color;
  }
  .el-menu li:hover,.el-menu li:focus{
    background: $theme-black-grey-color;
  }

  .el-main {
    margin-left: 200px;
    overflow: visible;
    margin-top: 30px;
  }
  .content-box-mobile{
    margin-left: 120px;
  }
  body > .el-container {
    position: absolute;
    height: 100%;
    width: 100%;
    background: $theme-grey-color;
  }

  .el-container:nth-child(5) .el-aside,
  .el-container:nth-child(6) .el-aside {
    line-height: 260px;
  }

  .el-container:nth-child(7) .el-aside {
    line-height: 320px;
  }

  .goback{
    float: right;
    margin-right: 20px;
    color: $theme-grey-color;
    &:hover{
      color: $theme-right-msg-color;
    }
  }

  .header_title{
    float: left;
    padding-right: 35px;
    padding-left: 25px;
    font-size: 20px;
    background-color: $theme-color;
    color: #fff;
  }
</style>

<script>
import Item from '@/components/admin/item/Index'
import User from '@/components/admin/user/Index'
import Setting from '@/components/admin/setting/Index'
import store from '@/store';

export default {
  data() {
    return {
      open_menu_index:1,
      isMobileDevice:false,
      nowUsername:'',
      pageLoading:false,
    };
  },
  created() {
    this.pageLoading = true;
    let userInfo = localStorage.getItem('user_info');
    this.nowUsername = JSON.parse(userInfo).username;
  },
  components:{
    Item,
    User,
    Setting,
  },
  methods:{
    closeLoading(){
      this.pageLoading = false;
    },
    select_menu(index,indexPath){
      this.open_menu_index = 0 ;
      this.pageLoading = true;
      this.$nextTick(()=>{
        this.open_menu_index = index ;
      });

    },
    check_upadte(){
        var that = this ;
        var url = DocConfig.server+'/api/adminUser/checkUpdate';
          var params = new URLSearchParams();
          that.axios.post(url, params)
            .then(function (response) {
              if (response && response.data && response.data.data && response.data.data.url) {
                  that.$message({
                    showClose: true,
                    duration:10000,
                    dangerouslyUseHTMLString: true,
                    message: '<a target="_blank" href="'+response.data.data.url+'">'+response.data.data.title+'</a>'
                  });
              };
            });
    },
  },
  mounted () {
    // this.check_upadte();
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice = true;
      // this.$nextTick(() => {
      //   this.AdaptToMobile();
      // });
    }

  },
  beforeDestroy(){
    this.$message.closeAll();
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>
