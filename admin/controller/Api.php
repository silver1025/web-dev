<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Env;

class Api extends Controller
{
    public function _initialize()
    {
        if (!input('?wx_openid')) {
            $this->redirect('auth/wx_login');
        }
        $user=\app\admin\model\User::where(['wx_openid'=>input('wx_openid')])->find();
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
                            'user_nickname'=>$user->name,
                            'username'=>$user->openid
                        ]);
        }
    }

    public function md5()
    {
        if (input('?input')) {
            $input = input('input');
            $output = md5($input);
            return json(['code'=>200,'md5'=>$output]);
        }
        return json(['code'=>400]);
    }


    public function set_user_tel()
    {
        $tel=input('telephone');
        $openid=input('openid');
        if (\app\admin\model\User::where('openid', $openid)->update(['telephone'=>$tel])) {
            return json(['code'=>200]);
        } else {
            return json(['code'=>400]);
        }
    }

    public function add_wo()
    {
        if (request()->isGet()) {
            $type_arr = \app\admin\model\Type::where('is_delete', 0)
            ->field('other1,other2,other3,create_time,update_time', true)->select();
            $location_arr = \app\admin\model\Location::where('is_delete', 0)
            ->field('other1,other2,other3,create_time,update_time', true)->select();
            $data=[];
            $data['code'] = (count($type_arr)&&count($location_arr))?200:400;
            if ($data['code'] == 200) {
                $data['location_list']=$location_arr;
                $data['type_list']=$type_arr;
            }
            return json($data);
        } elseif (request()->isPost()) {
            $data = [];
            $data['woid'] = date("YmdHis").rand(0, 9);
            $data['openid1'] = input('openid');
            $data['type'] = input('type');
            $data['name1'] = input('name');
            $data['telephone1'] = input('telephone');
            $data['alone'] = input('?alone')?1:0;
            $data['location'] = input('location');
            $data['address'] = input('address');
            $data['content'] = input('content');
            $data['picture'] = input('picnum');
            $tag = \app\admin\model\RepairList::insert($data);
            $on_duty_tel = \app\admin\model\Config::where(['type'=>3])->value('content');
            $sms_result = \sms_send(1, $on_duty_tel);
            $data = [
              'code'=>$tag?200:400,
              'woid'=>$data['woid'],
              ];
            $data['$sms_result']= $sms_result;
            return json($data);
        }
    }

    public function picture_add()
    {
        $data = [];
        $data['woid'] = input('woid');
        $data['type'] = input('type');
        $file = request()->file('inputfile');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            // 如输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $data['path'] = DS .'uploads/'.$info->getSaveName();
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
        }
        $tag = \app\admin\model\Picture::insert($data);
        $data= [
              'code'=>$tag?200:400,
              ];
        return json($data);
    }

    public function picture_get()
    {
        $type = input('type');
        $list = \app\admin\model\Picture::where('woid', input('woid'))->where('is_delete', 0)
            ->where('type', $type)->field('other1,other2,other3,create_time,update_time', true)->select();
        $data['code'] = count($list)?200:400;
        if ($data['code'] == 200) {
            $data['picture']=$list;
        }
        return json($data);
    }
    
    public function get_user_woinfo()
    {
        $openid1 = input('openid1');
        $state = input('state');
        $list = \app\admin\model\RepairList::where('is_delete', 0)->where('openid1', $openid1)->where('state', $state)
        ->field('other1,other2,other3,create_time,update_time', true)->select();
        foreach ($list as &$item) {
            // 此处单独用了表名
            $item['name2'] = \app\admin\model\Assign::alias('a')->join('repair_users u', 'a.openid2 = u.openid')
                ->where('a.woid', $item['woid'])->where('a.is_delete', 0)->column('name');
            $item['telephone2'] = \app\admin\model\Config::where(['type'=>2])->value('content');
            $item['type'] = \app\admin\model\Type::where('is_delete', 0)->where('id', $item['type'])->value('type');
            $item['location'] = \app\admin\model\Location::where('is_delete', 0)->where('id', $item['location'])->value('location');
        }
        if ($list) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        'state'=>$state,
        'user_woinfo'=>$list
        ];
        return json($data);
    }
    
    public function get_repairman_woinfo()
    {
        $openid2 = input('openid2');
        $state = input('state');
        $accept = input('accept');
        $list = \app\admin\model\Assign::alias('a')->join('repair_workorders w', 'a.woid = w.woid')
        ->where('openid2', $openid2)->where('state', $state)->where('accept', $accept)
        ->where('a.is_delete', 0)->where('w.is_delete', 0)->select();
        foreach ($list as &$item) {
            $item['name2'] = \app\admin\model\Assign::alias('a')->join('repair_users u', 'a.openid2 = u.openid')
                ->where('a.woid', $item['woid'])->where('a.is_delete', 0)->column('name');
            $item['telephone2'] = \app\admin\model\Config::where(['type'=>2])->value('content');
            $item['type'] = \app\admin\model\Type::where('is_delete', 0)->where('id', $item['type'])->value('type');
            $item['location'] = \app\admin\model\Location::where('is_delete', 0)->where('id', $item['location'])->value('location');
        }
        if ($list) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        'state'=>$state,
        'accept'=>$accept,
        'user_woinfo'=>$list
        ];
        return json($data);
    }

    public function get_all_woinfo()
    {
        $state = input('state');
        $assign = input('assign');
        $comment_state = input('comment_state');
        $list = \app\admin\model\RepairList::where('is_delete', 0)->where('assign', $assign)
        ->where('state', $state)->where('comment_state', $comment_state)
        ->field('other1,other2,other3,create_time,update_time', true)->select();
        foreach ($list as &$item) {
            $item['name2'] = \app\admin\model\Assign::alias('a')->join('repair_users u', 'a.openid2 = u.openid')
                ->where('a.woid', $item['woid'])->where('a.is_delete', 0)->column('name');
            $item['telephone2'] = \app\admin\model\Config::where(['type'=>2])->value('content');
            $item['type'] = \app\admin\model\Type::where('is_delete', 0)->where('id', $item['type'])->value('type');
            $item['location'] = \app\admin\model\Location::where('is_delete', 0)->where('id', $item['location'])->value('location');
        }
        if ($list) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        'user_woinfo'=>$list
        ];
        return json($data);
    }
    
    public function get_people_list()
    {
        $type = input('type');
        $model = model('Api');
        $data = $model->get_people_list($type);
        if ($data) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        'type'=>$type,
        'num'=>count($data),
        'user_woinfo'=>$data
        ];
        return json($data);
    }
    
    public function repairman_accept()
    {
        $woid = input('woid');
        $openid2 = input('openid2');
        $model = model('Api');
        $data = $model->repairman_accept($woid, $openid2);
        if ($data) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        ];
        return json($data);
    }
    
    public function repairman_error()
    {
        $woid = input('woid');
        $openid2 = input('openid2');
        $reason = input('reason');
        $picnum = input('picnum');
        $model = model('Api');
        $data = $model->repairman_error($woid, $openid2, $reason, $picnum);
        if ($data) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        ];
        return json($data);
    }
    
    public function repairman_complete()
    {
        $woid = input('woid');
        $openid2 = input('openid2');
        $picnum = input('picnum');
        $model = model('Api');
        $data = $model->repairman_complete($woid, $openid2, $picnum);
        if ($data) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        ];
        return json($data);
    }
    
    public function user_comment()
    {
        $woid = input('woid');
        $star = input('star');
        $comment = input('comment');
        $model = model('Api');
        $data = $model->user_comment($woid, $star, $comment);
        if ($data) {
            $code=200;
        } else {
            $code=400;
        }
        $data= [
        'code'=>$code,
        ];
        return json($data);
    }
}
