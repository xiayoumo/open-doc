<template>
  <div class="nav-box">
    <el-card class="markdown-nav-box latest-activity-card">
      <div class="latest-activity-card__badge">{{$t('quick_navigation')}}</div>
      <div>
        <i v-if="quickNavLoading" class="el-icon-loading tree-node-loading"></i>
        <el-empty v-if="markdownNavData.length==0" :description="$t('empty_data')" :image-size="100"  class="edit-model-box-empty"></el-empty>
        <el-timeline hide-timestamp class="markdown-nav-timelie">
          <el-timeline-item
            v-for="(activity, index) in markdownNavData"
            :class="activity.showType!='root'?'timeline-item-sub-title':''"
            :key="index">
            <span :name="'nav-timelie-item-'+index" @click="scrollIntoViewByMarkdownNav(activity.headId,index)" >{{activity.content}}</span>
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-card>
  </div>
</template>

<script>

if (typeof window !== 'undefined') {
  var $s = require('scriptjs');
}
export default {
  props: {
    jqueryMinPath: {
      type: String,
      default: '../../../static/editor.md/../jquery.min.js',
    },

  },
  watch: {
    option: {
      handler(val) {
        this.chart.setOption(val);
      },
      deep: true
    },
  },
  created() {
  },
  mounted() {
    //加载依赖""
    // $s([`${this.jqueryMinPath}`],()=>{
    // });
  },
  data() {
    return {
      quickNavLoading:true,
      isScrollByNav:false,//是否便捷目录点击的滚动
      offsetTopByHtmlHead:false,// 是否由加粗实现的定位滚动（默认是h1等）
      offsetTopDefault:209,
      offsetTopArray:[],
      markdownNavData: [],
      doubleTitleData:{name:'',navId:'',headId:''},// 重复出现的标题名称
    }
  },
  methods:{
    setQuickNavLoading(val){
      var that = this;
      if(val){
        that.$emit('refrash',true);
        that.quickNavLoading = false;
      }
      that.quickNavLoading = val;
    },
    scrollIntoViewByMarkdownNav(headId='',cIndex=0){
      this.scrollIntoViewByClick(headId);
      this.setNowMarkdownNav(cIndex);
    },
    setNowMarkdownNav(navNowIndex){
      $('.el-timeline-item__node').css('background-color','#E4E7ED');
      let className = 'nav-timelie-item-' + navNowIndex;
      $('.el-timeline-item__content span').removeClass('is-active');
      let obj= document.querySelector("span[name='"+className+"']");
      $(obj).addClass('is-active');
      $(obj).parent().parent().prev().css('background-color','rgb(170, 0, 10)');
    },
    scrollIntoViewByClick(headId=''){
      this.setContentTitleSelected(headId);
      this.scrollIntoViewBySelector(headId);
    },
    scrollIntoViewBySelector(headId=''){
      var that = this;
      that.isScrollByNav = true;
      document.querySelector("#"+headId).scrollIntoView({block:'center',behavior:'smooth'});
      setTimeout(function (){
        that.$set(that, 'isScrollByNav', false)
      }, 800)
    },
    setContentTitleSelected(headId=''){
      $('.scroll-into-view').removeClass('scroll-into-view');
      $("#"+headId).addClass('scroll-into-view');
    },
    initScrollNav(){
      var  that = this;
      //获取页面元素的高度
      that.offsetTopArray = that.getOffsetTopArray()

      //监听toc 或 tocm 菜单点击事件
      $(".toc-menu-span-btn").click(function(){
        let clickText = $(this).text();
        that.markdownNavData.some((navData,n) =>{
          if(navData.content== clickText){
            that.scrollIntoViewByMarkdownNav(navData.headId,n);
            return true;
          }
        });
      });
      // 提示是否重复
      if(that.doubleTitleData.navId > ''){
        let warningMsg = that.$t("double_title_tips")+'：'+that.doubleTitleData.name;
        that.$alert(warningMsg, '温馨提示', {
          confirmButtonText: '前往编辑',
          customClass:'message-box',
          callback: action => {
            that.$emit("doGoEdit",true) //increment: 随便自定义的事件名称   第二个参数是传值的数据
          }
        });
        //滚动到重复的地方
        that.scrollIntoViewByMarkdownNav(that.doubleTitleData.headId,that.doubleTitleData.navId);
      }else{
        that.setNowMarkdownNav(0); // 默认显示第一个菜单
      }
    },
    onScroll(currentScrollTop){// 父页面滚动调用
      if(!this.isScrollByNav){// 当为按钮触发不进行快捷导航的设置监听
        let that = this;
        if(that.offsetTopArray.length>0){
          let offsetTopArr = that.offsetTopArray;
          // 定义当前点亮的导航下标
          let addNum = that.offsetTopDefault + 200;
          currentScrollTop = Math.ceil(currentScrollTop) + addNum;// 小数进一,当为点击快速导航滚动时，不需要加距离高度
          for (let n = 0; n < offsetTopArr.length; n++) {
            // 如果 scrollTop 大于等于第 n 个元素的 offsetTop 则说明 n-1 的内容已经完全不可见
            // 那么此时导航索引就应该是 n 了
            if (currentScrollTop >= offsetTopArr[n].offsetTop) {
              if (n < (offsetTopArr.length - 1) && currentScrollTop < offsetTopArr[n + 1].offsetTop) {
                that.setNowMarkdownNav(n);
                that.setContentTitleSelected(offsetTopArr[n].headId);
                break;
              }
            }
          }
        }
      }
    },
    toScrollBottom(){// 父页面滚动到底部调用
      if(this.offsetTopArray.length>0 && !this.isScrollByNav) {// 当为按钮触发不进行快捷导航的设置监听
        let nowIndex = this.markdownNavData.length - 1;
        this.setNowMarkdownNav(nowIndex);
        this.setContentTitleSelected(this.markdownNavData[nowIndex].headId);
      }
    },
    getOffsetTopArray(){// 获取当前全部标题的对于页面顶部的距离
      // 所有锚点元素的 offsetTop
      const offsetTopArr = []
      let navContents = document.querySelectorAll("a.reference-link");
      if(navContents.length == 0){
        navContents = document.querySelectorAll("strong");
        if(navContents.length > 0){
          navContents.forEach((item,i) => {
            let temp={
              headId:item.id,
              offsetTop:Math.floor($(item).offset().top),
              name:item.innerText.replace(/<.*?>/g,""),
            }
            offsetTopArr.push(temp)
          })
        }
      }else{
        navContents.forEach((item,i) => {
          let temp={
            headId:item.parentNode.id,
            offsetTop:Math.floor($(item).offset().top),
            name:item.name.replace(/<.*?>/g,""),
          }
          offsetTopArr.push(temp)
        })
      }
      return offsetTopArr;
    },
    getMarkdownNavData(){
      // 获取便捷菜单内容
      var that = this;
      that.markdownNavData = [];
      that.doubleTitleData.name = '';
      that.doubleTitleData.navId = '';
      that.doubleTitleData.headId = '';
      let maxHeadTag = '';
      let nodeHeadType = {};
      let headHtmlData = document.querySelectorAll(".reference-link");
      if(headHtmlData.length == 0){ // strong 等
        headHtmlData = document.querySelectorAll("strong");
        if(headHtmlData.length>0){
          [].forEach.call(headHtmlData, function(e,i){
            let tempName = e.innerHTML.replace(/<.*?>/g,"");
            let headId = 'strong-'+i;
            $(e).attr('id',headId);
            let tempData = {
              headId:headId,
              content:tempName,
              showType:'root'
            };
            that.markdownNavData.unshift(tempData);
          });
        }
      }else{// h1 等标题
        let nameDoubleCheck = [];
        [].forEach.call(headHtmlData, function(e,i){
          let tempName = e.name.replace(/<.*?>/g,"");
          let headData = e.parentNode.id.split('-');
          if(nameDoubleCheck.indexOf(tempName)!==-1){
            that.doubleTitleData.name = tempName;
            that.doubleTitleData.navId = i;
            that.doubleTitleData.headId = e.parentNode.id;
          }
          nodeHeadType[tempName] = headData[0];// 登记类型
          if(maxHeadTag==''){
            maxHeadTag = headData[0];
          }else{
            if(headData[0] < maxHeadTag){
              maxHeadTag = headData[0];
            }
          }
          nameDoubleCheck.push(tempName);// 重复标题登记检查
        });
        [].forEach.call(headHtmlData, function(e){
          let tempName = e.name.replace(/<.*?>/g,"");
          let tempData = {
            headId:e.parentNode.id,
            content:tempName,
            showType:'root'
          };
          if(nodeHeadType[tempName] > maxHeadTag){ // 当不为一级标题时，需要约进
            tempData.showType = 'sub';
          }
          that.markdownNavData.unshift(tempData);
        });
      }
      that.markdownNavData.reverse()
      return that.markdownNavData;
    }
  },
  beforeDestroy() {
    //清理所有定时器
    for (var i = 1; i < 999; i++){
      window.clearInterval(i);
    };
    // 必须移除监听器，不然当该vue组件被销毁了，监听器还在就会出错
    window.removeEventListener('scroll', this.onScroll)
  }
}
</script>
<style lang="scss">
@import '~@/components/common/base.scss';

.tree-node-loading{
  margin: 0 auto;
  display: table;
  margin-top: 20vh;
  font-size: 18px;
}
.timeline-item-sub-title{
  padding-left: 20px;
}
.table-auto-overflow-x{
  overflow-x: auto;
}
.scroll-into-view{
  color:$theme-right-msg-color !important;
  span{
    color:$theme-right-msg-color !important;
  }
}
.markdown-nav-timelie{
  margin-top: 2rem !important;
  padding-left: 5px;
  width: 100%;
  //max-width: 300px;
  min-width: 200px;
  min-height: 425px;
  height: 48vh;
  overflow-y: auto;
  @include scroll-bar-box;
}
.markdown-nav-timelie .el-timeline-item {
  padding-bottom: 5px;
}
.markdown-nav-timelie .el-timeline-item__wrapper {
  //padding-left: 18px;
}
.el-timeline-item__wrapper .el-timeline-item__content{
  cursor: pointer;
  color: $theme-color;
  font-weight: bold;
  &:hover,&:focus,&:visited{
    color:$theme-right-msg-color !important;
  }
}
.el-timeline-item__node--normal {
  //left: -2px;
}
.el-timeline-item__content .is-active{
  color:$theme-right-msg-color !important;
  font-weight: bold;
}

.nav-box{
  position: fixed;
  top: 190px;
  width: 20%;
}
.markdown-nav-box{
  margin-left: 0px;
  position: relative;
  height: 60vh;
  background-color: #fff;
  margin-right: 10px;
}
.latest-activity-card{
  overflow: visible !important;
  //height: 236px;
  background: #fff;
  box-shadow: 8px 8px 20px 0 rgba(55,99,170,.1);
  //position: relative;
  background-size: 100vh;
  background-repeat: no-repeat;
  background-position: right -92px bottom -115px;
  border: 2px solid #fff;
  z-index: 0;
  padding: 22px;
  box-sizing: border-box;
  transition: all .3s ease-in-out;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  //background-image: url('../../../static/images/nav-bg.png');
  background-position: right -41px bottom -146px;
}
.doc-container .latest-activity-card:before {
  content: "";
  z-index: -1;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  border-radius: 4px;
  background: linear-gradient(96.75deg,#fafcff 2.24%,hsla(0,0%,100%,.06) 103.12%);
}

.latest-activity-card__badge:before {
  content: "";
  width: 14px;
  height: 26px;
  position: absolute;
  background-size: auto 100%;
  top: 0;
  left: -8px;
  background-image: url(../../../static/images/nav-head-badge-corner.svg);
}
.latest-activity-card__badge {
  position: absolute;
  right: -2px;
  top: -6px;
  height: 26px;
  background: $theme-color;
  border-radius: 0 6px 0 6px;
  font-weight: bold;
  font-size: 14px;
  line-height: 18px;
  color:$theme-grey-color;
  text-align: center;
  box-sizing: border-box;
  padding: 4px 10px;
}

.markdown-nav-box .el-divider{
  margin-top: 10px;
}
.quick-navigation-title{
  font-weight: bold;
  font-size: 14px;
  color: $theme-color;
}

</style>
