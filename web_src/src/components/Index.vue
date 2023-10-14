<template>
  <div class="hello">

    <div class="block">
      <div class="row header  ">
        <div class="right pull-right">
          <ul class="inline pull-right">
          <li ><router-link :to="link">{{link_text}}</router-link></li>
                </ul>
          </div>
        </div>
      <div
        v-loading="pageLoading"
        element-loading-body="true"
        element-loading-background="rgba(0, 0, 0, 0.2)"
        class="content-box">
        <el-row class="index-card-box-head">
          <el-col :span="24">
            <el-card class="head-box-card">
<!--              <div class="card-text-item">-->
                <div class="head-figure"></div>
                <div class='head-modal'>
                    <p>
                      <img src="../../static/logo/b_64.png" alt=""> <span class="common-card-title card-title-1"> {{$t("section_title1")}}</span>
                    </p>
                  <p><span class="common-card-content card-content-1"  ><div class="typing">{{$t('section_description1')}}</div></span></p>
                    <p>
                      <a class="el-button go-show-btn" href="http://open-doc.docker-sky.cn/item-show/cb7b18" target="_blank">{{$t("demo")}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <a class="el-button go-show-btn" href="http://open-doc.docker-sky.cn/item-show/5e4c22" target="_blank" >{{$t("help")}}&nbsp;</a>
                    </p>
                </div>
<!--              </div>-->
            </el-card>
          </el-col>
        </el-row>
        <el-row class="index-card-box">
          <el-col class="index-card-box-col" v-for="(srcUrl,sIndex) in srcList" :key="'s-'+sIndex"  :xs="24" :sm="24" :md="12" :lg="12" :xl="6">
                <el-card :class="(showBox[sIndex+1]?'show-card':'no-show-card')+' box-card suit'">
                  <div class="figure" :style="getFigureClass(sIndex+1,80)"></div>
                  <div class='modal'>
                    <el-row>
                      <el-col :span="6">
                        <el-image class="home-card-img" :src="srcUrl" :initial-index="sIndex" :preview-src-list="srcList"></el-image>
                      </el-col>
                      <el-col :span="18" class="card-text-box">
                        <p><span :class="'common-card-title card-title-'+(sIndex+2)"> {{$t("section_title"+(sIndex+2))}}</span></p>
                        <p><span :class="'common-card-content card-content-'+(sIndex+2)" v-html="$t('section_description'+(sIndex+2))"></span></p>
                      </el-col>
                    </el-row>
                  </div>
                </el-card>
          </el-col>
        </el-row>
        <el-row class="index-card-box-footer">
          <el-col class="index-card-box-col" v-for="(srcSecondUrl,ssIndex) in srcSecondList" :key="'ss-'+ssIndex" :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
            <el-card :class="(showBox2[ssIndex+1]?'show-card':'no-show-card')+' box-card suit'" >
              <div class="figure" :style="getFigureClass(ssIndex+1,90)"></div>
              <div class='modal'>
                <el-row>
                  <el-col :span="6">
                    <el-image class="home-card-img" :src="srcSecondUrl" :initial-index="ssIndex" :preview-src-list="srcSecondList"></el-image>
                  </el-col>
                  <el-col :span="18" class="card-text-box">
                    <p><span :class="'common-card-title card-title-'+(ssIndex+9)"> {{$t("section_title"+(ssIndex+9))}}</span></p>
                    <p><span :class="'common-card-content card-content-'+(ssIndex+9)" v-html="$t('section_description'+(ssIndex+9))"></span></p>
                  </el-col>
                </el-row>
              </div>
            </el-card>
          </el-col>
        </el-row>
      </div>
    </div>


  </div>
</template>

<script>
export default {
  name: 'Index',
  created() {

  },
  data () {
    return {
      pageLoading:false,
      showBox:{
        1:false,
        2:false,
        3:false,
        4:false
      },
      showBox2:{
        1:false,
        2:false,
        3:false
      },
      height: '',
      link:'',
      link_text:'',
      srcList:[
        '../../static/images/api.png',
        '../../static/images/shujuzidian.png',
        '../../static/images/shuomingwendan.png',
        '../../static/images/tuanduixiezuo.png',
      ],
      srcSecondList:[
        '../../static/images/chanpingwendan.png',
        '../../static/images/bangongriji.png',
        '../../static/images/gongzuozongjie.png',
      ],
      timeIndex:1,
      timer:'',
      timer2:'',
    }
  },
  methods:{
    getFigureClass(num=1,baseNum=50){
      return 'background-size: '+(baseNum*num)+'%;';
    },
    startShow(that){
      that.timer = setInterval(this.showCard, 1000,that.srcList.length,1); // 注意: 第一个参数为方法名的时候不要加括号;
    },
    showCard(showDataLength=0,execRowNum=1){
      this.pageLoading = false;
      if(this.timeIndex <= showDataLength){
        if(execRowNum==1){
          this.showBox[this.timeIndex] = true;
        }
        if(execRowNum==2){
          this.showBox2[this.timeIndex] = true;
        }
        this.timeIndex++;
        if(this.timeIndex > showDataLength){
          if(execRowNum==1){
            clearInterval(this.timer);
            this.timeIndex = 1;
            this.timer2 = setInterval(this.showCard, 2000,this.srcSecondList.length,2); // 注意: 第一个参数为方法名的时候不要加括号;
          }else{
            clearInterval(this.timer2);
          }
        }
      }
    },
    getHeight(){
       if (window.innerHeight){
          var winHeight = window.innerHeight;
       }
       else if ((document.body) && (document.body.clientHeight)){
          var winHeight = document.body.clientHeight;
       }
        this.height = winHeight+'px' ;
      }
  },
  mounted () {

    var that = this ;
    that.pageLoading = true;
    this.getHeight();
    that.link = '/user/login';
    that.link_text = that.$t("index_login_or_register");
    this.get_user_info(function(response){
      if (response.data.error_code === 0 ) {
        that.link = '/item/index';
        that.link_text = that.$t("my_item");
      }
    });
    this.startShow(that);
  },
  beforeDestroy() {
    clearInterval(this.timer);
    clearInterval(this.timer2);
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
  /* 逐字打印的打字特效 */
  .typing{
    animation: typewriter 30s steps(1000) forwards;
    display: inline-block;
    vertical-align: middle;
    overflow: hidden;
    white-space: nowrap;
    font-family: monospace;//等宽字体
    line-height:30px;
    &:hover{
      color: $theme-color;
      font-weight: bold;
    }
  }
  /* 定义动画 */
  @keyframes typewriter{
    from{width: 0;}
    to{width: 100%;}
  }
  .head-box-card{
    min-height: 25vh;
  }
  .head-figure{
    background: url('../../static/images/beijinzhuanyong.jpg');
    background-size: 70%;
    height: 16vh;
    width: 100%;
    filter: blur(10px)
  }
  .head-modal{
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0px; left: 0px;
    color: white;
    text-align: center;
  }
  .index-card-box-col{
    padding: 10px 30px;
  }
  img {
    transform: perspective(400px) rotateY(28deg);
  }
  img:hover{
    transform: perspective(400px) rotateY(-28deg);
  }
  .box-card {
    padding-left: 20px;
    transform: perspective(400px) rotateY(13deg);
  }
  .box-card:hover {
    transform: perspective(400px) rotateY(0deg);
    .common-card-title,.common-card-content{
      color: $theme-color;
      font-weight: bold;
    }
  }
  .box-card,.head-box-card{
    background-image: linear-gradient(0deg,#fff,#f3f5f8);
    border: 2px solid #fff;
    box-shadow: 8px 8px 20px 0 rgba(55,99,170,.1), -8px -8px 20px 0 #fff;
    border-radius: 4px;
  }
  .no-show-card{
    display: none;
  }
  .show-card {
    display: block; /* 显示元素 */
    animation: fadeIn 2s ease-out; /* 渐显动画效果 */
  }
  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }
  .suit{
    position: relative
  }
  .figure{
    background: url('../../static/images/beijinzhuanyong.jpg');
    background-size: 150%;
    height: 16vh;
    width: 100%;
    filter: blur(16px)
  }
  .modal{
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0px; left: 0px;
    background: rgba(0,0,0,.2);
    color: white;
    text-align: center;
  }
  .card-text-box{
    padding: 0px 20px;
  }
  .home-card-img{
    width: 100%;
    padding: 20px;
    border-radius: 15px;
    height: 16vh;
  }
  .card-text-item-child{
    position: relative;
  }
  .go-show-btn{
     @include yes-full-btn;
  }
  .content-box{
    padding-top: 10vh;
    background: rgba(0,0,0,.1);
    height: 100%;
    min-height: 90vh;
  }
  .card-text-item{
    text-align: center;
  }
  .common-card-title{
    font-size: 1.5rem;
    font-weight: bold;
  }
  .common-card-content{
    //font-size: 1rem;
    line-height: 2rem;
    text-align: center;
  }
  .card-title-1,.card-content-1{
    color: rgb(21, 24, 34);
  }
  .card-title-2,.card-content-2{
      color: #2C606A;
  }
  .card-title-3,.card-content-3{
    color: #f90;
  }
  .card-title-4,.card-content-4{
    color: #7CBD9D;
  }
  .card-title-5,.card-content-5{
    color: #A77DC2;
  }
  .card-title-9,.card-content-9{
    color: #85CE92;
  }
  .card-title-10,.card-content-10{
    color: #4BBFC3;
  }
  .card-title-11,.card-content-11{
    color: #1bbc9b;
  }
  .index-card-box-head{
    margin-top: 20px;
    padding: 20px 6vh;
  }
  .index-card-box{
    margin-top: 10px;
    padding: 20px 0px 20px 5vh;
  }
  .index-card-box-footer{
    margin-top: 10px;
    padding: 20px 0px 20px 8vh;
  }
  .el-carousel__item {
    text-align: center;
    font: 25px "Microsoft Yahei";
    color: #fff;
  }

  .header{
     background: rgb(21, 24, 34);
     padding-right: 100px;
     padding-top: 30px;
     font-size: 18px;
     position: fixed;
     right: 0;
     left: 0;
     z-index: 1030;
     margin-bottom: 0;
  }
  .header a {
      color: white;
      font-size: 12px;
      font-weight: bold;
  }

</style>
