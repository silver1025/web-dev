<?php


namespace app\admin\controller;
class Index extends Common{
    public function index(){
        $this->assign('user_group',session('user_group'));
        $this->assign('user_access',session('user_access'));
        return $this->fetch();
    }
    public function dashboard(){
        $count['today']=\app\admin\model\RepairList::whereTime('create_time','d')->where('is_delete','0')->count();
        $count['yestoday']=\app\admin\model\RepairList::whereTime('create_time','yesterday')->where('is_delete','0')->count();
        $count['month']=\app\admin\model\RepairList::whereTime('create_time','m')->where('is_delete','0')->count();
        $count['dwx']=\app\admin\model\RepairList::where('state','1')->where('is_delete','0')->count();
        $count['line']=\think\Db::query('select count(*) as count, date_format(create_time, \'%Y-%m-%d\') as datetime from repair_workorders where DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= create_time AND is_delete=0 GROUP BY  date_format(create_time, \'%Y-%m-%d\') ; ');
        $this->assign('count',$count);
        return $this->success('获取成功','',$this->fetch());
    }
}