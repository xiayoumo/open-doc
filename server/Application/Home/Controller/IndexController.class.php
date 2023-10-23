<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
        $tmp = @file_get_contents('./server/Application/Home/Conf/config.php');
        if (strstr($tmp, "opendoc not install")) {
            header("location:./install/index.php");
            exit();
        }

        //跳转到web目录
        header("location:./web/#/");
        exit();

    	$this->checkLogin(false);
    	$login_user = session("login_user");
    	$this->assign("login_user" ,$login_user);
    	if (LANG_SET == 'en-us') {
    		$demo_url = C("HOME_URL")."/demo-en";
    		$help_url = C("HOME_URL")."/help-en";
    		$creator_url = "https://gitee.com/xiayoumo";
    	}
    	else{
    		$demo_url = C("HOME_URL")."/demo";
    		$help_url = C("HOME_URL")."/help";
    		$creator_url = "https://gitee.com/xiayoumo/open-doc";
    	}
    	$this->assign("demo_url" ,$demo_url);
    	$this->assign("help_url" ,$help_url);
    	$this->assign("creator_url" ,$creator_url);

        $this->display();
    }
}
