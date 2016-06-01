<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/9
 * Time: 20:35
 */

namespace Common\Service;

use Common\Conf\MyException;

class AccessService {
    /**
     * @param $id(ActionID)
     * @param $action(Action URL)
     * @param $operate(操作）
     * @param $errorCode(错误代码)
     * @return string
     */
    private static  function buildMessage($id,$action,$operate,$errorCode){
        $operation=array(
            'R'=>'read',
            'M'=>'modify',
            'D'=>'delete',
            'E'=>'execute',
            'A'=>'add'
        );
        return 'id:'.$id.'<br/>action:'.$action . '<br/>'.$operation[$operate].AccessService::getErrorMessage($errorCode);
    }
    /**检查是否有读取权限
     * @param string $operate操作类型：R读取、M修改、D删除、A增加、E执行，默认为读取
     * @return bool
     * @throws \Think\Exception
     */
    public static  function checkAccess($operate='R'){
        $action=__ACTION__;
        $session=session('user');
        if($session){
            //首先检查用户权限表
            if($session['role']=='T'){
                $condition=null;
                $condition['userid']=$session['userid'];
                $user =D('user')->where($condition)->find(); //获取用户角色
                $condition=null;
                $condition["CONCAT('" . __ROOT__ . "',action.action)"] = $action;
                $actionInfo=D('action')->where($condition)->find();
                if(is_array($user)&&is_array($actionInfo)) {
                    $roles = str_split($user['role']);
                    $condition=null;
                    $condition["CONCAT('" . __ROOT__ . "',action.action)"] = $action;
                    $condition['actionrole.role'] = array('in', $roles);
                    $data = D('action')->join('inner join actionrole on actionrole.actionid=action.id')
                        ->where($condition)->field('access')->select();
                    $result = 0;
                    if (is_array($data) && count($data) > 0) {
                        foreach ($data as $one) {
                            $result = $result | $one['access'];
                        }
                        switch ($operate) {
                            case 'R':
                                if (($result & 1) == 1)
                                    return true;
                                break;
                            case 'M':
                                if (($result & 2) == 2)
                                    return true;
                                break;
                            case 'D':
                                if (($result & 4) == 4)
                                    return true;
                                break;
                            case 'A':
                                if (($result & 8) == 8)
                                    return true;
                                break;
                            case 'E':
                                if (($result & 16) == 16)
                                    return true;
                                break;
                            default:
                                throw new \Think\Exception($action . 'undefined operation' .AccessService::getErrorMessage(MyException::WITH_OUT_PERMISSION), MyException::WITH_OUT_PERMISSION);
                                break;
                        }
                    }
                    throw new \Think\Exception(AccessService::buildMessage($actionInfo['id'],$action,$operate,MyException::WITH_OUT_PERMISSION), MyException::WITH_OUT_PERMISSION);

                }
                else{
                    throw new \Think\Exception($action . 'is not found！' .AccessService::getErrorMessage(MyException::WITH_OUT_PERMISSION), MyException::WITH_OUT_PERMISSION);
                }
            }

        }
        else
            throw new \Think\Exception(AccessService::getErrorMessage(MyException::NOT_LOGIN),MyException::NOT_LOGIN);
        return false;
    }

    /**检查IP地址是否变化
     * @return bool
     * @throws \Think\Exception
     */
    public static function checkLoginIP(){
        $session=session('user');
        $ip=$_SERVER['REMOTE_ADDR'];
        if($session&&$session['role']=='T'){
            $condition['userid']=$session['userid'];
            $data=D('user')->where($condition)->find();
            if(is_array($data)){
                if($data['lastloginip']==$ip)
                    return true;
                else {
                    session('user', null); //注销登录，返回异常
                    throw new \Think\Exception(AccessService::getErrorMessage(MyException::LOGIN_BY_OHTER) . ' your address:' . $ip . ',login address:' . $data['lastloginip'], MyException::LOGIN_BY_OHTER);
                }
            }
        }
        return false;
    }

    /**抛出错误信息
     * @param $errorCode
     * @param $errorMessage
     */
    public static function throwException($errorCode,$errorMessage){
        if($errorCode==0) { //如果是默认错误，一般就是数据库问题。
            if(strpos($errorMessage, '[ SQL语句 ]')!==false) {
                $errorCode = 705;
                $errorMessage = substr($errorMessage, 0, strpos($errorMessage, '[ SQL语句 ]'));
            }
            elseif(strpos($errorMessage,'SQLSTATE[HY000] [2002]')!==false) {
                $errorCode = 706;
                $errorMessage='SQLSTATE[HY000] [2002]';
            }
        }
        header('Content-type: text/html; charset=utf-8');
        header("HTTP/1.1 " . $errorCode . " " .$errorMessage." ");
        $redirect='';
        if($errorCode=='701'||$errorCode=="702")
            $redirect='<script language="javascript">top.location="'.__ROOT__.'/Home/Login/login"</script>';
        echo '<html><head>'.$redirect.'</head><body>'.AccessService::getErrorChineseMessage($errorCode).'<br/>'.$errorMessage.'</body></html>';
        die();
    }
    /**获取错误代码消息
     * @param $errorCode
     * @return string
     */
     public static function getErrorMessage($errorCode){
        $error=array(
            '200'=>'ok',
            '701'=>'you have not login system!',
            '702'=>'your account has been used on another computer!',
            '703'=>' without permission!',
            '704'=>'user is not exist!',
            '705'=>'input data error!',
            '706'=>'connect string is wrong!'
        );
        $result=$error[$errorCode]==NULL?'Error code '.$errorCode.'is not defined':$error[$errorCode];
        return $result;
    }
    /**获取错中文错误误代码消息
     * @param $errorCode
     * @return string
     */
    public static function getErrorChineseMessage($errorCode){
        $error=array(
            '200'=>'正常',
            '701'=>'您你尚未登录系统!',
            '702'=>'您的账户已在其他电脑登录!',
            '703'=>'无权访问！',
            '704'=>'用户不存在!',
            '705'=>'输入数据有误！',
            '706'=>'数据库连接字符串错误!',
            '707'=>'访问参数错误!',
        );
        $result=$error[$errorCode]==NULL?'Error code '.$errorCode.'is not defined':$error[$errorCode];
        return $result;
    }
} 