<template>
  <div class="login-footer">

    <span class="footer-pic">
      <img src="../../../static/logo/doc-log3.png" style="width:100px;height:36px;margin-bottom:-10px;" alt="">
    </span>
<!--    <a href="https://www.opendoc.com.cn/" class="other-web-a" target="_blank"> 友情推荐：ShowDoc</a>-->

    <div class="footer-copyright"><span>Copyright © 2019-2023 Open Doc. All Rights Reserved.</span> </div>
  </div>
</template>

<script>
export default {
  name: 'Footer',
  data () {
    return {
      msg: '尾部'
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
.beian-btn{
  color: $theme-words-color;
  font-size: 16px;
}
.other-web-a{
  color: $theme-grey-color;
  margin-top: 8px;
  float: right;
  margin-right: 3%;
}
.footer-copyright {
  color: $theme-words-color;
}
.login-footer {
  height: 60px;
  line-height: 25px;
  text-align: left;
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: $theme-color;
  border-top: 1px solid $theme-input-color;
  padding-left: 2%;
  padding-top: 10px;
  color: $theme-words-color;
  z-index: 999;
}
.footer-pic{
  font-size: 28px;
}
.beian-jump a{
  color: $theme-grey-color;
}
</style>
