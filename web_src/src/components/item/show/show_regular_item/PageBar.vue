<template>
  <div>
      <ul class="page-bar">
        <li v-if="!isMobileDevice">
            <el-button type="text" class="hover-word-btn" @click="edit_page">
              <span class="iconfont icon-bianji word-btn-default"></span><span class="word-btn-content">{{$t('edit_page')}}</span>
            </el-button>
        </li>
        <li>
          <el-button type="text" class="hover-word-btn" @click="share_page">
            <span class="iconfont icon-arrow- word-btn-default"></span><span class="word-btn-content">{{$t('share_page')}}</span>
          </el-button>
        </li>
        <li>
          <el-dropdown @command="dropdown_callback" class="">
            <span class="el-dropdown-link">
              <i class="el-icon-arrow-down el-icon--down"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <router-link v-if="!isMobileDevice" :to="'/page/edit/'+item_id+'/0?copy_page_id='+page_id"><el-dropdown-item>{{$t('copy')}}</el-dropdown-item></router-link>
              <el-dropdown-item :command="show_page_info">{{$t('detail')}}</el-dropdown-item>
              <el-dropdown-item v-if="!isMobileDevice" :command="ShowHistoryVersion">{{$t('history_version')}}</el-dropdown-item>
              <el-dropdown-item :command="delete_page">{{$t('delete')}}</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </li>
      </ul>

  <el-dialog
    :title="$t('share_page')"
    :visible.sync="dialogVisible"
    :width="isMobileDevice?'40vh':'60vh'"
    :modal="false"
    class="text-center"
    >

    <el-tabs tab-position="right">
      <el-tab-pane :label="$t('item_page_address')">
        <p ><img  id="qr-page-link" class="qrcode-img" :src="qr_page_link"> </p>
        <p>
          <el-button type="text" class="copy-link-btn" v-clipboard:copyhttplist="copyText1" v-clipboard:success="onCopy"><span class="iconfont icon-fuzhi"></span> {{$t('copy_link')}}</el-button>
          <code >{{share_page_link}}</code>
        </p>

      </el-tab-pane>
      <el-tab-pane :label="$t('single_page_address')">
        <p><img  id="qr-single-link" class="qrcode-img" :src="qr_single_link"> </p>
        <p>
          <el-button type="text" class="copy-link-btn" v-clipboard:copyhttplist="copyText2" v-clipboard:success="onCopy"><span class="iconfont icon-fuzhi"></span> {{$t('copy_link')}}</el-button>
          <code id="share-single-link">{{share_single_link}}</code>
        </p>
      </el-tab-pane>
    </el-tabs>



      <p class="page-diff-tips-box">
        <el-popover
          placement="right-start"
          width="200"
          trigger="hover"
          >
          <div  v-html="$t('project_share_and_page_share_diff_tip')"></div>

          <el-button type="text" slot="reference">{{$t('page_diff_tips')}}</el-button>
        </el-popover>
      </p>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogVisible = false">{{$t('confirm')}}</el-button>
      </span>
  </el-dialog>

    <!-- 历史版本 -->
    <HistoryVersion :page_id="page_id" :is_show_recover_btn="false" :is_modal="false" callback="insertValue" ref="HistoryVersion"></HistoryVersion>

  </div>
</template>


<style lang="scss">
@import '~@/components/common/base.scss';

.el-dialog__header {
  text-align: left;
}
//.word-btn:hover{
//  content:"分享";
//}
/* Float Shadow */
.hover-word-btn {
  @include hover-word-btn;
}

</style>
<style scoped lang="scss">
@import '~@/components/common/base.scss';


.copy-link-btn{
  margin-right: 20px;
}
.qrcode-img{
    width:114px;height:114px;
}


</style>

<script>
  import HistoryVersion from '@/components/page/edit/HistoryVersion'
  export default {
  props:{
    item_id:'',
    page_id:'',
    page_info:{},
    item_info:'',
    goEdit:false,
  },
    data() {
      return {
        homeUrl:DocConfig.homeUrl,
        menu: [],
        dialogVisible:false,
        qr_page_link:"#",
        qr_single_link:"#",
        share_page_link:"",
        share_single_link:"",
        copyText1:'',
        copyText2:'',
        isMobileDevice:false,
      }
    },
  components:{
    HistoryVersion
  },
  watch: {
      //   监听属性对象，newValue为新的值，也就是改变后的值
      "goEdit"(newValue, oldValue) {
        if (newValue) {
          this.edit_page();
        }
      },
  },
  methods:{
    edit_page(){
      var page_id = this.page_id > 0 ? this.page_id : 0 ;
      var url = '/page/edit/'+this.item_id+'/'+page_id;
      this.$router.push({path:url}) ;
    },
    share_page(){
      var page_id = this.page_id > 0 ? this.page_id : 0 ;
      var that = this ;
      var url = DocConfig.server+'/api/page/getPageShareCode';
      var params = new URLSearchParams();
      params.append('page_id',  page_id);
      that.axios.post(url, params)
        .then(function (response) {
          if (response.data.error_code === 0 ) {
            let page_code = response.data.data;
            // that.share_page_link = that.getRootPath()+"/item-show/"+that.item_id +'?page_id='+page_id ;
            // that.share_single_link= that.getRootPath()+"/page/"+page_id ;
            that.share_page_link = that.getRootPath()+"/item-show/"+page_code ;
            that.share_single_link= that.getRootPath()+"/page/"+page_code ;
            that.qr_page_link = DocConfig.server +'/api/common/qrcode&size=3&url='+encodeURIComponent(that.share_page_link);
            that.qr_single_link = DocConfig.server +'/api/common/qrcode&size=3&url='+encodeURIComponent(that.share_single_link);
            that.dialogVisible = true;
            that.copyText1 = that.item_info.item_name+' - '+that.page_info.page_title+"\r\n"+ that.share_page_link;
            that.copyText2 = that.page_info.page_title+"\r\n"+ that.share_single_link;
          }else{
            that.$alert(response.data.error_message);
          }
        });


    },
    get_share_code(page_id){
      var that = this ;
      var url = DocConfig.server+'/api/page/getPageShareCode';
      var params = new URLSearchParams();
      params.append('page_id',  page_id);
      that.axios.post(url, params)
        .then(function (response) {
          if (response.data.error_code === 0 ) {
            window.location.reload()
          }else{
            that.$alert(response.data.error_message);
          }
        });
    },
    dropdown_callback(data){
      if (data) {
        data();
      };
    },
    show_page_info(){
      var html ="本页面由用户： "+this.page_info.author_username+' 于 '+this.page_info.addtime+' 更新';
      this.$alert(html);
    },


    //展示历史版本
    ShowHistoryVersion(){
        let childRef = this.$refs.HistoryVersion ;//获取子组件
        childRef.show() ;
    },

    delete_page(){
      var page_id = this.page_id > 0 ? this.page_id : 0 ;
      var that = this ;
      var url = DocConfig.server+'/api/page/delete';

      this.$confirm(that.$t('comfirm_delete'), ' ', {
        confirmButtonText: that.$t('confirm'),
        cancelButtonText: that.$t('cancel'),
        type: 'warning'
      }).then(() => {
        var params = new URLSearchParams();
        params.append('page_id',  page_id);
        that.axios.post(url, params)
        .then(function (response) {
          if (response.data.error_code === 0 ) {
            window.location.reload()
          }else{
            that.$alert(response.data.error_message);
          }
        });
      });
    },
    onCopy(){
      this.$message(this.$t("copy_success"));
    },
  },
  mounted () {
    var that = this ;
    document.onkeydown=function(e){  //对整个页面文档监听 其键盘快捷键
      var keyNum=window.event ? e.keyCode :e.which;  //获取被按下的键值
      if (keyNum == 69 && e.ctrlKey) {  //Ctrl +e 为编辑
        that.edit_page();
        e.preventDefault();
      };

      if (keyNum == 46 && e.ctrlKey) {  //Ctrl +del 为删除
        that.delete_page();
        e.preventDefault();
      };
    }
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice = true;
    }
  }
};
</script>
