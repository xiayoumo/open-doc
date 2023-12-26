
<template>
  <div class="gotop-box"
    v-show="status"
    @click="gototop">
    	<i class="el-icon-caret-top" :title="$t('back_to_top')"></i>
  </div>
</template>
<script>
export default {

  data () {
    return {
      status: false,
      scrollTop: 0,
      timer: null,
      speed:30
    }
  },
  mounted () {
    let _t = this;
    window.onscroll = function () {
      _t.scrollTop = document.body.scrollTop ? document.body.scrollTop : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : null
      _t.status = _t.scrollTop && _t.scrollTop > 0;
    }
  },
  methods: {
    gototop () {
      let _t = this;
      _t.timer = setInterval(function(){
        _t.scrollTop -= 100
        if (_t.scrollTop < 100) {
          _t.scrollTop = 0;
          _t.status = false;
          clearInterval(_t.timer);
        }
        scrollTo(0, _t.scrollTop)
      }, this.speed)
    }
  },
  unmounted () {
    clearInterval(this.timer)
  }
}
</script>
<style scoped lang="scss">
@import '~@/assets/base.scss';
.gotop-box {
	position: fixed;
	cursor: pointer;
	bottom: 50px;
	right: 5%;
	font-size: 50px;
	color: $theme-color;
}

</style>
