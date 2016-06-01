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

class UserController extends MyController{
    //显示信息
    public function query($page=1,$rows=10,$userid='%',$name='%',$school=''){
        try {
            $Obj = D('Common/User', 'Service');
            $result = $Obj->getUserList($page,$rows,$userid,$name,$school);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    //更新信息
    public function update(){
        try {
            $Obj = D('Common/User', 'Service');
            $result = $Obj->updateUser($_POST);//无法用I('post.')获取二维数组
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    public function getRole(){

        $Obj=D('user');
        $condition['userid'] ='12312';
        $user = $Obj->join('inner join school on school.school=user.school')
            ->field('user.password,user.userid,user.name, user.school,school.manage')->where($condition)->select();

    }
    public function role(){
        try {
            $Obj = D('Common/Role', 'Service');
            $result = $Obj->getRoleList();
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    //更新角色信息
    public function updaterole($userid=0,$role="*"){
        try {
            $Obj = D('Common/User', 'Service');
            $result = $Obj->updateUserRole($userid,$role);//无法用I('post.')获取二维数组
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(),$e->getMessage());
        }
    }
    public function resetself($oldpwd = '', $newpwd = '')
    {
        try {
            $Obj = D('Common/User', 'Service');
            $result = $Obj->changeSelfPassword($oldpwd, $newpwd);
            if ($result) {
                $info = '密码修改成功。';
                $status = 1;
            } else {
                $info = '旧密码错误。';
                $status = 0;
            }
            $result = array('info' => $info, 'status' => $status);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(), $e->getMessage());
        }
    }
    public function changepassword($userid,$password){
        try {
            $Obj = D('Common/User', 'Service');
            $result = $Obj->changeUserPassword($userid,$password);
            if ($result) {
                $info = '密码修改成功。';
                $status = 1;
            } else {
                $info = '修改失败。';
                $status = 0;
            }
            $result = array('info' => $info, 'status' => $status);
            $this->ajaxReturn($result, 'JSON');
        } catch (\Exception $e) {
            AccessService::throwException($e->getCode(), $e->getMessage());
        }
    }
}