<?php
namespace Home\Controller;
use Common\Controller\MyController;
use Admin\Service;
class IndexController extends MyController {
    public function index(){
        $session=session('user');
        $userinfo='用户名：'.$session['userid'].' 姓名：'.$session['name'];
        $this->assign('userinfo',$userinfo);

        $menuJson=json_encode(array('menus'=>D('Common/Action','Service')->getUserAccessMenu($session['userid'],1)));
        $this->assign('menu',$menuJson);
        $this->display();
    }
    public function test(){

    }
}