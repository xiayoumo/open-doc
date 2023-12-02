<template>
  <div >
    <el-row :gutter="20">
      <el-col :span="24">
        <el-button type="text" class="edit-drag-btn" @click="changeDragStatus(!isDrag)"><i :class="isDrag?'el-icon-circle-close':'el-icon-rank'"></i> {{isDrag?$t('close_drag'):$t('open_drag')}}</el-button>
        <el-button type="text" class="edit-model-btn" @click="setLeftMenuEdit()"><i class="el-icon-close"></i> {{$t('readModel')}}</el-button>

      </el-col>
    </el-row>
    <el-row :gutter="20" class="new-bar" >
      <el-col :span="12"  :offset="0">
        <div class="edit-root-menu-left-btn">
          <el-popover :visible-arrow="false" ref="popoverAppendRoot" placement="bottom" width="260" trigger="click">
            <el-row :gutter="20">
              <el-col :span="16"><el-input @keyup.enter.native="appendRoot(treeData,addMenuInput)" v-model="addMenuInput" :placeholder="'请输入文档名称'"></el-input></el-col>
              <el-col :span="8">
                <el-button size="mini" type="text" class="save-menu-btn" @click="appendRoot(treeData,addMenuInput)">{{$t('save')}}</el-button>
                <el-button size="mini" type="text" class="save-menu-btn" @click="closePopover('popoverAppendRoot')">{{ $t('cancel') }}</el-button>
              </el-col>
            </el-row>
            <el-button title="添加根文档" slot="reference" type="text"  @click="addMenuInput=''"><i class="el-icon-document-add add-new-page-btn"></i>  {{$t('new_page')}}</el-button>
          </el-popover>
        </div>

      </el-col>
      <el-col :span="12" >
        <div class="edit-root-menu-right-btn">
          <el-popover :visible-arrow="false" ref="popoverAppendRootDir"  placement="bottom-end" width="260" trigger="click">
            <el-row :gutter="20">
              <el-col :span="16"><el-input @keyup.enter.native="appendRootDir(treeData,addMenuInput)" v-model="addMenuInput" :placeholder="'请输入文件夹名称'"></el-input></el-col>
              <el-col :span="8">
                <el-button size="mini" type="text" class="save-menu-btn" @click="appendRootDir(treeData,addMenuInput)">{{$t('save')}}</el-button>
                <el-button size="mini" type="text" class="save-menu-btn" @click="closePopover('popoverAppendRootDir')">{{ $t('cancel') }}</el-button>
              </el-col>
            </el-row>
            <el-button title="添加根文件夹" slot="reference" type="text" @click="addMenuInput=''"><span class="iconfont icon-xinjianwenjianjia add-new-page-btn"></span>  {{$t('new_catalog')}}</el-button>
          </el-popover>
        </div>
      </el-col>
    </el-row>

    <el-tree
      :data="treeData"
      node-key="id"
      :class="isDrag?'menu-tree-drag-box':'menu-tree-box'"
      :empty-text="$t('empty_menu_tip')"
      :expand-on-click-node="false"
      default-expand-all
      :default-expanded-keys="defaultExpandedKeys"
      @node-drop="handleDrop"
      :draggable="isDrag"
      :allow-drop="allowDrop"
    >
      <span class="custom-tree-node" slot-scope="{ node, data }">
        <div class="tree-node-title" :title="node.label">
          <span v-if="data.children&&data.level==1" class="iconfont icon-24gl-folderOpen edit-menu-title-icon" ></span>
          <span v-if="data.children&&data.level==2" class="iconfont icon-24gl-folderMinus edit-menu-title-icon" ></span>
          <span v-if="data.children&&data.level==3" class="iconfont icon-24gl-folderStar edit-menu-title-icon" ></span>
          <i v-if="!data.children" class="el-icon-document edit-menu-title-doc-icon"></i>　
          <span class="edit-menu-title-box"><div :ref="'page-'+data.id" :class="'edit-menu-title '+(data.children?'':'edit-menu-doc-title')">{{ node.label }}</div></span>
        </div>
        <span class="menu-edit-btn-group">
          <el-popover :visible-arrow="false" v-if="data.children && data.level<3" :ref="'popoverAppendDir'+data.id" ref="popoverAppendDir" placement="bottom-end" width="260" trigger="click">
            <el-row :gutter="20">
              <el-col :span="16"><el-input @keyup.enter.native="appendDir(data,addMenuInput)" v-model="addMenuInput" :placeholder="'请输入文件夹名称'"></el-input></el-col>
              <el-col :span="8">
                <el-button size="mini" type="text" class="save-menu-btn" @click="appendDir(data,addMenuInput)">{{ $t('save') }}</el-button>
                <el-button size="mini" type="text" class="save-menu-btn" @click="closePopover('popoverAppendDir'+data.id)">{{ $t('cancel') }}</el-button>
              </el-col>
            </el-row>
            <span title="添加文件夹" slot="reference"  @click="addMenuInput=''" class="iconfont icon-xinjianwenjianjia edit-menu-btn"></span>
          </el-popover>

          <el-popover :visible-arrow="false" v-if="data.children" :ref="'popoverAppend'+data.id" placement="bottom-end" width="260" trigger="click">
            <el-row :gutter="20">
              <el-col :span="16"><el-input @keyup.enter.native="append(data,addMenuInput)" v-model="addMenuInput" :placeholder="'请输入文档名称'"></el-input></el-col>
              <el-col :span="8">
                <el-button size="mini" type="text" class="save-menu-btn" @click="append(data,addMenuInput)">{{ $t('save') }}</el-button>
                 <el-button size="mini" type="text" class="save-menu-btn" @click="closePopover('popoverAppend'+data.id)">{{ $t('cancel') }}</el-button>
              </el-col>
            </el-row>
            <i title="添加文档" slot="reference" @click="addMenuInput=''" class="el-icon-document-add edit-menu-btn"></i>
          </el-popover>

          <el-popover :visible-arrow="false" :ref="'popoverSaveMenu'+data.id" placement="bottom-end" width="260" trigger="click">
            <el-row :gutter="10">
              <el-col :span="15"><el-input @keyup.enter.native="saveMenu(data)" v-model="nowMenuInput" placeholder="请输入内容"></el-input></el-col>
              <el-col :span="9">
                <el-button size="mini" type="text" class="save-menu-btn" @click="saveMenu(data)">{{$t('save')}}</el-button>
                <el-button size="mini" v-if="!data.children&&!isMobileDevice" type="text" class="save-menu-btn" @click="closePopover('popoverSaveMenu'+data.id)&goEditPage(data.id)">{{ $t('edit_page') }}</el-button>
                <el-button size="mini" v-if="data.children" type="text" class="save-menu-btn" @click="closePopover('popoverSaveMenu'+data.id)">{{ $t('cancel') }}</el-button>
              </el-col>
            </el-row>
            <span :title="$t('edit')" slot="reference" @click="()=>editMenu(data)" class="iconfont icon-bianji edit-menu-btn"></span>
          </el-popover>

          <el-popconfirm v-if="!data.children||data.children.length==0" :visible-arrow="false" placement="top-end" @confirm="()=>remove(node, data)" :title="'将删除 '+(data.children?$t('new_catalog'):$t('new_page'))+'：【 '+data.label+' 】，'+$t('comfirm_delete')" >
            <span :title="$t('delete')" slot="reference" class="iconfont icon-shanchusekuai edit-menu-btn"></span>
          </el-popconfirm>
        </span>
      </span>
    </el-tree>
  </div>
</template>

<script>
import httpToolHelper from '@/js/http-tool-helper';
import pageHelper from '@/js/page-helper'

let id = 1000;
export default {
  props:{
    item_info:'',
  },
  data() {
    return {
      isDrag:false,
      defaultExpandedKeys:[],
      nowMenuInput:'',
      addMenuInput:'',
      treeData:[],//[
                  //   {id: 1, level:1, parentId:0, label: '一级 1', children: [
                  //     {id: 4, level:2, parentId:1, label: '二级 1-1', children: [{id: 9, level:3, parentId:4, label: '三级 1-1-1'}]}
                  //   ]}
                  // ]
      lastAddPageData:[],
      isMobileDevice:false,
    }
  },
  methods: {
    changeDragStatus(targetStatus=false){
      if(targetStatus){
        let tipMessage = this.$t('open_drag_model_tip');
        this.$confirm(tipMessage, {confirmButtonText: this.$t('confirm'), showCancelButton:false, type: 'success'
        }).then(() => {
          this.isDrag = targetStatus;
        });
      }else{
        let tipMessage = this.$t('close_drag_model_tip');
        this.$confirm(tipMessage, {confirmButtonText: this.$t('confirm'), showCancelButton:false, type: 'success'
        }).then(() => {
          this.isDrag = targetStatus;
        });
      }
    },
    async saveDir(catName='',catId=0,parentCatId=0,sNumber=99) {
      var that = this;
      var url = '/api/catalog/save';
      var loading = that.$loading();
      let params = {
        item_id:this.item_info.item_id,
        cat_id:catId,
        parent_cat_id:parentCatId,
        cat_name:catName,
        s_number:sNumber,
      };
      let res = await httpToolHelper.execPost(url,params);
      setTimeout(() => {
        loading.close();
      }, 500);// 1 s
      if(res.error_code !== 0){
        that.$alert(res.error_message);
      }
      return res.data;
    },
    goEditPage(page_id=0){
      var that =this;
      that.$emit('editPage',page_id);
      // 追加选中样式
      that.$nextTick(()=>{
        let activeClassName = 'edit-menu-title-active';
        const refs = this.$refs;
        const prefix = "page-";
        const refsWithPrefix = Object.keys(refs).filter(ref => ref.startsWith(prefix));
        refsWithPrefix.map(refId=>{
          if(that.$refs[refId].classList.contains(activeClassName)){
            that.$refs[refId].classList.remove(activeClassName);
          }
        });
        // 追加活跃样式
        that.$refs['page-'+page_id].classList.add(activeClassName);
      });
    },
    async deleteDir(catId=0){
      var that = this ;
      var url = '/api/catalog/delete';
      var loading = that.$loading();
      let res = await httpToolHelper.execPost(url,{cat_id:catId,item_id:this.item_info.item_id});
      setTimeout(() => {
        loading.close();
      }, 500);// 1 s
      if(res.error_code !== 0){
        that.$alert(res.error_message);
        return false;
      }else{
        return true;
      }
    },
    async deletePage(pageId=0){
      var that = this ;
      var url = '/api/page/delete';
      var loading = that.$loading();
      let res = await httpToolHelper.execPost(url,{page_id:pageId});
      setTimeout(() => {
        loading.close();
      }, 500);// 1 s
      if(res.error_code !== 0){
        that.$alert(res.error_message);
        return false;
      }else{
        return true;
      }
    },
    async updatePageMsg(pageId=0,title='',catId=0,itemId=0,s_number=99){
      var that = this ;
      var loading = that.$loading();
      var url = '/api/page/updateMsgById';
      let paragramer = {
        page_id:pageId,
        item_id:itemId,
        s_number:s_number,
        page_title:title,
        cat_id:catId,
      };
      let res = await httpToolHelper.execPost(url,paragramer);
      setTimeout(() => {
        loading.close();
      }, 500);// 1 s
      if(res.error_code !== 0){
        that.$alert(res.error_message);
      }
      return res.data;
    },
    async createPage(pageId=0,title='',catId=0,itemId=0,s_number=99){
      var that = this ;
      var loading = that.$loading();
      var content = that.$t('welcome_tip');// 默认内容
      let res = await pageHelper.savePage(catId,itemId,pageId,content,title,s_number,this.item_info.item_use)
      setTimeout(() => {
        loading.close();
      }, 500);// 1 s
      if(res.error_code !== 0){
        that.$alert(res.error_message);
      }
      return res.data;
    },
    saveMenu(data){
      data.label = this.nowMenuInput;
      if(data.children){// 文件夹
        this.saveDir(data.label,data.id,data.parentId,data.order);
      }else{
        this.updatePageMsg(data.id,data.label,data.parentId,this.item_info.item_id,data.order);
      }
      this.closePopover('popoverSaveMenu'+data.id);// 关闭正打开的对话框
    },
    setLeftMenuEdit(){
      // 关闭编辑
      this.$emit("setEditLeftMenu",false); //increment: 随便自定义的事件名称   第二个参数是传值的数据
    },
    async appendRootDir(data,title) {
      if(title==''){
        title = this.$t('default_new_dir');
      }
      let newCat = await this.saveDir(title,0,0,99);
      const newChild = { id: newCat.cat_id, label: title,level:1, parentId:0,order:99, children: [] };
      data.push(newChild);
      this.closePopover('popoverAppendRootDir');// 关闭正打开的对话框
    },
    async appendRoot(data,title) {
      if(title==''){
        title = this.$t('default_new_doc');
      }
      let newPage = await this.createPage(0,title,0,this.item_info.item_id,99);
      const newChild = { id: newPage.page_id, label: title ,level:1, parentId:0,order:99 };
      data.push(newChild);
      this.closePopover('popoverAppendRoot');// 关闭正打开的对话框
      // 直接进入编辑
      this.goEditPage(newPage.page_id);
    },
    async appendDir(data,title) {
      if(title==''){
        title = this.$t('default_new_dir');
      }
      let newCat = await this.saveDir(title,0,data.id,99);
      const newChild = { id: newCat.cat_id, label: title,level:data.level+1, parentId:data.id,order:99, children: [] };
      if (!data.children) {
        this.$set(data, 'children', []);
      }
      data.children.push(newChild);
      this.defaultExpandedKeys = [data.id];
      this.closePopover('popoverAppendDir'+data.id);// 关闭正打开的对话框
    },
    async append(data,title) {
      if(title==''){
        title = this.$t('default_new_doc');
      }
      let newPage = await this.createPage(0,title,data.id,this.item_info.item_id,99);
      const newChild = { id: newPage.page_id, label: title,level:data.level+1,order:99, parentId:data.id};
      if (!data.children) {
        this.$set(data, 'children', []);
      }
      data.children.push(newChild);
      this.defaultExpandedKeys = [data.id];
      this.closePopover('popoverAppend'+data.id);// 关闭正打开的对话框
      // 直接进入编辑
      this.goEditPage(newPage.page_id);
    },
    closePopover(popoverRefsId=''){
      this.$refs[popoverRefsId].doClose();// 关闭正打开的对话框
    },
    editMenu(data){
      this.nowMenuInput = data.label;
    },
    async remove(node, data) {
      let execResult = data.children?await this.deleteDir(data.id):await this.deletePage(data.id);
      if(execResult){
        const parent = node.parent;
        const children = parent.data.children || parent.data;
        const index = children.findIndex(d => d.id === data.id);
        children.splice(index, 1);
      }
    },
    handleDrop(draggingNode, dropNode, dropType, ev) {
      // draggingNode 被拖对象，dropNode拖入对象
      let targetNodeData = [];
      if(dropType=='inner'){ // 拖入时
        draggingNode.data.level = dropNode.data.level+1;
        draggingNode.data.parentId = dropNode.data.id;
        targetNodeData = dropNode.childNodes;
      }else{ // 拖动到before或after时(跨级拖出)
        draggingNode.data.level = dropNode.data.level;
        draggingNode.data.parentId = dropNode.data.parentId;

        if(dropType=='before'){
          draggingNode.data.order = parseInt(dropNode.data.order)-1;// order 越小越靠前，默认是99，都一样大按id
        }
        if(dropType=='after'){
          draggingNode.data.order = parseInt(dropNode.data.order)+1;// order 越小越靠前，默认是99，都一样大按id
        }
        targetNodeData = dropNode.parent.childNodes;
      //  console.log(draggingNode)
      }
      // 初始化全部新节点的排序以及归属信息
      let itemId = this.item_info.item_id;// 项目id
      let sameGroupCount = targetNodeData.length;
      targetNodeData.map((item,iIndex)=>{
        let newOrder = (99-sameGroupCount)+iIndex+1;// 越小越靠前展示，一样大按id
       // console.log(newOrder+' '+item.data.label)
        if(item.data.children){// 文件夹
          this.saveDir(item.data.label,item.data.id,item.data.parentId,newOrder);
        }else{// 文档
          this.updatePageMsg(item.data.id,item.data.label,item.data.parentId,itemId,newOrder);
        }
      })
      this.defaultExpandedKeys = [dropNode.data.id];// 设置拖入展开
      console.log('tree drop: ', dropNode.label, dropType ,dropNode.data.order,draggingNode.data.order);
    },
    //拖拽时判定目标节点能否被放置。type 参数有三种情况：'prev'、'inner' 和 'next'，分别表示放置在目标节点前、插入至目标节点和放置在目标节点后
    // 拖拽时判定目标节点能否被放置
    // 'prev'、'inner' 和 'next'，分前、插入、后
    allowDrop(draggingNode, dropNode, type) {
      // draggingNode 被拖对象，dropNode拖入对象
      // if (draggingNode.data.level === dropNode.data.level) {
      //   if (draggingNode.data.parentId === dropNode.data.parentId) {
      if (draggingNode.data.parentId === dropNode.data.parentId) {// 同文件夹
            if(type === "prev" || type === "next"){
              return true;
            }
            if(type === "inner"){
              if(dropNode.data.children){
                if(!draggingNode.data.children){// 拖动文档
                  return true;
                }else{// 拖动文件夹
                  return this.checkDragTargetDir(draggingNode,dropNode);
                }
              }
            }
      }else{// 跨文件夹
          if(type === "prev" || type === "next"){
            if(dropNode.data.level==4&&draggingNode.data.children){
              return false;
            }
            if(dropNode.data.level < draggingNode.data.level){// 向外拖
              return true;
            }
            if(dropNode.data.level > draggingNode.data.level){// 向内拖
              if(draggingNode.data.children){// 被拖对象为文件夹时
                return this.checkDragTargetDir(draggingNode,dropNode);
              }
            }
            return true;
          }
          if(type === "inner" && dropNode.data.children && !draggingNode.data.children){
            return true;
          }
      }

          return false;
        // } else {
        //   return false;
        // }
      // } else {
      //   // 不同级进行处理
      //   return false;
      // }
    },
    checkDragTargetDir(draggingNode,dropNode){
      let sourceDirLevelCount = this.getFloors(draggingNode);
      if(dropNode.level==2 && sourceDirLevelCount<=2){
        return true;
      }
      if(dropNode.level==3 && sourceDirLevelCount<=1){
        return true;
      }
      return false;
    },
    // 树形结构的深度
    getFloors(root, onePathDeep = 0, deepArr = []){
      if (!root || typeof root !== 'object') return 0
      if (!root.childNodes.length) {
        deepArr.push(onePathDeep)
      } else {
        onePathDeep++
        for (let i = 0; i < root.childNodes.length; i++) {
          this.getFloors(root.childNodes[i], onePathDeep, deepArr)
        }
      }
      return Math.max(...deepArr);
    },
    // 递归遍历树,通过map遍历直接修改原数组数据，数组其他的数据不变
    getNewTree(catalogsData=[],pageData=[],level=1,tempTreeData=[]){
      pageData.map(val=>{
        // addtime: "1697127458",author_uid: "1",cat_id: "722",page_id: "3930",page_title: "用户注册"
        tempTreeData.push({label:val.page_title,id:val.page_id,level:level,parentId:val.cat_id,order:val.s_number});
      });
      catalogsData.map(item=>{
          // addtime: "1697468051",cat_id: "729",cat_name: "自动化文档",catalogs: [],item_id: "36",level: "2",
          // pages: [{page_id: "3954", author_uid: "1", cat_id: "729", page_title: "自动生成API文档", addtime: "1697623792",…},…]
          // parent_cat_id: "0",s_number: "99"
          let tempData = {label:item.cat_name,id:item.cat_id,level:level,parentId:item.parent_cat_id,order:item.s_number,children:[]};
          if((item.hasOwnProperty('catalogs')&&item.catalogs.length>0)||item.pages.length>0){
            let catalogsData = item.hasOwnProperty('catalogs')&&item.catalogs.length>0?item.catalogs:[];
            tempData.children = this.getNewTree(catalogsData,item.pages,level+1);
          }
          tempTreeData.push(tempData);
      })
      return tempTreeData;
    }
  },
  mounted () {
    var that = this ;
    let menuData = that.item_info.menu;
    if(menuData.catalogs.length>0||menuData.pages.length>0){
      that.treeData = that.getNewTree(menuData.catalogs,menuData.pages,1,[]);
    }
    //根据屏幕宽度进行响应(应对移动设备的访问)
    if( this.isMobile() ||  window.screen.width< 1000){
      this.isMobileDevice=true;
    }
  }
};
</script>

<style lang="scss">
@import '~@/components/common/base.scss';
.el-aside .el-tree {
  background: $theme-grey-color;
  margin-top: 1rem;
}
.menu-edit-btn-group{
  float: right;
}
.menu-tree-box{
  .menu-edit-btn-group{
    display: none;
  }
  .el-tree-node__content{
    height: 38px;
    cursor: auto;
    &:hover,&:focus,&:link,&:visited{
      background: $theme-border-color;
      color: $theme-color;
      font-weight: 500;
      .menu-edit-btn-group {
        display:block;
      }
    }
  }
}
.menu-tree-drag-box{
  .menu-edit-btn-group{
    display: none;
  }
  .el-tree-node__content{
    height: 38px;
    cursor: n-resize;
    &:hover,&:focus,&:link,&:visited{
      background: $theme-border-color;
      color: $theme-color;
      font-weight: 500;
      .menu-edit-btn-group {
        display:block;
      }
    }
  }
}
.el-tree__empty-text {
  font-size: 16px;
  width: 90%;
}
.el-popper[x-placement^=bottom] .popper__arrow, .el-popper[x-placement^=bottom] .popper__arrow::after {
  border-bottom-color: #ffffff;
}
</style>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';

.edit-menu-title-active{
  font-weight: bold !important;
  color: $theme-right-msg-color !important;
}
.tree-node-title{
  max-width: 130px;
  width: 8rem;
}
.edit-menu-title-box{
  display: inline-flex;
}
.edit-menu-title-doc-icon{
  color: $theme-words-color;
}
.edit-menu-title-icon{
  color: $theme-color;
  font-weight: bold;
  font-size: 16px;
}
.edit-menu-title-box .edit-menu-doc-title{
  color: $theme-color;
  font-weight: 400;
}
.edit-menu-title{
  @include text-auto-thumbnail;// 文字自动缩略
  width: 130px;
  color: $theme-color;
  font-weight: bold;
}

.edit-root-menu-right-btn{
  padding-right:18%;
  @include content-card;
}
.edit-root-menu-left-btn{
  padding-left:30%;
  @include content-card;
}
.save-menu-btn{
  padding: 8px 0px;
}
.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
}
.edit-menu-btn{
  padding: 7px 1px;
  font-size: 14px;
  margin:0px;
  &:hover{
    cursor:pointer;
    color: $theme-right-msg-color;
  }
}
.add-new-page-btn{
  font-size: 16px;
}

.edit-drag-btn{
  margin-left: 20px;
  padding: 8px 5px;
  margin-top: 17px;
  font-size: 13px;
  margin-bottom: 5px;
}
.edit-model-btn{
  float: right;
  margin-right: 20px;
  padding: 8px 5px;
  margin-top: 17px;
  font-size: 13px;
  margin-bottom: 5px;
}
</style>
