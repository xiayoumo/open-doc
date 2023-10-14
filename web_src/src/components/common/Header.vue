<template>
    <div class="common-header">
      <i v-if="showHeadMenu" @click="handleShowLeftMenu()" class="el-icon-menu head-menu-btn"></i>
      <span v-if="headTitle!=''" class="coommon-header-title">{{ headTitle }}</span>
      <router-link class="goback-to-index-btn" to="/item/index">{{$t('web_home')}}</router-link>
<!--      <a v-if="showHeadCountMenu && !isNowCountDashboard" :href="'/item-count-show/'+ nowItemCode"  class="dashboard-count-btn"><span class="iconfont icon-24gf-chartPie"></span> {{$t('web_dashboard')}}</a>-->
      <router-link v-if="showHeadCountMenu && !isNowCountDashboard" class="dashboard-count-btn" :to="'/item-count-show/'+ nowItemCode" append><span class="iconfont icon-24gf-chartPie"></span> {{$t('web_dashboard')}}</router-link>
      <el-button v-if="isNowCountDashboard" class="goback-btn" type="text" @click="gobackUrl">{{$t('goback')}}</el-button>

    </div>
</template>

<script>
import store from '@/store'

export default {
  name: 'Header',
  data () {
    return {
      msg: '头部',
    }
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

  }
  // mounted() {
  //   this.headTitle = store.getters.nowHeadTitle;
  // },
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
@import '~@/components/common/base.scss';
.goback-btn{
  color: $theme-grey-color;
  float: right;
  margin-right: 20px;
  margin-top: 13px;
  &:hover,&:focus{
    color: $theme-right-msg-color;
  }
}
.dashboard-count-btn{
  color: $theme-grey-color;
  float: right;
  margin-right: 20px;
  margin-top: 20px;
  &:hover,&:focus{
    color: $theme-right-msg-color;
  }
}
.head-menu-btn{
  font-size: 24px;
  margin-right: 10px;
  margin-top: 22px;
}
.goback-to-index-btn{
  color: $theme-grey-color;
  float: right;
  margin-right: 5%;
  margin-top: 22px;
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
  height: 60px;
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
@import '~@/components/common/base.scss';


.el-input .el-input__inner{
  height: 30px;
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
.el-button--default,.el-button--medium{
  @include yes-btn;
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
  @include no-btn;
}

.el-dropdown-link {
  color: $theme-color;
}
.el-dropdown-menu{
  background-color: $theme-third-color;
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

.el-popper[x-placement^=bottom] .popper__arrow,.el-popper[x-placement^=bottom] .popper__arrow::after{
  border-bottom-color: $theme-third-color;
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
}
.el-table th.el-table__cell{
  background-color: $theme-color;
}
</style>
