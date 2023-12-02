<template>
  <div class="hello">
    <Header > </Header>

    <!-- 展示多文档项目 -->
    <ShowRegularItem  v-if="item_info && item_info.item_type == 1" @setNowPageId="set_now_page_id" :item_info="item_info" :get_item_menu="get_item_menu" :search_item="search_item">

    </ShowRegularItem>

    <!-- 展示单页项目 -->
    <ShowSinglePageItem :item_info="item_info" v-if="item_info && item_info.item_type == 2 ">

    </ShowSinglePageItem>

    <Footer> </Footer>

  </div>
</template>

<script>
  import ShowRegularItem from '@/components/item/show/show_regular_item/Index'
  import ShowSinglePageItem from '@/components/item/show/show_single_page_item/Index'
  import store from '@/store'
  export default {
    data() {
        return {
          now_page_id:0,
          page_id:0,
          item_id:0,
          item_info:'' ,
        }
      },
    components:{
        ShowRegularItem,
        ShowSinglePageItem
    },
    methods:{
        set_now_page_id(page_id=0){
          this.now_page_id = page_id;
        },
        //获取菜单
        get_item_menu(keyword){
            if (!keyword) {
              keyword = '' ;
            };
            var that = this ;
            var loading = that.$loading();
            var item_id = this.item_id;
            var page_id = this.now_page_id;
            var url = DocConfig.server+'/api/item/info';

            var params = new URLSearchParams();
            params.append('item_id',  item_id);
            params.append('keyword',  keyword);
            if ( !that.keyword) {
              params.append('default_page_id',page_id  );
            };
            that.axios.post(url, params)
              .then(function (response) {
                loading.close();
                if (response.data.error_code === 0 ) {
                  var json = response.data.data ;

                  if(page_id!=0){ // 当用户确实有请求具体页面id时
                    if (json.default_page_id <= 0 ) {// 当默认为0的页面时，自动拿menu中的第一个页面作为展示页面
                      if (json.menu.pages[0]) {
                        json.default_page_id = json.menu.pages[0].page_id
                      };
                    };
                  }
                  if(json.is_login){
                    store.dispatch('SetShowHeadCountMenu', true);
                  }
                  store.dispatch('SetNowItemCode', json.item_code);
                  that.item_info = json ;
                  document.title = that.item_info.item_name +"--OpenDoc";
                  if (json.unread_count > 0 ) {
                    that.$message({
                      showClose: true,
                      duration:10000,
                      dangerouslyUseHTMLString: true,
                      message: '<a href="/notice/index">你有新的未读消息，点击查看</a>'
                    });
                  };
                  // 设置项目title
                  store.dispatch('SetNowHeadTitle', json.item_name);
                  localStorage.setItem('head_title', json.item_name);
                  if(that.item_info.item_type == 2){// 单页面项目
                    store.dispatch('SetShowHeadMenu', false);
                  }
                }
                else if (response.data.error_code === 10307 || response.data.error_code === 10303 ) {
                  //需要输入密码
                  that.$router.replace({
                      path: '/item/password/'+item_id,
                      query: {page_id: page_id,redirect: that.$router.currentRoute.fullPath}
                  });
                }
                else{
                  that.$alert(response.data.error_message);
                }

              });
            //设置一个最长关闭时间
            setTimeout(() => {
              loading.close();
            }, 20000);
        },
        search_item(keyword){
          this.item_info = '';
          this.get_item_menu(keyword);
        }
    },
    mounted () {
      this.item_id = this.$route.params.item_id ? this.$route.params.item_id : 0;
      this.now_page_id = this.page_id = this.$route.query.page_id ? this.$route.query.page_id : 0  ;
      this.get_item_menu();
    },
    beforeDestroy(){
      this.$message.closeAll();
      /*去掉添加的背景色*/
      document.body.removeAttribute("class","grey-bg");
      document.title = "OpenDoc" ;
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';
.hello{
  background: $theme-grey-color;
}
</style>
