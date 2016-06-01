<?php
namespace Home\Controller;
use Common\Service\AccessService;
use Think\Controller;
use Common\Service\LoginService;
class LoginController extends Controller {
    //登录界面，首先进行判断。是否已经登录。
    public function login()
    {
        $guid=getguid();
        session('rand',$guid);
        $this->assign('randguid',$guid);
        $this->display();
    }
    /*
   * 验证用户名密码
   * */
    public function checkLogin($username='',$pwd='')
    {
        try {
            $Obj=new LoginService();
            $result=$Obj->checkPasswordAsUser($username,$pwd);
            $status=$result>0?1:0;
            $info=$result>0?'登录成功！':'用户名或者密码错误！';
            $info=$result==-1?'用户名不存在！':$info;
            $result=array('info'=>$info,'status'=>$status);
            $this->ajaxReturn($result,'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    /**
     * 用户注销页面
     */
    public function logout()
    {
        session('user', null);
        $this->redirect('/Home/Login/login');
    }
}