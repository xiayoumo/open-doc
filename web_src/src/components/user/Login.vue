<template>
  <div class="hello">
    <Header> </Header>

    <el-container class="login-box">
          <el-card class="center-card">
            <el-form  status-icon  label-width="0px" class="demo-ruleForm" @keyup.enter.native="onSubmit">
              <h2>{{$t("login")}}</h2>
              <el-form-item label="" >
                <el-input type="text" auto-complete="off" :placeholder="$t('username_description')" v-model="username"></el-input>
              </el-form-item>

              <el-form-item label="" >
                <el-input type="password" auto-complete="off" v-model="password" :placeholder="$t('password')"></el-input>
              </el-form-item>

              <el-form-item label="" v-if="show_v_code">
                <el-input type="text" auto-complete="off" v-model="v_code" :placeholder="$t('verification_code')"></el-input>
                <img v-bind:src="v_code_img"  class="v_code_img" v-on:click="change_v_code_img" >

              </el-form-item>

               <el-form-item label="" >
                 <el-button type="primary" style="width:100%;" @click="onSubmit" :title="$t('login')" ><span class="iconfont icon-denglu login-btn"></span></el-button>
              </el-form-item>

              <el-form-item label="" >
                <el-button type="text" @click="$router.replace('/user/register')" ><span class="iconfont icon-a-2rt53k6cjnn49zfu-kewj4lq2 register-pic-background"></span> {{$t("register_new_account")}}</el-button>
              </el-form-item>
            </el-form>
          </el-card>
    </el-container>

    <Footer> </Footer>

  </div>
</template>

<script>
import store from '@/store';

export default {
  name: 'Login',
  components : {

  },
  data () {
    return {
      username: '',
      password: '',
      v_code: '',
      v_code_img:DocConfig.server+'/api/common/verify',
      show_v_code:false,
      is_show_alert:false
    }

  },
  methods: {
      onSubmit() {
          if (this.is_show_alert) { return ;};
          //this.$message.success(this.username);
          var that = this ;
          var url = DocConfig.server+'/api/user/login';
          var params = new URLSearchParams();
          params.append('username', this.username);
          params.append('password', this.password);
          params.append('v_code', this.v_code);

          that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                // 设置站点信息
                let returnObject = response.data.data;
                if(returnObject.hasOwnProperty('custom_logo_form')){
                  localStorage.setItem('custom_logo_form',returnObject.custom_logo_form);
                }
                if(returnObject.hasOwnProperty('outside_btn_form')) {
                  localStorage.setItem('outside_btn_form', returnObject.outside_btn_form);
                }
                //that.$message.success("登录成功");
                let redirect = decodeURIComponent(that.$route.query.redirect || '/item/index');
                that.$router.replace({
                  path: redirect
                });
              }else{
                if (response.data.error_code === 10206 || response.data.error_code === 10210) {
                  that.show_v_code = true ;
                  that.change_v_code_img() ;
                };
                that.is_show_alert = true ;
                that.$alert(response.data.error_message,{callback:function(){
                 setTimeout(function(){
                    that.is_show_alert = false;
                 },500);

                }});
              }

            });
      },
      change_v_code_img(){
        var rand = '&rand='+Math.random();
        this.v_code_img += rand ;
      },
      script_cron(){
        var url = DocConfig.server+'/api/ScriptCron/run';
        this.axios.get(url);
      }

  },
  mounted() {
    var that = this ;
    /*给body添加类，设置背景色*/
    document.getElementsByTagName("body")[0].className="grey-bg";
    this.get_user_info(function(response){
      if (response.data.error_code === 0 ) {
        let redirect = decodeURIComponent(that.$route.query.redirect || '/item/index');
        that.$router.replace({
          path: redirect
        });
      }
    });

    this.script_cron();
  },
  beforeDestroy(){
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>

<style lang="scss">
@import '~@/components/common/base.scss';

</style>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
.login-btn{
  font-size: 28px;
}
.register-pic-background{
  font-size: 20px;
  position: relative;
  color: #151822;
  opacity: .4;
  &:hover{
    color: $theme-right-msg-color;
  }
}
.login-box{
  padding-top: 4rem;
}
.center-card a {
  font-size: 12px;
}

.center-card{
  text-align: center;
}

.v_code_img{
  margin-top: 20px;
}

</style>
