<template>
  <div class="hello">
    <el-form  status-icon  label-width="100px" class="infoForm" v-model="infoForm">
      <el-form-item :label="$t('item_name')+':'" >
        <el-input type="text" auto-complete="off" v-model="infoForm.item_name" placeholder="" ></el-input>
      </el-form-item>

      <el-form-item :label="$t('item_description')+':'" >
        <el-input type="text" auto-complete="off" v-model="infoForm.item_description" :placeholder="$t('optional')" ></el-input>
      </el-form-item>

      <el-form-item :label="$t('visit_password')+':'">
        <el-input type="password" auto-complete="off" :placeholder="$t('visit_password_description')" v-model="infoForm.password"></el-input>
      </el-form-item>

       <el-form-item label="" >
        <el-button type="primary" style="width:100%;" @click="FormSubmit" >{{$t('submit')}}</el-button>
      </el-form-item>

    </el-form>
  </div>
</template>

<script>


export default {
  name: 'Login',
  props:{
    infoForm:{}
  },
  components : {

  },
  data () {
    return {
      // infoForm:{
      //
      // }
    }

  },
  methods: {
      FormSubmit() {
          var that = this ;
          var url = DocConfig.server+'/api/item/update';

          var params = new URLSearchParams();
          params.append('item_id',  that.$route.params.item_id);
          params.append('item_name', this.infoForm.item_name);
          params.append('item_description', this.infoForm.item_description);
          params.append('item_domain', this.infoForm.item_domain);
          params.append('password', this.infoForm.password);

          that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                that.$message.success(that.$t("modify_success"));
              }else{
                that.$alert(response.data.error_message);
              }

            })
            .catch(function (error) {
              console.log(error);
            });
      },
  },

  mounted(){
    //this.get_item_info();
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.center-card a {
  font-size: 12px;
}

.center-card{
  text-align: center;
  width: 600px;
  height: 500px;
}

.infoForm{
  min-width:350px;
  padding: 4rem;
}

.goback-btn{
  z-index: 999;
  float: right;
}
</style>
