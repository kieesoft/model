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
class SchoolService extends MyModel{
    /**获取学院列表
     * @param int $page
     * @param int $rows
     * @param int $fail
     * @return array
     */
   public function getSchoolList($page=1,$rows=10,$fail=-1){
       $result=array();
       $condition=null;
       if($fail!=-1) $condition['fail']=$fail; //注意在php中数字0和空字符是相等的,但是类型不等
       $data=$this->order('school')->page($page,$rows)->where($condition)->select();
       $count=$this->where($condition)->count();
       if(is_array($data)&&count($data)>0){ //小于0的话就不返回内容，防止IE下无法解析rows为NULL时的错误。
           $result=array('total'=>$count,'rows'=>$data);
       }
       return $result;
   }

    /**更新学院信息
     * @param $postData
     * @return array
     * @throws \Exception
     */
    public function  updateSchool($postData)
    {
        $updateRow = 0;
        $deleteRow = 0;
        $insertRow = 0;
        //更新部分
        //开始事务
        $Trans = D();
        $Trans->startTrans();
        try {
            if (isset($postData["updated"])) {
                $updated = $postData["updated"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one) {
                    $condition = null;
                    $condition['school'] = $one->school;
                    $data['name'] = $one->name;
                    $data['fail'] = (int)$one->fail;
                    $data['manage'] = (int)$one->manage;
                    $updateRow += $this->where($condition)->save($data);
                }
            }
            //删除部分
            if (isset($postData["deleted"])) {
                $updated = $postData["deleted"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one) {
                    $condition = null;
                    $condition['school'] = $one->school;
                    $deleteRow += $this->where($condition)->delete();
                }
            }
            if (isset($postData["inserted"])) {
                $updated = $postData["inserted"];
                $listUpdated = json_decode($updated);
                $data = null;
                foreach ($listUpdated as $one) {
                    $data['school'] = $one->school;
                    $data['name'] = $one->name;
                    $data['fail'] = (int)$one->fail;
                    $data['manage'] = (int)$one->manage;
                    $row = $this->add($data);
                    if ($row > 0)
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
}

