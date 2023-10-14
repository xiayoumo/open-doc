<template>
  <div class="hello">
     <Header > </Header>
     <div class="unfind-error-box">
        <div class="unfind-error-div">
           <el-row class="unfind-error-pic-row">
             <img class="unfind-error-pic" src="static/images/404.gif" />
           </el-row>
           <el-row class="unfind-error-callback-btn">
             <el-button type="primary" size="medium" @click="gobackUrl">返回</el-button>
           </el-row>
        </div>
     </div>
     <Footer> </Footer>
  </div>
</template>

<script>
export default {
  name: 'unfindPage',
  data () {
    return {
      msg: '尾部'
    }
  },
  methods:{
    gobackUrl(){
      this.$router.go(-1);
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
.unfind-error-pic-row{
  text-align: center;
}
.unfind-error-pic{
  height: 30vh;
  border-radius: 10px;
}
.unfind-error-callback-btn{
  padding-top: 25px;
  text-align: center;
}
.unfind-error-box{
  padding-top: 20vh;
  display: flex;
}
.unfind-error-div{
  margin: 0 auto;
}
</style>
