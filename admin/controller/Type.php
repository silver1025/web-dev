<?php

namespace app\admin\controller;

use app\admin\model\Type as Tp;

class Type extends Common
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
            $list = Tp::where('is_delete', 0)->whereLike('type', "%".input('search')."%")
            ->limit(input('offset'), input('limit'))->select();
            $all = Tp::where('is_delete', 0)->whereLike('type', "%".input('search')."%")->count();
            $data = json(['total' => $all, 'rows' => $list]);
            return $data;
        }
    }
    public function add()
    {
        if (request()->isPost()) {
            if (Tp::create(input())) {
                return json(['msg'=>'添加成功','code'=>1]);
            } else {
                return json(['msg'=>'添加失败','code'=>0]);
            }
        }
    }
    public function delete()
    {
        if (request()->isPost()) {
            if (Tp::where('id', input('id'))->update(['is_delete'=>1])) {
                return json(['msg'=>'删除成功','code'=>1]);
            } else {
                return json(['msg'=>'删除失败','code'=>0]);
            }
        }
    }
}
