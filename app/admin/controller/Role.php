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
use app\common\access\MyController;
use think\Log;

class Role extends MyController {
//显示信息
    public function query($page = 1, $rows = 20)
    {
        try {
            $Obj =new \app\common\service\Role();
            $result = $Obj->getRoleList($page, $rows);
            return json($result);
        } catch (\Exception $e) {
            MyAccess::throwException($e->getCode(),$e->getMessage());
        }
    }

    //更新信息
    public function update()
    {
        try {
            $Obj =new \app\common\service\Role();
            $result = $Obj->updateRole($_POST);//无法用I('post.')获取二维数组
            return json($result);

        } catch (\Exception $e) {
            Log::record('错误代码：'.$e->getCode());
            Log::record('错误消息：'.$e->getMessage());
            MyAccess::throwException($e->getCode(),$e->getMessage());
        }
    }
} 