/**
 * Echarts Setting Tools
 */
export default {
  //消息窗口滚动到消息最新处
  scrollPageById(idName='right-side',scrollType='top',that){
    that.$nextTick(()=>{
      setTimeout(function(){
        let chat = document.getElementById(idName);
        if(scrollType=='top'){// 滚动到头（向上）
          chat.scrollTo({ top: 0, behavior: 'smooth' });
        }else{// 滚动到底（向下）
          chat.scrollTo({ top: chat.scrollHeight, behavior: 'smooth' });
        }
      },"200");
    })

  },
};
