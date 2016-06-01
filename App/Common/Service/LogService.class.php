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
class LogService extends MyModel{
    /**获取日志列表
     * @param int $page
     * @param int $rows
     * @param string $start 开始
     * @param string $end 结束
     * @param string $userId
     * @param string $action
     * @param string $success
     * @return array
     */
   function getLogList($page=1,$rows=10,$start='',$end='',$userId='%',$action='%',$success=''){
       $result=array();
       $condition=null;
       if($start!=''&&$end!='')
           $condition['requesttime']= array('between',array($start,$end));
       if($action!='%') $condition['action']=array('like',$action);
       if($userId!='%') $condition['userid']=array('like',$userId);
       if($success!='') $condition['success']=(int)$success;
       $count= $this->where($condition)->count();// 查询满足要求的总记录数
       // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
       $data=$this->where($condition)->order('requesttime desc')->page($page,$rows)->select();
       if(is_array($data)&&count($data)>0)
            $result=array('total'=>$count,'rows'=>$data);
       return $result;
   }

    /**
     * @return array
     */
    function clearLog()
    {
        $this->where("1=1")->delete();
        $info = '清空成功！';
        $status = 1;
        $result = array('info' => $info, 'status' => $status);
        return $result;
    }
}

