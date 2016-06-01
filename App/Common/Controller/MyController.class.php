<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-5-23
 * Time: 上午10:21
 * To change this template use File | Settings | File Templates.
 */
namespace Common\Controller;
use Admin\Service\ActionService;
use Think\Controller;
use Common\Service\AccessService;
//通用操作类
class MyController extends Controller{
    public function _initialize(){
        try {
            $access=new AccessService();
            $access->checkAccess();
            $access->checkLoginIP();
        }
        catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
}