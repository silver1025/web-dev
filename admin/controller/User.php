<?php

namespace app\admin\controller;

use app\admin\model\User as U;

class User extends Common
{
    public function show()
    {
        if (request()->isGet()) {
            $this->success('获取成功', '', $this->fetch());
        }
    }
    public function data()
    {
        if (request()->isPost()) {
                $list = U::where('is_delete', 0)->whereLike('name|openid', "%".input('search')."%")
                ->where('type',input('type'))->limit(input('offset'), input('limit'))->select();
                $all = U::where('is_delete', 0)->whereLike('name|openid', "%".input('search')."%")
                ->where('type',input('type'))->count();
            $data = json(['total' => $all, 'rows' => $list]);
            return $data;
        }
    }
    public function set_repairman()
    {
        if (request()->isPost()) {
            if (U::where('openid', input('openid'))->update(['type'=>input('type')])) {
                return json(['msg'=>'设置成功','code'=>1]);
            } else {
                return json(['msg'=>'设置失败','code'=>0]);
            }
        }
    }
}
