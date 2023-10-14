
// Vuex分成五个部分：
// 1.State：单一状态树  //可以理解成定义属性
// 2.Getters：状态获取    //可以理解成取值，也就是get方法
// 3.Mutations：触发同步事件    //同步的赋值
// 4.Actions：提交mutation，可以包含异步操作    //异步的赋值（但是其实就是在Mutations的基础上包装了一层）
// 5.Module：将vuex进行分模块
const base = {
	 state: {
	    nowHeadTitle:'',// 当前项目名称
      showHeadMenu:false,// 头部菜单
      showLeftMenu:true,// 右侧菜单栏
      isAdmin:false,// 是否管理员
      showHeadCountMenu:false,// 头部统计菜单
      nowItemCode:'',// 当前项目标识码
      isShowDashboard:false, // 是否展示统计页面
      userInfo:{}, // 当前登录账信息
	},
	mutations:{
	    SET_NOW_HEAD_TITLE: (state, data) => {
        state.nowHeadTitle = data;
	    },
      SET_SHOW_HEAD_MENU: (state, data) => {
        state.showHeadMenu = data;
      },
      SET_SHOW_LEFT_MENU: (state, data) => {
        state.showLeftMenu = data;
      },
      SET_IS_ADMIN: (state, data) => {
        state.isAdmin = data;
      },
      SET_SHOW_HEAD_COUNT_MENU: (state, data) => {
        state.showHeadCountMenu = data;
      },
      SET_NOW_ITEM_CODE: (state, data) => {
        state.nowItemCode = data;
      },
      SET_IS_SHOW_DASHBOARD: (state, data) => {
        state.isShowDashboard = data;
      },
      SET_USER_INFO: (state, data) => {
        state.userInfo = data;
      },

	},
	actions: {
      SetNowHeadTitle({ commit }, data) {
          commit('SET_NOW_HEAD_TITLE', data);
      },
      SetShowHeadMenu({ commit }, data) {
        commit('SET_SHOW_HEAD_MENU', data);
      },
      SetShowLeftMenu({ commit }, data) {
        commit('SET_SHOW_LEFT_MENU', data);
      },
      SetIsAdmin({ commit }, data) {
        commit('SET_IS_ADMIN', data);
      },
      SetShowHeadCountMenu({ commit }, data) {
        commit('SET_SHOW_HEAD_COUNT_MENU', data);
      },
      SetNowItemCode({ commit }, data) {
        commit('SET_NOW_ITEM_CODE', data);
      },
      SetIsShowDashboard({ commit }, data) {
        commit('SET_IS_SHOW_DASHBOARD', data);
      },
      SetUserInfo({ commit }, data) {
        commit('SET_USER_INFO', data);
      },
	},

}
//store.dispatch('SetValidity', validityKey,token).then(() => {
//     store.getters.validityKey,
//     store.getters.validityEnd,// 授权最后有效日期
//     store.getters.isValidity,// 当前是否有效授权
//  })
export default base;
