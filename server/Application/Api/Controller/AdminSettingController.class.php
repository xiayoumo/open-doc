<?php
namespace Api\Controller;
use Think\Controller;
class AdminSettingController extends BaseController {

    //保存配置
    public function saveConfig(){
        $login_user = $this->checkLogin();
        $this->checkAdmin();
        $register_open = intval(I("register_open")) ;
        $ldap_open = intval(I("ldap_open")) ;
        $ldap_form = I("ldap_form") ;
        $custom_logo_open = intval(I("custom_logo_open")) ;
        $custom_logo_form = I("custom_logo_form") ;
        $outside_btn_open = intval(I("outside_btn_open")) ;
        $outside_btn_form = I("outside_btn_form") ;

        D("Options")->set("register_open" ,$register_open) ;
        if ($ldap_open) {

            if( !extension_loaded( 'ldap' ) ) {
               $this->sendError(10011,"你尚未安装php-ldap扩展。如果是普通PHP环境，请手动安装之。如果是使用之前官方docker镜像，则需要重新安装镜像。方法是：备份 /opendoc_data 整个目录，然后全新安装opendoc，接着用备份覆盖/opendoc_data 。然后递归赋予777可写权限。");
               return ;
            }

            $ldap_conn = ldap_connect($ldap_form['host'], $ldap_form['port']);//建立与 LDAP 服务器的连接
            if (!$ldap_conn) {
               $this->sendError(10011,"Can't connect to LDAP server");
               return ;
            }
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_form['version']);
            $rs=ldap_bind($ldap_conn, $ldap_form['bind_dn'], $ldap_form['bind_password']);//与服务器绑定 用户登录验证 成功返回1 
            if (!$rs) {
               $this->sendError(10011,"Can't bind to LDAP server");
               return ;
            }

            $result = ldap_search($ldap_conn,$ldap_form['base_dn'],"(cn=*)");
            $data = ldap_get_entries($ldap_conn, $result);
            for ($i=0; $i<$data["count"]; $i++) {
                $ldap_user = $data[$i]["cn"][0] ;
                //如果该用户不在数据库里，则帮助其注册
                if(!D("User")->isExist($ldap_user)){
                    D("User")->register($ldap_user,$ldap_user.time());
                }
            }
            D("Options")->set("ldap_form" , json_encode($ldap_form,JSON_UNESCAPED_UNICODE)) ;
        }
        D("Options")->set("ldap_open" ,$ldap_open) ;
        // logo
        D("Options")->set("custom_logo_open" ,$custom_logo_open) ;
        D("Options")->set("custom_logo_form" , json_encode($custom_logo_form,JSON_UNESCAPED_UNICODE)) ;
        // 外链
        D("Options")->set("outside_btn_open" ,$outside_btn_open) ;
        D("Options")->set("outside_btn_form" , json_encode($outside_btn_form,JSON_UNESCAPED_UNICODE)) ;

        $this->sendResult(array());
    }

    //加载配置
    public function loadConfig(){
        $login_user = $this->checkLogin();
        $this->checkAdmin();
        $ldap_open = D("Options")->get("ldap_open" ) ;
        $register_open = D("Options")->get("register_open" ) ;
        $ldap_form = D("Options")->get("ldap_form" ) ;
        $ldap_form = json_decode($ldap_form,1);

        $custom_logo_open = D("Options")->get("custom_logo_open" ) ;
        $custom_logo_form = D("Options")->get("custom_logo_form" ) ;
        $custom_logo_form = json_decode($custom_logo_form,1);

        $outside_btn_open = D("Options")->get("outside_btn_open" ) ;
        $outside_btn_form = D("Options")->get("outside_btn_form" ) ;
        $outside_btn_form = json_decode($outside_btn_form,1);

        //如果强等于false，那就是尚未有数据。关闭注册应该是有数据且数据为字符串0
        if ($register_open === false) {
            $this->sendResult(array());
        }else{
            $array = array(
                "ldap_open"=>$ldap_open ,
                "register_open"=>$register_open ,
                "ldap_form"=>$ldap_form ,
                "custom_logo_open"=>$custom_logo_open ,
                "custom_logo_form"=>$custom_logo_form ,
                "outside_btn_open"=>$outside_btn_open ,
                "outside_btn_form"=>$outside_btn_form ,
                );
            $this->sendResult($array);
        }

    }
    // 上传logo image
    public function uploadLogoImg(){
        $login_user = $this->checkLogin();
        if ($_FILES['file']['name'] == 'blob') {
            $_FILES['file']['name'] .= '.jpg';
        }
        if (strstr(strtolower($_FILES['file']['name']), ".php") ) {
            return false;
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './../Public/Uploads/';// 设置附件上传目录
        $upload->savePath = '';// 设置附件上传子目录
        $info = $upload->upload() ;
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
            return;
        }else{// 上传成功 获取上传文件信息
            $url = $_SERVER['HTTP_X_FORWARDED_PROTO'].'://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/'.$info['file']['savepath'].$info['file']['savename'];
//            $url = get_domain().__ROOT__.substr($upload->rootPath,1).$info['file']['savepath'].$info['file']['savename'] ;
            echo json_encode(array("url"=>$url,"success"=>1));
        }
    }

    public function checkLdapLogin(){
            $username = 'admin';
            $password = '123456';

            $ldap_open = D("Options")->get("ldap_open" ) ;
            $ldap_form = D("Options")->get("ldap_form" ) ;
            $ldap_form = json_decode($ldap_form,1);
            if (!$ldap_open) {
                return ;
            }
            $ldap_conn = ldap_connect($ldap_form['host'], $ldap_form['port']);//建立与 LDAP 服务器的连接
            if (!$ldap_conn) {
               $this->sendError(10011,"Can't connect to LDAP server");
               return ;
            }
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_form['version']);
            $rs=ldap_bind($ldap_conn, $ldap_form['bind_dn'], $ldap_form['bind_password']);//与服务器绑定 用户登录验证 成功返回1 
            if (!$rs) {
               $this->sendError(10011,"Can't bind to LDAP server");
               return ;
            }

            $result = ldap_search($ldap_conn,$ldap_form['base_dn'],"(cn=*)");
            $data = ldap_get_entries($ldap_conn, $result);
            for ($i=0; $i<$data["count"]; $i++) {
                $ldap_user = $data[$i]["cn"][0] ;
                $dn = $data[$i]["dn"] ;
                if ($ldap_user == $username) {
                    //如果该用户不在数据库里，则帮助其注册
                    $userInfo = D("User")->isExist($username) ;
                    if(!$userInfo){
                        D("User")->register($ldap_user,$ldap_user.time());
                    }
                    $rs2=ldap_bind($ldap_conn, $dn , $password);
                    if ($rs2) {
                       D("User")->updatePwd($userInfo['uid'], $password);
                       $this->sendResult(array());
                       return ;
                    }
                }
            }
           $this->sendError(10011,"用户名或者密码错误");
    }

}