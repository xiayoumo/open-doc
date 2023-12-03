<template>
  <el-row v-if="sheetPageId+'-'+type" :class="type=='editor'?'excel-box':'excel-html-box'">
    <link rel='stylesheet' :href="editorPath+'/plugins/css/pluginsCss.css'" />
    <link rel='stylesheet' :href="editorPath+'/plugins/plugins.css'" />
    <link rel='stylesheet' :href="editorPath+'/css/luckysheet.css'" />
    <link rel='stylesheet' :href="editorPath+'/assets/iconfont/iconfont.css'" />
    <!--    <link rel='stylesheet' :href="editorPath+'/expendPlugins/chart/chartmix.css'" />-->
<!--    打印预览 start    -->
    <el-button id="printPreviewBtn" style="display: none;" @click="printExcel()">{{ $t('excel_print_preview') }}</el-button>
    <el-button id="printExcelBtn" style="display: none;" plain
               v-print="{ id: 'print_html', popTitle: 'test111' }">{{ $t('excel_print') }}</el-button>
    <div id="print_html" class="procedure" style="text-align: center;z-index: 0;"></div>
<!--    打印预览 start    -->
    <el-row v-if="type=='editor'">
        <el-col class="upload-excel-file-box">
          <el-button type="primary" :loading="downloadFileLoading" size="small" @click="exportExcel">{{ $t('excel_export') }}</el-button>
          <el-upload
            class="upload-excel-file"
            action="#"
            :auto-upload="false"
            ref="inputFile"
            :show-file-list="false"
            accept=".xls,.xlsx"
            :on-change="importExcel">

            <el-button :loading="uploadFileLoading" size="small" type="primary">{{ $t('excel_upload_excel') }}</el-button>
          </el-upload>
          <div class="show-upload-file-div">
            <transition name="el-zoom-in-center">
              <div v-show="nowUploadFileName" class="now-upload-file-name-box">  <i class="el-icon-date"></i>  {{ nowUploadFileName }} <i class="el-icon-success upload-file-success"></i></div>
            </transition>
          </div>
          <el-button type="primary" size="small" class="clean-excel-btn" @click="cleanSheet">{{ $t('excel_clean') }}</el-button>

            <el-tooltip class="item" effect="dark" :content="$t('excel_open_share_tip')" placement="top-end">
              <el-checkbox class="start-share-btn" v-model="sheetAllowEdit">{{ $t('excel_open_share') }}</el-checkbox>
            </el-tooltip>

        </el-col>
    </el-row>
    <!--web spreadsheet组件-->
    <div :class="type=='editor'?'excel':'excel-html'" v-loading="luckysheetLoading" element-loading-background="transparent">
      <div v-if="type=='editor'" :key="sheetPageId+'-'+type" :id="sheetEditId+sheetPageId" class="luckysheet-view-box"></div>
      <div v-if="type=='html'" :key="sheetPageId+'-'+type" :id="sheetHtmlId+sheetPageId" class="luckysheet-view-box"></div>
    </div>
  </el-row>
</template>

<script>
if (typeof window !== 'undefined') {
  var $s = require('scriptjs');
}
// 参考文档 :https://blog.csdn.net/zby18638890018/article/details/131239228
// 官方：https://mengshukeji.gitee.io/LuckysheetDocs/zh/guide/#%E5%BC%80%E5%8F%91%E6%A8%A1%E5%BC%8F
//引入依赖包
import LuckyExcel from 'luckyexcel'
import { exportExcel } from '@/js/export-sheet.js';
// 支持打印
import Vue from 'vue'
import Print from 'vue-print-nb'
Vue.use(Print)

export default {
  name: 'luckysheet',
  watch:{
    '$i18n.locale'(newValue) {
      window.luckysheet.changLang(newValue);//暂支持`"zh"`、`"en"`、`"es"`；默认为`"zh"`；
    },
  },
  props:{
    sheetPageId:{type: String,default: '0'},
    sheetEditId:{type: String,default: 'sheetEditId'},
    sheetHtmlId:{type: String, default: 'sheetHtmlId'},
    sheetContent:{type: String, default: ''},
    sheetTitle:{type: String, default: 'title'},
    type: {type:String, default: 'editor'},
    editorPath: {type: String,default: '../../../static/luckysheet'},
    jqueryMinPath: {
      type: String,
      default: '../../../static/jquery.min.js',
    },
  },
  data() {
    return {
      luckysheetLoading:true,
      sheetAllowEdit:false,
      nowUserName:'',
      downloadFileLoading:false,
      uploadFileLoading:false,
      nowUploadFileName:'',
      editConfig:{
        allowEdit:true,
        container: this.sheetEditId+this.sheetPageId,
        title: this.sheetTitle,
        lang: this.$i18n.locale,
        sheetFormulaBar:true,
        column:30,
        row:50,
        chart: true,
        defaultRowHeight: 22, //自定义行高
        data: [{name: 'Sheet1'}],
        showtoolbar: true, // 想要使用 showtoolbarConfig, 必须将showtoolbar设为false
        showtoolbarConfig:{importExcel: false, exportExcel: false, saveExcel: false},
        plugins:['chart'],// 参考文档 https://blog.csdn.net/qq_34645412/article/details/127209040
        showinfobar: false
      },
      htmlConfig:{
        container: this.sheetHtmlId+this.sheetPageId,
        title: this.sheetTitle,
        lang: this.$i18n.locale,
        sheetFormulaBar:false,
        defaultRowHeight: 22, //自定义行高
        data: [{name: 'Sheet1'}],
        showstatisticBar: false,
        showsheetbar: false,
        showtoolbar: false, // 想要使用 showtoolbarConfig, 必须将showtoolbar设为false
        showtoolbarConfig:{screenshot: true, exportExcel: true, saveExcel: true},
        // 其他配置
        userInfo:'',
        myFolderUrl:'',// 信息栏中的返回按钮url，不设置则为www.baidu.com
        showinfobar: true
      },
    }
  },
  created(){

  },
  mounted() {
    let userInfo = localStorage.getItem('user_info');
    if(userInfo != 'undefined'&&userInfo>''){
      let userInfoObject = JSON.parse(userInfo);
      if(userInfoObject.hasOwnProperty('username')){
        this.nowUserName = userInfoObject.username;
      }
    }
    if (typeof window.luckysheet !== 'undefined') {
      this.init();
    }else{
        //加载依赖""
        $s([
          `${this.jqueryMinPath}`
        ],()=>{
          $s([
            `${this.editorPath}/plugins/js/plugin.js`,// 内已包含jq已去除
            `${this.editorPath}/luckysheet.umd.js`,
            // `${this.editorPath}/expendPlugins/chart/chartmix.umd.min.js`
          ], () => {
            this.init();
          });
        });
    }
  },
  methods: {
    async init() {
      var that = this;
      that.$forceUpdate();// 解决页面来回切换后的顶部出现断层问题
      var luckysheet = window.luckysheet
      let dataConfig = that.getConfig();
      // var loadUrl = DocConfig.server+'/api/page/getExcelTableByGridKey';
      // dataConfig.loadUrl = loadUrl;
      // dataConfig.gridKey = this.sheetPageId;
      // // dataConfig.editMode = true;
      // dataConfig.allowUpdate = true;
      // dataConfig.updateUrl=`ws://`+DocConfig.server+`/luckysheet/api/updateUrl`;
      dataConfig.lang = that.$i18n.locale;
      if (that.type == 'html') {
        dataConfig.showstatisticBar = false; //  最下方字数统计工具栏
        dataConfig.showsheetbar = true;// 最下方sheet表工具栏
        dataConfig.showtoolbar = false;
        dataConfig.showtoolbarConfig = {saveExcel: dataConfig.allowEdit, exportExcel: !dataConfig.allowEdit, screenshot: true};
        let userInfoIcon = '<i class="' + (dataConfig.allowEdit ? 'el-icon-share' : 'el-icon-view') + ' user-info-icon" aria-hidden="true"></i>';
        let userInfoName = dataConfig.allowEdit ? that.$t('share_excel') : that.$t('non_share_excel');
        dataConfig.userInfo = userInfoIcon + ' ' + userInfoName;
        dataConfig.myFolderUrl = '';
        dataConfig.showinfobar = true;
      }else{
        dataConfig.allowEdit = true;
        dataConfig.showtoolbar = true;
        dataConfig.showinfobar = false;
        dataConfig.title = that.sheetTitle;
        dataConfig.chart = true;
        dataConfig.plugins = ['chart'];// 参考文档 https://blog.csdn.net/qq_34645412/article/details/127209040
        dataConfig.showtoolbarConfig = {importExcel: false, exportExcel: false, saveExcel: false};
      }
      that.luckysheetLoading = false;
      await luckysheet.create(dataConfig);
      window.exportLuckysheetExcel = that.exportExcel;// 定义自定义导出方法（供自定义工具栏导出按钮使用）
      window.excelUpload = that.importExcel;// 定义自定义导入
      window.saveExcel = that.saveExcel;// 定义自定义保存

    },
    getConfig(){
      let dataConfig = false;
      let isJsonContent = this.isJsonString(this.sheetContent);
      if(isJsonContent){
        dataConfig = JSON.parse(this.sheetContent);
      }else{
        if(this.sheetContent!=this.$t('welcome_tip')&&this.sheetContent.trim()!=''){
          this.$message.warning({offset:400,duration:3000,message:this.$t('excel_load_error_tip')});
        }
      }
      if (dataConfig) {
        // 编辑已有文档
        let nodeIdDiv = document.getElementById(dataConfig.container);
        if (!nodeIdDiv) {// 当渲染页面的id不存在时
          dataConfig.container = (this.type == 'html'?this.sheetHtmlId:this.sheetEditId)+this.sheetPageId;
        }
        this.sheetAllowEdit = dataConfig.allowEdit;
      }else{//默认空excel
        dataConfig = this.type == 'editor'?this.editConfig:this.htmlConfig;
      }
      return dataConfig;
    },
    isJsonString(str) {
      try {
        JSON.parse(str);
        return true;
      } catch (e) {
        return false;
      }
    },
    getLuckysheetContent(){
      let data = luckysheet.toJson();
      data.allowEdit = this.sheetAllowEdit;
      data = JSON.stringify(data);
      return data;
    },
    cleanSheet(){
      this.editConfig.showtoolbar = true
      this.editConfig.allowEdit = true
      luckysheet.create(this.editConfig)
    },
    exportExcel() {
      this.downloadFileLoading = true;
      exportExcel(luckysheet.getluckysheetfile(), this.sheetTitle).then(()=>{
        this.downloadFileLoading = false;
      })
    },
    importExcel(file, fileList=[]) {
      this.uploadFileLoading = true;
      let fileData = file.raw;// 外层导入
      // let fileData = file;// 自定义工具导入
      if(fileData.size < 5*1024*1024){// 不能超过5M
        this.importExcelTool(fileData)
      }else{
        this.$message({type:"error",message:this.$t('excel_upload_error_tip'),offset:500});
      }
      this.uploadFileLoading = false;
    },
    saveExcel(){
      let excelContent = this.getLuckysheetContent();
      this.$emit('savePage',excelContent);
    },
    importExcelTool(file) {
      let name = file.name
      //获取文件后缀
      let suffixArr = name.split('.'),
        suffix = suffixArr[suffixArr.length - 1]
      if (suffix !== 'xlsx') {
        this.$message({message:this.$t('excel_upload_only_tip'),duration:10000,offset:400,type:'warning'})
        return false;
      }else{
        LuckyExcel.transformExcelToLucky(file, this.fileCb, this.errorCb);
        this.nowUploadFileName = name;
      }
    },
    fileCb(exportJson, luckysheetfile) {
      // 转换后获取工作表数据
      if (exportJson.sheets === null || exportJson.sheets.length === 0) {
        this.$message({message:this.$t('excel_upload_error_read_tip'),duration:10000,offset:400,type:'error'})
        return
      }
      luckysheet.destroy();
      luckysheet.create({
        container: this.sheetEditId+this.sheetPageId, //luckysheet is the container id
        showinfobar: false,
        data: exportJson.sheets,
        title: exportJson.info.name,
        userInfo: exportJson.info.name.creator,
        showtoolbarConfig: {importExcel: false, exportExcel: false, saveExcel: false}
      })
      this.uploadFileLoading = false;
    },
    errorCb(error) {
      console.log(error)
    },

    async printExcel() { // 打印预览事件实现， 参考文档: https://blog.csdn.net/alan_ji198573/article/details/128624832
      this.printSheet()
    },
    printSheet() { // 打印操作：自动选中打印区域，并生成打印截图，进行打印
      document.querySelector('#print_html').style = "display:block"
      window.luckysheet.hideGridLines();
      let currentSelected = luckysheet.getRange() // 获取当前选中区域
      if (currentSelected[0] != null && (currentSelected[0].row[1] - currentSelected[0].row[0] >= 1 || currentSelected[0].column[1] - currentSelected[0].column[0] >= 1)) { // 如果当前选中区只是一个单元格，则认为选取无效。
            luckysheet.getScreenshotNew((imgSrc) => { // 将打印区域生成base64图片（*将生成的base64编码复制粘贴到浏览器地址框，是可以预览图片样式的），生成后执行的后续打印操作，取用匿名委托函数做为参数传入
              window.luckysheet.showGridLines()
              let $img = `<img src=${imgSrc} style="max-width: 90%;" />`
              this.$nextTick(() => {
                document.querySelector('#print_html').innerHTML = $img
              })
              document.getElementById('printExcelBtn').click()
              setTimeout(() => {
                document.querySelector('#print_html').style = "display:none"
              }, 100)
            })
      } else {
            const RowColumn = this.getPrintSheetArea() // 获取打印区域的行列
            // 因需要打印左边的边框，需重新设置第一列
            // RowColumn.column[0] = 0;

            luckysheet.setRangeShow(RowColumn) // 进行选区操作
            luckysheet.getScreenshotNew((imgSrc) => { // 将打印区域生成base64图片（*将生成的base64编码复制粘贴到浏览器地址框，是可以预览图片样式的），生成后执行的后续打印操作，取用匿名委托函数做为参数传入
              window.luckysheet.showGridLines()
              const $img = `<img src=${imgSrc} style="max-width: 90%;" />`
              this.$nextTick(() => {
                document.querySelector('#print_html').innerHTML = $img
              })
              document.getElementById('printExcelBtn').click()
              setTimeout(() => {
                document.querySelector('#print_html').style = "display:none"
              }, 100)
            })
      }
    },
    getPrintSheetArea() { // 获取打印区域（即表格中有内容、非空白的区域）（用row，column数组表示）
      const sheetData = luckysheet.getSheetData();
      let objRowColumn = {
        row: [0, 0], // 行
        column: [0, 0], // 列
      };
      sheetData.forEach((item, index) => { // * item是行、index是行索引、it是一行里的一格、itemIndex是这一格在这一行里的列索引
        item.forEach((it, itemIndex) => { // 行数
          if (it !== null) {
            if (objRowColumn.row[1] < index) {
              objRowColumn.row[1] = index; // row第二位
            }
            if (objRowColumn.column[1] < itemIndex) {
              objRowColumn.column[1] = itemIndex; // column第二位
            }
          }
        });
      });
      return objRowColumn;
    },

  },
  beforeDestroy() {
     //window.luckysheet.destroy();// 销毁会提示错误
  }
}
</script>

<style lang="scss">
@import '~@/components/common/base.scss';

.luckysheet-modal-dialog{
  @include dialog-box;
  .luckysheet-modal-dialog-title-close{
    @include icon-close-btn;
  }
}
.luckysheet-modal-dialog-content input{
  border: none !important;
  border-radius: 5px;
  @include input-box;
}
.luckysheet-modal-dialog-content select{
  border: none !important;
  border-radius: 5px;
  option{
    width: 100px;
    overflow: hidden;        /*内容会被修剪，并且其余内容是不可见的*/
    text-overflow:ellipsis;  /*显示省略符号来代表被修剪的文本。*/
    white-space: nowrap;     /*文本不换行*/
    float: left;
    &:hover,&:focus,&:visited,&:link{
      cursor: pointer !important;
      color: $theme-right-msg-color !important;
      background: $theme-black-grey-color !important;
    }
  }
}
.luckysheet-modal-dialog .btn-primary {
  @include yes-full-btn;
}
.luckysheet-modal-dialog .btn-default {
  @include no-btn;
}
.luckysheet-modal-dialog-content,.luckysheet-modal-dialog-title {
  background-color: $theme-grey-color !important;
}
.luckysheet_info_detail{
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}
#luckysheet-copy-btn{// 解决编辑页面多余空格问题
  display: none;
}
.luckysheet_info_detail{
  height: auto !important;
}
.luckysheet-cell-selected {
  border: 1px dotted $theme-fourth-color !important;
}
.luckysheet-rows-h-selected {
  border-right: 1px dotted $theme-fourth-color !important;
}
.luckysheet-cols-h-selected {
  border-bottom: 1px dotted $theme-fourth-color !important;
}
.luckysheet-cs-fillhandle {
  background-color: $theme-fourth-color !important;
}
#luckysheet-input-box {
  border: 2px $theme-fourth-color solid;
}
.user-info-icon{
  font-size:16px;
  color:$theme-right-msg-color;
}
.excel {
  z-index: 0;
  height: 70vh !important;
}
.excel-html {
  z-index: 0;
  height: 72vh !important;
}
.excel-html .luckysheet{
  //height: 72vh !important;
  border-radius: 8px;
}
.excel .luckysheet{
  //height: 70vh !important;
  border-radius: 8px;
}
.luckysheet-wa-editor{
  background: $theme-third-color !important;
}
.jfk-tooltip{
  border: 1px solid $theme-color !important;
}
.luckysheet-icon-img-container:hover{
  color: $theme-right-msg-color !important;
}
.luckysheet-toolbar-button, .luckysheet-toolbar-menu-button{
  color: $theme-grey-color !important;
}
.luckysheet-toolbar-combo-button{
  color: $theme-grey-color !important;
}
.luckysheet-toolbar-combo-button-input{
  color: rgba(255,255,255,.7) !important;
}
.luckysheet-modal-dialog{
  border-radius: 10px;
}
.luckysheet-modal-dialog-buttons .luckysheet-model-conform-btn,.luckysheet-modal-dialog-buttons .luckysheet-model-copy-btn{
  @include yes-full-btn;
  padding: 10px 15px;
}
.luckysheet-modal-dialog-buttons .luckysheet-model-cancel-btn{
  @include no-btn;
  padding: 10px 15px;
}
</style>
<style scoped lang="scss">
@import '~@/components/common/base.scss';

.clean-excel-btn{
  margin-left: 10px;
}
.start-share-btn{
  margin-left: 10px;
  margin-top: 8px;
}
.luckysheet-view-box{
  margin: 0px;
  padding: 0px;
  width: 100%;
  height: 100%;
}
.now-upload-file-name-box{
  color: $theme-words-color;
  display: block;
  margin-left: 20px;
  padding-top: 5px;
  overflow: hidden;
  padding-left: 4px;
  text-overflow: ellipsis;
  transition: color .3s;
  white-space: nowrap;
  font-size: 14px;
  line-height: 26px;
}
.upload-file-success{
  color: $theme-fourth-color;
  font-size: 16px;
}
.show-upload-file-div{
  display: inline-flex;
}
.upload-excel-file-box{
  display: inline-flex;
  margin-bottom: 10px;
}
.upload-excel-file{
  margin-left: 10px;
  //float: left;
}
.excel-edit-box{
  float: right;
}
.excel-box {
  width: 100%;
  height: 72vh;
  display: flex;
  flex-direction: column;
  .excel {
    flex: 1;
  }
}
.excel-html-box {
  width: 100%;
  height: 72vh;
  display: flex;
  margin-top: 2rem;
  flex-direction: column;
  .excel-html {
    flex: 1;
  }
}
</style>
