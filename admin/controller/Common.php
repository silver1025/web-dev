<?php

namespace app\admin\controller;

use think\Controller;
use think\Config;

class Common extends Controller
{
    public function _initialize()
    {
        if (!session('?admin_auth')) {
            $this->error('请先登录', '/admin/auth/index');
        }

        // 权限管理
        // 非超级管理员需要验证
        // 超级管理员 user_group id 为 -1025
        if (session('user_group')!==-1025) {
            // [控制器/方法]拼接
            $rule_name = strtolower(request()->controller().'/'.request()->action());
            // 验证权限
            if (!in_array($rule_name, session('user_access'))) {
                $html = '<body class="gray-bg"><div class="middle-box text-center animated fadeInDown"><h2>您没有权限</h2></div></body>';
                $this -> error("您没有权限", null, $html, 1);
            }
        }

        /*操作日志记录*/
        if (request()->isPost()||request()->isGet()) {
            $url=strtolower(request()->module().'/'.request()->controller().'/'.request()->action());
            \app\admin\model\Option::insert([
                    'method'=>request()->method(),
                    'url'=>$url,
                    'ip'=>request()->ip(),
                    'request'=>json_encode(input(),JSON_UNESCAPED_UNICODE),
                    'user_nickname'=>session('user_nickname'),
                    'username'=>session('username')
                ]);
        }
    }
}
