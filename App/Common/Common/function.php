<?php
    /**
     * App公共函数库
     * @category   App
     * @package  Common
     * @author   kiee<fangrenfu@126.com>
     */
/**
 * 生成一个GUID值,38位字符
 * @param null
 * @return string  {65034FBB-0526-F03D-5262-5C4314ABD0A7}
 */
function getGuid(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid =strtoupper(md5(uniqid(rand(),true)));
    $hyphen =chr(45); //-
    $uuid=chr(123) //{
        .substr($charid,0,8).$hyphen 
        .substr($charid,8,4).$hyphen 
        .substr($charid,12,4).$hyphen 
        .substr($charid,16,4).$hyphen 
        .substr($charid,20,12)
        .chr(125); //}
    return  $uuid;
}
//日志函数，传入用户名与成功与否的标记。
//@param $name,$role,$userId
//@return void
function writelog($userId='',$success=1,$param="")
{
    $Obj=D('log');
    $session=session('user');
    //有登录信息的话填写
    if($session){
        $data['name']=$session['name'];
    }
    else{
        $data['name']='';
    }
    $data['userid']=$userId;
    $data['success']=$success;
    $data['logid']=getGuid();
    $data['remoteip'] = $_SERVER['REMOTE_ADDR'];
    $data['requesttime'] = date('Y-m-d H:i:s');
    $data['action']=__INFO__;

    $str=json_encode($param);

  //  $data['data']=$str;
    $Obj->add($data);
}




