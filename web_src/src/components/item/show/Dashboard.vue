<template>
    <div class="page-dashboard-box">
        <el-row :gutter="20" class="page-dashboard-row">
          <el-col  :xs="24" :sm="12" :md="6" :lg="6" :xl="6">
            <el-card class="page-dashboard-count-card">
              <p class="page-dashboard-count-main">今日活跃：<span class="page-dashboard-count-num">{{ projectCountData.todayActiveUser }}</span></p>
              <p class="page-dashboard-count-minor">历史活跃：<span class="page-dashboard-count-num">{{ projectCountData.activeUser }}</span> <span class="page-dashboard-unit">单位：人</span></p>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="12" :md="6" :lg="6" :xl="6">
            <el-card class="page-dashboard-count-card">
              <p class="page-dashboard-count-main">总页面：<span class="page-dashboard-count-num">{{ projectCountData.pageNum }}</span></p>
              <p class="page-dashboard-count-minor">今日新增：<span class="page-dashboard-count-num">{{ projectCountData.todayNewPageNum }}</span> <span class="page-dashboard-unit">单位：页</span></p>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="12" :md="6" :lg="6" :xl="6">
            <el-card class="page-dashboard-count-card">
              <p class="page-dashboard-count-main">今日访问：<span class="page-dashboard-count-num">{{ projectCountData.todayPageTime }}</span></p>
              <p class="page-dashboard-count-minor">今日访问（无登录）：<span class="page-dashboard-count-num">{{ projectCountData.todayPageTimeByNoLogin }}</span><span class="page-dashboard-unit">单位：次</span></p>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="12" :md="6" :lg="6" :xl="6">
            <el-card class="page-dashboard-count-card">
              <p class="page-dashboard-count-main">总访问：<span class="page-dashboard-count-num">{{ projectCountData.pageTime }}</span></p>
              <p class="page-dashboard-count-minor">总访问（无登录）：<span class="page-dashboard-count-num">{{ projectCountData.pageTimeByNoLogin }}</span><span class="page-dashboard-unit">单位：次</span></p>
            </el-card>
          </el-col>
        </el-row>
        <el-row :gutter="20" class="page-dashboard-row">
          <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
            <el-card class="page-dashboard-chart-card" v-loading="countDataLoading" element-loading-background="transparent" >
              <div class="page-dashboard-echart-box">
                <Echarts :option="everydayUserTimeOption" class="echarts-box" />
              </div>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
            <el-card class="page-dashboard-chart-card" v-loading="countDataLoading" element-loading-background="transparent" >
              <div class="page-dashboard-echart-box">
                <Echarts :option="everydayUserOption" class="echarts-box" />
              </div>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
            <el-card class="page-dashboard-chart-card" v-loading="countDataLoading" element-loading-background="transparent" >
              <div class="page-dashboard-echart-box">
                <Echarts :option="everydayPageOption" class="echarts-box" />
              </div>
            </el-card>
          </el-col>
        </el-row>
        <el-row v-if="item_info.ItemCreator||item_info.ItemCreator||isAdmin" :gutter="20" class="page-table-row">
          <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <el-card class="page-dashboard-table-card" v-loading="tableCountDataLoading" element-loading-background="transparent" >
              <div slot="header" class="page-dashboard-table-card-header">
                <span class="table-card-title">文档访问</span>
                <el-autocomplete
                  autosize
                  select-when-unmatched
                  class="page-dashboard-table-card-input"
                  v-model="searchPageKeyword"
                  popper-class="search-input-popper"
                  :fetch-suggestions="queryPageSearch"
                  placeholder="请输入标题"
                  :trigger-on-focus="false"
                  @select="handlePageSelect"
                ></el-autocomplete>
              </div>
              <div class="page-dashboard-echart-box">
                <el-table
                  :data="pageTableData"
                  height="250"
                  stripe
                  style="width: 100%">
                  <el-table-column
                    prop="page_id"
                    label="ID"
                    width="80">
                  </el-table-column>
                  <el-table-column
                    prop="title"
                    label="标题">
                  </el-table-column>
                  <el-table-column
                    prop="count"
                    width="80"
                    label="访问次数">
                  </el-table-column>
                  <el-table-column
                    prop="address"
                    label="归属目录">
                  </el-table-column>
                </el-table>
              </div>
            </el-card>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <el-card class="page-dashboard-table-card" v-loading="tableCountDataLoading" element-loading-background="transparent" >
              <div slot="header" class="page-dashboard-table-card-header">
                <span class="table-card-title">用户活跃</span>
                <el-autocomplete
                  select-when-unmatched
                  popper-class="search-input-popper"
                  class="page-dashboard-table-card-input"
                  v-model="searchUserKeyword"
                  :fetch-suggestions="queryUserSearch"
                  placeholder="请输入账号名或团队名称"
                  :trigger-on-focus="false"
                  @select="handleUserSelect"
                ></el-autocomplete>
              </div>
              <div class="page-dashboard-echart-box">
                <el-table
                  :data="userTableData"
                  height="250"
                  stripe
                  style="width: 100%">
                  <el-table-column
                    prop="user_id"
                    label="ID"
                    width="80">
                  </el-table-column>
                  <el-table-column
                    prop="name"
                    label="名称">
                  </el-table-column>
                  <el-table-column
                    prop="count"
                    label="总访问"
                    width="80">
                  </el-table-column>
                  <el-table-column
                    prop="week_count"
                    label="近一周访问"
                    width="100">
                  </el-table-column>
                  <el-table-column
                    prop="team_msg"
                    label="归属团队">
                  </el-table-column>
                </el-table>
              </div>
            </el-card>
          </el-col>
        </el-row>
    </div>
</template>

<script>
import * as echarts from 'echarts';
//引入Echart的包
import Echarts from "@/components/common/Echarts";
import echartsSettingHelper from "@/js/echarts-setting-helper";
import store from '@/store';

export default {
    computed: {
      isAdmin(){
        return store.getters.isAdmin;
      }
    },
    props:{
      item_info:'',
    },
    components:{
      Echarts,
    },
    watch: {
      projectEchartsData: {
        handler(val) {
          this.initEchars(val);
        },
        deep: true
      }
    },
    data() {
        return {
          searchUserKeyword:'',
          searchUserData:[],// 账号查询提示列表内容 [{ "value": "zhangsan", "user_id": "1" }]
          searchPageData:[],// 账号查询提示列表内容 [{ "value": "zhangsan", "user_id": "1" }]
          searchPageKeyword:'',
          pageTableCopyData:[],
          pageTableData:[
            {page_id: 1, title: '我是文档', count: 7, address: '数据/我是文档'},
            {page_id: 1, title: '我是文档', count: 7, address: '数据/我是文档'},
            {page_id: 1, title: '我是文档', count: 7, address: '数据/我是文档'},
            {page_id: 1, title: '我是文档', count: 7, address: '数据/我是文档'},
            {page_id: 1, title: '我是文档', count: 7, address: '数据/我是文档'},
            {page_id: 1, title: '我是文档', count: 7, address: '数据/我是文档'},
          ],
          userTableCopyData:[],
          userTableData:[
            {user_id: 1, name: '夏天', count: 7,week_count:3, team_msg: '数据平台团队'},
            {user_id: 1, name: '夏天', count: 7,week_count:3, team_msg: '数据平台团队'},
            {user_id: 1, name: '夏天', count: 7,week_count:3, team_msg: '数据平台团队'},
            {user_id: 1, name: '夏天', count: 7,week_count:3, team_msg: '数据平台团队'},
          ],
          everydayUserOption:{},
          everydayUserTimeOption:{},
          everydayPageOption:{},
          countDataLoading:true,
          tableCountDataLoading:true,
          projectCountData:{
              activeUser:0,// 活跃人数
              todayActiveUser:0,// 今日活跃人数
              pageTime:0,// 全部页面访问
              todayPageTime:0,// 今天页面访问
              pageTimeByNoLogin:0,// 全部页面访问(无登录)
              todayPageTimeByNoLogin:0,// 今天页面访问(无登录)
              pageNum:0,// 全部页面数量
              todayNewPageNum:0,// 今天新增数量
          },
          projectEchartsData:{
            everydayUser:{
              name:'活跃账号',
              data:[2.0, 2.2, 3.3, 4.5, 6.3, 10.2, 20.3, 23.4, 23.0, 16.5, 12.0, 6.2],
              xAxisData:['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
              dataUnit:'人',
            },
            everydayAllPage:{
              name:'总访问',
              data:[2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3],
              xAxisData:['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
              dataUnit:'次',
            },
            everydayPage:{
              name:'每日页面',
              data:[2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3],
              xAxisData:['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
              dataUnit:'页',
            },
          }
        }
      },
    created: function () {
       // console.log(this.itemId)
        this.getProjectCountByItemId();
    },
    mounted () {
        this.initEchars(this.projectEchartsData);
        this.getProjectTableCountByItemId();
    },
    methods:{
       queryPageSearch(queryString, cb) {
          var restaurants = this.searchPageData;
          var results = queryString ? restaurants.filter(this.createFilter(queryString,restaurants)) : restaurants;
          // 调用 callback 返回建议列表的数据
          cb(results);
          // 解决el-autocomplete弹出建议层错位问题(poper.js就是监听resize事件重新计算dom位置的)
          window.dispatchEvent(new Event('resize'))
       },
       queryUserSearch(queryString, cb) {
          var restaurants = this.searchUserData;
          var results = queryString ? restaurants.filter(this.createFilter(queryString,restaurants)) : restaurants;
          // 调用 callback 返回建议列表的数据
          cb(results);
          // 解决el-autocomplete弹出建议层错位问题(poper.js就是监听resize事件重新计算dom位置的)
          window.dispatchEvent(new Event('resize'))
       },
       createFilter(queryString,searchData) {
          return (searchData) => {
            return (searchData.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1);
          };
       },
       handlePageSelect(item) {
         if(item.value==''){
           this.pageTableData = this.pageTableCopyData;
         }else {
           let searchPageResultArray = [];
           if(!item.hasOwnProperty('page_id')){
               this.pageTableData.map(pageTable => {
                 if (pageTable.title.indexOf(item.value)!==-1) {
                   searchPageResultArray.push(pageTable);
                 }
               });
           }else{
               this.pageTableData.some(pageTable => {
                 if (pageTable.page_id == item.page_id) {
                   searchPageResultArray.push(pageTable);
                   return true;
                 }
               });
           }
           this.pageTableData = searchPageResultArray;
         }
       },
       handleUserSelect(item) {
         if(item.value==''){
           this.userTableData = this.userTableCopyData;
         }else{
           let searchUserResultArray = [];
           if(!item.hasOwnProperty('user_id')){
             this.userTableData.map(userTable => {
               if (userTable.name.indexOf(item.value)!==-1||userTable.team_msg.indexOf(item.value)!==-1) {
                 searchUserResultArray.push(userTable);
               }
             });
           }else{
             this.userTableData.some(userTable=>{
               if(userTable.user_id==item.user_id){
                 searchUserResultArray.push(userTable);
                 return true;
               }
             });
           }
           this.userTableData = searchUserResultArray;
         }
       },
       getProjectTableCountByItemId(){
          var that = this ;
          var url = DocConfig.server+'/api/item/getProjectTableCountByItemId';
          var params = new URLSearchParams();
          params.append('item_id', that.item_info.item_id);
          that.axios.post(url, params).then(function (response) {
            that.tableCountDataLoading = false;
            if (response.data.error_code === 0 ) {
              let res = response.data.data;
              that.pageTableCopyData = that.pageTableData = res.pageTableData;
              that.userTableCopyData = that.userTableData = res.userTableData;
              that.userTableData.map(userData=>{
                that.searchUserData.push({value: userData.name+' '+userData.team_msg,user_id:userData.user_id})
              });
              that.pageTableData.map(pageData =>{
                that.searchPageData.push({value: pageData.address,page_id:pageData.page_id})
              });
            }else{
              that.$alert(response.data.error_message);
            }
          });
       },
       getProjectCountByItemId(){
         var that = this ;
         var url = DocConfig.server+'/api/item/getProjectCountByItemId';
         var params = new URLSearchParams();
         params.append('item_id', that.item_info.item_id);
         that.axios.post(url, params).then(function (response) {
              that.countDataLoading = false;
              if (response.data.error_code === 0 ) {
                  let res = response.data.data;
                  that.projectEchartsData = res.projectEchartsData;
                  that.projectCountData = res.projectCountData;
              }else{
                that.$alert(response.data.error_message);
              }
         });
       },
       initEchars(projectData={}){ // 初始化echarts 图表数据
         //let projectData = this.projectEchartsData;
         let everydayUserData = projectData.everydayUser;
         let lineData = {};
         lineData[everydayUserData.name]=everydayUserData.data;
         let userColor = 'rgb(34,195,170)';
         this.everydayUserOption = echartsSettingHelper.getLineOption(lineData,everydayUserData.dataUnit,everydayUserData.xAxisData,everydayUserData.yAxisMaxNum,userColor);
         let everydayUserTimeData = projectData.everydayAllPage;
         let barData = {};
         barData[everydayUserTimeData.name]=everydayUserTimeData.data;
         let userTimeColor = '#4ea397';
         this.everydayUserTimeOption = echartsSettingHelper.getBarOption(barData,everydayUserTimeData.dataUnit,everydayUserTimeData.xAxisData,everydayUserTimeData.yAxisMaxNum,userTimeColor);
         let everydayPageData = projectData.everydayPage;
         let barData2 = {};
         barData2[everydayPageData.name]=everydayPageData.data;
         let pageColor = '#d0648a';
         this.everydayPageOption = echartsSettingHelper.getBarOption(barData2,everydayPageData.dataUnit,everydayPageData.xAxisData,everydayPageData.yAxisMaxNum,pageColor);
       },
    }
  }
</script>
<style lang="scss">
@import '~@/components/common/base.scss';
  //.page-dashboard-table-card .el-card__header{
  //  background: $theme-color;
  //  color: $theme-grey-color;
  //}
  .search-input-popper{
    width: auto !important;
    max-width: 300px;
  }
  .page-dashboard-table-card .el-table__header-wrapper{
    background: $theme-grey-color;
  }
</style>
<style scoped lang="scss">
@import '~@/components/common/base.scss';

  .page-dashboard-table-card-input{
    width:60%;
    max-width: 180px;
    float: right;
  }
  .page-dashboard-table-card-input .el-input .el-input__inner{
    height:30px
  }
  .table-card-title{
    font-weight: bold;
    font-size: 16px;
  }
  .page-dashboard-unit{
    float: right;
    padding-top: 10px;
    font-size: 12px;
  }
  .page-dashboard-echart-box{
    background: $theme-grey-color;
  }
  .page-dashboard-chart-card,.page-dashboard-table-card{
    background: $theme-grey-color;
  }
  .page-dashboard-count-minor{
    color: $theme-words-color;
  }
  .page-dashboard-count-main .page-dashboard-count-num{
    font-size: 20px;
  }
  .page-dashboard-count-main{
    font-size: 18px;
    font-weight: bold;
  }
  .page-dashboard-count-card{
    min-height: 60px;
    border-radius: 10px;
    background:$theme-grey-color;
  }
  .page-dashboard-row{
    margin-bottom: 40px;
    padding-left: 2%;
    padding-right: 2%;
  }
  .page-table-row{
    padding-bottom: 40px;
    padding-left: 2%;
    padding-right: 2%;
  }
  .echarts-box{
    height: 350px;
    width: 100%
  }
  .page-dashboard-box{
      width: 100%;
      margin-top: 80px;
      margin-bottom: 20px;
      //margin: 80px 40px 20px 40px;
  }
</style>
