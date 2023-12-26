/**
 * page helper Tools
 */
import httpHelper from '@/js/http-tool-helper'

export default {
  //消息窗口滚动到消息最新处
  scrollPageById(idName='right-side',scrollType='top',that){
    that.$nextTick(()=>{
      setTimeout(function(){
        const chat = document.getElementById(idName);
        if(scrollType=='top'){// 滚动到头（向上）
          chat.scrollTo({ top: 0, behavior: 'smooth' });
        }else{// 滚动到底（向下）
          chat.scrollTo({ top: chat.scrollHeight, behavior: 'smooth' });
        }
      },"200");
    })

  },
  async savePage(cat_id=0,item_id=0,page_id=0,page_content='',page_title='',s_number=99,page_use='api'){
    const url = '/api/page/save';
    const params = {
      page_id:page_id,
      item_id:item_id,
      s_number:s_number,
      page_title:page_title,
      page_content:encodeURIComponent(page_content),
      is_urlencode:1,
      cat_id:cat_id,
      page_use:page_use
    };
    const res = await httpHelper.execPost(url, params);
    return res;
  },
  async getPage(page_id=0){
    const url = '/api/page/info';
    const params = {page_id:page_id};
    const res = await httpHelper.execPost(url, params);
    return res;
  },
};
