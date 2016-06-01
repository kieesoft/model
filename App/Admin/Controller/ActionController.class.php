<?php
/**
 * Created by PhpStorm.
 * User: fangrenfu@126.com
 * Date: 13-11-7
 * Time: 下午4:20
 */
namespace Admin\Controller;

use Common\Controller\MyController;
use Common\Service\AccessService;
class ActionController extends MyController
{
    //显示信息
    public function query($action = '', $description = '',$searchid='',$id=1)
    {
        try {
            $Obj = D('Common/Action', 'Service');
            $result = $Obj->getActionList($action, $description,$searchid,$id);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }

    //更新信息
    public function update()
    {
        try {
            $Obj = D('Common/Action', 'Service');
            $result = $Obj->updateAction($_POST);//无法用I('post.')获取二维数组
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }

    //显示角色信息
    public function role($page=1,$rows=10,$id=0)
    {
        try {
            $Obj = D('Common/Action', 'Service');
            $result = $Obj->getActionRole($page=1,$rows=10,$id);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }

    //更新角色信息
    public function updaterole()
    {
        try {
            $Obj = D('Common/Action', 'Service');
            $result = $Obj->updateActionRole($_POST);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
}