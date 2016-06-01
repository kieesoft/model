<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/22
 * Time: 12:36
 */
namespace Common\Service;
use Common\Model\MyModel;

/**动作服务
 * Class ActionService
 * @package Admin\Service
 */
class ActionService extends MyModel{
    /**判断该节点是否有子节点
     * @param $id
     * @return bool
     */
    private function hasChild($id){
        $condition['parentid']=$id;
        $count=$this->where($condition)->count();
        return $count>0?true:false;
    }

    /**获取所有父亲节点
     * @param $id
     */
    private function appendParentNode($nodes,$id){
        //检查是否已经在了或者为顶级节点
        if($id==1||in_array($id,$nodes['id'])) {
            return $nodes;
        }
        else{
            $condition['id']=$id;
            $data=$this->where($condition)->find();
            if(is_array($data)&&count($data)>0){
                array_push($nodes['node'],$this->buildNode($data,'open'));
                array_push($nodes['id'],$data['id']);
                $nodes=$this->appendParentNode($nodes,$data['parentid']);
                return $nodes;
            }
            else
                return $nodes;
        }
    }

    /**检索action条目
     * @param string $action
     * @param string $description
     * @param string $searchId
     * @return array
     */
    private function searchAction($action='%',$description='%',$searchId=''){
        $result=array();
        $nodeArray=null;
        $idArray=null;
        $condition=null;
        if($searchId!='') $condition['action.id']=$searchId;
        if($action!='%') $condition['action.action']=array('like',$action);
        if($description!='%') $condition['action.description']=array('like',$description);
        $data = $this->where($condition)->order('action.rank,action.action')->select();
        if (count($data)>0) {
            foreach ($data as $one) {
                if($nodeArray==null) {
                    $nodeArray[] = $this->buildNode($one,'open');
                    $idArray[]=$one['id'];
                }
                else {
                    array_push($nodeArray, $this->buildNode($one,'open'));
                    array_push($idArray,$one['id']);
                }
                $nodes=$this->appendParentNode(array('node'=>$nodeArray,'id'=>$idArray),$one['parentid']);
                $nodeArray=$nodes['node'];
                $idArray=$nodes['id'];
            }
            $result = array('total' =>count($nodes['node']), 'rows' => $nodes['node']);
        }
        return $result;
    }

    /**构建节点。
     * @param $one int 单一节点
     * @param string $state 打开与关闭状态
     * @return array
     */
    private function buildNode($one,$state=''){
        if($state=='') $state = $this->hasChild($one['id']) ? 'closed' : 'open';
        if($one['parentid']==1) {
            return array('id' => $one['id'], 'description' => $one['description'], 'action' => $one['action'], 'rank' => $one['rank'],
                'image' => $one['image'], 'ismenu' => $one['ismenu'],'state'=>$state,'parentid'=>$one['parentid'],'shortname'=>$one['shortname']);
        }
        else
            return array('id'=>$one['id'],'description'=>$one['description'],'action'=>$one['action'],'rank'=>$one['rank'],
                'image'=>$one['image'],'ismenu'=>$one['ismenu'],'_parentId'=>$one['parentid'],'state'=>$state,'parentid'=>$one['parentid'],'shortname'=>$one['shortname']);
    }
    private function getChildAction($parentId=1){
        $result=array();
        $new=array();
        $condition['parentid'] =$parentId;
        $data = $this->field('*, parentid as _parentId')->where($condition)->order('action.rank,action.action')->select();
        $count = $this->where($condition)->count();
        if (is_array($data)&&count($data)>0) {
            foreach($data as $one) {
                $new[] = $this->buildNode($one);
            }
            $result = array('total' => $count, 'rows' => $new);
        }
        return $result;
    }
    /**获取用户授权的菜单列表
     * @param string $userId 用户ID
     * @param int $parentId 开始节点ID，默认为1
     * @return array
     */
    public function getUserAccessMenu($userId='',$parentId=1){
        $Obj = D('Common/User', 'Service');
        $result= $Obj->getUserRole($userId);
        $menu=array();
        $condition=null;
        $condition['actionrole.role']=array('in',$result);
        $condition['action.ismenu']=1;
        $condition['action.parentid']=$parentId;
        $condition['action.id']=array('NEQ',1);
        $data=$this->join('inner join actionrole on actionrole.actionid=action.id')
            ->field('distinct action.image as icon,action.description as menuname,action.id menuid,action.action url')->order('action.rank')->where($condition)->select();
        if(is_array($data)&&count($data)>0){
            foreach ($data as $one) {
                $array=null;
                $menudata=$this->getUserAccessMenu($userId,$one['menuid']);
                $array[]=array('menuid' => $one['menuid'], 'menuname' => $one['menuname'], 'icon' => $one['icon'],'url'=>__ROOT__.$one['url'],'menus'=>$menudata);
                $menu=array_merge($menu,$array);
            }
        }
        return $menu;
    }

    /**获取Action列表
     * @param string $action
     * @param string $description
     * @param string $searchId
     * @param int $parentId
     * @return array
     */
    public function getActionList($action='',$description='',$searchId='',$parentId=1){
        if($searchId==''&&$action==''&$description=='')
            return $this->getChildAction($parentId); //如果没有设置检索条件，那就获取儿子节点
        else {
            return $this->searchAction($action, $description, $searchId); //有检索条件就开始检索
        }
    }
    /**
     * @param int $page 页码
     * @param int $rows 每页行
     * @param string $id ActionID
     * @return array|string
     */
    public function getActionRole($page=1,$rows=10,$id=''){
        $result = array();
        $Obj = D('actionrole');
        if ($id != '') {
            $condition['actionid'] = $id;
            $data = $Obj->join('role on role.role=actionrole.role')
                ->field('role.name,actionrole.*,actionrole.access&16 as exe,actionrole.access&8 as ins,actionrole.access&4 as del,actionrole.access&2 as modi,actionrole.access&1 as que')
                ->where($condition)->page($page, $rows)->order('role')->select();
            $count = $Obj->where($condition)->count();
            if (is_array($data)&&count($data)>0)
                $result = array('total' => $count, 'rows' => $data);
        }
        return $result;
    }
    /**更新Action
     * @param $postData
     * @return array
     * @throws \Exception
     */
    public function  updateAction($postData)
    {
        $updateRow = 0;
        $deleteRow = 0;
        $insertRow = 0;

        $Trans = D();
        $Trans->startTrans();
        //更新部分
        try {
            if (isset($postData["updated"])) {
                $one = $postData["updated"];
                if($one['id']==0){
                    $data['ismenu'] = (int)$one['ismenu'];
                    $data['image'] = $one['image'];
                    $data['action'] = $one['action'];
                    $data['description'] = $one['description'] ;
                    $data['parentid'] = $one['parentid'];
                    $data['shortname'] = $one['shortname'];
                    $data['rank'] = $one['rank'];
                    $row = $this->add($data);
                    if ($row > 0)
                        $insertRow++;
                }
                else {
                    $condition['id'] = $one['id'];
                    $data['image'] = $one['image'];
                    $data['action'] = $one['action'];
                    $data['description'] = $one['description'] ;
                    $data['parentid'] = $one['parentid'];
                    $data['shortname'] = $one['shortname'];
                    $data['rank'] = $one['rank'];
                    $updateRow += $this->where($condition)->save($data);
                }
            }
            //删除部分
            if (isset($postData["deleted"])) {
                $one= $postData["deleted"];
                //删除授权表信息
                $Obj = D('actionrole');
                $condition = null;
                $condition['actionid'] = $one['id'];
                $Obj->where($condition)->delete();
                //删除动作信息表
                $condition = null;
                $condition['id'] = $one['id'];
                $deleteRow += $this->where($condition)->delete();
            }
        }
        catch (\Exception $e) {
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
    /**更新Action的授权角色
     * @param $postData
     * @return array
     * @throws \Exception
     */
    public function updateActionRole($postData)
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
                    $condition['id'] = $one->id;
                    $data['actionid'] = $one->actionid;
                    $data['access']=(int)((int)$one->exe|(int)$one->ins|(int)$one->del|(int)$one->modi|(int)$one->que);
                    $data['role'] = $one->role;
                    $Obj = D('actionrole');
                    $updateRow += $Obj->where($condition)->save($data);
                }
            }
            //删除部分
            if (isset($postData["deleted"])) {
                $updated = $postData["deleted"];
                $listUpdated = json_decode($updated);
                foreach ($listUpdated as $one) {
                    $condition['id'] = $one->id;
                    $Obj = D('actionrole');
                    $deleteRow += $Obj->where($condition)->delete();
                }
            }
            if (isset($postData["inserted"])) {
                $updated = $postData["inserted"];
                $listUpdated = json_decode($updated);
                $data = null;
                foreach ($listUpdated as $one) {
                    $data['role'] = $one->role;
                    $data['actionid'] = $one->actionid;
                    $data['access']=(int)((int)$one->exe|(int)$one->ins|(int)$one->del|(int)$one->modi|(int)$one->que);
                    $Obj = D('actionrole');
                    $row = $Obj->add($data);
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

