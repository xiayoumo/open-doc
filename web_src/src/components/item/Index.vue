<template>
  <div class="hello">
<!--    <Header> </Header>-->

    <el-container
      class="head-container container-narrow">
      <el-row class="masthead">

        <el-col :span="18" :offset="3">
          <el-row class="logo-title ">
<!--            <div >-->
                <div class="muted" v-if="!isCustomWebTitle"><img :src="logoPicUrl" style="width:100.5px;height:36px;object-fit: contain;" alt=""></div>
                <div v-else class="custom-log-box">
                    <div v-if="logoPicUrl!=''" class="muted" >
                      <img :src="logoPicUrl" class="custom-logo-pic" alt="">
                    </div>
                    <span v-if="webTitleName!=''" class="custom-web-name">{{webTitleName}}</span>
                </div>
<!--            </div>-->
          </el-row>
          <el-row class="header-btn-group pull-right">
<!--            <div >-->
  <!--            <el-button type="text"  @click="feedback">{{$t("feedback")}}</el-button>-->
              <Lang></Lang>
              <router-link to="/show/index" >{{$t('show_index')}} </router-link>
              <a v-if="outsideBtnData.length>0" v-for="data in outsideBtnData" class="outside-web-btn" :href="data.url" target="_blank">{{data.name}}</a>
              <router-link class="home-top-menu-btn" to="/team/index" >{{$t('team_mamage')}} </router-link>
              <router-link class="home-top-menu-btn" to="/admin/index" v-if="isAdmin&&!isMobileDevice">{{$t('background')}}</router-link>
              &nbsp;&nbsp;&nbsp;
              <el-dropdown @command="dropdown_callback">
                <span class="el-dropdown-link">
                  {{$t("more")}}<i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown" >
                  <el-dropdown-item class="head-dropdown-link-box">
                    <el-row>
                      <el-col :span="24">
                        <p class="head-dropdown-user-msg user-name-title"> {{nowUserInfo.username}} </p>
                        <p class="head-dropdown-user-msg" v-if="nowUserInfo.name>''"> 名称： {{nowUserInfo.name}}</p>
                      </el-col>
                    </el-row>
                  </el-dropdown-item>
                  <el-dropdown-item :command="goSetting" class="head-dropdown-link-box">
                    <span class="iconfont icon-bianji user-info-icon"></span> {{$t("personal_setting")}}
                  </el-dropdown-item>
                  <el-dropdown-item :command="logout" class="head-dropdown-link-box" divided>
                    <span class="iconfont icon-tuichudenglu user-info-icon"></span> {{$t("logout")}}
                  </el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
<!--            </div>-->
          </el-row>
        </el-col>

      </el-row>

      </el-container>

      <el-container
        v-loading="pageLoading"
        element-loading-body="true"
        element-loading-background="rgba(0, 0, 0, 0.2)"
        class="container-narrow">
        <div class="container-thumbnails">
          <ul class="thumbnails" id="item-list" v-if="itemList">

                <li slot="reference" class="itme-li-box"  v-for="item in itemList">
                    <el-tooltip :manual="true" effect="dark" :content="$t('create_item_tip')" placement="bottom-start" v-model="isFirstTimeCreate">
                       <div>
                         <span v-if="item.item_use=='excel'" class="item-use-icon iconfont icon-excelwenjian"></span>
                         <span v-if="item.item_use=='api'" class="item-use-icon iconfont icon-APIwendang"></span>
                         <span v-if="item.item_use=='doc'" class="item-use-icon iconfont icon-word"></span>
                         <router-link class="thumbnail item-thumbnail"  :to="'/item-show/' +  (item.item_domain ? item.item_domain:item.item_code )" title="">
                          <span v-if="item.is_creator" class="item-setting " @click.prevent="click_item_setting(item.item_id)" :title="$t('item_setting')" >
                            <i class="el-icon-setting"></i>
                          </span>
                           <span class="item-top " @click.prevent="click_item_top(item.item_id,item.top)" :title="item.top ? $t('cancel_item_top'):$t('item_top')" >
                            <i :class="item_top_class(item.top)"></i>
                          </span>

                           <p class="my-item" v-if="item.item_name.length<=10">{{item.item_name}}</p>
                           <p class="my-item" v-if="item.item_name.length>10">
                             <el-tooltip class="item" effect="dark" :content="item.item_name" placement="bottom">
                               <span>{{item.item_name}}</span>
                             </el-tooltip>
                           </p>
                           <span v-if="item.item_type==1" class="item-type-icon iconfont icon-MGL-R1"></span>
                           <span v-if="item.item_type==2" class="item-type-single-icon iconfont icon-MGL-R1-copy-copy"></span>
                         </router-link>
                       </div>
                  </el-tooltip>
                </li>

              <li class="itme-li-box"  >
                <el-tooltip :manual="true" effect="dark" :content="$t('never_create_item_tip')" placement="bottom-start" v-model="isNeverCreate">
                  <router-link class="thumbnail item-thumbnail"  to="/item/add" title="">
                    <p class="my-item">{{$t('new_item')}}<i class="el-icon-plus"></i></p>
                  </router-link>
                </el-tooltip>
              </li>

          </ul>
        </div>

    </el-container>

    <Footer> </Footer>

  </div>
</template>

<style lang="scss">
@import '~@/components/common/base.scss';
.el-dropdown-menu{
  min-width: 200px;
  background-color: $theme-third-color;
  box-shadow: 8px 8px 20px 0 rgba(55,99,170,.1), -8px -8px 20px 0 #fff;
  .el-dropdown-menu__item{
    color: $theme-grey-color;
    &:hover,&:focus{
      color: $theme-right-msg-color;
      background: $theme-words-color;
    }

  }
}
.el-popper[x-placement^=bottom] .popper__arrow,.el-popper[x-placement^=bottom] .popper__arrow::after{
  border-bottom-color: $theme-third-color;
}
.el-dropdown-menu__item--divided:before {
  background-color: $theme-third-color !important;
  height: 2px !important;
}
.el-popper[x-placement^=bottom] .popper__arrow::after {
  border-bottom-color: $theme-third-color;
}
</style>
<style scoped lang="scss">
@import '~@/components/common/base.scss';
    .user-name-title{
      font-size: 14px;
      font-weight: bold;
    }
    .user-info-icon{
      font-size: 18px;
      margin-right: 10px;
      color: $theme-grey-color;
    }
    .itme-li-box{
      text-align: center;
    }
   .itme-li-box span{
     color: $theme-btn-other-color;
     &:hover{
       color: $theme-right-msg-color;
     }
   }
  .my-item{
    margin-top: 2rem;
    @include text-auto-thumbnail;// 文字自动缩略
    width: 100%;
    color: $theme-btn-other-color;
    font-weight: bold;
    &:hover{
      color: $theme-right-msg-color;
    }
  }
  .item-use-icon{
    font-size: 26px;
    float: left;
    position: relative;
    top: -20%;
    color: #151822;
    opacity: .4;
    right: -44%;
    padding-top: 1rem;
  }
  .item-type-icon{
    font-size: 38px;
    float: left;
    position: relative;
    top: -82%;
    color: #151822;
    opacity: .1;
    left: -4%;
  }
  .item-type-single-icon{
    font-size: 38px;
    float: left;
    position: relative;
    top: -4%;
    color: #151822;
    opacity: .1;
    right: -84%;
  }
  .head-dropdown-user-msg{
    color: $theme-grey-color;
    line-height: 16px;
    cursor: default;
  }
  .home-top-menu-btn{
    padding: 0px 6px;
  }
  .outside-web-btn{
    padding: 0px 10px;
  }
  .custom-log-box{
    display: inline-flex;
  }
  .custom-logo-pic{
    height:36px;
    border-radius: 4px;
    object-fit: contain;
  }
  .custom-web-name{
    line-height: 36px;
    font-size: 18px;
    font-weight: bold;
    padding-left: 10px;
    color: $theme-grey-color;
  }
  .hello{
    min-height: 100vh;
  }
  .head-container{
    width: 100%;
    background-color: $theme-color;
  }
  .container-narrow{
    margin: 0 auto;
  }

  .masthead{
    width: 100%;
    margin-top: 30px;
    //padding-left: 25%;
    //padding-right: 20%;
  }

  .header-btn-group{
    margin-bottom: 15px;
    background-color: $theme-third-color;
  }
  .header-btn-group a{
    margin-left: 1rem;
  }
  .header-btn-group a,.header-btn-group .el-button,.header-btn-group .el-dropdown-link{
      color: $theme-grey-color;
  }
  .header-btn-group a:hover,.header-btn-group .el-button:hover,.header-btn-group .el-dropdown-link:hover{
      color: $theme-right-msg-color;
  }

  .head-dropdown-link-box{
     background-color: $theme-third-color;
     line-height: 28px;
     color: $theme-grey-color;
  }
  .head-dropdown-link-box:hover{
    line-height: 28px;
    color: $theme-right-msg-color;
    background-color:$theme-third-color;
    //font-weight: bold;
    //opacity:0.5;
  }
  .logo-title{
    margin-left: 0px;
  }

  .container-thumbnails{
    margin: 0 auto;
    max-width: 700px;
    height: 86vh;
  }
  .thumbnails{
    padding-top: 40px;
  }
  .thumbnails>li {
      float: left;
      margin-bottom: 20px;
      margin-left: 20px;
    }

  .thumbnails li a{
    height: 100px;
    width: 180px;
    @include content-card;
  }

  .thumbnail {
    display: block;
    padding: 4px;
    line-height: 20px;
    border: 1px solid #ddd;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.055);
    -moz-box-shadow: 0 1px 3px rgba(0,0,0,0.055);
    box-shadow: 0 1px 3px rgba(0,0,0,0.055);
    -webkit-transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -o-transition: all .2s ease-in-out;
    transition: all .2s ease-in-out;
    list-style: none;
  }

  .item-setting{
    float:right;
    margin-right:15px;
    margin-top:5px;
    display: none;
  }

  .item-top{
    float:right;
    margin-right:5px;
    margin-top:5px;
   display: none;
  }

  .thumbnails li a i{
    font-weight: bold;
    margin-left: 5px;
    color: rgb(21, 24, 34);
  }

</style>

<script>
import Diff from "../page/Diff";
import store from '@/store';
import Lang from '@/components/common/Lang';

if (typeof window !== 'undefined') {
  var $s = require('scriptjs');
}
export default {
  components: {Diff,Lang},
  data() {
    return {
      isNeverCreate:false,
      isFirstTimeCreate:false,
      isCustomWebTitle:false,
      logoPicUrl:'../../../static/logo/doc-log3.png',
      webTitleName:'',
      outsideBtnData:[],// 站长自定义跳转按钮
      currentDate: new Date(),
      itemList:{},
      isAdmin:false,
      pageLoading:false,
      isMobileDevice:false,
    };
  },
  created() {
    let customLogoData = localStorage.getItem('custom_logo_form');
    if(typeof customLogoData !=="undefined" && customLogoData){
      customLogoData = JSON.parse(customLogoData);
      this.webTitleName = customLogoData.title;
      this.logoPicUrl = customLogoData.logo;
      this.isCustomWebTitle = true;
    }
    let outsideBtnData = localStorage.getItem('outside_btn_form');
    if(typeof outsideBtnData !=="undefined" && outsideBtnData){
       let tempOutsideBtnData = JSON.parse(outsideBtnData);
       tempOutsideBtnData.map(data =>{
         if(data.name!=''&& data.url!=''){
           this.outsideBtnData.push(data);
         }
       });

    }
  },
  methods:{
    get_item_list(){
        var that = this ;
        that.pageLoading = true;
        var url = DocConfig.server+'/api/item/myList';
        var params = new URLSearchParams();

        that.axios.get(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              //that.$message.success("加载成功");
              setTimeout(function() {
                  var json = response.data.data ;
                  that.itemList = json ;
                  that.bind_item_even();
                  // 设置第一次添加项目标记
                  if(that.itemList.length==1&&that.itemList[0].last_update_time==0){
                     that.isFirstTimeCreate = true;
                  }
                  if(that.itemList.length==0){
                    that.isNeverCreate = true;
                  }
                  that.pageLoading = false;
              }, 300);
            }else{
              that.$alert(response.data.error_message);
            }

          });
    },
    feedback(){
      if (DocConfig.lang =='en') {
        window.open('https://gitee.com/xiayoumo/open-doc/issues');
      }else{
        var msg = "你正在使用免费开源版OpenDoc，如有问题或者建议，请到gitee提issue：";
        msg += "<a href='https://gitee.com/xiayoumo/open-doc/issues' target='_blank'>https://gitee.com/xiayoumo/open-doc/issues</a><br>";
        msg += "如果你觉得OpenDoc好用，不妨给开源项目点一个star。良好的关注度和参与度有助于开源项目的长远发展。";
        this.$alert(msg, {
            dangerouslyUseHTMLString: true
        });
      }

    },
    item_top_class(top){
      if (top) {
        return 'el-icon-arrow-down';
      };
      return 'el-icon-arrow-up';
    },

    bind_item_even(){

      //这里偷个懒，直接用jquery来操作DOM。因为老版本的代码就是基于jquery的，所以复制过来稍微改下
      $s(["../static/jquery.min.js"],()=>{

          //当鼠标放在项目上时将浮现设置和置顶图标
          $(".item-thumbnail").mouseover(function(){
            $(this).find(".item-setting").show();
            $(this).find(".item-top").show();
            $(this).find(".item-down").show();
          });

          //当鼠标离开项目上时将隐藏设置和置顶图标
          $(".item-thumbnail").mouseout(function(){
            $(this).find(".item-setting").hide();
            $(this).find(".item-top").hide();
            $(this).find(".item-down").hide();
          });
      });
    },

    //进入项目设置页
    click_item_setting(item_id){
       this.$router.push({path:'/item/setting/'+item_id});
    },
    click_item_top(item_id ,old_top){
      if (old_top) {
        var action = 'cancel';
      }else{
        var action = 'top';
      }
      var that = this ;
      var url = DocConfig.server+'/api/item/top';
      var params = new URLSearchParams();
      params.append('action', action);
      params.append('item_id', item_id);
      that.axios.post(url, params)
        .then(function (response) {
          if (response.data.error_code === 0 ) {
            that.get_item_list();
          }else{
            that.$alert(response.data.error_message);
          }

        });
    },
    goSetting(){
      this.$router.replace('/user/setting');
    },
    logout(){
        var that = this ;
        var url = DocConfig.server+'/api/user/logout';

        var params = new URLSearchParams();

        that.axios.get(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              localStorage.clear();
              that.$router.push({
                  path: '/show/index'
                });
            }else{
              that.$alert(response.data.error_message);
            }

          });
    },

    user_info(){
        var that = this ;
        this.get_user_info(function(response){
          if (response.data.error_code === 0 ) {
            if (response.data.data.groupid == 1 ) {
              that.isAdmin = true ;
              store.dispatch('SetIsAdmin', true);
            };
          }
        });

    },
    dropdown_callback(data){
      if (data) {
        data();
      };
    },
  },
  computed: {
    nowUserInfo(){
      return typeof store.getters.userInfo ==='undefined'?{username:''}:store.getters.userInfo;
    },
  },
  mounted () {
    this.get_item_list();
    this.user_info();
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice=true;
    }
  },
  beforeDestroy(){
    this.$message.closeAll();
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>
