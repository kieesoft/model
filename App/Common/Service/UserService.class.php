<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/22
 * Time: 12:36
 */
namespace Common\Service;
use Common\Conf\MyException;
use Common\Model\MyModel;

/**用户接口
 * Class UserService
 * @package Admin\Service
 */
class UserService extends MyModel{
    /**获取用户列表
     * @param int $page
     * @param int $rows
     * @param string $userId
     * @param string $name
     * @param string $school
     * @return array
     */
    public function getUserList($page=1,$rows=10,$userId='%',$name='%',$school=''){
        $result=array();
        $condition=null;
        if($school!='') $condition['user.school']= $school;
        if($name!='%') $condition['user.name']=array('like',$name);
        if($userId!='%')  $condition['user.userid']=array('like',$userId);
        $data=$this->join('school on school.school=user.school')
            ->field('user.role,user.id,userid,user.name,sex,lock,lastlogintime,lastloginip,createdate,teacherno,school.school,school.name as schoolname')
            ->where($condition)->page($page,$rows)->order('id desc')->select();
        $count=$this->where($condition)->count();
        if(is_array($data)&&count($data)>0){ //小于0的话就不返回内容，防止IE下无法解析rows为NULL时的错误。
            $result=array('total'=>$count,'rows'=>$data);
        }
        return $result;
    }

    /**更新用户信息
     * @param $postData
     * @return array
     * @throws \Exception
     */
    public function  updateUser($postData)
    {
        $updateRow = 0;
        $deleteRow = 0;
        $insertRow = 0;
        //更新部分
        //开始事务
        $Trans = D();
        $Trans->startTrans();
        try {
            if(isset($postData["updated"])){
                $updated = $postData["updated"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one){
                    $condition=null;
                    $condition['id']=$one->id;
                    $data['lock']=(int)$one->lock;
                    $data['userid']=$one->userid;
                    $data['school']=$one->school;
                    $data['name']=$one->name;
                    $data['sex']=$one->sex;
                    $updateRow+=$this->where($condition)->save($data);
                }
            }
            //删除部分
            if(isset($postData["deleted"])){
                $updated = $postData["deleted"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one){
                    $condition=null;
                    $condition['id']=$one->id;
                    $deleteRow+=$this->where($condition)->delete();
                }
            }
            if(isset($postData["inserted"])){
                $updated = $postData["inserted"];
                $listUpdated = json_decode($updated);
                $data=null;
                foreach ($listUpdated as $one){
                    $data['lock']=(int)$one->lock;
                    $data['userid']=$one->userid;
                    $data['teacherno']=$one->teacherno;
                    $data['school']=$one->school;
                    $data['name']=$one->name;
                    $data['createdate']=date("Y-m-d H:i:s");
                    $data['sex']=$one->sex;
                    $data['password']=md5($one->teacherno);
                    $row=$this->add($data);
                    if($row>0)
                        $insertRow++;
                }
            }
        } catch (\Exception $e) {
            $Trans->rollback();
            throw $e;
        }
        $Trans->commit();
        $info = '';
        if ($updateRow > 0) $info .= $updateRow . '条更新！</br>';
        if ($deleteRow > 0) $info .= $deleteRow . '条删除！</br>';
        if ($insertRow > 0) $info .= $insertRow . '条添加！</br>';
        $status = 1;
        if($info=='') {
            $info="没有数据被更新";
            $status=0;
        }
        $result = array('info' => $info, 'status' => $status);
        return $result;
    }

    /**
     * @param string $userId
     * @throws \Think\Exception
     */
    public function getUserRole($userId='')
    {
        $condition['userid']=$userId;
        $data=$this->where($condition)->find();
        if(is_array($data)&&count($data)>0){
            return str_split($data['role']);
        }
        else
            throw new \Think\Exception(AccessService::getErrorMessage(MyException::USER_NOT_EXISTS).$userId,MyException::USER_NOT_EXISTS);
    }

    /**更新用户角色
     * @param int $userId
     * @param string $role
     * @return array
     */
    public  function  updateUserRole($userId=0,$role='*')
    {

        $condition['id']= $userId;
        $data['role']=$role;
        $this->where($condition)->save($data);
        $status = 1;
        $info="保存成功";
        $result = array('info' => $info, 'status' => $status);
        return $result;
    }

    /**修改z制定用户密码
     * @param $uerId
     * @param $password
     * @return bool
     */
    public function changeUserPassword($uerId,$password){
        //todo:完成功能
        $condition['userid']= $uerId;
        $data['password']=md5($password);
        $this->where($condition)->setField($data);
        return true;
    }

    /**修改本人密码
     * @param $oldPassword
     * @param $newPassword
     * @return bool
     */
    public function changeSelfPassword($oldPassword,$newPassword){
        $session=session('user');
        $condition['userid']= $session['userid'];
        $condition['password']= md5($oldPassword);
        $data['password']=md5($newPassword);
        $result= $this->where($condition)->find();

        if(is_array($result)&&count($result)>0)
        {
            $this->where($condition)->setField($data);
            return true;
        }
        else
            return false;
    }
}

