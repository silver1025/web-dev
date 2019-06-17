<?php

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username'  =>  'require|unique:users',
        'password' =>  'require',
        'user_type' =>  'require|number',
        'user_nickname' =>  'require',
    ];
    protected $scene = [
        'login'  =>  ['username'=>'require','password'=>'require'],
    ];
}
