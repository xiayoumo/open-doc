<template>
  <div class="hello">
    <Header> </Header>

    <el-container>
          <el-card class="center-card">
          <template>
            <el-button type="text" @click="goback" class="goback-btn " >{{$t('goback')}}</el-button>
            <el-tabs  value="first" tab-position="left">
              <el-tab-pane :label="$t('base_info')" name="first">
                  <Info :infoForm="infoForm"> </Info>
              </el-tab-pane>
              <el-tab-pane :label="$t('member_manage')" name="second">
                  <Member> </Member>
              </el-tab-pane>
              <el-tab-pane :label="$t('advance_setting')" name="third">
                  <Advanced> </Advanced>
              </el-tab-pane>
              <el-tab-pane v-if="infoForm.item_use=='api'" :label="$t('open_api')" name="four">
                    <OpenApi> </OpenApi>
              </el-tab-pane>
            </el-tabs>
          </template>
          </el-card>
    </el-container>

    <Footer> </Footer>

  </div>
</template>

<script>

import Info from '@/components/item/setting/Info'
import Member from '@/components/item/setting/Member'
import Advanced from '@/components/item/setting/Advanced'
import OpenApi from '@/components/item/setting/OpenApi'
export default {
  name: 'Login',
  components : {
    Info,
    Member,
    Advanced,
    OpenApi
  },
  data () {
    return {
      userInfo:{},
      infoForm:{},
    }

  },
  methods: {

      get_item_info(){
        var that = this ;
        var url = DocConfig.server+'/api/item/detail';
        var params = new URLSearchParams();
        params.append('item_id',  that.$route.params.item_id);
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              var Info = response.data.data
              that.infoForm =  Info;
            }else{
              that.$alert(response.data.error_message);
            }

          })
          .catch(function (error) {
            console.log(error);
          });
      },
      goback(){
        this.$router.go(-1);
      }

  },

  mounted(){
    this.get_item_info()
  },
  beforeCreate() {
    /*给body添加类，设置背景色*/
    document.getElementsByTagName("body")[0].className="grey-bg";
  },
  beforeDestroy(){
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>


<style lang="scss">
@import '~@/components/common/base.scss';


.center-card .el-card__body,.center-card .el-tabs__content{
  height: 70vh;
  overflow-x: auto;
}

</style>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';


.center-card a {
  font-size: 12px;
}

.center-card{
  text-align: center;
  min-width: 600px;
  width: 50%;
  min-height: 500px;
  max-height: 700px;
  background: $theme-grey-color;
  overflow-x: auto;
  @include scroll-bar-box;
}
.infoForm{
  max-width: 350px;
  width: 100%;
  margin-left: 2%;
  margin-top: 5%;
}

.goback-btn{
  z-index: 999;
  float: right;
}
</style>
