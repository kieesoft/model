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

namespace app\common\access;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;

class MyAccess {

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
            'E'=>'batch execute',
            'A'=>'add'
        );
        return 'id:'.$id.'<br/>action:'.$action . '<br/>'.$operation[$operate].MyAccess::getErrorMessage($errorCode);
    }
    /**检查是否有读取权限
     * @param string $operate操作类型：R读取、M修改、D删除、A增加、E执行，默认为读取
     * @return bool
     * @throws \Think\Exception
     */
    public static  function checkAccess($operate='R'){
        $request = Request::instance();
        $action='/'.$request->module().'/'.$request->controller().'/'.$request->action();
        Log::record('myaction'.$action);
        Log::record('myaction'.$request->root());
        $session=session("S_LOGIN_TYPE");
        if($session){
            //首先检查用户权限表
            if($session==1){
                $condition=null;
                $condition['username']=session('S_USER_NAME');;
                $role =\think\Db::table('users')->where($condition)->field('rtrim(roles) roles')->find(); //获取用户角色
                $condition=null;
                $condition["action.action"] = $action;
                $actionInfo=\think\Db::table('action')->where($condition)->find();
                if(is_array($role)&&is_array($actionInfo)) {
                    $roles = str_split($role['roles']);
                    $condition=null;
                    $condition["action.action"] = $action;
                    $condition['actionrole.role'] = array('in', $roles);
                    $data = \think\Db::table('action')->join('actionrole','actionrole.actionid=action.id')
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
                                throw new Exception($action . ' undefined operation' .MyAccess::getErrorMessage(MyException::WITH_OUT_PERMISSION),MyException::WITH_OUT_PERMISSION);
                                break;
                        }
                    }
                    throw new Exception(MyAccess::buildMessage($actionInfo['id'],$action,$operate,MyException::WITH_OUT_PERMISSION), MyException::WITH_OUT_PERMISSION);

                }
                else{
                    throw new Exception($action . ' is not found!' .MyAccess::getErrorMessage(MyException::WITH_OUT_PERMISSION), MyException::WITH_OUT_PERMISSION);
                }
            }

        }
        else
            throw new Exception(MyAccess::getErrorMessage(MyException::NOT_LOGIN),MyException::NOT_LOGIN);
        return false;
    }

    /**抛出错误信息
     * @param $errorCode
     * @param $errorMessage
     */
    public static function throwException($errorCode,$errorMessage){
        if(MyAccess::getErrorMessage($errorCode)=='not defined error')
            $errorCode='700';
        header('Content-type: text/html; charset=utf-8');
        header("HTTP/1.1 ".$errorCode." ".$errorMessage." ");

        $redirect='';
        if($errorCode=='701'||$errorCode=="702")
            $redirect='<script language="javascript">top.location="'.Request::instance()->root().'/Home/Index/login"</script>';
        echo '<html><head>'.$redirect.'</head><body>'.MyAccess::getErrorChineseMessage($errorCode).'<br/>'.$errorMessage.'</body></html>';
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
            '705'=>'parameter is not correct!',
        );
        $result=!isset($error[$errorCode])?'not defined error':$error[$errorCode];
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
            '705'=>'访问参数错误!',
        );
        $result=!isset($error[$errorCode])?'未定义错误！':$error[$errorCode];
        return $result;
    }
    /**
     * 确认课程任课教师
     */
    public static function  checkCourseTeacher($year='',$term='',$courseno=''){
        if($year==''||$term==''||$courseno=='')
            throw new Exception(MyAccess::getErrorMessage(MyException::PARAM_NOT_CORRECT), MyException::PARAM_NOT_CORRECT);
        $condition['year']=$year;
        $condition['term']=$term;
        $condition['courseno']=$courseno;
        $condition['teacherno']=session('S_TEACHERNO');
        $data= \think\Db::table('viewteachercourse')->where($condition)->find();
        if(is_array($data))
            return true;
        else
            return false;
    }

    /*
     * 检查教师所在学院是否与登录账户一致。
     */
    public static function checkTeacherSchool($teacherno=''){

        $condition['teacherno']=$teacherno;
        $data= \think\Db::table('teachers')->where($condition)->field('school')->find();
        if(!is_array($data))
            throw new Exception(MyAccess::getErrorMessage(MyException::PARAM_NOT_CORRECT), MyException::PARAM_NOT_CORRECT);

        if($data['school']==session('S_USER_SCHOOL')||session('S_MANAGE')==1){
            return true;
        }
        else{
            return false;
        }

    }
    /**检查学生所在学院是不是和操作员一致
     * @param string $studentno 学号
     * @return bool true一致/false 不一致
     * @throws \Think\Exception
     */
    public static function checkStudentSchool($studentno=''){
        $condition=null;
        $condition['students.studentno']=$studentno;
        $data= \think\Db::table('students')->join('classes','classes.classno=students.classno') //取班级中的学院
        ->where($condition)->field('classes.school')->find();
        if(!is_array($data))
            throw new Exception(MyAccess::getErrorMessage(MyException::PARAM_NOT_CORRECT), MyException::PARAM_NOT_CORRECT);
        if($data['school']==session('S_USER_SCHOOL')||session('S_MANAGE')==1){
            return true;
        }
        else{
            return false;
        }
    }

    /**检查班级的学院是否与操作员一致
     * @param string $classno
     * @return bool
     * @throws Exception
     * @throws \think\exception\DbException
     */
    public static function checkClassSchool($classno=''){
        $condition=null;
        $condition['classno']=$classno;

        $data= \think\Db::table('classes')->where($condition)->field('classes.school')->find();
        if(!is_array($data))
            throw new Exception(MyAccess::getErrorMessage(MyException::PARAM_NOT_CORRECT), MyException::PARAM_NOT_CORRECT);
        if($data['school']==session('S_USER_SCHOOL')||session('S_MANAGE')==1){
            return true;
        }
        else{
            return false;
        }
    }
}