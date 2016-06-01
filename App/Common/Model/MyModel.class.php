<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/4
 * Time: 14:05
 */
namespace Common\Model;
use Think\Model;
use Common\Service\AccessService;
/**定义MyModel类，用于权限控制
 * Class MyModel
 * @package Common\Model
 */
class MyModel extends Model {
    /**重写save操作
     * @param string $data
     * @param array $options
     * @return bool
     */
    public function save($data='',$options=array()){
        AccessService::checkAccess('M');
        return parent::save($data,$options);
    }
    /**重写add操作
     * @param string $data
     * @param array $options
     * @param bool $replace
     * @return mixed
     */
    public function add($data='',$options=array(),$replace=false){
        AccessService::checkAccess('A');
        return parent::add($data,$options,$replace);
    }

    /**重写select操作
     * @param array $options
     * @return mixed
     */
    public function select($options=array())
    {
        AccessService::checkAccess('R');
        return parent::select($options);
    }

    /**重写delete操作
     * @param array $options
     * @return mixed
     */
    public function delete($options=array()){
        AccessService::checkAccess('D');
        return parent::delete($options);
    }

    /**重写find操作
     * @param array $options
     * @return mixed
     */
    public function find($options=array()){
        AccessService::checkAccess('R');
        return parent::find($options);
    }

    /**重写query操作
     * @param string $sql
     * @param bool $parse
     * @return mixed
     */
    public function query($sql,$parse=false){
        AccessService::checkAccess('R');
        return  parent::query($sql,$parse);
    }

    /**重写execute操作
     * @param string $sql
     * @param bool $parse
     * @return false|int
     */
    public function execute($sql,$parse=false){
        AccessService::checkAccess('E');
        return parent::execute($sql,$parse);
    }

    /**重写procedure操作
     * @param string $sql
     * @param bool $parse
     * @return array
     */
    public function procedure($sql, $parse = false){
        AccessService::checkAccess('E');
        return  parent::procedure($sql,$parse);
    }
}