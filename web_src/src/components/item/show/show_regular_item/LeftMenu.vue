<template>
  <div >
    <el-menu  @select="select_menu"
      background-color="#fafafa"
      text-color=""
      active-text-color="#008cff"
      :default-active="item_info.default_page_id"
      :default-openeds='openeds'
    >
      <el-row :gutter="20">
        <el-col :span="17">
          <el-input
            class="search-keyword-input"
            @keyup.enter.native="input_keyword"
            :placeholder="$t('input_keyword')"
            v-model="keyword">

          </el-input>
        </el-col>
        <el-col :span="7">
          <div class="new-bar" v-if="item_info.ItemPermn && item_info.is_archived < 1 ">
            <el-tooltip class="item" effect="dark" :content="$t('new_page')" placement="top-start">
              <i class="el-icon-plus add-new-page-btn" @click="new_page"></i>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" :content="$t('new_catalog')" placement="top-start">
<!--                <i class="el-icon-folder" @click="mamage_catalog"></i>-->
              <span class="iconfont icon-xinjianwenjianjia add-new-page-btn" @click="mamage_catalog"></span>
              <!--              <img src="static/images/folder.png" @click="mamage_catalog" class="icon-folder add-new-page-btn">-->
            </el-tooltip>
          </div>
        </el-col>
      </el-row>

      <!-- 一级页面 -->
        <el-menu-item  v-if="menu.pages.length " v-for="(page ,index) in menu.pages" :index="page.page_id" :key="page.page_id" >
          <i class="el-icon-document" style="margin-left:10px;"></i>{{page.page_title}}
        </el-menu-item>

        <!-- 目录开始 -->
      <el-submenu  v-if="menu.catalogs.length" v-for="(catalog2 ,catalog_index) in menu.catalogs" :index="catalog2.cat_id" :key="catalog2.cat_id">
        <!-- 二级目录名 -->
        <template slot="title"><span class="iconfont icon-24gl-folderOpen" ></span> {{catalog2.cat_name}}</template>

        <!-- 二级目录的页面 -->
        <el-menu-item-group v-if="catalog2.pages" v-for="(page2 ,page2_index) in catalog2.pages"  :key="page2.page_id">
          <el-menu-item :index="page2.page_id"><i class="el-icon-document"></i>{{page2.page_title}}</el-menu-item>
        </el-menu-item-group>

        <!-- 二级目录下的三级目录 -->
        <el-submenu  v-if="catalog2.catalogs.length" v-for="(catalog3 ,catalog_index3) in catalog2.catalogs" :index="catalog3.cat_id" :key="catalog3.cat_id">
          <template slot="title"><span class="iconfont icon-24gl-folderMinus" ></span> {{catalog3.cat_name}}</template>
          <!-- 三级目录的页面 -->
          <el-menu-item  v-if="catalog3.pages" v-for="(page3 ,page3_index) in catalog3.pages"  :index="page3.page_id" :key="page3.page_id">
            <i class="el-icon-document"></i>{{page3.page_title}}
          </el-menu-item>

            <!-- 三级目录下的四级目录 -->
            <el-submenu  v-if="catalog3.catalogs.length" v-for="(catalog4 ,catalog_index4) in catalog3.catalogs" :index="catalog4.cat_id" :key="catalog4.cat_id">
              <template slot="title"><span class="iconfont icon-24gl-folderStar" ></span>  {{catalog4.cat_name}}</template>
              <!-- 四级目录的页面 -->
              <el-menu-item  v-if="catalog4.pages" v-for="(page4 ,page4_index) in catalog4.pages" :index="page4.page_id"  :key="page4.page_id">
                <i class="el-icon-document"></i>{{page4.page_title}}
              </el-menu-item>
            </el-submenu>
        </el-submenu>
      </el-submenu>
    </el-menu>
  </div>
</template>


<script>
  import Editormd from '@/components/common/Editormd'
  export default {
  props:{
    get_page_content:'',
    item_info:'',
    search_item:''
  },
    data() {
      return {
        defaultActiveIndex:'',
        keyword:'',
        openeds:[],
        menu:''
      }
    },
  components:{
    Editormd
  },
  methods:{
    //选中菜单的回调
    select_menu(index, indexPath){
      this.change_url(index);
      this.get_page_content(index);
    },
    new_page(){
      var url = '/page/edit/'+this.item_info.item_id+'/0';
      this.$router.push({path:url});
    },

    mamage_catalog(){
      var url = '/catalog/'+this.item_info.item_id;
      this.$router.push({path:url});
    },

    //改变url
    change_url(page_id){
        var base_url = '';
        var item_domain = '';
        var domain = this.item_info.item_domain ? this.item_info.item_domain : this.item_info.item_id ;
        this.$router.replace({
            path: '/item-show/'+domain,
            query: {page_id:page_id}
        });
    },

    input_keyword(){
      this.search_item(this.keyword);
    }

  },
  mounted () {
    var that = this ;
    this.menu = this.item_info.menu ;
    var item_info = this.item_info ;
    //默认展开页面
    if (item_info.default_page_id > 0 ) {
       that.defaultActiveIndex = item_info.default_page_id;
       that.select_menu(item_info.default_page_id);
      if (item_info.default_cat_id4) {
        that.openeds = [ item_info.default_cat_id4,item_info.default_cat_id3, item_info.default_cat_id2, item_info.default_page_id];
      }
      else if (item_info.default_cat_id3) {
        that.openeds = [ item_info.default_cat_id3, item_info.default_cat_id2, item_info.default_page_id];
      }
      else if (item_info.default_cat_id2) {
        that.openeds = [ item_info.default_cat_id2, item_info.default_page_id];
      };
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
  .search-keyword-input{
    border-radius: 0px;
    margin: 15px 10px 10px 10px;
  }
  .el-header {
    color: $theme-light-black-color;
    line-height: 60px;
  }

  .el-aside {
    color: $theme-light-black-color;
    position:fixed;
    //height: calc(100% - 20px);
  }
  .el-aside::-webkit-scrollbar-thumb{
    /*滚动条里面小方块*/
    border-radius: 10px;
    height: 20px;
    background-color: $theme-btn-other-hover-color;
  }
  .el-aside::-webkit-scrollbar{
    /*滚动条整体样式*/
    width: 7px; /*高宽分别对应横竖滚动条的尺寸*/
  }
.el-input-group__append button.el-button{
    background-color: #ffffffa3;

  }
.new-bar{
  font-size: 20px;
  margin-top: 15px;
  margin-bottom: 5px;
}
//.new-bar i{
//  cursor:pointer ;
//}
.add-new-page-btn{
  @include icon-yes-btn;
}

//.new-bar i:first-child{
//  margin-right: 5px;
//}
.el-menu{
  border-right:none;
  min-height: 100vh;
}
.icon-folder{
  width: 18px;
  height: 15px;
  cursor: pointer;
}

.menu-icon-folder{
  margin-right: 5px;
  margin-top: -5px;
}

.el-menu-item, .el-submenu__title{
    height: 30px;
    line-height: 30px;
}

.el-submenu .el-menu-item {
    height: 40px;
    line-height: 40px;
}


</style>
