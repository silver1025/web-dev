<?php

namespace app\admin\controller;

use app\admin\model\Role as R;

class Role extends Common
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
            $list = R::where('is_delete', 0)
            ->whereLike('title', "%".input('search')."%")
            ->limit(input('offset'), input('limit'))->select();
            $all = R::where('is_delete', 0)
            ->whereLike('title', "%".input('search')."%")
            ->count();
            $data = json(['total' => $all, 'rows' => $list]);
            return $data;
        }
    }

    public function lock()
    {
        if (request()->isPost()) {
            if ($user=R::where('id', input('id'))->find()) {
                $user->status=!$user->status;
                if ($user->save()) {
                    return json(['msg'=>'成功','code'=>1]);
                } else {
                    return json(['msg'=>'失败','code'=>0]);
                }
            }
        }
    }

    //del group
    public function delete()
    {
        if (request()->isPost()) {
            if (R::where('id', input('id'))->update(['is_delete' => 1])) {
                return json(['msg'=>'删除成功','code'=>1]);
            } else {
                return json(['msg'=>'删除失败','code'=>0]);
            }
        }
    }

    public function edit()
    {
        if (request() -> isGet()) {
            //query the group needed edit
            $auth_rule = new \app\admin\model\Access();
            $auth_rule_list = $auth_rule -> authRuleTree();
            $this -> assign('auth_rule_list', $auth_rule_list);
            $auth_group = R::where('id', input('id'))->find();
            $this -> assign('auth_group', $auth_group);
            $this->success('获取成功', '', $this->fetch('edit_role'));
        }
    }

    public function edit_post_data()
    {
        if (request() -> isPost()) {
            $data = input('post.');
            if (!array_key_exists("rules", $data)) {
                return json(['msg'=>'没有选择权限','code'=>0]);
            }
  
            if (array_key_exists("rules", $data)) {
                $data['rules'] = implode(',', $data['rules']);
            }else {
                $data['rules'] = '';
            }
  
            $save = R::update($data);
            if ($save) {
                return json(['msg'=>'编辑成功','code'=>1]);
            } else {
                return json(['msg'=>'编辑失败','code'=>0]);
            }
        }
    }
  
    public function add()
    {
        if (request() -> isGet()) {
            $auth_rule = new \app\admin\model\Access();
            $auth_rule_list = $auth_rule -> authRuleTree();
            $this ->assign('auth_rule_list', $auth_rule_list);
            $this->success('获取成功', '', $this->fetch('add_role'));
        }
    }

    public function add_post_data()
    {
        if (request() -> isPost()) {
            $data = input('post.');
  
            // 验证是否选择了rule
            if (!array_key_exists("rules", $data)) {
                return json(['msg'=>'没有选择权限','code'=>0]);
            }
  
            if (array_key_exists("rules", $data)) {
                $data['rules'] = implode(',', $data['rules']);
            }else {
                $data['rules'] = '';
            }

            $add = R::insert($data);
            if ($add) {
                return json(['msg'=>'添加用户组成功','code'=>1]);
            } else {
                return json(['msg'=>'添加用户组失败','code'=>0]);
            }
        }
    }

}
