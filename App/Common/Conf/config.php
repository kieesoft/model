<?php
return array(
    //'配置项'=>'配置值'

    'DB_TYPE' => 'mysql',
  //  'DB_HOST' =>'127.0.0.1:3306',
    'DB_HOST' =>'172.18.0.42:3336',
    'DB_NAME'=>'model',
    'DB_USER'=>'model',
    'DB_PWD'=>'comefirst',
    'DB_PREFIX'=>'',
    'DB_PARAMS'    =>    array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
    'DEFAULT_MODULE'=>'Home',
    'DEFAULT_CONTROLLER'    =>  'Login',
    'DEFAULT_ACTION'        =>  'login', // 默认操作名称
    'URL_MODEL'=>2,   //URL地址类型
    //模版标记起止符  1
    'TMPL_L_DELIM'	=>'{%',
    'TMPL_R_DELIM' =>'%}',
    'TMPL_FILE_DEPR'=>'_',  //Action_Function.html格式的模板文件，减少层次结构
    'DB_FIELDTYPE_CHECK'=>true,  // 开启字段类型验证
    'DB_FIELDS_CACHE'=>false, //不缓存字段名称。开发时建议
    'LOG_RECORD'            =>  true,
    'LOG_EXCEPTION_RECORD'  =>  true,
    'TMPL_PARSE_STRING'  =>array( //模板配置文件
        '__TITLE__' => '宁波城市学院教务管理系统',
        '__COPYRIGHT__' => '版权所有：宁波城市职业技术学院 @2015    技术支持：方仁富  88221932',
        '__WELCOME__' => '欢迎使用'
    )
);