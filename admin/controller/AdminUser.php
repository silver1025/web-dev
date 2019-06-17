<?php

namespace app\admin\controller;

use app\admin\model\AdminUser as U;
use app\admin\model\Role as R;

class AdminUser extends Common
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
            $list = U::where('user_group', '>',0)->where('is_delete', 0)
            ->whereLike('username|user_nickname', "%".input('search')."%")
            ->limit(input('offset'), input('limit'))->select();
            foreach ($list as $key => $value) {
                $value['group_title'] = R::where('id',$value['user_group'])->value('title');
            }
            $all = U::where('user_group', '>',0)->where('is_delete', 0)
            ->whereLike('username|user_nickname', "%".input('search')."%")
            ->count();
            $data = json(['total' => $all, 'rows' => $list]);
            return $data;
        }
    }
    public function edit(){
        if (request()->isGet()) {
            $admin = U::where('id',input('id'))->find();
            $auth_group_list = R::where('status', 1) -> where('is_delete', 0)->select();
            $this -> assign('admin', $admin);
            $this -> assign('auth_group_list', $auth_group_list);
            $this->success('获取成功', '', $this->fetch('edit_admin_user'));
        }
    }
    public function edit_post_data()
    {
        if (request()->isPost()) {
            $data=[];
            $salt=rand(1, 1000);
            $data['id']=input('id');
            $data['username']=input('username');
            $data['user_nickname']=input('user_nickname');
            $data['user_group']=input('user_group');
            $data['password']=md5(input('password').$salt);
            $data['salt']=$salt;
            if (U::update($data)) {
                return json(['msg'=>'修改成功','code'=>1]);
            } else {
                return json(['msg'=>'修改失败','code'=>0]);
            }
        }
    }
    public function add(){
        if (request()->isGet()) {
            $auth_group_list = R::where('status', 1) -> where('is_delete', 0)->select();
            $this -> assign('auth_group_list', $auth_group_list);
            $this->success('获取成功', '', $this->fetch('add_admin_user'));
        }
    }
    public function add_post_data()
    {
        if (request()->isPost()) {
            if (U::where('username', input('username'))->where('is_delete', 0)->find()) {
                return json(['msg'=>'账号已存在','code'=>0]);
            }
            $data=[];
            $salt=rand(1, 1000);
            $data['username']=input('username');
            $data['user_nickname']=input('user_nickname');
            $data['user_group']=input('user_group');
            $data['password']=md5(input('password').$salt);
            $data['salt']=$salt;
            if (U::insert($data)) {
                return json(['msg'=>'添加成功','code'=>1]);
            } else {
                return json(['msg'=>'添加失败','code'=>0]);
            }
        }
    }
    public function delete()
    {
        if (request()->isPost()) {
            if (U::where('id', input('id'))->update(['is_delete' => 1])) {
                return json(['msg'=>'删除成功','code'=>1]);
            } else {
                return json(['msg'=>'删除失败','code'=>0]);
            }
        }
    }
    public function lock()
    {
        if (request()->isPost()) {
            if ($user=U::where('id', input('id'))->find()) {
                $user->statu=!$user->statu;
                if ($user->save()) {
                    return json(['msg'=>'成功','code'=>1]);
                } else {
                    return json(['msg'=>'失败','code'=>0]);
                }
            }
        }
    }
    public function reset()
    {
        if (request()->isPost()) {
            if ($user=U::where('id', input('id'))->find()) {
                $password='sspku'.(string)rand(1, 1000);
                $salt=rand(1, 1000);
                $user->password=md5($password.$salt);
                $user->salt=$salt;
                if ($user->save()) {
                    return json(['msg'=>'密码重置成功，新密码为'.$password,'code'=>1]);
                } else {
                    return json(['msg'=>'密码重置失败','code'=>0]);
                }
            }
        }
    }
    public function change_psd()
    {
        if (request()->isPost()) {
            if ($user=\app\admin\model\AdminUser::where('username', session('username'))->find()) {
                if ($user->password==md5(input('old_psd').$user->salt)) {
                    $user->password=md5(input('new_psd').$user->salt);
                    $user->save();
                    return json(['msg'=>'修改成功！','code'=>1]);
                } else {
                    return json(['msg'=>'旧密码不正确！','code'=>0]);
                }
                return json(['msg'=>'修改失败！','code'=>0]);
            }
        }
    }
}
