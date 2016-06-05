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
use think\db\Query;
use think\Request;

class MyLog {
    /**
     * @var Query
     */
    protected  $query;
    public function __construct(){
        //配置日志服务器
        $config=[
            // 数据库类型
            'type'        => 'mysql',
            // 数据库连接DSN配置
            'dsn'         => '',
            // 服务器地址
            'hostname'    => '127.0.0.1',
            // 日志服务器数据库名
            'database'    => 'logdb',
            // 数据库用户名
            'username'    => 'root',
            // 数据库密码
            'password'    => 'comefirst',
            // 数据库连接端口
            'hostport'    => '3306',
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => '',
        ];
        $connection=Db::connect($config);
        $this->query=new Query($connection);
    }

    /**将日志写入数据库
     * @param string $operate
     */
    public function write($operate=''){
        $request = Request::instance();
        $data['host']=$request->domain();
        $data['username']=session("S_USER_NAME");
        $data['name']=session("S_TEACHER_NAME");
        $data['role']=session("S_ROLES");

        $data['url']=$request->url();
        $data['remoteip']=get_client_ip();
        $dataString='';
        //获得插入的内容
        if(isset($_POST["inserted"])){
            $dataString.='insert:'.$_POST["inserted"];
            $operate.='A';
        }
        //获得更新的内容
        if(isset($_POST["updated"])){
            $dataString.='update'.$_POST["updated"];
            $operate.='M';
        }
        //获得删除的内容
        if(isset($_POST["deleted"])){
            $dataString.='delete'.$_POST["deleted"];
            $operate.='M';
        }
        //如果以上都没有，直接输出_POST内容
        $dataString=$dataString==''?json_encode($_POST):$dataString;
        $data['data']=$dataString;
        $data['operate']=$operate;
        $data['requesttime']=date("Y-m-d H:i:s");
        $this->query->table('log')->insert($data);
    }

    public function getList($page=1, $rows=20,$start='',$end='', $username='%', $url='%'){
        $condition=null;
        if($start!='') $condition['requesttime']=array('egt',$start);
        if($end!='') $condition['requesttime']=array('elt',$end);
        if($username!='%') $condition['username']=array('like',$username);
        if($url!='%') $condition['url']=array('like',$url);
        $result=array();
        $data=$this->query->table('log')->field("host,username,name,role,operate,url,remoteip,data,requesttime")
            ->page($page,$rows)->order('requesttime desc')->where($condition)->select();
        $count=$this->query->table('log')->count();
        if(is_array($data)&&count($data)>0){ //小于0的话就不返回内容，防止IE下无法解析rows为NULL时的错误。
            $result=array('total'=>$count,'rows'=>$data);
        }
        return $result;
    }
} 