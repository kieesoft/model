<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-5-23
 * Time: 下午2:03
 * To change this template use File | Settings | File Templates.
 */
namespace All\Controller;
use Common\Service\AccessService;
use Think\Controller;
use All\Service;
class OptionController extends Controller{
    public function role(){
        try {
            $result=D('role')->field('role,rtrim(name) as name')->select();
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    //获取学院列表 only=0时添加全部选项
    public function school($only=1){
        try {
            $condition['active']=1;
            $result = D('school')->order('school')->where($condition)->field('school,rtrim(name) as name')->select();
            if($only==0) {
                $all[] = array('school' => '', 'name' => '全部');
                $result = array_merge($all, $result);
            }
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    //获取小图标
    public function icon(){
        try {
            $result=D('icon')->select();
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
}