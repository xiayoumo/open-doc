<template>
  <div class="edit-page-box" @keydown.ctrl.83.prevent="save" @keydown.meta.83.prevent="save">
    <Header v-if="!is_children"> </Header>
    <el-container
      v-loading="pageLoading"
      element-loading-body="true"
      :class="containerNarrowClass"
      element-loading-background="rgba(0, 0, 0, 0.2)"
      >
        <el-row class="masthead" id="edit-page-container">
            <el-form>
              <el-descriptions :title="title" :column="1" :colon="false" size="mini">
                <template slot="extra">
                  <el-dropdown  @command="dropdown_callback" split-button type="primary" size="medium" trigger="click" @click="save">
                    <i v-if="pageSaveLoading" class="el-icon-loading"></i> {{$t('save')}}
                    <el-dropdown-menu slot="dropdown">
                      <el-dropdown-item :command="save_to_template">{{$t('save_to_templ')}}</el-dropdown-item>
                      <!-- <el-dropdown-item>保存前添加注释</el-dropdown-item> -->
                    </el-dropdown-menu>
                  </el-dropdown>
                  <el-button v-if="!is_children" type="" class="goback-full-btn" size="medium" @click="goback">{{$t('goback')}}</el-button>
                </template>
                <el-descriptions-item :label="$t('catalog')" labelClassName="user-statistic-name">
                  <el-row class="edit-tool-box">
                    <el-col :span="8" ><span class="user-statistic-value">{{ nowCatName }}</span></el-col>
                    <el-col :span="8" v-if="page_use=='doc'||page_use=='api'">
                      <el-row :gutter="10" >
                        <el-col :span="24">
                          <el-dropdown split-button type="primary" size="medium"  @click="ShowTemplateList">
                            <i class="el-icon-s-grid"></i> {{$t('more_templ')}}
                            <el-dropdown-menu slot="dropdown">
                              <el-dropdown-item>
                                <span @click="insert_job_plan_template">{{$t('insert_job_plan_template')}}</span>
                              </el-dropdown-item>
                              <el-dropdown-item>
                                <span @click="insert_database_template">{{$t('insert_database_doc_template')}}</span>
                              </el-dropdown-item>
                              <el-dropdown-item>
                                <span @click="insert_api_template">{{$t('insert_apidoc_template')}}</span>
                              </el-dropdown-item>
                              <el-dropdown-item v-if="page_use=='api'">
                                <span @click="insert_flow_chart_template">{{$t('insert_flow_chart_template')}}</span>
                              </el-dropdown-item>
                              <el-dropdown-item v-if="page_use=='api'">
                                <span @click="insert_sequence_diagram_template">{{$t('insert_sequence_diagram_template')}}</span>
                              </el-dropdown-item>
                            </el-dropdown-menu>
                          </el-dropdown>
                          <el-dropdown split-button type="primary" size="medium" trigger="hover" >
                            <i class="el-icon-tickets"></i> {{$t('doc_tools')}}
                            <el-dropdown-menu slot="dropdown">
                              <el-dropdown-item @click.native="ShowJsonTool">{{$t('json_tools')}}</el-dropdown-item>
                              <el-dropdown-item @click.native="ShowRunApi">{{$t('http_test_api')}}</el-dropdown-item>
                            </el-dropdown-menu>
                          </el-dropdown>
                        </el-col>
                      </el-row>
                    </el-col>
                    <el-col :span="page_use=='excel'?16:8">
                        <el-select
                          class="editor-select-box"
                          ref="editorTypeSelectChange"
                          @change="(e) => editorTypeChange(e)"
                          v-model="editorType" placeholder="请选择" popper-class="editor-type-select">
                          <el-option :label="$t('word_editor')" value="tinymce">
                            <span class="select-left-tip" >{{ $t("word_editor") }}</span>
                            <span class="select-right-tip">{{ $t("word_editor_tip") }}</span>
                          </el-option>
                          <el-option :label="$t('markdowm_editor')" value="editormd">
                            <span class="select-left-tip" >{{ $t("markdowm_editor") }}</span>
                            <span class="select-right-tip">{{ $t("markdowm_editor_tip") }}</span>
                          </el-option>
                          <el-option :label="$t('excel_editor')" value="luckysheet">
                            <span class="select-left-tip" >{{ $t("excel_editor") }}</span>
                            <span class="select-right-tip">{{ $t("excel_editor_tip") }}</span>
                          </el-option>
                        </el-select>
                    </el-col>
                  </el-row>
                </el-descriptions-item>
              </el-descriptions>
            </el-form>
            <Editormd v-if="page_use=='api'&&content"  :key="page_id" :content="content" :pageId="page_id" ref="Editormd"  type="editor" ></Editormd>
            <Tinymce v-if="page_use=='doc'&&content" :key="page_id" :tinymceContent="content" ref="Tinymce"  type="editor" ></Tinymce>
            <Luckysheet v-if="page_use=='excel'&&content" :key="page_id" :sheetTitle="title" :sheetPageId="page_id" :sheetContent="content" ref="Luckysheet"  type="editor" ></Luckysheet>
        </el-row>

        <!-- 更多模板 -->
        <TemplateList :callback="insertValue" ref="TemplateList"></TemplateList>

        <!-- 历史版本 -->
        <HistoryVersion :callback="insertValue" :is_show_recover_btn="true"  ref="HistoryVersion"></HistoryVersion>

        <!-- Json转表格 组件 -->
        <JsonTool   :callback="insertValue" ref="JsonTool" ></JsonTool>

    </el-container>
<!-- 模板存放的地方 -->
    <SystemTemplate ref="SystemTemplate"></SystemTemplate>
  </div>
</template>

<style lang="scss">
@import '~@/components/common/base.scss';

.user-statistic-name{
  color: #999;
  font-size: 16px;
}
.user-statistic-value{
  color: #000;
  font-size: 16px;
  font-weight: bold;
}

.editor-type-select .el-select-dropdown__list {
  min-width: 30vh;
}
.select-left-tip{
  float: left;
}
.select-right-tip{
  float: right; color: #8492a6; font-size: 13px
}
</style>
<style scoped lang="scss">
@import '~@/components/common/base.scss';

.edit-tool-box{
  width: 100%;
}
.editor-select-box{
  float: right;
}
.page-edit-div{
  width: 60%;
  margin: 0 auto;
}
.page-edit-full-div{
  width: 100%;
}
.edit-page-box{
  //height: 100vh;
  overflow-y: auto;
  background: $theme-grey-color;
  @include scroll-bar-box;
}
  .container-narrow{
    margin: 0 auto;
    width: 60%;
    padding-top: 30px;
  }
  .container-narrow-excel{
    margin: 0 auto;
    width: 90%;
    padding-top: 30px;
  }
  .container-narrow-excel-children{
    //margin: 0 auto;
    margin-left: 350px;
    width: 80%;
    padding-top: 30px;
  }
  .masthead{
    width: 100%;
    margin-top: 40px;
    margin-bottom: 60px;
  }

  .cat{
    width: 200px;
  }

  .num{
    width: 60px;

  }
  .fun-btn-group{
    margin-bottom: 15px;
  }

</style>

<script>
import Editormd from '@/components/common/Editormd'
import Tinymce from '@/components/common/Tinymce'
import Luckysheet from '@/components/common/Luckysheet'
import JsonTool from '@/components/common/JsonTool'
import TemplateList from '@/components/page/edit/TemplateList'
import SystemTemplate from '@/components/page/edit/SystemTemplate'
import HistoryVersion from '@/components/page/edit/HistoryVersion'
import pageHelper from '@/js/page-helper'
import store from '@/store'

export default {
  props: {
    now_page_id: {type: String, default: '0'},
    is_children: {type: Boolean, default: false}
  },
  components:{
    Tinymce,
    Editormd,
    Luckysheet,
    JsonTool,
    TemplateList,
    SystemTemplate,
    HistoryVersion
  },
  watch: {
    //   监听属性对象，newValue为新的值，也就是改变后的值
    "page_use"(newValue, oldValue) {
      this.getContainerNarrowClass(newValue);
    },
  },
  data () {
    return {
      containerNarrowClass:'my-box',
      pageSaveLoading:false,
      editorTypeByPageTypeArray:{
        doc:'tinymce',
        api:'editormd',
        excel:'luckysheet',
      },
      editorType:'tinymce', // tinymce/editormd
      runApiUrl: DocConfig.runApiUrl,
      isFullPage:true,
      currentDate: new Date(),
      itemList:{},
      content:"",
      title:"",
      item_id:0,
      cat_id:'',
      s_number:'',
      page_id:0,
      copy_page_id:0,
      // catalogs:[],
      pageLoading:false,
      nowCatName:'',
      page_use:'',
    };
  },
  methods:{
    getContainerNarrowClass(page_use='doc'){
      let classString = this.isFullPage?'page-edit-full-div':'page-edit-div';
      let classStringAfter = 'container-narrow';
      if(page_use=='excel'){
        classStringAfter = this.is_children?'container-narrow-excel-children':'container-narrow-excel';
      }
      this.containerNarrowClass = classString +' '+ classStringAfter;
    },
    editorTypeChange(editorType){
      let nowEditorType = this.$refs.editorTypeSelectChange.value;
      let editorName = this.getEditorNameByEditorType(editorType);
      let targetPageUse = this.getPageUseByEditorType(editorType);
      let otherTip = this.getTipByPageUse(targetPageUse);
      let tipMsg = this.$t("switch_editor")+' 【'+editorName+'】 '+ otherTip +', '+this.$t("is_continue");
      this.$confirm(tipMsg, '', {
        confirmButtonText: this.$t("confirm"),
        cancelButtonText: this.$t("cancel"),
        type: 'warning'
      }).then(() => {
        let content = this.getNewContentByPageUse(targetPageUse,this.page_use);
        let isReloadPage = this.is_children?false:true;
        this.savePage(this.cat_id,this.item_id,this.page_id,content,this.title,this.s_number,targetPageUse,isReloadPage);
      }).catch((e) => {
        if(e=='cancel'){
          this.editorType = nowEditorType;
        }
        console.log(e)
      });
    },
    //获取页面内容
    async get_page_content(targetPageId,isByCopy=false){
        var that = this ;
        if (!targetPageId) {
          var targetPageId = isByCopy?that.copy_page_id:that.page_id;
        }
        that.$forceUpdate();// 必须强制更新
        that.pageLoading = true;
        let response = await pageHelper.getPage(targetPageId);// 同步获取
        if (response.error_code === 0) {
          that.page_use = response.data.page_use;
          that.editorType = that.getEditorTypeByPageUse(that.page_use);
          if (that.page_use == 'doc') {
            that.isFullPage = false;
          }
          that.content = response.data.page_content;
          let titleTip = isByCopy ? '-copy' : '';
          that.title = response.data.page_title + titleTip;
          that.item_id = response.data.item_id;
          that.s_number = response.data.s_number;
        } else {
          that.$alert(response.error_message);
        }
        that.pageLoading = false;
    },
    getContentByPageUse(nowPageUse='doc'){
      let content;
      if(nowPageUse=='api'){
        content = this.$refs.Editormd.getMarkdown();
      }
      if(nowPageUse=='doc'){
        content = this.$refs.Tinymce.getTinymceContent();
      }
      if(nowPageUse=='excel'){
        content = this.$refs.Luckysheet.getLuckysheetContent();
      }
      return content;
    },
    getTipByPageUse(targetPageUse){
      let tip = '';
      if(targetPageUse=='api'&&this.page_use=='doc'){
        tip = this.$t("clean_editor");
      }
      if(targetPageUse=='excel' && this.page_use!='excel'){
        tip = this.$t("clean_editor");
      }
      if(this.page_use=='excel' && targetPageUse!='excel'){
        tip = this.$t("clean_editor");
      }
      return tip;
    },
    getNewContentByPageUse(targetPageUse='doc',nowPageUse='doc'){// doc/api/excel
      let content = this.getContentByPageUse(nowPageUse);
      if(targetPageUse=='doc' && nowPageUse=='api'){// markdown 转 html
        content = this.$refs.Editormd.markdownDataToHtml(content);
        if(content.length==0){
          content = ' ';
        }
      }
      if(targetPageUse=='api' && nowPageUse=='doc'){
        content = ' ';
      }
      if(targetPageUse=='excel' && nowPageUse!='excel'){
        content = ' ';
      }
      if(nowPageUse=='excel' && targetPageUse!='excel'){
        content = ' ';
      }
      return content;
    },
    getEditorNameByEditorType(editorType='tinymce'){
      let editName = this.$t("markdowm_editor");
      if(editorType=='tinymce'){
        editName = this.$t("word_editor");
      }
      if(editorType=='luckysheet'){
        editName = this.$t("excel_editor");
      }
      return editName;
    },
    getEditorTypeByPageUse(pageUse='doc'){
      let editorTypeByPageTypeObject = this.editorTypeByPageTypeArray;
      return editorTypeByPageTypeObject[pageUse];
    },
    getPageUseByEditorType(editorType='tinymce'){
      let editorTypeByPageTypeObject = this.editorTypeByPageTypeArray;
      // 实现键值互换
      let pageTypeByEditorTypeObject = {};
      for (let key in editorTypeByPageTypeObject) {
        pageTypeByEditorTypeObject[editorTypeByPageTypeObject[key]] = key;
      }
      return pageTypeByEditorTypeObject[editorType];
    },
    //获取默认该选中的目录
    get_default_cat(){
      var that = this ;
      var url = DocConfig.server+'/api/catalog/getDefaultCat';
      var params = new URLSearchParams();
      params.append('page_id',  that.page_id);
      params.append('item_id',  that.item_id);
      params.append('copy_page_id',  that.copy_page_id);
      that.axios.post(url, params)
        .then(function (response) {
          if (response.data.error_code === 0 ) {
            //that.$message.success("加载成功");
            var json = response.data.data ;
            that.cat_id = json.default_cat_id ;
            that.nowCatName = json.default_cat_name;
          }else{
            that.$alert(response.data.error_message);
          }
        });
    },
    //插入数据到编辑器中。插入到光标处。如果参数is_cover为真，则清空后再插入(即覆盖)。
    insertValue(value,is_cover){
      if (value) {
          let editType = this.getEditorTypeByPageUse(this.page_use);
          let refsKey = editType.toLowerCase();
          let childRef = this.$refs[refsKey];//获取子组件
          if (is_cover) {
            // 清空
            childRef?childRef.clear():'';
          }
          childRef.insertValue(value); //调用子组件的方法
      }

    },

    //插入api模板
    insert_api_template(){
      let apiContent = this.$refs.SystemTemplate.getApiDocTempl(this.page_use);
      this.insertValue(apiContent) ;
    },

    //插入数据字典模板
    insert_database_template(){
      let databaseDocContent = this.$refs.SystemTemplate.getDatabaseDocTempl(this.page_use);
      this.insertValue(databaseDocContent ) ;
    },
    //任务计划模板
    insert_job_plan_template(){
      let jobPlanContent = this.$refs.SystemTemplate.getJobPlanTempl(this.page_use);
      this.insertValue(jobPlanContent) ;
    },
    //流程图模板
    insert_flow_chart_template(){
      this.insertValue(this.$refs.SystemTemplate.getFlowChartTempl() ) ;
    },
    //流程图模板
    insert_sequence_diagram_template(){
      this.insertValue(this.$refs.SystemTemplate.getSequenceDiagramTempl() ) ;
    },
    //json转参数表格
    ShowJsonTool(){
        let childRef = this.$refs.JsonTool ;//获取子组件
        childRef.dialogFormVisible = true ;
        childRef.outputTabelType = this.page_use=='doc'?'html':'markdown' ;
    },

    ShowRunApi(){
        window.open(this.runApiUrl);
    },
    //更多模板、模板列表
    ShowTemplateList(){
        let childRef = this.$refs.TemplateList ;//获取子组件
        childRef.show() ;
    },

    //展示历史版本
    ShowHistoryVersion(){
        let childRef = this.$refs.HistoryVersion ;//获取子组件
        childRef.show() ;
    },
    save(){
      let content = this.getContentByPageUse(this.page_use);
      this.savePage(this.cat_id,this.item_id,this.page_id,content,this.title,this.s_number,this.page_use,false);
    },
    async savePage(cat_id=0,item_id=0,page_id=0,page_content='',page_title='',s_number=99,page_use='api',isReloadPage=false){
      var that = this ;
      that.pageSaveLoading = true;
      let response = await pageHelper.savePage(cat_id,item_id,page_id,page_content,page_title,s_number,page_use);
      if (response.error_code === 0 ) {
        that.page_id = response.data.page_id;
        localStorage.removeItem("page_content");
        if(isReloadPage){
          that.$confirm(that.$t("save_success")+', 将重置页面信息.', '', {
            confirmButtonText: that.$t("confirm"),
            showCancelButton: false,
            type: 'success'
          }).then(() => {
            if (page_id <= 0 ) {
              that.$router.push({path:'/page/edit/'+item_id+'/'+response.data.page_id}) ;
            }else{
              window.location.reload();
            };
          }).catch(() => {});
        }else{
          that.get_page_content(that.page_id);
          that.$message.success({
            message: that.$t("save_success"),
            duration:500,
            type: 'success'
          });
        }
      }else{
        that.$alert(response.error_message);
      }
      that.pageSaveLoading = false;
      //设置一个最长关闭时间
      setTimeout(() => {
        that.pageSaveLoading = false;
      })
    },
    goback(){
      localStorage.removeItem("page_content");
      var url = '/item-show/'+this.item_id;
      this.$router.push({path:url,query:{page_id:this.page_id}}) ;
    },
    dropdown_callback(data){
      if (data) {
        data();
      };
    },
    //另存为模板
    save_to_template(){
      var that = this ;
      var content;
      if(that.page_use=='api'){
        let childRef = this.$refs.Editormd ;
        content = childRef.getMarkdown() ;
      }else{
        content = that.content;
      }
       this.$prompt(that.$t("save_templ_title"), ' ', {
       }).then(function(data){
          var url = DocConfig.server+'/api/template/save';
          var params = new URLSearchParams();
          params.append('template_title',  data.value);
          params.append('template_content',  content);
          that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                  that.$alert(that.$t("save_templ_text"));
              }else{
                that.$alert(response.data.error_message);
              }

            });
       });
    },

    /** 粘贴上传图片 **/
    upload_paste_img(e){
      var that = this;
      var url = DocConfig.server+'/api/page/uploadImg';
      var clipboard = e.clipboardData;
      for (var i = 0, len = clipboard.items.length; i < len; i++) {
        if (clipboard.items[i].kind == 'file' || clipboard.items[i].type.indexOf('image') > -1) {
          var imageFile = clipboard.items[i].getAsFile();
          var form = new FormData;
          form.append('t', 'ajax-uploadpic');
          form.append('editormd-image-file', imageFile);
          var loading = '';
          var callback = function(type, data) {
            type = type || 'before';
            switch (type) {
              // 开始上传
              case 'before':
                loading = that.$loading();
                break;
                // 服务器返回错误
              case 'error':
                loading.close();
                that.$alert('图片上传失败');
                break;
                // 上传成功
              case 'success':
                loading.close();
                if (data.success == 1) {
                  var value = '![](' + data.url + ')';
                  that.insertValue(value);
                } else {
                  that.$alert(data.message);
                }

                break;
            }
          };
          $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: form,
            processData: false,
            contentType: false,
            beforeSend: function() {
              callback('before');
            },
            error: function() {
              callback('error');
            },
            success: function(data) {
              callback('success', data);
            }
          })
          e.preventDefault();
        }
      }
    }
  },
  created() {
    this.page_id = this.now_page_id==0 ? this.$route.params.page_id:this.now_page_id;
    this.item_id = this.$route.params.item_id ? this.$route.params.item_id:0;
    this.copy_page_id = this.$route.query.copy_page_id ? this.$route.query.copy_page_id : '' ;
    if (this.copy_page_id > 0 ) {
      this.get_page_content(this.copy_page_id,true);
    }else {
      if (this.page_id > 0 ) {
        this.get_page_content(this.page_id);
      }else{
        this.content = this.$t("welcome_use_showdoc") ;
      }
    }
  },
  mounted () {
    this.$forceUpdate();// 必须强制更新
    var that = this ;

    that.get_default_cat();

    /** 监听粘贴上传图片 **/
    document.addEventListener('paste', that.upload_paste_img);

    // 设置题头
    let headTitle = localStorage.getItem('head_title');
    store.dispatch('SetNowHeadTitle', headTitle);
  },

  beforeDestroy(){

    //解除对粘贴上传图片的监听
    document.removeEventListener('paste', this.upload_paste_img);
  }
}
</script>
