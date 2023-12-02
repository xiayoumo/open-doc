<template>
  <div class="hello">
    <Header> </Header>

    <el-container>
          <el-card class="center-card">
            <el-form :rules="rules" ref="ruleForm" :model="itemForm"  status-icon  label-width="0px" class="demo-ruleForm">
              <h2></h2>
              <el-form-item prop="item_use">
                <el-select  v-model="itemForm.item_use" :placeholder="$t('item_use')" @change="itemUseSelect">
                  <el-option key="doc" :label="$t('item_use1')" value="doc">
                    <span> {{ $t('item_use1') }}</span>
                    <span  class="iconfont icon-word1 select-right-icon"></span>
                  </el-option>
                  <el-option key="api" :label="$t('item_use2')" value="api">
                    <span> {{ $t('item_use2') }}</span>
                    <span  class="iconfont icon-APIwendang select-right-icon"></span>
                  </el-option>
                  <el-option key="excel" :label="$t('item_use3')" value="excel">
                    <span> {{ $t('item_use3') }}</span>
                    <span  class="iconfont icon-excel select-right-icon"></span>
                  </el-option>
                </el-select>
                <el-popover
                  placement="bottom"
                  width="auto"
                  v-model="itemUseVisible"
                  trigger="manual">
                  <el-divider content-position="left">{{$t('item_use1')}}</el-divider>
                  <p>{{ $t('item_use_doc_info') }}</p>
                  <el-divider content-position="left">{{$t('item_use2')}}</el-divider>
                  <p>{{ $t('item_use_api_info') }}</p>
                  <el-divider content-position="left">{{$t('item_use3')}}</el-divider>
                  <p>{{ $t('item_use_excel_info') }}</p>
                  <div style="text-align: right; margin: 0">
                    <el-button type="primary" size="mini" @click="itemUseVisible = false">{{ $t('confirm') }}</el-button>
                  </div>
                  <i slot="reference" class="el-icon-question question-btn" @click="itemUseVisible = true"></i>
                </el-popover>
              </el-form-item>

              <transition name="el-zoom-in-center">
                  <el-form-item v-show="itemForm.item_use" prop="item_type">
                    <el-radio v-model="itemForm.item_type" label="1">{{$t('item_type1')}}</el-radio>
                    <el-radio v-model="itemForm.item_type" label="2">{{$t('item_type2')}}</el-radio>
                    <el-popover
                      placement="bottom"
                      width="200"
                      v-model="itemTypeVisible"
                      trigger="click">
                      <el-divider content-position="left">{{$t('item_type1')}}</el-divider>
                      <p>{{ $t('item_type_regular_info') }}</p>
                      <el-divider content-position="left">{{$t('item_type2')}}</el-divider>
                      <p>{{ $t('item_type_single_info') }}</p>
                      <div style="text-align: right; margin: 0">
                        <el-button type="primary" size="mini" @click="itemTypeVisible = false">{{ $t('confirm') }}</el-button>
                      </div>
                      <i slot="reference" class="el-icon-question question-btn"></i>
                    </el-popover>
                  </el-form-item>
              </transition>

              <transition name="el-zoom-in-center">
                <div  v-show="itemForm.item_type">
                  <el-form-item label=" " prop="item_name">
                    <el-input type="text" class="request-input" auto-complete="off" :placeholder="$t('item_name')" v-model="itemForm.item_name"></el-input>
                  </el-form-item>

                  <el-form-item label="　" prop="item_description">
                    <el-input type="text" class="request-input" auto-complete="off" :placeholder="$t('item_description')" v-model="itemForm.item_description"></el-input>
                  </el-form-item>


                  <el-form-item label="　"  prop="password">
                    <el-input type="text" class="request-input" auto-complete="off" v-model="itemForm.password"  :placeholder="$t('visit_password_placeholder')"></el-input>
                  </el-form-item>
                </div>
              </transition>

              <transition name="el-zoom-in-top">
                <div  v-show="itemForm.item_name.length>=3">
                    <el-form-item v-if="copyItemList.length>0" label="" class="text-left" prop="copy_item_id">
                       <el-checkbox v-model="show_copy">{{$t('copy_exists_item')}}</el-checkbox>
                       <el-select v-model="itemForm.copy_item_id" :placeholder="$t('please_choose')" v-if="show_copy" @change="choose_copy_item">
                          <el-option
                            v-for="item in copyItemList"
                            :key="item.item_id"
                            :label="item.item_name"
                            :value="item.item_id">
                            <span> {{ item.item_name }}</span>
                            <span v-if="item.item_use=='api'"  class="iconfont icon-APIwendang select-right-icon"></span>
                            <span v-if="item.item_use=='excel'"  class="iconfont icon-excel select-right-icon"></span>
                            <span v-if="item.item_use=='doc'" class="iconfont icon-word1 select-right-icon"></span>
                          </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item  v-if="itemList.length>0&&itemForm.item_use=='api'"  label=""  style="text-align: left;margin-bottom:5px;margin-left:15px;margin-top:-25px;">
                        <el-button type="text" @click="auto_doc">{{ $t('auto_produce_doc') }}</el-button>
                        &nbsp;&nbsp;&nbsp;
                    </el-form-item>

                     <el-form-item label="" >
                      <el-button type="primary" class="add-new-object-btn" style="width:100%;" @click="onSubmit" >{{$t('submit')}}</el-button>
                    </el-form-item>
                </div>
              </transition>
            </el-form>
          </el-card>
    </el-container>

    <Footer> </Footer>

  </div>
</template>

<script>
import store from '@/store'
export default {
  name: 'Login',
  components : {
  },
  created() {
    store.dispatch('SetNowHeadTitle','');
  },
  data () {
    return {
      itemUseVisible:false,
      itemTypeVisible:false,
      homeUrl:DocConfig.homeUrl,
      show_copy:false,
      itemList:[],
      copyItemList:[],
      itemForm:{
        item_type:'',// 1/2/3
        item_name:'',
        item_domain: '',
        item_description:'',
        password:'',
        copy_item_id:'',
        item_use:'',// api/doc/excel
      },
      rules: {
        item_name: [
          { required: true, message: '请输入项目名称', trigger: 'blur' },
          { min: 3, max: 50, message: '长度在 3 到 50 个字符', trigger: 'blur' }
        ]
      }

    }

  },
  methods: {
    itemUseSelect(itemUse){
      this.copyItemList = [];
      this.itemList.map(item=>{
        if(item.item_use==itemUse){
          this.copyItemList.push(item);
        }
      });
      // 清空已选中的内容
      this.show_copy = false;
      this.itemForm.copy_item_id = '';
    },
    get_item_list(){
        var that = this ;
        var url = DocConfig.server+'/api/item/myList';

        var params = new URLSearchParams();

        that.axios.get(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              //that.$message.success("加载成功");
              var json = response.data.data ;
              that.itemList = json ;
              if(that.itemList.length==0){
                that.itemUseVisible = true;
              }
            }else{
              that.$alert(response.data.error_message);
            }

          });
    },
    choose_copy_item(item_id){
      for (var i = 0; i < this.itemList.length; i++) {
        if (item_id == this.itemList[i].item_id) {
            this.itemForm.item_name = this.itemList[i].item_name+'--copy';
            this.itemForm.item_description = this.itemList[i].item_description;
        };
      };
    },
    onSubmit() {
      this.$refs['ruleForm'].validate((valid) => {
        if (valid) {
          var that = this ;
          var url = DocConfig.server+'/api/item/add';

          var params = new URLSearchParams();
          params.append('item_type', this.itemForm.item_type);
          params.append('item_name', this.itemForm.item_name);
          params.append('password', this.itemForm.password);
          params.append('item_domain', this.itemForm.item_domain);
          params.append('copy_item_id', this.itemForm.copy_item_id);
          params.append('item_description', this.itemForm.item_description);
          params.append('item_use', this.itemForm.item_use);

          that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                that.$router.push({path:'/item/index'});
              }else{
                that.$alert(response.data.error_message);
              }

            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    auto_doc(){
      var msg = '<p>'+this.$t('open_api_tips1')+'</p>';
      msg += '<p>'+this.$t('open_api_tips2')+'</p>';
      msg += '<p>'+this.$t('open_api_tips3')+'</p>';
      msg += '<p>'+this.$t('open_api_tips4')+'</p>';
      this.$alert(msg, {
          dangerouslyUseHTMLString: true
        });
    }

  },
  mounted() {
    this.get_item_list();
    /*给body添加类，设置背景色*/
    document.getElementsByTagName("body")[0].className="grey-bg";
  },
  beforeDestroy(){
    /*去掉添加的背景色*/
    document.body.removeAttribute("class","grey-bg");
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped  lang="scss">
@import '~@/components/common/base.scss';

.select-right-icon{
  float: right;
  font-size: 18px;
}
.request-input{
  width: 95%;
}
.question-btn{
  cursor: pointer;
}
.goback-btn{
  @include no-btn;
}
.add-new-object-btn{
  @include yes-full-btn;
}

.center-card a {
  font-size: 12px;
}

.center-card{
  background: $theme-grey-color;
  text-align: left;
  width: 42vh;
  min-height: 48vh;
  overflow-x: auto;
  @include scroll-bar-box;
}

</style>


