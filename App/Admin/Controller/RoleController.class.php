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

class RoleController extends MyController
{
    //显示信息
    public function query($page = 1, $rows = 20)
    {
        try {
            $Obj = D('Common/Role', 'Service');
            $result = $Obj->getRoleList($page, $rows);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }

    //更新信息
    public function update()
    {
        try {
            $Obj = D('Common/Role', 'Service');
            $result = $Obj->updateRole($_POST);//无法用I('post.')获取二维数组
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
}