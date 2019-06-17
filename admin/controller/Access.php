<?php

namespace app\admin\controller;

use app\admin\model\Access as A;

class Access extends Common
{
    public function show()
    {
        if (request()->isGet()) {
            $auth_rule = new A();
            $auth_rule_list = $auth_rule -> authRuleTree();
            $this -> assign('auth_rule_list', $auth_rule_list);
            $this->success('获取成功', '', $this->fetch());
        }
    }

    public function delete()
    {
        if (request()->isPost()) {
            $auth_rule = new A();
            $authRuleIds = $auth_rule -> getchilrenid(input('id'));
            $authRuleIds[] = input('id');
            $tag=true;
            // 启动事务
            \think\Db::startTrans();
            try {
                A::where('id', input('id'))->update(['is_delete' => 1]);
                foreach ($authRuleIds as &$item) {
                    A::where('id', $item)->update(['is_delete' => 1]);
                }
                // 提交事务
                \think\Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                \think\Db::rollback();
                $tag=false;
            }
            if ($tag) {
                return json(['msg'=>'删除成功','code'=>1]);
            } else {
                return json(['msg'=>'删除失败','code'=>0]);
            }
        }
    }

    public function lock()
    {
        if (request()->isPost()) {
            $auth_rule = new A();
            $authRuleIds = $auth_rule -> getchilrenid(input('id'));
            $authRuleIds[] = input('id');
            $status=A::where('id', input('id'))->value('status')?0:1;
            $tag=true;
            // 启动事务
            \think\Db::startTrans();
            try {
                A::where('id', input('id'))->update(['status' => $status]);
                foreach ($authRuleIds as &$item) {
                    A::where('id', $item)->update(['status' => $status]);
                }
                // 提交事务
                \think\Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                \think\Db::rollback();
                $tag=false;
            }
            if ($tag) {
                return json(['msg'=>'成功','code'=>1]);
            } else {
                return json(['msg'=>'失败','code'=>0]);
            }
        }
    }

    public function add()
    {
        if (request() -> isGet()) {
            $auth_rule = new A();
            $auth_rule_list = $auth_rule -> authRuleTree();
            $this -> assign('auth_rule_list', $auth_rule_list);
            $this->success('获取成功', '', $this->fetch('add_access'));
        }
    }
    public function add_post_data()
    {
        if (request() -> isPost()) {
            $data = input('post.');
            // 验证是否重复
            $validate = \think\Loader::validate('Access');
            if (!$validate -> check($data)) {
                return json(['msg'=>$validate -> getError(),'code'=>0]);
            }

            $pre_level = A::where('id', $data['pid']) -> field('level') -> find();
            if ($pre_level) {
                $data['level'] = $pre_level['level']+1;
            }
  
            $add = A::insert($data);
            if ($add) {
                return json(['msg'=>'成功','code'=>1]);
            } else {
                return json(['msg'=>'失败','code'=>0]);
            }
        }
    }

    public function edit()
    {
        if (request() -> isGet()) {
            $auth_rule = new A();
            $auth_rule_list = $auth_rule -> authRuleTree();
            $auth_rule = $auth_rule -> find(input('id'));
            $this -> assign(
                array(
                'auth_rule_list' => $auth_rule_list,
                'auth_rule' => $auth_rule
              )
            );
            $this->success('获取成功', '', $this->fetch('edit_access'));
        }
    }
  
    public function edit_post_data()
    {
        if (request() -> isPost()) {
            $data = input('post.');
            // 验证是否重复
            $validate = \think\Loader::validate('Access');
            if (!$validate -> check($data)) {
                return json(['msg'=>$validate -> getError(),'code'=>0]);
            }

            $pre_level = A::where('id', $data['pid']) -> field('level') -> find();
            if ($pre_level) {
                $data['level'] = $pre_level['level']+1;
            }
  
            $edit = A::update($data);
            if ($edit) {
                return json(['msg'=>'成功','code'=>1]);
            } else {
                return json(['msg'=>'失败','code'=>0]);
            }
        }
    }
}
