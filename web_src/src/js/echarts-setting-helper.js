/**
 * Echarts Setting Tools
 */
export default {
    // 柱形图
    getBarOption(barData={},dataUnit='ml',xAxisData=[],yAxisMaxNum=250,color=''){
      let intervalNum = parseInt(yAxisMaxNum/5);
      let yAxisSettingArray = [{name:'单位：'+dataUnit,axisLabelFormatter:'{value}',maxNum:yAxisMaxNum,intervalNum:intervalNum}];
      let chartDataArray = [];
      //实现思路：通过Object.keys()对象方法将对象的key转化为一个数组，再通过forEach遍历出数组的值，再通过[key]去获取对象的value值。
      Object.keys(barData).forEach(key=>{
        //yAxisSettingArray.push({name:key,axisLabelFormatter:'{value} ' + dataUnit,maxNum:250,intervalNum:50})
        chartDataArray.push({type:'bar', name:key, data:barData[key]});
      });
      return this.getOptionByData(chartDataArray,yAxisSettingArray,xAxisData,dataUnit,color);
    },
    // 线形图
    getLineOption(lineData={},dataUnit='°C',xAxisData=[],yAxisMaxNum=25,color=''){
      let intervalNum = parseInt(yAxisMaxNum/5);
      let yAxisSettingArray = [{name:'单位：'+dataUnit,axisLabelFormatter:'{value}',maxNum:yAxisMaxNum,intervalNum:intervalNum}];
      let chartDataArray = [];
      //实现思路：通过Object.keys()对象方法将对象的key转化为一个数组，再通过forEach遍历出数组的值，再通过[key]去获取对象的value值。
      Object.keys(lineData).forEach(key=>{
        //yAxisSettingArray.push({name:key,axisLabelFormatter:'{value} ' + dataUnit,maxNum:250,intervalNum:50})
        chartDataArray.push({type:'line', name:key, data:lineData[key]});
      });
      return this.getOptionByData(chartDataArray,yAxisSettingArray,xAxisData,dataUnit,color);
    },
    getOptionByData(chartData=[],yAxisSetting=[],xAxisSettingData=[],dataUnit='ml',color=''){
      let seriesData = [];
      let legendData = chartData.map(cData =>{
        let seriesItem = this.getSeriesSettingByType(cData.name,cData.type,cData.data,dataUnit);
        seriesData.push(seriesItem);
        return cData.name;
      });
      let yAxisData = yAxisSetting.map(yData =>{
        return this.getYAxisSettingByType(yData.name,yData.axisLabelFormatter,yData.maxNum,yData.intervalNum)
      });
      let optionSetting = {
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross',
            crossStyle: {
              color: '#999'
            }
          }
        },
        toolbox: {
          feature: {
            dataView: {show: true, readOnly: false},
            // magicType: {show: true, type: ['line', 'bar']},
            // restore: {show: true},
            // saveAsImage: {show: true}
          }
        },
        legend: {
          data: legendData
        },
        xAxis: [
          {
            type: 'category',
            data: xAxisSettingData,
            axisPointer: {
              type: 'shadow'
            }
          }
        ],
        yAxis:yAxisData,
        series: seriesData
      };
      if(color!=''){
        optionSetting.color = color;
      }
      return optionSetting;
    },
    getYAxisSettingByType(name='',axisLabelFormatter='{value} °C',maxNum=25,intervalNum=5){
      return {
        type: 'value',
        name: name,
        min: 0,
        max: maxNum,
        interval: intervalNum,
        axisLabel: {
          formatter: axisLabelFormatter
        }
      };
    },
    getSeriesSettingByType(name='',type='bar',numData=[],dataUnit='ml'){
      let valueFormatterData = function (value) {return value + ' '+dataUnit};
      return {
        name: name,
        type: type,
        tooltip: {
          valueFormatter: valueFormatterData,
        },
        data: numData,
        markPoint: {
          data: [
            { type: 'max', name: 'Max' },
            { type: 'min', name: 'Min' }
          ]
        },
        markLine: {
          data: [{ type: 'average', name: 'Avg' }]
        }
      };
    }
};
