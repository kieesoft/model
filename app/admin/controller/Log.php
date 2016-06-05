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

namespace app\admin\controller;


use app\common\access\MyAccess;

class Log {
    public function query($page = 1, $rows = 10,$start='',$end='', $username = '%', $url = '%')
    {
        try {
            $Obj = new \app\common\access\MyLog();
            $result = $Obj->getList($page, $rows,$start,$end, $username, $url);
            return json($result);
        } catch (\Exception $e) {
            MyAccess::throwException($e->getCode(), $e->getMessage());
        }
    }
} 