<?php

namespace app\admin\controller;

use think\Cache;

class Option extends Common
{
    public function show()
    {
        $this->success('获取成功', '', $this->fetch());
    }

    public function get_list()
    {
        $total=\app\admin\model\Option::whereLike('username|ip|user_nickname|url|method', "%".input('search')."%")->count();
        $list=\app\admin\model\Option::whereLike('username|ip|user_nickname|url|method', "%".input('search')."%")
        ->limit(input('offset'), input('limit'))->order('create_time desc')->select();
        return json(['total'=>$total,'rows'=>$list]);
    }

    public function detail()
    {
        $this->assign('info', \app\admin\model\Option::get(input('id')));
        $this->success('获取成功', '', $this->fetch());
    }
}
