<template>
  <el-skeleton class="skeleton-template-box" :loading="pageLoading" animated  :throttle="1500">
    <template slot="template">
      <Skeleton :contentRow="15"></Skeleton>
    </template>
    <template>
      <Editormd v-if="page_use=='api'&&content" :key="page_id" :content="content" :pageType="pageType" :scrollBoxClass="scrollBoxClass"  ref="child" @doGoEdit="do_go_edit" type="html"></Editormd>
      <Tinymce v-if="page_use=='doc'&&content" :key="page_id" :tinymceContent="content" :pageType="pageType" :scrollBoxClass="scrollBoxClass" ref="child" @doGoEdit="do_go_edit" type="html" ></Tinymce>
      <Luckysheet v-if="page_use=='excel'&&content" :key="page_id" :sheetTitle="page_title"  :sheetContent="content"  :pageType="pageType" :scrollBoxClass="scrollBoxClass" ref="child"  @savePage="save_page" type="html"></Luckysheet>
      <el-empty v-if="content==''" :description="$t('empty_data')" :image-size="250"  class="edit-model-box-empty"></el-empty>
    </template>
  </el-skeleton>
</template>

<script>
import Editormd from '@/components/common/Editormd'
import Tinymce from '@/components/common/Tinymce'
import Luckysheet from '@/components/common/Luckysheet'
import Skeleton from '@/components/common/Skeleton' // 骨架屏

export default {
  name: "htmlView",
  props:{
    pageLoading:false,
    page_title:'',
    pageType:'',// single/many
    page_use:'',
    content:'',
    page_id:0,
    scrollBoxClass:''
  },
  components:{
    Editormd,
    Tinymce,
    Luckysheet,
    Skeleton
  },
  data() {
    return {
      isMobileDevice:false,
      // pageLoading:false
    }
  },
  methods: {
    do_go_edit() {
      this.$emit('goEdit',true);
      // this.goEdit = true;
    },
    save_page(page_content=''){
      this.$emit('savePage',page_content);
    },
    get_scroll(){
      if(!this.isMobileDevice&&this.page_use!='excel') {
        const ele = document.querySelector('.'+this.scrollBoxClass);
        let isBottom = ele.scrollTop + ele.clientHeight - ele.scrollHeight;
        if (isBottom < 0) {
          this.$refs.child.onScroll(ele.scrollTop);
        } else {
          this.$refs.child.toScrollBottom();
        }
      }
    }
  },
  mounted () {
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice=true;
    }
  }
}
</script>

<style scoped lang="scss">
@import '~@/components/common/base.scss';

.skeleton-template-box{
  width:100%;
  height: 72vh;
}
</style>
