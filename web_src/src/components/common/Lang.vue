<template>
  <el-dropdown @command="handleLangCommand">
      <span class="el-dropdown-link header-right-menu-btn">
        <span v-if="!isMobileDevice" :class="'iconfont swich-lang-icon '+nowLangIcon"></span> {{ nowLangTitle }} <i class="el-icon-arrow-down el-icon--right"></i>
      </span>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item v-for="(item) in langOption" :key="item.value" :command="item.value">
          <span :class="'iconfont swich-lang-icon '+item.icon"></span>　{{item.title}}
        </el-dropdown-item>
      </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
export default {
  name: "lang",
  data () {
    return {
      isMobileDevice:false,
      langOption:[
        {title:'中文',value:'zh',icon:'icon-zhongyingqiehuan-qiehuanzhongwen'},
        {title:'English',value:'en',icon:'icon-zhongyingqiehuan-qiehuanyingwen'}
      ],
      nowLang:'zh',
      nowLangTitle:'中文',
      nowLangIcon:'icon-zhongyingqiehuan-qiehuanzhongwen'
     // nowLangIcon:'icon-a-zhongyingwenzhongwen'
    }
  },
  created() {
    this.nowLang = this.$i18n.locale;
    this.langOption.map(item=>{
      if(item.value == this.nowLang){
        this.nowLangTitle = item.title;
        this.nowLangIcon = item.icon;
      }
    })
  },
  methods: {
    handleLangCommand(langType = 'zh') {
      this.nowLang = langType;
      this.langOption.map(item => {
        if (item.value == langType) {
          this.nowLangTitle = item.title;
          this.nowLangIcon = item.icon;
        }
      })
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
@import '~@/components/common/base.scss';

.header-right-menu-btn{
  margin-right: 12px;
  margin-left: 12px;
  color: $theme-grey-color !important;
  &:hover,&:focus{
    border: none;
    color: $theme-right-msg-color !important;
  }
}
.swich-lang-icon{
  font-size: 20px;
}
</style>
