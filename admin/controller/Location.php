<?php

namespace app\admin\controller;

use app\admin\model\Location as L;

class Location extends Common
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
            $list = L::where('is_delete', 0)->whereLike('location', "%".input('search')."%")
            ->limit(input('offset'), input('limit'))->select();
            $all = L::where('is_delete', 0)->whereLike('location', "%".input('search')."%")->count();
            $data = json(['total' => $all, 'rows' => $list]);
            return $data;
        }
    }
    public function add()
    {
        if (request()->isPost()) {
            if (L::create(input())) {
                return json(['msg'=>'添加成功','code'=>1]);
            }
        }
    }
    public function delete()
    {
        if (request()->isPost()) {
            if (L::where('id', input('id'))->update(['is_delete'=>1])) {
                return json(['msg'=>'删除成功','code'=>1]);
            } else {
                return json(['msg'=>'删除失败','code'=>0]);
            }
        }
    }
}
