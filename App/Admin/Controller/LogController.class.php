<?php
/**
 * @category 系统管理
 * @package  日志管理
 * @author kiee<fangrenfu@126.com>
 * Date: 13-7-15 下午10:19
 * To change this template use File | Settings | File Templates.
 */
namespace Admin\Controller;

use Common\Controller\MyController;
use Common\Service\AccessService;

class LogController extends MyController
{
    //显示所有记录
    public function query($page = 1, $rows = 10, $start = '', $end = '', $userid = '%', $action = '', $success = '')
    {
        try {
            $Obj = D('Common/Log', 'Service');
            $result = $Obj->getLogList($page, $rows, $start, $end, $userid, $action, $success);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }

    //删除所有记录
    public function delete()
    {
        try {
            $Obj = D('Common/Log', 'Service');
            $result = $Obj->clearLog();
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
}