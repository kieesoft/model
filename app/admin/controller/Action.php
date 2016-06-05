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

use app\common\access\MyAccess;
use app\common\access\MyController;

class Action extends MyController{
    //显示信息
    public function query($action = '%', $description = '%',$searchid='',$id=1)
    {
        try {

            $Obj=new \app\common\service\Action();
            $result = $Obj->getActionList($action, $description,$searchid,$id);
            return json($result);
        } catch (\Exception $e) {
            MyAccess::throwException($e->getCode(),$e->getMessage());
        }
    }

    //更新信息
    public function update()
    {
        try {
            $Obj=new \app\common\service\Action();
            $result = $Obj->updateAction($_POST);//无法用I('post.')获取二维数组
            return json($result);
        } catch (\Exception $e) {
            MyAccess::throwException($e->getCode(),$e->getMessage());
        }
    }

    //显示角色信息
    public function role($page=1,$rows=10,$id=0)
    {
        try {
            $Obj=new \app\common\service\Action();
            $result = $Obj->getActionRole($page=1,$rows=10,$id);
            return json($result);
        } catch (\Exception $e) {
            MyAccess::throwException($e->getCode(),$e->getMessage());
        }
    }

    //更新角色信息
    public function updaterole()
    {
        try {
            $Obj=new \app\common\service\Action();
            $result = $Obj->updateActionRole($_POST);
            return json($result);
        } catch (\Exception $e) {
            MyAccess::throwException($e->getCode(),$e->getMessage());
        }
    }
} 