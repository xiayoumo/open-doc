<template>
    <div class="common-header">
      <el-icon v-if="showHeadMenu" @click="handleShowLeftMenu()" class="head-menu-btn"><Menu /></el-icon>
      <span v-if="headTitle!=''&&!isMobileDevice" class="coommon-header-title">{{ headTitle }}</span>
      <div class="header-right-menu-box">
          <Lang></Lang>
          <el-button text v-if="showHeadCountMenu && !isNowCountDashboard" class="hover-word-btn" @click="$router.replace('/item-count-show/'+ nowItemCode)">
            <span class="iconfont icon-24gf-chartPie word-btn-default"></span><span class="word-btn-content">{{$t('web_dashboard')}}</span>
          </el-button>

          <el-button text v-if="isNowCountDashboard" class="hover-word-btn" @click="gobackUrl">
            <span class="iconfont icon-fanhui1 word-btn-default"></span><span class="word-btn-content">{{$t('goback')}}</span>
          </el-button>
          <el-button text class="hover-word-btn" @click="$router.replace('/item/index')">
            <el-icon class="word-btn-default"><HomeFilled /></el-icon><span class="word-btn-content">{{$t('web_home')}}</span>
          </el-button>
      </div>

    </div>
</template>

<script>
import store from '@/store';
import Lang from '@/components/common/Lang'

export default {
  name: 'Header',
  components:{
    Lang
  },
  data () {
    return {
      isMobileDevice:false,
      msg: '头部',
    }
  },
  created() {
  },
  methods: {
    handleShowLeftMenu(){
      store.dispatch('SetShowLeftMenu', !store.getters.showLeftMenu);
    },
    gobackUrl(){
      this.$router.go(-1);
    }
  },
  computed: {
    headTitle () {
      return store.getters.nowHeadTitle;
    },
    showHeadMenu(){
      return store.getters.showHeadMenu;
    },
    showHeadCountMenu(){
      return store.getters.showHeadCountMenu;
    },
    nowItemCode(){
      return store.getters.nowItemCode;
    },
    isNowCountDashboard(){
      return store.getters.isShowDashboard;
    }

  },
  mounted() {
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice=true;
    }
  },
  // computed: {
  //   headTitle() {
  //     return localStorage.getItem('head_title')
  //   },
  // },
  // watch: {
  //   showHeadTitle(newData) {
  //     this.headTitle = localStorage.getItem('head_title')
  //   }
  // },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/assets/base.scss';

/* Float Shadow */
.hover-word-btn {
  margin-top: 10px;
  margin-right: 10px;
  margin-left: 10px;
  .word-btn-default{
    font-size: 16px;
    color: $theme-grey-color;
  }
  @include hover-word-btn;
}

.goback-btn{
  color: $theme-grey-color;
  &:hover,&:focus{
    color: $theme-right-msg-color;
  }
}

.dashboard-count-btn{
  color: $theme-grey-color;
  &:hover,&:focus{
    color: $theme-right-msg-color;
  }
}
.head-menu-btn{
  font-size: 24px;
  //margin-right: 10px;
  margin-top: 22px;
}
.header-right-menu-box{
  color: $theme-grey-color;
  float: right;
  margin-right: 5%;
  margin-top: 22px;
  &:hover,&:focus{
    color: $theme-right-msg-color;
  }
}
.header-right-menu-btn{
  margin: 5px;
  border: none !important;
  color: $theme-grey-color !important;
  &:hover,&:focus{
    border: none !important;
    color: $theme-right-msg-color !important;
  }
}
.goback-to-index-btn{
  color: $theme-grey-color;
  &:hover,&:focus{
    color: $theme-right-msg-color;
  }
}
.coommon-header-title{
  color: $theme-grey-color;
  font-size: 20px;
  line-height: 60px;
  font-weight: bold;
}
.common-header{
  width: 100%;
  padding-left: 2%;
  padding-right: 2%;
  min-height: 60px;
  height: 5rem;
  color: $theme-grey-color;
  background: $theme-color;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 999;
  transition: all 0.3s ease;
}
</style>

<style lang="scss">
@import '~@/assets/base.scss';

.el-descriptions__header{
  margin-bottom: 0px;
}
.el-input__wrapper{
  background: $theme-input-color;
}
.el-input.is-disabled .el-input__wrapper {
  background-color: $theme-input-color;
}
.text-center{
  text-align: center;
}
body{
  margin: auto;
}
a{
  text-decoration: none;
}
ul{
  list-style-type: none;
}
.pull-right{
  float: right;
}
.edit-model-box-empty{
  //margin-top: 80px;
  height: 95vh;
  margin: 0 auto;
  padding-bottom: 40vh;
}

.el-input .el-input__inner{
  height: 30px;
  @include input-box;
}
.el-select .el-input__wrapper{
  @include input-box;
}
.el-form-item__content .el-input__inner{
  @include input-box;
}

.el-radio__input.is-checked .el-radio__inner {
  border-color: $theme-color;
  background: $theme-color;
}
.el-radio__input.is-checked+.el-radio__label {
  color: $theme-color;
}
a {
  color: $theme-color;
}
.el-button--text{
  color: $theme-color;
  &:hover,&:visited,&:focus,&:link{
    color: $theme-right-msg-color;
  }
}
.el-checkbox__label,.el-checkbox__inner{
  color: $theme-color;
  &:hover,&:visited,&:focus,&:link,&:active{
    color: $theme-color;
    border-color: $theme-color;
  }
}
.el-checkbox__input.is-focus .el-checkbox__inner{
  border-color: $theme-color;
}
.el-checkbox__input.is-checked .el-checkbox__inner, .el-checkbox__input.is-indeterminate .el-checkbox__inner {
  background-color: $theme-fourth-color;
  border-color: $theme-fourth-color;
}
.el-checkbox__input.is-checked+.el-checkbox__label{
  color: $theme-fourth-color;
}
.el-button--default,.el-button--small,.el-button--primany,.el-button{
  @include yes-btn;
}
.el-button.is-text:hover {
  color: $theme-right-msg-color;
}
.el-table tr,.el-table__body-wrapper{
  background: $theme-grey-color;
}
.el-table__body-wrapper{
  @include scroll-bar-box;
}
.el-button--primary{
  @include yes-full-btn;
}
.el-dialog{
  background: $theme-grey-color;
}
.el-dialog__close{
  @include close-btn;
}
.el-tabs__item{
   color: $theme-color;
   &:hover,&:focus{
     color: $theme-right-msg-color;
   }
}
.el-tabs__item.is-active {
  color: $theme-right-msg-color;
}
.el-tabs__active-bar{
  background: $theme-right-msg-color;
}
.icon-xinjianwenjianjia{
  margin: 0px 10px;
  font-size: 20px;
}
.el-menu-item.is-active {
  color: $theme-right-msg-color !important;
}
.el-menu-item.is-active i,.el-menu-item i{
  font-size: 14px !important;
}
.el-submenu.is-active .el-submenu__title {
  border-bottom-color: $theme-right-msg-color !important;
  font-weight: bold;
}
.el-submenu__title{
  font-weight: bold;
}
.goback-full-btn{
  margin-left: 1rem;
  @include no-btn;
}
.el-dropdown{
  line-height: 24px;
  border: none;
  &:hover,&:focus,&:visited{
    border: none !important;
  }
}
.el-dropdown-link {
  color: $theme-color;
  border: none;
  &:hover,&:focus,&:visited{
    border: none !important;
  }
}
.el-select-dropdown{
  background-color: $theme-third-color;
  border: 1px solid $theme-black-grey-color;
}
.el-select-dropdown__item.hover, .el-select-dropdown__item:hover {
  background-color: $theme-third-color;
  color: $theme-grey-color;
}
.el-dropdown-menu{
  background-color: $theme-third-color !important;
  box-shadow: 8px 8px 20px 0 rgba(55,99,170,.1), -8px -8px 20px 0 #fff;
  .el-dropdown-menu__item{
      color: $theme-grey-color;
      &:hover,&:focus{
        color: $theme-right-msg-color;
        background-color: $theme-words-color;
        a{
          background-color: $theme-words-color;
        }
      }
  }
}
.el-popper[x-placement^=top] .popper__arrow,.el-popper[x-placement^=top] .popper__arrow::after{
  border-top-color: $theme-third-color !important;
}
.el-popper[x-placement^=bottom] .popper__arrow,.el-popper[x-placement^=bottom] .popper__arrow::after{
  border-bottom-color: $theme-third-color !important;
}
.el-popper[x-placement^=right] .popper__arrow,.el-popper[x-placement^=right] .popper__arrow::after {
  border-right-color: $theme-third-color !important;
}
.el-message-box{
  background: $theme-grey-color;
  width: 40vh;
}
.el-message {
  background-color: $theme-secondary-color;
  border-color: $theme-secondary-color;
  .el-message__content{
    color: $theme-grey-color;
  }
}
.el-message--success,.el-message--info {
  .el-message__content,.el-message__icon{
    color: $theme-fourth-color;
  }
}
.el-message--error {
  .el-message__content,.el-message__icon{
    color: $theme-right-msg-color;
  }
}
.el-autocomplete-suggestion{
  background: $theme-color;
}
.el-autocomplete-suggestion li{
  color: $theme-grey-color;
  &:hover{
    color: $theme-right-msg-color;
    background: $theme-words-color;
  }
}
.el-table th.el-table__cell{
  background-color: $theme-color;
}
.el-aside{
  padding-bottom: 100px;
}
.el-descriptions__body{
  background: $theme-grey-color;
}
.el-popover{
  background: $theme-third-color;
  color: $theme-grey-color;
  .el-button{
    color: $theme-grey-color;
  }
}
.el-divider {
  background-color: $theme-fourth-color;
}
.el-divider__text {
  background-color: $theme-third-color;
  color: $theme-grey-color;
}
.el-descriptions__header .el-descriptions__title {
  font-size: 18px;
  font-weight: 700;
}
//.el-menu-item{
//  @include text-auto-thumbnail;// 文字自动缩略
//  width: 300px;
//  color: $theme-color;
//  font-weight: bold;
//}

</style>
