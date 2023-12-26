//全局函数/变量
import store from '@/store';
import axios from '@/js/http'
// import { useStore } from 'vuex'
// const store = useStore()  只能在setup()函数中使用
export default{
  install(Vue,options)
  {
    //Vue.prototype.getData = function () {
    Vue.config.globalProperties.getData = function () {
      console.log('我是插件中的方法');
    }

    //Vue.prototype.DocConfig = {
     // "server":'http://127.0.0.1/opendoc.cc/server/index.php?s=',
      //"server":'../server/index.php?s=',
    //}
         // .config.globalProperties
    Vue.config.globalProperties.request = function(){

    }

    Vue.config.globalProperties.getRootPath = function(){
       // return window.location.protocol +'//' +window.location.host + window.location.pathname
        return window.location.protocol +'//' +window.location.host
    }

    /*判断是否是移动设备*/
    Vue.config.globalProperties.isMobile = function (){
      return navigator.userAgent.match(/iPhone|iPad|iPod|Android|android|BlackBerry|IEMobile/i) ? true : false;
    }

    Vue.config.globalProperties.get_user_info = function(callback){

        const url = DocConfig.server+'/api/user/info';
        const params = new URLSearchParams();
        params.append('redirect_login', false);
        axios.post(url, params)
          .then(function (response) {

            localStorage.setItem('user_info',JSON.stringify(response.data.data));
            store.dispatch('SetUserInfo', response.data.data);
            if (callback) {callback(response);}
          });
    }

    Vue.config.globalProperties.get_notice = function(callback){
        const that = this ;
        const url = DocConfig.server+'/api/notice/getList';
        const params = new URLSearchParams();
        params.append('notice_type', 'unread');
        params.append('count', '1');
        that.axios.post(url, params)
          .then(function (response) {
            if (callback) {callback(response);}
          });
    }

  }
}
