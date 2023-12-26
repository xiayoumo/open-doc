<template>
		<el-dialog class="json-tool-box" top="6vh" width="50%" :close-on-click-modal="false" :title="$t('json_tools')" v-model="dialogFormVisible">
      <CodemirrorTool ref="codemirrorEditor" @inputData="setNowJson" :editorContent="content"></CodemirrorTool>
      <el-row class="check-json-box">
        <el-col>
          <div v-show="showCheckTip" :class="checkStatus?'jsonc-check-success':'jsonc-check-error'"><i :class="checkStatus?'el-icon-success':'el-icon-error'"></i> {{validateJsonResult}}</div>
        </el-col>
      </el-row>
      <template #footer>
          <el-button @click="cleanJson()">{{$t('clean_json')}}</el-button>
          <el-button type="primary" @click="validateJson(content)">{{$t('format_json')}}</el-button>
          <el-button-group class="button-group-box">
            <el-button type="primary"  @click="insertJson(content)" >{{$t('insert_json')}}</el-button>
            <el-button type="primary" @click="transform('data')">{{$t('insert_table')}}</el-button>
            <el-button type="primary" @click="transform('parameter')">{{$t('insert_parameter_json')}}</el-button>
          </el-button-group>
      </template>
		</el-dialog>
</template>

<script>
import jsonlint from '@/js/json-check.js';
import CodemirrorTool from '@/components/common/Codemirror';

export default {
  name: 'JsonToTable',
  props:{
  	formLabelWidth: '120px',
  	callback:'',
    jsonValue: {// jsonValue为json数据即输入框的初始值
      type: String,
      default: ''
    }
  },
  components: {
    CodemirrorTool
  },
  data () {
    return {
    	content:'',
    	json_table_data:'',
    	dialogFormVisible:false,
      outputTabelType:'markdown',// markdown/html
      validateJsonResult: '', // 校验结果
      showCheckTip:false,// 展示提示
      checkStatus:true
    }
  },
  methods:{
    handleInsertCommand(type){
      if(type=='insertJson'){
        this.insertJson(this.content);
      }
      if(type=='transformData'){
        this.transform('data');
      }
      if(type=='transformParameter'){
        this.transform('parameter');
      }
    },
    cleanJson(){
      this.$refs.codemirrorEditor.resetData();
      this.showCheckMessage('已清空.',true);
    },
    showCheckMessage(msg='',status=true,showCheckTip=true){
      this.showCheckTip = !showCheckTip;
      this.validateJsonResult = msg;
      this.checkStatus = status;
      this.showCheckTip = showCheckTip;
    },
    setNowJson(jsonData){
      this.content = jsonData;
    },
    validateJson (jsonContent) {
      if(jsonContent==''){
        this.showCheckMessage('json内容为空，请输入后再点击操作.',false);
        return false
      }else{
        try {
          if(this.checkStatus){// 只在第一次以及正确的情况进行替换
            jsonContent = jsonContent.replace(/{/ig,"{\n");
            jsonContent = jsonContent.replace(/",/ig,"\",\n");
            jsonContent = jsonContent.replace(/,"/ig,",\n\"");
            jsonContent = jsonContent.replace(/}/ig,"}\n");
          }
          // 验证通过
          let result = jsonlint.parse(jsonContent)
          this.showCheckMessage('通过校验.',true);
          // 验证通过时，使验证结果定时消失
          setTimeout(() => {
            this.showCheckMessage('',true,false);
          }, 3000)
         // if (this.isReformat) {
         this.content = JSON.stringify(result, null, '  ');
         // }
          return true
        } catch (e) {
          this.content = jsonContent;
          this.showCheckMessage(e,false);
          return false
        }
      }
    },
  	transform(htmlShowType='parameter'){// outputTabelType 为html时生效（data 数据表格 parameter参数表格）
      let checkResult = this.validateJson (this.content);
      if(checkResult){
        try {
          if(this.outputTabelType=='html'){ // tinymce
            // 参考文档https://baijiahao.baidu.com/s?id=1718085042154618064&wfr=spider&for=pc
            let isJson = this.isJsonString(this.content);
            if(isJson){
              this.json_table_data = this.jsonToHTMLTable(JSON.parse(this.content),htmlShowType);
            }else{
              this.$alert("输入json格式不正确，请重新调整后再提交解析.");
            }
          }else{ // markdown
            var jsonData = JSON.parse(this.content);
            this.json_table_data = '\n\n|参数|类型|描述|\n|:-------|:-------|:-------|\n';
            this.Change(jsonData);
            this.json_table_data += '\n';
          }
          this.callback(this.json_table_data);
        } catch (e) {
          this.$alert("Json解析失败，请联系管理员.");
        }
        this.dialogFormVisible = false;
      }
  	},
    insertJson(jsonData){
      let checkResult = this.validateJson(jsonData);
      if(checkResult) {
        try {
          if (this.outputTabelType == 'html') { // tinymce
            let language = 'javascript';
            let global$1 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');
            let newCode = global$1.DOM.encode(jsonData);
            var text = '<pre id="__new" class="language-' + language + '">' + newCode + '</pre>'
          } else {// markdown
            var formattedStr = JSON.stringify(JSON.parse(jsonData), null, 2);
            var text = "\n ``` \n " + formattedStr + " \n\n ```\n\n"; //
          }
          this.callback(text);
        } catch (e) {
          //非json数据直接显示
          this.callback(jsonData);
        }
        this.dialogFormVisible = false;
      }
    },
    // 判断的是否是JSON字符串
    isJsonString(str){
      if (typeof str == 'string') {
        try {
          var obj = JSON.parse(str);
          // 等于这个条件说明就是JSON字符串 会返回true
          if (typeof obj == 'object' && obj) {
            return true;
          } else {
            //不是就返回false
            return false;
          }
        } catch (e) {
          return false;
        }
      }
      return false;
    },
    jsonToHTMLTable(jsonData,showType='data'){//showType: data 数据表格 parameter参数表格
  	  if(showType=='data'){// 当为数组，即为数据表格
        return this.outputHtmlDataTable(jsonData);
      }
  	  if(showType=='parameter'){// 输出为api的parameter参数表格
        return this.outputHtmlParameterTable(jsonData);
      }
    },
    outputHtmlParameterTable(jsonData,level=1){
  	  let inputDataType =  this.getDataType(jsonData);
  	  if(inputDataType=='array' && jsonData.length>0){
        let firstItemData = jsonData.shift();
        let firstItemDataType = this.getDataType(firstItemData);
        if(firstItemDataType=='object'){
          jsonData = firstItemData;
        }
        if(firstItemDataType=='string'){// 当为纯数组时，直接序列化返回（非第一层数据）
          return JSON.stringify(jsonData);
        }
      }
      // 3.生成表头
      var thead = '<thead><tr><th>参数</th><th>名称</th><th>是否必填</th><th>类型</th><th>示例</th><th>说明</th></tr></thead>';
      // 4.生成表格内容
      var trString = '';
      for (var prop in jsonData) {
        let moreInfo = '';
        let isRequired = jsonData[prop]>''?'是':'否';
        let tempData = jsonData[prop];
        let dataType = this.getDataType(jsonData[prop]);
        if(dataType=='array'||dataType=='object'){
          if(dataType=='object' && Object.keys(tempData).length>0){
            // 最多只解析3层，超过三层不再解析
            moreInfo = level>3?JSON.stringify(tempData):this.outputHtmlParameterTable(tempData,level+1);
          }else if(dataType=='array' && tempData.length>0){
            // 最多只解析3层，超过三层不再解析
            moreInfo = level>3?JSON.stringify(tempData):this.outputHtmlParameterTable(tempData,level+1);
          }
        }
        tempData = JSON.stringify(tempData);
        var td = '<td>'+prop+'</td>' +
          '<td>'+prop+'</td>' +
          '<td>'+isRequired+'</td>' +
          '<td>'+dataType+'</td>' +
          '<td>'+tempData+'</td>' +
          '<td>'+moreInfo+'</td>';
        trString += '<tr>'+td+'</tr>';
      }
      var tbody = '<tbody>'+trString+'</tbody>';
      let tableClass = level==1?'json-html-table':'';
      let table = '<div class="'+tableClass+'"><table>'+thead + tbody +'</table><div><br>';
      return table;
    },
    getDataType(data){
  	  let type = typeof data;
  	  if(type == 'string'){
  	    if(!isNaN(data) && data>''){
          type = 'string(number)';
        }
      }
      if(type == 'object'){
        if(data == null){
          type = 'string';
        }else{
          if(Object.prototype.toString.call(data) === '[object Array]'){
            type = 'array';
          }
          if(Object.prototype.toString.call(data) === '[object object]'){
            type = 'object';
          }
        }
      }
      return type;
    },
    outputHtmlDataTable(jsonData,level=1){
      let inputDataType =  this.getDataType(jsonData);
      let headObject = {};
      if(inputDataType=='object' && Object.keys(jsonData).length>0){
        headObject = jsonData;
      }
      if(inputDataType=='array' && jsonData.length>0){
        headObject = jsonData[0];
        let firstItemDataType = this.getDataType(headObject);
        if(firstItemDataType=='string'){// 当为纯数组时，直接序列化返回（非第一层数据）
          return JSON.stringify(jsonData);
        }
      }
      // 3.生成表头
      var th = '';
      for (var prop in headObject) {
        th += '<th>'+prop+'</th>';
      }
      var thead = '<thead><tr>'+ th +'</tr></thead>';
      // 4.生成表格内容
      var trString = '';
      if(inputDataType=='array'){
        for (var i = 0; i < jsonData.length; i++) {
          var td = '';
          for (var prop in jsonData[i]) {
            let tempData = this.getDataTableChild(jsonData[i][prop],level);
            td += '<td>'+tempData+'</td>';
          }
          trString += '<tr>'+td+'</tr>';
        }
      }
      if(inputDataType=='object'){
          var td = '';
          for (var prop in jsonData) {
            let tempData = this.getDataTableChild(jsonData[prop],level)
            td += '<td>'+tempData+'</td>';
          }
          trString += '<tr>'+td+'</tr>';
      }
      var tbody = '<tbody>'+trString+'</tbody>';
      var table = '<table>'+thead + tbody +'</table><br>';
      return table;
    },
    getDataTableChild(nowData,level){
      let tempType = this.getDataType(nowData);
      let tempData = nowData;
      if(tempType=='object' && Object.keys(tempData).length>0){
        // 最多只解析3层，超过三层不再解析
        tempData = level>2?JSON.stringify(tempData):this.outputHtmlDataTable(tempData,level+1);
      }else if(tempType=='array' && tempData.length>0){
        // 最多只解析3层，超过三层不再解析
        tempData = level>2?JSON.stringify(tempData):this.outputHtmlDataTable(tempData,level+1);
      }else{
        tempData = JSON.stringify(tempData);
      }
      return tempData;
    },
  	Change(data){
  		var that =  this;
	    var level_str = "- ";
	    if (arguments.length > 1) {
	      var level;
	      arguments[1] > 0 ? level = arguments[1] : level = 1;
	      for (var i = 0; i < level; i++) {
	        level_str += "- ";
	      }
	    }

	    for (var key in data) {
	      var value = data[key];
	      var type = typeof(value);
	      if (type == "object") {
	        that.json_table_data += '| ' + level_str + key + ' |' + type + '  | 无 |\n';
	        if (value instanceof Array) {
	          var j = level + 1;
	          this.Change(value[0], j);
	          continue;
	        }
	        //else
	        //{
	        this.Change(value, level);
	        //}

	      } else {
	        that.json_table_data += '| ' + key + ' | ' + type + '| 无 |\n';
	      }
	  }
  	}
  }
}
</script>


<style lang="scss">
@import '~@/assets/base.scss';
.json-tool-box .el-dialog__body{
  padding: 0px;
}
.json-tool-box {
  height: 75vh;
}
.cm-s-mbo.CodeMirror{
  height: 58vh;
}
</style>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/assets/base.scss';

.button-group-box{
  margin-left: 1rem;
}
.jsonc-check-success{
  color: $theme-fourth-color;
}
.jsonc-check-error{
  color: $theme-red-color;
}
.check-json-box{
  margin: 1rem;
}

</style>
