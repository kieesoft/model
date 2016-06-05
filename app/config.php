<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,

    'template'               => [
        // 模板文件名分隔符，默认为文件夹分隔符
        /*  'view_depr'    => DS,*/
        'view_depr'    => '_',
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '<',
        // 标签库标签结束标记
        'taglib_end'   => '>',
    ],
// 默认模块名
    'default_module'         => 'home',
// 禁止访问模块

// 默认控制器名
    'default_controller'     => 'index',
// 默认操作名
    'default_action'         => 'index',

    'log'                    => [
        'type' => 'File',  // 日志记录方式，支持 file socket trace sae
        'path' => LOG_PATH,  // 日志保存目录
    ],
];
