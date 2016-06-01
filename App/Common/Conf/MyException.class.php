<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/26
 * Time: 8:33
 */
namespace Common\Conf;
class MyException {
    const NOT_LOGIN='701'; //尚未登录
    const LOGIN_BY_OHTER='702'; //在其他地方登陆了。
    const WITH_OUT_PERMISSION='703'; //无权访问
    const USER_NOT_EXISTS='704'; //用户不存在
    const INPUT_ERROR='705'; //输入数据有误
    const DATABASE_CONNECT_STRING_ERROR='706'; //数据库连接字符串错误
    const PARAM_NOT_CORRECT='707'; //参数错误
} 