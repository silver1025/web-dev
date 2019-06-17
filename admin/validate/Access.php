<?php
namespace app\admin\validate;

use think\Validate;

class Access extends Validate
{
    protected $rule = [
      'name' => 'require|unique:admin_auth_rule',
      'title' => 'require|unique:admin_auth_rule',
    ];
    

    protected $message = [
      'name.unique' => '权限 [控制器/方法] 不能重复',
      'title.unique' => '权限名称不能重复',
      'name.require' => '请输入 [控制器/方法]',
      'title.require' => '请输入权限名称'
    ];
}
