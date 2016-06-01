<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/22
 * Time: 12:36
 */
namespace Common\Service;
use Common\Model\MyModel;
/**
 * Class LogService
 * @package Admin\Service
 */
class RoleService extends MyModel{
    /**获取角色列表
     * @param int $page
     * @param int $rows
     * @return array
     */
    public function getRoleList($page=1,$rows=1000){
        $result=array();
        $data=$this->order('role')->field("*,'' as checked")->page($page,$rows)->select();
        $count=$this->count();
        if(is_array($data)&&count($data)>0){ //小于0的话就不返回内容，防止IE下无法解析rows为NULL时的错误。
            $result=array('total'=>$count,'rows'=>$data);
        }
        return $result;
    }

    /**更新角色信息
     * @param $postData
     * @return array
     * @throws \Exception
     */
    public function  updateRole($postData){
        $updateRow=0;
        $deleteRow=0;
        $insertRow=0;
        //更新部分
        //开始事务
        $Trans=D();
        $Trans->startTrans();
        try {
            if (isset($postData["inserted"])) {
                $updated = $postData["inserted"];
                $listUpdated = json_decode($updated);
                $data = null;
                foreach ($listUpdated as $one) {
                    $data['role'] = $one->role;
                    $data['name'] = $one->name;
                    $row = $this->add($data);
                    if ($row > 0)
                        $insertRow++;
                }
            }
            if (isset($postData["updated"])) {
                $updated = $postData["updated"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one) {
                    $condition = null;
                    $condition['role'] = $one->role;
                    $data['name'] = $one->name;
                    $updateRow += $this->where($condition)->save($data);
                }
            }
            //删除部分
            if (isset($postData["deleted"])) {
                $updated = $postData["deleted"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one) {
                    $condition = null;
                    $condition['role'] = $one->role;
                    $deleteRow += $this->where($condition)->delete();
                }
            }
        }
        catch(\Think\Exception $e){
            $Trans->rollback();
            throw $e;
        }
        $Trans->commit();
        $info='';
        if($updateRow>0) $info.=$updateRow.'条更新！</br>';
        if($deleteRow>0) $info.=$deleteRow.'条删除！</br>';
        if($insertRow>0) $info.=$insertRow.'条添加！</br>';
        $status=1;
        if($info=='') {
            $info="没有数据被更新";
            $status=0;
        }
        $result=array('info'=>$info,'status'=>$status);
        return $result;
    }
}

