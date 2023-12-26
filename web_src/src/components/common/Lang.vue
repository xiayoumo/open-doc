<template>
  <el-switch
      v-model="isChinese"
      inline-prompt
      style="--el-switch-on-color: #aa000a; --el-switch-off-color: #13ce66"
      active-text="English"
      inactive-text="中文"
  />
</template>

<script>
export default {
  name: "lang",
  data () {
    return {
      isChinese:true,
      isMobileDevice:false,
    }
  },
  watch:{
    "isChinese"(nowValue,oldValue){
      if(nowValue){
        this.handleLangCommand('zh');
      }else{
        this.handleLangCommand('en');
      }
    }
  },
  created() {
    this.isChinese = this.$i18n.locale == 'zh';
  },
  methods: {
    handleLangCommand(langType = 'zh') {
      this.$i18n.locale = langType
      localStorage.setItem('lang', langType);
    },
  },
  mounted() {
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice=true;
    }
  }
}
</script>

<style scoped lang="scss">
@import '~@/assets/base.scss';

</style>
