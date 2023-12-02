<template>

<div>
      <el-form ref="form" :model="form" label-width="150px">

      </el-form-item>

      <el-form-item :label="$t('register_open_label')">
        <el-switch v-model="form.register_open"></el-switch>
      </el-form-item>
      <!-- 待支持
      <el-form-item label="所有人可以新建项目">
        <el-switch v-model="form.register_open"></el-switch>
      </el-form-item>

      <el-form-item label="网站首页设置为">
          <el-select v-model="form.home_page" placeholder="请选择">
            <el-option label="全屏介绍页" value="1"></el-option>
            <el-option label="展示全站项目" value="2"></el-option>
          </el-select>
      </el-form-item>
      -->
      <el-form-item :label="$t('ldap_open_label')">
        <el-switch v-model="form.ldap_open"></el-switch>
      </el-form-item>

      <div v-if="form.ldap_open" style="margin-left: 4%;" >
        <el-form-item label="ldap host">
           <el-input v-model="form.ldap_form.host"   class="form-el"></el-input>
        </el-form-item>
        <el-form-item label="ldap port">
          <el-input v-model="form.ldap_form.port"  style="width:90px"></el-input>
        </el-form-item>
        <el-form-item label="ldap base dn ">
          <el-input v-model="form.ldap_form.base_dn"  class="form-el" placeholder="例如 dc=OpenDoc,dc=com"></el-input>
        </el-form-item>
        <el-form-item label="ldap bind dn ">
          <el-input v-model="form.ldap_form.bind_dn"  class="form-el" placeholder="cn=admin,dc=OpenDoc,dc=com"></el-input>
        </el-form-item>
        <el-form-item label="ldap bind password ">
          <el-input v-model="form.ldap_form.bind_password"  class="form-el" placeholder="例如 123456"></el-input>
        </el-form-item>
        <el-form-item label="ldap version">
            <el-select v-model="form.ldap_form.version"  class="form-el">
              <el-option label="3" value="3"></el-option>
              <el-option label="2" value="2"></el-option>
            </el-select>
        </el-form-item>



        <!--
        <el-form-item label="作为用户名的属性">
          <el-input v-model="form.ldap_form.uid_field" class="form-el" placeholder="例如 sAMAccountName"></el-input>
        </el-form-item>
        -->
      </div>


        <el-form-item :label="$t('outside_btn_open_label')">
          <el-switch v-model="form.outside_btn_open"></el-switch>
        </el-form-item>
        <div v-if="form.outside_btn_open" style="margin-left: 4%;" >
          <el-form :inline="true" style="margin-left: 4%;">
            <el-form-item label="名称">
              <el-input style="width:90px" v-model="form.outside_btn_form[0].name"  class="form-el"></el-input>
            </el-form-item>
            <el-form-item label="url">
              <el-input v-model="form.outside_btn_form[0].url"  class="form-el"></el-input>
            </el-form-item>
          </el-form>
          <el-form :inline="true" style="margin-left: 4%;">
            <el-form-item label="名称">
              <el-input style="width:90px" v-model="form.outside_btn_form[1].name" class="form-el"></el-input>
            </el-form-item>
            <el-form-item label="url">
              <el-input v-model="form.outside_btn_form[1].url"  class="form-el"></el-input>
            </el-form-item>
          </el-form>
        </div>

      <el-form-item :label="$t('custom_logo_open_label')">
        <el-switch v-model="form.custom_logo_open"></el-switch>
      </el-form-item>
      <div v-if="form.custom_logo_open" style="margin-left: 4%;" >
        <el-form-item label="站点名称">
          <el-input v-model="form.custom_logo_form.title"   class="form-el"></el-input>
        </el-form-item>
        <el-form-item label="站点Logo">
          <el-upload
            class="upload-logo-pic"
            :limit="1"
            :action="uploadLogoUrl"
            :file-list="fileList"
            :on-remove="removeLogoPic"
            :on-exceed="overLogoPicLimit"
            :on-success="uploadLogoPicSuccess"
            list-type="picture">
            <el-button size="small" type="primary">点击上传</el-button>
            <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
          </el-upload>
<!--          <el-input v-model="form.custom_logo_form.logo"  class="form-el"></el-input>-->
        </el-form-item>
      </div>

      <el-form-item >
        <el-button class="save-setting-btn" type="primary" @click="onSubmit">{{$t('save')}}</el-button>
      </el-form-item>
    </el-form>
</div>

</template>

<style scoped lang="scss">
@import '~@/components/common/base.scss';
  .save-setting-btn{
    margin-top: 40px;
    padding: 10px 6%;
  }
  .form-el{
    width: 230px;
  }
  .upload-logo-pic{
    width: 15%;
  }

</style>

<script>

export default {
  data() {
    return {
      uploadLogoUrl:DocConfig.server+'/api/adminSetting/uploadLogoImg',
      fileList: [],//[{name: 'food.jpeg', url: ''}]
      form:{
        register_open:true,
        ldap_open:false,
        home_page:'1',
        ldap_form:{
          "host":'',
          "port":'389',
          "version":"3",
          "base_dn":'',
          "bind_dn":'',
          "bind_password":'',
          "uid_field":'sAMAccountName',
        },
        custom_logo_open:false,
        custom_logo_form:{"title":'',"logo":''},
        outside_btn_open:false,
        outside_btn_form:[{"name":'',"url":''},{"name":'',"url":''}],
      }
    };
  },
  methods:{
    overLogoPicLimit(files, fileList) {
      this.$alert(this.$t("pic_upload_over_limit"));
    },
    removeLogoPic(file, fileList){
      this.form.custom_logo_form.logo = '';
      return true;
    },
    uploadLogoPicSuccess(response, file, fileList){
      this.form.custom_logo_form.logo = response.url;
    },
    _getfileNameByUrl(url=''){
        let filename ='';
        if (url.indexOf('/') > 0) {
          if (url.indexOf('?') > 0) {
            url = url.split('?')[0];
          }
          //如果包含有"/"号 从最后一个"/"号+1的位置开始截取字符串
          filename = url.substring(
            url.lastIndexOf('/') + 1,
            url.length
          );
        }
      return filename;
    },
    onSubmit(){
      var url = DocConfig.server+'/api/adminSetting/saveConfig';
      this.axios.post(url, this.form)
        .then( (response) =>{
            if (response.data.error_code === 0 ) {
                this.$alert(this.$t("setting_save_success"));
            }else{
              this.$alert(response.data.error_message);
            }
        });
    },
    loadConfig(){
      var url = DocConfig.server+'/api/adminSetting/loadConfig';
      this.axios.post(url, this.form)
        .then( (response) =>{
          if (response.data.error_code === 0 ) {
            if (response.data.data.length === 0) {
              return ;
            };
            let settingObject = response.data.data;
            this.form.register_open =   settingObject.register_open > 0 ? true :false ;
            this.form.ldap_open =   settingObject.ldap_open > 0 ? true :false ;
            this.form.ldap_form =   settingObject.ldap_form ? settingObject.ldap_form : this.form.ldap_form ;
            this.form.custom_logo_open =   settingObject.custom_logo_open > 0 ? true :false ;
            this.form.custom_logo_form =   settingObject.custom_logo_form ? settingObject.custom_logo_form : this.form.custom_logo_form ;
            this.form.outside_btn_open =   settingObject.outside_btn_open > 0 ? true :false ;
            this.form.outside_btn_form =   settingObject.outside_btn_form ? settingObject.outside_btn_form : this.form.outside_btn_form ;
            if(settingObject.custom_logo_form.logo!=''){
              let logoPicName = this._getfileNameByUrl(settingObject.custom_logo_form.logo);
              this.fileList.push({name:logoPicName,url:settingObject.custom_logo_form.logo});
            }

            this.$emit("closeLoading",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据


          }else{
            this.$alert(response.data.error_message);
          }
        });
    }

  },
  mounted () {
    this.loadConfig();
  },
  beforeDestroy(){
    this.$message.closeAll();
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>
