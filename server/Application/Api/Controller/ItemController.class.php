<?php
namespace Api\Controller;
use Think\Controller;
class ItemController extends BaseController {
    // 获取统计信息
    public function getProjectTableCountByItemId()
    {
        $this->checkLogin(false);
        $item_id = I("item_id/s");
        $login_user = session("login_user");
        $uid = $login_user['uid'] ? $login_user['uid'] : 0;
        if (!$this->checkItemVisit($uid, $item_id)) {
            $this->sendError(10303);
            return;
        }
        $item = D("Item")->where("item_id = '%d' ", array($item_id))->find();
        if (!$item || $item['is_del'] == 1) {
            sleep(1);
            $this->sendError(10101, '项目不存在或者已删除');
            return false;
        }
        $return = [];

        $tableCountData = array(
            'pageTableData'=>[['page_id'=>1, 'title'=>'我是文档', 'count'=>7, 'address'=>'数据/我是文档']],
            'userTableData'=>[['user_id'=> 1, 'name'=> '夏天', 'count'=> 7,'week_count'=>3, 'item_msg'=>'数据平台团队']],
        );
        // 获取页面
        $pageVisitLogData = D("PageVisitLog")->where(" item_id = '".$item_id."' ")->field("count(1) as count_num,page_id")->group("page_id")->select();
        $pageCountByPageIdArray = array_column($pageVisitLogData,'count_num','page_id');
        $pageData = D("Page")->where(" item_id = '".$item_id."' ")->field("page_title,cat_id,page_id")->select();
        $catalogData = D("Catalog")->where(" item_id = '".$item_id."' ")->field("cat_name,cat_id,parent_cat_id")->select();
        //$catalogArray = array();
        $catalogKeyArray = array_column($catalogData,'cat_name','cat_id');
        $catalogKeyParentArray = array_column($catalogData,'parent_cat_id','cat_id');
        $catalogUrlByCatalogIdArray = array();
        foreach ($catalogData as $cKey =>$cValue){
            $catalogUrlArray = [$catalogKeyArray[$cValue['cat_id']]];
            $catalogUrlByCatalogIdArray[$cValue['cat_id']] = self::_getCatalogUrlByMenuId($cValue['parent_cat_id'],$catalogUrlArray,$catalogKeyArray,$catalogKeyParentArray);
        }
        $pageTableData = array();
        foreach ($pageData as $pKey =>$pValue){
            $tempPage = array();
            $tempPage['page_id'] = $pValue['page_id'];
            $tempPage['title'] = $pValue['page_title'];
            $tempPage['count'] = isset($pageCountByPageIdArray[$pValue['page_id']])?$pageCountByPageIdArray[$pValue['page_id']]:0;
            $address = $pValue['page_title'];
            if($pValue['cat_id']>0){
                $address = $catalogUrlByCatalogIdArray[$pValue['cat_id']].'/'.$address;
            }
            $tempPage['address'] = $address;
            $pageTableData[] =  $tempPage;
        }
        $pageTableData = self::_arrayToSort($pageTableData,'count','desc');
        $return['pageTableData'] = array_values($pageTableData);
        // 获取用户
        $userData = D("User")->field("uid,username")->select();
        $userMsgByIdArray = array_column($userData,'username','uid');
        $beforeWeekTime = time()-7*24*3600;
        $userVisitLogData = D("PageVisitLog")->where(" item_id = '".$item_id."' ")->field("count(1) as count_num,uid")->group("uid")->select();
        $userCountByPageIdArray = array_column($userVisitLogData,'count_num','uid');
        $userWeekVisitLogData = D("PageVisitLog")->where(" item_id = '".$item_id."' and create_time > '".$beforeWeekTime."' ")->field("count(1) as count_num,uid")->group("uid")->select();
        $userWeekCountByPageIdArray = array_column($userWeekVisitLogData,'count_num','uid');
        //$userTeamData = D("TeamMember")->field("group_concat(team_id) as team_id_string,member_uid")->group("member_username")->select();
        $userTeamData = D("TeamMember")->join(" left join team on team.id = team_member.team_id")->field("team_member.member_uid , group_concat(team.team_name) as team_name_string")->group("team_member.member_username")->select();
        $teamMsgByUidArray =  array_column($userTeamData,'team_name_string','member_uid');
        $userTableData = array();
        foreach ($userCountByPageIdArray as $userId =>$userCount){
            $tempPage = array();
            $tempPage['user_id'] = $userId;
            $tempPage['name'] = $userId==0?'游客':$userMsgByIdArray[$userId];
            $tempPage['count'] = $userCount;
            $tempPage['week_count'] = isset($userWeekCountByPageIdArray[$userId])?$userWeekCountByPageIdArray[$userId]:0;
            $tempPage['team_msg'] = isset($teamMsgByUidArray[$userId])?$teamMsgByUidArray[$userId]:' - ';
            $userTableData[] = $tempPage;
        }
        $userTableData = self::_arrayToSort($userTableData,'count','desc');
        $return['userTableData'] = array_values($userTableData);
        $this->sendResult($return);
    }

    /*
     * 此函数由本控制器actionshow 调用，实现二维数组按某一键值排序的功能
    */
    private function _arrayToSort($array=[], $key='', $order = "asc"){//asc是升序 desc是降序
        $arr_nums = $arr = array();
        foreach ($array as $k => $v) {
            $arr_nums[$k] = $v[$key];
        }
        if ($order == 'asc') {
            asort($arr_nums);
        } else {
            arsort($arr_nums);
        }
        foreach ($arr_nums as $k => $v) {
            $arr[$k] = $array[$k];
        }
        return $arr;
    }

    /**
     * 获取目录层级关系
     * @param int $menuParentId
     * @param array $catalogUrlArray
     * @param array $catalogKeyArray
     * @param array $catalogKeyParentArray
     * @return string
     */
    private function _getCatalogUrlByMenuId($menuParentId=0,$catalogUrlArray = [],$catalogKeyArray = [],$catalogKeyParentArray =[]){
        $i=0;//登记执行次数，发现死循环自动退出
        $isGet=false;
        if($menuParentId>0){
            $catalogUrlArray[] = $catalogKeyArray[$menuParentId];
            while ($isGet==false&&$i<10) {
                $parentId = $catalogKeyParentArray[$menuParentId];
                if ($parentId > 0) {//当还有上级目录时
                    $menuParentId = $parentId;
                    $catalogUrlArray[] = $catalogKeyArray[$menuParentId];
                    //$catalogUrl .= '/'.$catalogKeyArray[$menuParentId];
                } else {//当为最大的一级目录时,退出判断
                    $isGet=true;
                }
                $i++;
            }
        }
        $catalogUrlArray = array_reverse($catalogUrlArray);
        return implode($catalogUrlArray,'/');
    }
    // 获取统计信息
    public function getProjectCountByItemId(){
        $this->checkLogin(false);
        $item_id = I("item_id/s");
        $login_user = session("login_user");
        $uid = $login_user['uid'] ? $login_user['uid'] : 0 ;
        if(!$this->checkItemVisit($uid , $item_id)){
            $this->sendError(10303);
            return ;
        }
        $item = D("Item")->where("item_id = '%d' ",array($item_id))->find();
        if (!$item || $item['is_del'] == 1) {
            sleep(1);
            $this->sendError(10101,'项目不存在或者已删除');
            return false;
        }
        $return = [];
        $projectCountData = array(
            'activeUser'=>0,// 活跃人数
            'todayActiveUser'=>0,// 今日活跃人数
            'pageNum'=>0,// 全部页面数量
            'todayNewPageNum'=>0,// 今天新增数量
            'pageTime'=>0,// 全部页面访问
            'todayPageTime'=>0,// 今天页面访问
            'pageTimeByNoLogin'=>0,// 全部页面访问(无登录)
            'todayPageTimeByNoLogin'=>0,// 今天页面访问(无登录)
        );
        $pageVisitLogData = D("PageVisitLog")->where(" item_id = '".$item_id."' ")->field("uid,create_time")->select();
        $allUserIdArray = array_column($pageVisitLogData,'uid');
        $projectCountData['activeUser'] = count(array_unique($allUserIdArray));// 全部活跃人数
        $projectCountData['pageTime'] = count($pageVisitLogData);// 全部页面访问
        $todayTime = strtotime(date("Y-m-d"),time());// 当天凌晨时间戳
        $todayPageVisitLogData = D("PageVisitLog")->where(" item_id = '".$item_id."' and create_time >= '".$todayTime."'")->field("uid,create_time")->select();
        $todayUserIdArray = array_column($todayPageVisitLogData,'uid');
        $projectCountData['todayActiveUser'] = count(array_unique($todayUserIdArray));// 今日活跃人数
        $projectCountData['todayPageTime'] = count($todayUserIdArray);// 今日页面访问
        $pageData = D("Page")->where(" item_id = '".$item_id."' ")->field("page_id,addtime")->select();
        $allPageIdArray = array_column($pageData,'page_id');
        $projectCountData['pageNum'] = count($allPageIdArray);// 全部页面数
        $todayPageData = D("Page")->where(" item_id = '".$item_id."' and addtime >= '".$todayTime."' ")->field("page_id,addtime")->select();
        $projectCountData['todayNewPageNum'] = count($todayPageData);// 今日新增页面数
        foreach ($pageVisitLogData as $pKey =>$pValue){
            if($pValue['uid']==0){
                $projectCountData['pageTimeByNoLogin']++;
            }
        }
        foreach ($todayPageVisitLogData as $tKey =>$tValue){
            if($tValue['uid']==0){
                $projectCountData['todayPageTimeByNoLogin']++;
            }
        }
        $return['projectCountData'] = $projectCountData;

        // 趋势统计
        $projectHalfMonthCountData = array(
            'everydayUser'=>['name'=>'活跃用户（日）','data'=>[],'xAxisData'=>[],'dataUnit'=>'人','yAxisMaxNum'=>0],
            'everydayPage'=>['name'=>'文档访问（日）','data'=>[],'xAxisData'=>[],'dataUnit'=>'次','yAxisMaxNum'=>0],
            'everydayAllPage'=>['name'=>'文档访问（总）','data'=>[],'xAxisData'=>[],'dataUnit'=>'次','yAxisMaxNum'=>0],
        );
        $halfAMonthTime = array();
        $nowTime = time();
        $todayStartTime = strtotime(date("Y-m-d"),$nowTime);
        for ($i=0; $i<10; $i++) {
            $startTimeNum = $todayStartTime-$i*3600*24;// 当天凌晨时间戳
            $timeKey = date("m-d",$startTimeNum);
            $endTimeNum = $i==0?'':$startTimeNum+3600*24;
            $halfAMonthTime[$timeKey] = ['min'=>$startTimeNum,'max'=>$endTimeNum];// 当天凌晨时间戳
        }
        $beforeHalfAMonthStartTime = $todayStartTime - 14*3600*24; // 半个月前开始的时间
        $everydayPageArray = array();
        $everydayUserArray = array();
        $beforeHalfAMonthPageCount = 0;
        $halfAMonthTime = array_reverse($halfAMonthTime);
        foreach ($pageVisitLogData as $pKey =>$pValue){
            if($pValue['create_time']>=$beforeHalfAMonthStartTime){
                foreach ($halfAMonthTime as $timeKey => $timeInterval){
                    // 每天页面
                    if($timeInterval['max']==''){//今天
                        if($pValue['create_time']>=$timeInterval['min']){
                            $everydayPageArray[$timeKey]++;
                            $everydayUserArray[$timeKey][$pValue['uid']]=1;
                            break;
                        }
                    }else{
                        if($pValue['create_time']>=$timeInterval['min']&&$pValue['create_time']<$timeInterval['max']){
                            $everydayPageArray[$timeKey]++;
                            $everydayUserArray[$timeKey][$pValue['uid']]=1;
                            break;
                        }
                    }
                }
            }else{
                $beforeHalfAMonthPageCount++;
            }
        }
        $tempAllPageCount = $beforeHalfAMonthPageCount;
        foreach ($halfAMonthTime as $timeKey => $timeInterval){
            $nowDaypageCount = $everydayPageArray[$timeKey] ?? 0;
            $projectHalfMonthCountData['everydayPage']['data'][] = $nowDaypageCount;
            $projectHalfMonthCountData['everydayUser']['data'][] = isset($everydayUserArray[$timeKey])?count($everydayUserArray[$timeKey]):0;
            $tempAllPageCount += $nowDaypageCount;
            $projectHalfMonthCountData['everydayAllPage']['data'][] = $tempAllPageCount;
        }
        $xAxisData = array_keys($halfAMonthTime);
        $projectHalfMonthCountData['everydayPage']['xAxisData'] = $xAxisData;
        $projectHalfMonthCountData['everydayUser']['xAxisData'] = $xAxisData;
        $projectHalfMonthCountData['everydayAllPage']['xAxisData'] = $xAxisData;
        // 获取y轴最大数值
        $everydayPageMaxNum = max($projectHalfMonthCountData['everydayPage']['data']);
        $projectHalfMonthCountData['everydayPage']['yAxisMaxNum'] = $everydayPageMaxNum==0?10:intval($everydayPageMaxNum*1.5);
        $everydayUserMaxNum = max($projectHalfMonthCountData['everydayUser']['data']);
        $projectHalfMonthCountData['everydayUser']['yAxisMaxNum'] = $everydayUserMaxNum==0?10:intval($everydayUserMaxNum*1.5);
        $everydayAllPageMaxNum = max($projectHalfMonthCountData['everydayAllPage']['data']);
        $projectHalfMonthCountData['everydayAllPage']['yAxisMaxNum'] = $everydayAllPageMaxNum==0?10:intval($everydayAllPageMaxNum*1.5);

        $return['projectEchartsData'] = $projectHalfMonthCountData;
        $this->sendResult($return);
    }

    //单个项目信息
    public function info(){
        $this->checkLogin(false);
        $item_id = I("item_id/s");
        $item_domain = I("item_domain/s");
        $default_page_id = I("default_page_id/d");// 多页项目
        $current_page_id = I("page_id/d");// 单页项目
        // 检测item_id是否为分享码
        if (! is_numeric($item_id)) {
            // 项目编码识别（若无再进行分享码识别）
            $item_msg = D("Item")->where("item_code = '%s' ",array($item_id))->find();
            if(empty($item_msg)){
                // 分享码识别
                $share_msg = D("PageShare")->where("share_code = '%s' ",array($item_id))->find();
                if(empty($share_msg)){
                    $this->sendError(10309);
                    return ;
                }
                $item_id = $share_msg['item_id'];
                $default_page_id = $share_msg['page_id'];
            }else{
                $item_id = $item_msg['item_id'];
            }
            $item_domain = $item_id ;
        }
        //判断个性域名
        if ($item_domain) {
            $item = D("Item")->where("item_domain = '%s'",array($item_domain))->find();
            if ($item['item_id']) {
                $item_id = $item['item_id'] ;
            }
        }
        $login_user = session("login_user");
        $uid = $login_user['uid'] ? $login_user['uid'] : 0 ;
        
        if(!$this->checkItemVisit($uid , $item_id)){
            $this->sendError(10303);
            return ;
        } 

        $item = D("Item")->where("item_id = '%d' ",array($item_id))->find();
        if (!$item || $item['is_del'] == 1) {
            sleep(1);
            $this->sendError(10101,'项目不存在或者已删除');
            return false;
        }
        if ($item['item_type'] == 1 ) {
            $this->_show_regular_item($item,$default_page_id);
        }
        elseif ($item['item_type'] == 2 ) {
            $this->_show_single_page_item($item,$current_page_id);
        }else{
           $this->_show_regular_item($item,$default_page_id);
        }
    }

    //展示常规项目
    private function _show_regular_item($item,$default_page_id){
        $item_id = $item['item_id'];

//        $default_page_id = I("default_page_id/d");
        $keyword = I("keyword");
        $default_cat_id2 = $default_cat_id3 = 0 ;

        $login_user = session("login_user");
        $uid = $login_user['uid'] ? $login_user['uid'] : 0 ;
        $is_login =   $uid > 0 ? true :false;
        $menu = array(
            "pages" =>array(),
            "catalogs" =>array(),
            );
        //是否有搜索词
        if ($keyword) {
            $keyword = \SQLite3::escapeString($keyword) ;
            $pages = D("Page")->where("item_id = '$item_id' and is_del = 0  and ( page_title like '%{$keyword}%' or page_content like '%{$keyword}%' ) ")->order(" `s_number` asc  ")->field("page_id,author_uid,cat_id,page_title,addtime")->select();
            $menu['pages'] = $pages ? $pages : array();
        }else{
            $menu = D("Item")->getMemu($item_id) ;
        }

        $domain = $item['item_domain'] ? $item['item_domain'] : $item['item_id'];
        $share_url = get_domain().__APP__.'/'.$domain;

        $ItemPermn = $this->checkItemPermn($uid , $item_id) ;

        $ItemCreator = $this->checkItemCreator($uid , $item_id);

        //如果带了默认展开的页面id，则获取该页面所在的二级目录/三级目录/四级目录
        if ($default_page_id) {
            $page = D("Page")->where(" page_id = '$default_page_id' ")->find();
            if ($page) {
                $default_cat_id4 = $page['cat_id'] ;
                $cat1 = D("Catalog")->where(" cat_id = '$default_cat_id4' and parent_cat_id > 0  ")->find();
                if ($cat1) {
                    $default_cat_id3 = $cat1['parent_cat_id'];
                }else{
                    $default_cat_id3 = $default_cat_id4;
                    $default_cat_id4 = 0 ;
                }

                $cat2 = D("Catalog")->where(" cat_id = '$default_cat_id3' and parent_cat_id > 0  ")->find();
                if ($cat2) {
                    $default_cat_id2 = $cat2['parent_cat_id'];
                }else{
                    $default_cat_id2 = $default_cat_id3;
                    $default_cat_id3 = 0 ;
                }
            }
        }

        if (LANG_SET == 'en-us') {
            $help_url = "https://www.showdoc.cc/help-en";
        }
        else{
            $help_url = "https://www.showdoc.cc/help";
        }


        $return = array(
            "item_id"=>$item_id ,
            "item_code"=>$item['item_code'] ,
            "item_domain"=>$item['item_domain'] ,
            "is_archived"=>$item['is_archived'] ,
            "item_name"=>$item['item_name'] ,
            "default_page_id"=>(string)$default_page_id ,
            "default_cat_id2"=>$default_cat_id2 ,
            "default_cat_id3"=>$default_cat_id3 ,
            "default_cat_id4"=>$default_cat_id4 ,
            "unread_count"=>$unread_count ,
            "item_type"=>1 ,
            "menu"=>$menu ,
            "is_login"=>$is_login,
            "ItemPermn"=>$ItemPermn ,
            "ItemCreator"=>$ItemCreator ,

            );
        $this->sendResult($return);
    }

    //展示单页项目
    private function _show_single_page_item($item,$current_page_id){
        $item_id = $item['item_id'];

//        $current_page_id = I("page_id/d");

        $login_user = session("login_user");
        $uid = $login_user['uid'] ? $login_user['uid'] : 0 ;
        $is_login =   $uid > 0 ? true :false;
        //获取页面
        $page = D("Page")->where(" item_id = '$item_id' ")->find();

        $domain = $item['item_domain'] ? $item['item_domain'] : $item['item_id'];
        $share_url = get_domain().__APP__.'/'.$domain;

        $ItemPermn = $this->checkItemPermn($uid , $item_id) ;

        $ItemCreator = $this->checkItemCreator($uid , $item_id);

        $menu = array() ;
        $menu['pages'] = $page ;
        $return = array(
            "item_id"=>$item_id ,
            "item_code"=>$item['item_code'] ,
            "item_domain"=>$item['item_domain'] ,
            "is_archived"=>$item['is_archived'] ,
            "item_name"=>$item['item_name'] ,
            "current_page_id"=>$current_page_id ,
            "unread_count"=>$unread_count ,
            "item_type"=>2 ,
            "menu"=>$menu ,
            "is_login"=>$is_login,
            "ItemPermn"=>$ItemPermn ,
            "ItemCreator"=>$ItemCreator ,

            );
        $this->sendResult($return);
    }


    //我的项目列表
    public function myList(){
        $login_user = $this->checkLogin();        
        $member_item_ids = array(-1) ; 
        $item_members = D("ItemMember")->where("uid = '$login_user[uid]'")->select();
        if ($item_members) {
            foreach ($item_members as $key => $value) {
                $member_item_ids[] = $value['item_id'] ;
            }
        }
        $team_item_members = D("TeamItemMember")->where("member_uid = '$login_user[uid]'")->select();
        if ($team_item_members) {
            foreach ($team_item_members as $key => $value) {
                $member_item_ids[] = $value['item_id'] ;
            }
        }
        $items  = D("Item")->field("item_id,item_name,item_domain,item_type,last_update_time,item_description,is_del,uid,item_code")->where("uid = '$login_user[uid]' or item_id in ( ".implode(",", $member_item_ids)." ) ")->order("item_id asc")->select();

        
        foreach ($items as $key => $value) {
            //如果项目已标识为删除
            if ($value['is_del'] == 1) {
                unset($items[$key]);
            }else{
                // 是否是自己添加的项目
                $items[$key]['is_creator'] = 1;
                if ($value['uid'] != $login_user['uid']) {
                    $items[$key]['is_creator'] = 0;
                }
            }

        }
        $items = array_values($items);
        //读取需要置顶的项目
        $top_items = D("ItemTop")->where("uid = '$login_user[uid]'")->select();
        if ($top_items) {
            $top_item_ids = array() ;
            foreach ($top_items as $key => $value) {
                $top_item_ids[] = $value['item_id'];
            }
            foreach ($items as $key => $value) {
                $items[$key]['top'] = 0 ;
                if (in_array($value['item_id'], $top_item_ids) ) {
                    $items[$key]['top'] = 1 ;
                    $tmp = $items[$key] ;
                    unset($items[$key]);
                    array_unshift($items,$tmp) ;
                }
            }
        }

        $items = $items ? array_values($items) : array();
        $this->sendResult($items);

    }

    //项目详情
    public function detail(){
        $login_user = $this->checkLogin();
        $item_id = I("item_id/d");  
        $uid = $login_user['uid'] ;
        if(!$this->checkItemCreator($uid , $item_id)){
            $this->sendError(10303);
            return ;
        }  
        $items  = D("Item")->where("item_id = '$item_id' ")->find();
        $items = $items ? $items : array();
        $this->sendResult($items);
    }

    //更新项目信息
    public function update(){
        $login_user = $this->checkLogin();
        $item_id = I("item_id/d");  
        $item_name = I("item_name");  
        $item_description = I("item_description");  
        $item_domain = I("item_domain");  
        $password = I("password");
        $uid = $login_user['uid'] ;
        if(!$this->checkItemCreator($uid , $item_id)){
            $this->sendError(10303);
            return ;
        }

        if ($item_domain) {
            
            if(!ctype_alnum($item_domain) ||  is_numeric($item_domain) ){
                //echo '个性域名只能是字母或数字的组合';exit;
                $this->sendError(10305);
                return false;
            }

            $item = D("Item")->where("item_domain = '%s' and item_id !='%s' ",array($item_domain,$item_id))->find();
            if ($item) {
                //个性域名已经存在
                $this->sendError(10304);
                return false;
            }
        }
        $save_data = array(
            "item_name" => $item_name ,
            "item_description" => $item_description ,
            "item_domain" => $item_domain ,
            "password" => $password ,
            );
        $items  = D("Item")->where("item_id = '$item_id' ")->save($save_data);
        $items = $items ? $items : array();
        $this->sendResult($items);  
    }

    //转让项目
    public function attorn(){
        $login_user = $this->checkLogin();

        $username = I("username");
        $item_id = I("item_id/d");
        $password = I("password");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        if(! D("User")-> checkLogin($item['username'],$password)){
            $this->sendError(10208);
            return ;
        }

        $member = D("User")->where(" username = '%s' ",array($username))->find();

        if (!$member) {
            $this->sendError(10209);
            return ;
        }

        $data['username'] = $member['username'] ;
        $data['uid'] = $member['uid'] ;
        

        $id = D("Item")->where(" item_id = '$item_id' ")->save($data);

        $return = D("Item")->where("item_id = '$item_id' ")->find();

        if (!$return) {
            $this->sendError(10101);
        }

        $this->sendResult($return);
    }

    //删除项目
    public function delete(){
        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");
        $password = I("password");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        if(! D("User")-> checkLogin($item['username'],$password)){
            $this->sendError(10208);
            return ;
        }


        $return = D("Item")->soft_delete_item($item_id);

        if (!$return) {
            $this->sendError(10101);
        }else{
        }

        $this->sendResult($return);
    }
    //归档项目
    public function archive(){
        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");
        $password = I("password");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        if(! D("User")-> checkLogin($item['username'],$password)){
            $this->sendError(10208);
            return ;
        }

        $return = D("Item")->where("item_id = '$item_id' ")->save(array("is_archived"=>1));

        if (!$return) {
            $this->sendError(10101);
        }else{
            $this->sendResult($return);
        }

        
    }
    public function getKey(){
        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        $item_token  = D("ItemToken")->getTokenByItemId($item_id);
        if (!$item_token) {
            $this->sendError(10101);
        }
        $this->sendResult($item_token);

    }

    public function resetKey(){

        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        $ret = D("ItemToken")->where("item_id = '$item_id' ")->delete();

        if ($ret) {
            $this->getKey();
        }else{
            $this->sendError(10101);
        }
    }

    public function updateByApi(){
        //转到Open控制器的updateItem方法
        R('Open/updateItem');
    }

    //置顶项目
    public function top(){
        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");
        $action = I("action");

        if ($action == 'top') {
            $ret = D("ItemTop")->add(array("item_id"=>$item_id,"uid"=>$login_user['uid'],"addtime"=>time()));
        }
        elseif ($action == 'cancel') {
            $ret = D("ItemTop")->where(" uid = '$login_user[uid]' and item_id = '$item_id' ")->delete();
        }
        if ($ret) {
            $this->sendResult(array());
        }else{
            $this->sendError(10101);
        }
    }
    
    //验证访问密码
    public function pwd(){
        $item_id = I("item_id/d");
        $password = I("password");
        $v_code = I("v_code");
        $refer_url = I('refer_url');

        //检查用户输错密码的次数。如果超过一定次数，则需要验证 验证码
        $key= 'item_pwd_fail_times_'.$item_id;
        if(!D("VerifyCode")->_check_times($key,10)){
            if (!$v_code || $v_code != session('v_code')) {
                $this->sendError(10206,L('verification_code_are_incorrect'));
                return;
            }
        }
        session('v_code',null) ;
        $item = D("Item")->where("item_id = '$item_id' ")->find();
        if ($item['password'] == $password) {
            session("visit_item_".$item_id , 1 );
            $this->sendResult(array("refer_url"=>base64_decode($refer_url))); 
        }else{
            D("VerifyCode")->_ins_times($key);//输错密码则设置输错次数
            
            if(D("VerifyCode")->_check_times($key,10)){
                $error_code = 10307 ;
            }else{
                $error_code = 10308 ;
            }
            $this->sendError($error_code,L('access_password_are_incorrect'));
        }

    }

    public function itemList(){
        $login_user = $this->checkLogin();        
        $items  = D("Item")->where("uid = '$login_user[uid]' ")->select();
        $items = $items ? $items : array();
        $this->sendResult($items);
    }

    //新建项目
    public function add(){
        $login_user = $this->checkLogin();
        $item_name = I("item_name");
        $item_domain = I("item_domain") ? I("item_domain") : '';
        $copy_item_id = I("copy_item_id");
        $password = I("password");
        $item_description = I("item_description");
        $item_type = I("item_type");

        if ($item_domain) {
            
            if(!ctype_alnum($item_domain) ||  is_numeric($item_domain) ){
                //echo '个性域名只能是字母或数字的组合';exit;
                $this->sendError(10305);
                return false;
            }

            $item = D("Item")->where("item_domain = '%s'  ",array($item_domain))->find();
            if ($item) {
                //个性域名已经存在
                $this->sendError(10304);
                return false;
            }
        }
        
        //如果是复制项目
        if ($copy_item_id > 0) {
            if (!$this->checkItemPermn($login_user['uid'] , $copy_item_id)) {
                $this->sendError(10103);
                return;
            }
            $ret = D("Item")->copy($copy_item_id,$login_user['uid'],$item_name,$item_description,$password,$item_domain);
            if ($ret) {
                $this->sendResult(array());             
            }else{
                $this->sendError(10101);
            }
            return ;
        }
        
        $insert = array(
            "uid" => $login_user['uid'] ,
            "username" => $login_user['username'] ,
            "item_name" => $item_name ,
            "password" => $password ,
            "item_description" => $item_description ,
            "item_domain" => $item_domain ,
            "item_type" => $item_type ,
            "addtime" =>time()
            );
        $item_id = D("Item")->add($insert);
        // 追加项目识别编码
        $update_data = array(
            "item_code" => substr(md5($item_id),-6)
        );
        D("Item")->where("item_id = '$item_id' ")->save($update_data);

        if ($item_id) {
            //如果是单页应用，则新建一个默认页
            if ($item_type == 2 ) {
                $insert = array(
                    'author_uid' => $login_user['uid'] ,
                    'author_username' => $login_user['username'],
                    "page_title" => $item_name ,
                    "item_id" => $item_id ,
                    "cat_id" => 0 ,
                    "page_content" => '欢迎使用showdoc。点击右上方的编辑按钮进行编辑吧！' ,
                    "addtime" =>time()
                    );
                $page_id = D("Page")->add($insert);
            }
            $this->sendResult(array());               
        }else{
            $this->sendError(10101);
        }
        
    }



}
