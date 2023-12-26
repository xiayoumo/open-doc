<template>
  <div ref="chartDom" ></div>
</template>

<script>
import "../../../public/static/js/echarts-theme-open-doc.js";
import echarts from "@/js/echarts";
import debounce from "lodash/debounce";
import { addListener, removeListener} from "resize-detector";

export default {
  props: {
    option: {
      type: Object,
      default: ()=> {}
    }
  },
  watch: {
    option: {
      handler(val) {
        this.chart.setOption(val);
      },
      deep: true
    }
  },
  created() {
    this.resize = debounce(this.resize, 300);
  },
  mounted() {
    this.renderChart();
     addListener(this.$refs.chartDom, this.resize);
  },
  beforeUnmount() {
     removeListener(this.$refs.chartDom, this.resize);
     this.chart.dispose();
     this.chart = null;
  },
  methods:{
    resize(){
      this.chart.resize();
    },
    renderChart() {
      this.chart = echarts.init(this.$refs.chartDom,'open-doc');
      this.chart.setOption(this.option);
    }
  },
}
</script>
<style>
</style>
