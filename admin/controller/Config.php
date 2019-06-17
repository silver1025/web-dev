<?php
/*
 * @Author: quqian
 * @Date: 2019-03-19 19:39:25
 * @Last Modified by: quqian
 * @Last Modified time: 2019-05-15 14:12:24
 */
namespace app\admin\controller;

class Config extends Common
{
    public function show()
    {
        if (request()->isGet()) {
            $system_state = \app\admin\model\Config::where(['type'=>1])->value('content');
            $repair_department_tel = \app\admin\model\Config::where(['type'=>2])->value('content');
            $on_duty_tel = \app\admin\model\Config::where(['type'=>3])->value('content');
            $work_time = \app\admin\model\Config::where(['type'=>4])->value('content');
            $feedback_group = \app\admin\model\Config::where(['type'=>5])->value('content');
            $this->assign('system_state', $system_state);
            $this->assign('repair_department_tel', $repair_department_tel);
            $this->assign('work_time', $work_time);
            $this->assign('feedback_group', $feedback_group);
            $this->assign('on_duty_tel', $on_duty_tel);
            $this->success('获取成功', '', $this->fetch());
        }
    }

    public function repair_department_tel()
    {
        if (request()->isPost()) {
            $content = input('content');
            if(\app\admin\model\Config::update(['type'=>2,'content'=>$content])){
                return json(['msg' => '修改成功', 'code' => 1]);
            } else {
                return json(['msg' => '修改失败', 'code' => 0]);
            }
        }
    }

    public function on_duty_tel()
    {
        if (request()->isPost()) {
            $content = input('content');
            if(\app\admin\model\Config::update(['type'=>3,'content'=>$content])){
                return json(['msg' => '修改成功', 'code' => 1]);
            } else {
                return json(['msg' => '修改失败', 'code' => 0]);
            }
        }
    }

    public function work_time()
    {
        if (request()->isPost()) {
            $content = input('content');
            if(\app\admin\model\Config::update(['type'=>4,'content'=>$content])){
                return json(['msg' => '修改成功', 'code' => 1]);
            } else {
                return json(['msg' => '修改失败', 'code' => 0]);
            }
        }
    }

    public function feedback_group()
    {
        if (request()->isPost()) {
            $content = input('content');
            if(\app\admin\model\Config::update(['type'=>5,'content'=>$content])){
                return json(['msg' => '修改成功', 'code' => 1]);
            } else {
                return json(['msg' => '修改失败', 'code' => 0]);
            }
        }
    }

    public function suspend()
    {
        if (request()->isPost()) {
            if(\app\admin\model\Config::update(['type'=>1,'content'=>'off'])){
                return json(['msg' => '成功', 'code' => 1]);
            } else {
                return json(['msg' => '失败', 'code' => 0]);
            }
        }
    }

    public function start()
    {
        if (request()->isPost()) {
            if(\app\admin\model\Config::update(['type'=>1,'content'=>'on'])){
                return json(['msg' => '成功', 'code' => 1]);
            } else {
                return json(['msg' => '失败', 'code' => 0]);
            }
        }
    }
}
