<?php
// +----------------------------------------------------------------------
// |  [ MAKE YOUR WORK EASIER]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2016 http://www.nbcc.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: fangrenfu <fangrenfu@126.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\common\access\Template;
use app\common\service\Action;
class Index extends Template{
    public function index(){
        $Obj=new Action();
        $menuJson=array('menus'=>$Obj->getUserAccessMenu(session('S_USER_NAME'),230));
        $this->assign('menu',json_encode($menuJson));
        return $this->fetch();
    }
}