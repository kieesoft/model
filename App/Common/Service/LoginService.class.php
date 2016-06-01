<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/22
 * Time: 12:36
 */
namespace Common\Service;

/**登录服务
 * Class LoginService
 * @package Home\Service
 */
class LoginService {
    /**检查用户表密码
     * @param $username string 用户名
     * @param $password string 密码
     * @return int -1用户名不存在，0密码错误，1登录成功
     */
    public function checkPasswordAsUser($username,$password){
            $Obj = D('user');
            $condition['userid'] = $username;
            $data = $Obj->join('inner join school on school.school=user.school')
                ->field('user.password,user.userid,user.name, user.school,school.manage')->where($condition)->find();
            if (is_array($data) && count($data) > 0) {
                //添加session信息
                if (md5($data['password'] . session('rand')) == $password) {
                    $session['userid'] = $data['userid'];//登录名
                    $session['name'] = $data['name']; //真实姓名
                    $session['role'] = 'T';//当前角色为管理员
                    $session['school'] = $data['school'];//学院部门
                    $session['manage'] = $data['manage'];//是否主管部门
                    session('user', $session);
                    //记录当次ip 时间，以便下次登陆用
                    $data = null;
                    $data['lastloginip'] = $_SERVER['REMOTE_ADDR'];
                    $data['lastlogintime'] = date('Y-m-d H:i:s');
                    $Obj->where($condition)->save($data);
                    return 1;
                }
            } else
                return -1;
            return 0;
    }

}

