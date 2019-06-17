<?php
namespace app\admin\controller;

use think\Controller;

class Auth extends Controller
{
    public function wx_login()
    {
        $code = input('?code')?input('code'):0;
        $wx_openid = $this->get_openid($code);
        if ($wx_openid) {
            $repair_department_tel = \app\admin\model\Config::where(['type'=>2])->value('content');
            $work_time = \app\admin\model\Config::where(['type'=>4])->value('content');
            $feedback_group = \app\admin\model\Config::where(['type'=>5])->value('content');
            if (\app\admin\model\Config::where(['type'=>1])->value('content')!='on') {
                return json(['code'=>403,
                'repair_department_tel'=>$repair_department_tel,
                'work_time'=>$work_time,
                'feedback_group'=>$feedback_group]);
            }
            $data = \app\admin\model\User::where(['wx_openid'=>$wx_openid])
            ->field('other1,other2,other3,create_time,update_time', true)->find();
            if ($data) {
                $data= [
                    'code'=>200,
                    'user_info'=>$data,
                    'wx_openid'=>$wx_openid,
                    'repair_department_tel'=>$repair_department_tel,
                    'work_time'=>$work_time,
                    'feedback_group'=>$feedback_group];
            } else {
                if (!\app\admin\model\Weixin::where(['wx_openid' => $wx_openid])->find()) {
                    \app\admin\model\Weixin::insert(['wx_openid' => $wx_openid]);
                }
                $data= [
                    'code'=>404,
                    'wx_openid'=>$wx_openid
                    ];
            }
            return json($data);
        } else {
            return json(['code'=>401]);
        }
    }

    public function user_init()
    {
        if (!input('?wx_openid')) {
            $this->redirect('auth/wx_login');
        }
        $user=\app\admin\model\Weixin::where(['wx_openid'=>input('wx_openid')])->find();
        if (!$user) {
            $this->redirect('auth/wx_login');
        }
        /*操作日志记录*/
        if (request()->isPost()||request()->isGet()) {
            $url=strtolower(request()->module().'/'.request()->controller().'/'.request()->action());
            \app\admin\model\Option::insert([
                        'method'=>request()->method(),
                        'url'=>$url,
                        'ip'=>request()->ip(),
                        'request'=>json_encode(input(), JSON_UNESCAPED_UNICODE),
                        'user_nickname'=>'init',
                        'username'=>'init'
                    ]);
        }

        $wx_openid=input('wx_openid');
        $openid=input('openid');
        $name=input('name');
        $type=input('type');
        if (\app\admin\model\User::insert(['wx_openid' => $wx_openid,'openid' => $openid,
        'name' => $name,'type' => $type])) {
            return json(['code'=>200]);
        } else {
            return json(['code'=>400]);
        }
    }

    public function get_openid($code)
    {
        $appid = "wx14b4777154feaec3";
        $appsecret = "dbb5972fced4b0eeb937f052f74dcfd5";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code";
        $json = \https_request($url);
        if (array_key_exists('openid', $json)) {
            $openid = $json['openid'];
            return $openid;
        } else {
            return 0;
        }
    }

    public function login()
    {
        if (request()->isPost()) {
            if ($user=\app\admin\model\AdminUser::where(['username'=>input('username'), 'statu'=>1, 'is_delete' => 0])->find()) {
                if ($user->password==md5(input('password').$user->salt)) {
                    $access=$this->get_user_access($user->user_group);
                    session('admin_auth', true);
                    session('username', input('username'));
                    session('user_nickname', $user->user_nickname);
                    session('user_group', $user->user_group);
                    session('user_access', $access);
                    session('admin_id', $user->id);
                    $this->redirect(url('index/index'));
                } else {
                    $this->error('用户名密码错误');
                }
            } else {
                $this->error('用户名密码错误');
            }
        }
    }

    public function get_user_access($group_id)
    {
        $user_access_ids = \app\admin\model\Role::where(['id'=>$group_id,"status" => 1, "is_delete" => 0])->value('rules');
        $ids = array_unique(explode(',', trim($user_access_ids, ',')));
        if (empty($ids)) {
            return [];
        }
        $map = [
            'id' => ['in', $ids],
            'is_delete' => 0,
            'status' => 1,
        ];
        //读取用户组所有权限规则
        $rules = \app\admin\model\Access::where($map)->column('name');
        return $rules;
    }

    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 退出
     */
    public function logout()
    {
        session(null);
        $this->redirect(url('auth/index'));
    }

    public function sms_test()
    {
        $on_duty_tel = \app\admin\model\Config::where(['type'=>3])->value('content');
        $sms_result = \sms_send(1, $on_duty_tel);
        $array = json_decode($sms_result, true);
        echo '<pre>';
        print_r($array);
    }
}
