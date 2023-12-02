<!-- 更多模板 -->
<template>
    <div class="">
      <!-- 模板存放的地方 -->
      <div id="doc-api-doc-templ"  ref="doc_api_doc_templ" style="display:none">
          <p><strong>简要描述：</strong></p>
          <ul>
            <li>用户注册接口</li></ul>
          <p><strong>请求URL：</strong></p>
          <ul><li><pre class="language-markup" contenteditable="false">http://xx.com/api/user/register</pre></li></ul>
          <p><strong>请求方式：</strong></p>
          <ul>
            <li>POST</li></ul>
          <p><strong>参数：</strong></p>
          <table>
            <thead>
            <tr>
              <th style="text-align:left;">参数名</th>
              <th style="text-align:left;">必选</th>
              <th style="text-align:left;">类型</th>
              <th>说明</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td style="text-align:left;">username</td>
              <td style="text-align:left;">是</td>
              <td style="text-align:left;">string</td>
              <td>用户名</td>
            </tr>
            <tr>
              <td style="text-align:left;">password</td>
              <td style="text-align:left;">是</td>
              <td style="text-align:left;">string</td>
              <td>密码</td>
            </tr>
            <tr>
              <td style="text-align:left;">name</td>
              <td style="text-align:left;">否</td>
              <td style="text-align:left;">string</td>
              <td>昵称</td>
            </tr>
            </tbody>
          </table>
          <p><strong>返回示例</strong></p>
        <pre class=" language-javascript" contenteditable="false" data-mce-selected="1"><span class="token punctuation">{</span>
              <span class="token string">"error_code"</span><span class="token punctuation">:</span> <span class="token number">0</span><span class="token punctuation">,</span>
              <span class="token string">"data"</span><span class="token punctuation">:</span> <span class="token punctuation">{</span>
                  <span class="token string">"uid"</span><span class="token punctuation">:</span> <span class="token string">"1"</span><span class="token punctuation">,</span>
                  <span class="token string">"username"</span><span class="token punctuation">:</span> <span class="token string">"12154545"</span><span class="token punctuation">,</span>
                  <span class="token string">"name"</span><span class="token punctuation">:</span> <span class="token string">"吴系挂"</span><span class="token punctuation">,</span>
                  <span class="token string">"groupid"</span><span class="token punctuation">:</span> <span class="token number">2</span> <span class="token punctuation">,</span>
                  <span class="token string">"reg_time"</span><span class="token punctuation">:</span> <span class="token string">"1436864169"</span><span class="token punctuation">,</span>
                  <span class="token string">"last_login_time"</span><span class="token punctuation">:</span> <span class="token string">"0"</span><span class="token punctuation">,</span>
              <span class="token punctuation">}</span>
 <span class="token punctuation">}</span>
          </pre>
          <p><strong>返回参数说明</strong></p>
          <table>
            <thead>
            <tr>
              <th style="text-align:left;">参数名</th>
              <th style="text-align:left;">类型</th>
              <th>说明</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td style="text-align:left;">groupid</td>
              <td style="text-align:left;">int</td>
              <td>用户组id，1：超级管理员；2：普通用户</td>
            </tr>
            </tbody>
          </table>
          <p><strong>备注</strong></p>
          <ul>
            <li>更多返回错误代码请看首页的错误代码描述</li></ul>
      </div>
      <div id="api-doc-templ"  ref="api_doc_templ" style="display:none">
**简要描述：**

- 用户注册接口

**请求URL：**
- ` http://xx.com/api/user/register `

**请求方式：**
- POST

**参数：**

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|username |是  |string |用户名   |
|password |是  |string | 密码    |
|name     |否  |string | 昵称    |

**返回示例**

```
{
"error_code": 0,
"data": {
"uid": "1",
"username": "12154545",
"name": "吴系挂",
"groupid": 2 ,
"reg_time": "1436864169",
"last_login_time": "0",
}
}
```

**返回参数说明**

|参数名|类型|说明|
|:-----  |:-----|-----                           |
|groupid |int   |用户组id，1：超级管理员；2：普通用户  |

**备注**

- 更多返回错误代码请看首页的错误代码描述

      </div>
      <div id="database-doc-templ" ref="database_doc_templ" style="display:none">

-  用户表，储存用户信息

|字段|类型|空|默认|注释|
|:----    |:-------    |:--- |-- -|------      |
|uid    |int(10)     |否 |  |             |
|username |varchar(20) |否 |    |   用户名  |
|password |varchar(50) |否   |    |   密码    |
|name     |varchar(15) |是   |    |    昵称     |
|reg_time |int(11)     |否   | 0  |   注册时间  |

- 备注：无


      </div>
      <div id="doc-database-doc-templ" ref="doc_database_doc_templ" style="display:none">
        <ul><li>用户表，储存用户信息</li></ul>
        <div class="table-auto-overflow-x"><table>
          <thead>
          <tr style="background-color: rgb(21, 24, 34); color: rgb(245, 245, 245); font-size: 15px;">
            <th style="text-align: left; width: 142px;">字段</th>
            <th style="text-align: left; width: 142px;">类型</th>
            <th style="text-align: left; width: 142px;">空</th>
            <th style="width: 142px;">默认</th>
            <th style="width: 142px;">注释</th>
          </tr>
          </thead>
          <tbody>
          <tr style="background-color: rgb(255, 255, 255); font-size: 14px;">
            <td style="text-align:left;">uid</td>
            <td style="text-align:left;">int(10)</td>
            <td style="text-align:left;">否</td>
            <td></td>
            <td></td>
          </tr>
          <tr style="background-color: rgb(255, 255, 255); font-size: 14px;">
            <td style="text-align:left;">username</td>
            <td style="text-align:left;">varchar(20)</td>
            <td style="text-align:left;">否</td>
            <td></td>
            <td>用户名</td>
          </tr>
          <tr style="background-color: rgb(255, 255, 255); font-size: 14px;">
            <td style="text-align:left;">password</td>
            <td style="text-align:left;">varchar(50)</td>
            <td style="text-align:left;">否</td>
            <td></td>
            <td>密码</td>
          </tr>
          <tr style="background-color: rgb(255, 255, 255); font-size: 14px;">
            <td style="text-align:left;">name</td>
            <td style="text-align:left;">varchar(15)</td>
            <td style="text-align:left;">是</td>
            <td></td>
            <td>昵称</td>
          </tr>
          <tr style="background-color: rgb(255, 255, 255); font-size: 14px;">
            <td style="text-align:left;">reg_time</td>
            <td style="text-align:left;">int(11)</td>
            <td style="text-align:left;">否</td>
            <td>0</td>
            <td>注册时间</td>
          </tr>
          </tbody>
        </table></div>
        <ul>
          <li>备注：无</li></ul>

      </div>
      <div id="job-plan-templ" ref="job_plan_templ" style="display:none">
#### 第1周
-  2032年5月1日至2032年5月5日

|任务内容|人员|预估耗时|实际耗时|备注|
|:----    |:-------    |:--- |------|------      |
|增加图表计算功能   |张三     |1天|  |             |
|实现多语言功能 |张三 |2天 |    |     |
|添加记账功能 |李四 |3天  |    |       |
|完成多账号同时登录     |王五|4天   |    |         |
|实现ip地址信息转 |赵六    | 4天  |   |     |   |

#### 第2周
-  2032年5月8日至2032年5月13日

|任务内容|人员|预估耗时|实际耗时|备注|
|:----    |:-------    |:--- |------|------      |
|增加图表计算功能   |张三     |1天|  |             |
|实现多语言功能 |张三 |2天 |    |     |
|添加记账功能 |李四 |3天  |    |       |
|完成多账号同时登录     |王五|4天   |    |         |
|实现ip地址信息转 |赵六    | 4天  |   |     |   |

      </div>
      <div id="doc-job-plan-templ" ref="doc_job_plan_templ" style="display:none">
        <h4>第1周</h4>
        <ul><li>2032年5月1日至2032年5月5日</li></ul>
        <table>
          <thead>
          <tr>
            <th style="text-align:left;">任务内容</th>
            <th style="text-align:left;">人员</th>
            <th style="text-align:left;">预估耗时</th>
            <th>实际耗时</th>
            <th>备注</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td style="text-align:left;">增加图表计算功能</td>
            <td style="text-align:left;">张三</td>
            <td style="text-align:left;">1天</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td style="text-align:left;">实现多语言功能</td>
            <td style="text-align:left;">张三</td>
            <td style="text-align:left;">2天</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td style="text-align:left;">添加记账功能</td>
            <td style="text-align:left;">李四</td>
            <td style="text-align:left;">3天</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td style="text-align:left;">完成多账号同时登录</td>
            <td style="text-align:left;">王五</td>
            <td style="text-align:left;">4天</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td style="text-align:left;">实现ip地址信息转</td>
            <td style="text-align:left;">赵六</td>
            <td style="text-align:left;">4天</td>
            <td></td>
            <td></td>
          </tr>
          </tbody>
        </table>

      </div>
      <div id="flow-chart-templ" ref="flow_chart_templ" style="display:none">

#### 流程图 Flowchart

```flow
st=>start: 用户登陆
op=>operation: 登陆操作
cond=>condition: 登陆成功 Yes or No?
e=>end: 进入后台

st->op->cond
cond(yes)->e
cond(no)->op
```

      </div>
      <div id="sequence-diagram-templ" ref="sequence_diagram_templ" style="display:none">

##### 序列图 Sequence Diagram

```seq
Andrew->China: Says Hello
Note right of China: China thinks\nabout it
China-->Andrew: How are you?
Andrew->>China: I am good thanks!
```
      </div>
    </div>
</template>
<script>
export default {
  data () {
    return {};
  },
  components:{

  },
  methods:{
    getApiDocTempl(type){
      return type=='doc'?this.$refs.doc_api_doc_templ.innerHTML:this.$refs.api_doc_templ.innerHTML;
    },
    getFlowChartTempl(){
      return this.$refs.flow_chart_templ.innerText;
    },
    getSequenceDiagramTempl(){
      return this.$refs.sequence_diagram_templ.innerText;
    },
    getDatabaseDocTempl(type){
      return type=='doc'?this.$refs.doc_database_doc_templ.innerHTML:this.$refs.database_doc_templ.innerHTML;
    },
    getJobPlanTempl(type){
      return type=='doc'?this.$refs.doc_job_plan_templ.innerHTML:this.$refs.job_plan_templ.innerHTML;
    },
  },
  mounted () {
  }
}
</script>
