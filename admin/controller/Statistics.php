<?php
/*
 * @Author: quqian
 * @Date: 2019-03-19 19:39:25
 * @Last Modified by: quqian
 * @Last Modified time: 2019-05-15 14:12:24
 */
namespace app\admin\controller;

class Statistics extends Common
{
    public function show()
    {
        if (request()->isGet()) {
            $list = \app\admin\model\User::where('type', 3)->select();
            $this->assign('repairman', $list);
            $this->success('获取成功', '', $this->fetch());
        }
    }

    public function get_type_score_data()
    {
        if (request()->isGet()) {
            $start = input('start') ? strtotime(input('start')) : (time()-3600*24*30);
            $end = input('end') ? strtotime(input('end')) : time();
            $start = date("Y-m-d", $start);
            $end1 = date("Y-m-d", $end+3600*24);
            $end2 = date("Y-m-d", $end);
            if (input('type')==1) {
                $task_list = \app\admin\model\RepairList::where('is_delete', 0)
                ->whereTime('create_time', 'between', [$start, $end1])
                ->group('type')->field('count(*) as value,type as name')->select();
                foreach ($task_list as &$info) {
                    $info['name'] = \app\admin\model\Type::where('id', $info['name'])->value('type');
                }
                if (!$task_list) {
                    $task_list[]=['value'=>0,'name'=>'没有数据'];
                }
                return json(['list' => $task_list, 'start' => $start,'end' => $end2 ]);
            }
            if (input('type')==2) {
                $pf_list =\app\admin\model\RepairList::where('state', 4)
                ->where('is_delete', 0)->whereTime('create_time', 'between', [$start, $end1])
                ->where('comment_state', 1)->group('star')
                ->field('count(*) as value,star as name')->select();
                if (!$pf_list) {
                    $pf_list[]=['value'=>0,'name'=>'没有数据'];
                }
                return json(['list' => $pf_list, 'start' => $start,'end' => $end2 ]);
            }
        }
    }

    public function get_year_data()
    {
        if (request()->isGet()) {
            $list =\app\admin\model\RepairList::where('is_delete', 0);
            switch (input('date_range')) {
                case 1:
                $list=$list->whereTime('create_time', 'year')->group('date_format(create_time, \'%y-%m\')')
                ->field('count(*) as 数量,date_format(create_time, \'%y-%m\') as 月份')->select();
                break;
                case 2:
                $list=$list->whereTime('create_time', 'last year')->group('date_format(create_time, \'%y-%m\')')
                ->field('count(*) as 数量,date_format(create_time, \'%y-%m\') as 月份')->select();
                break;
                default:break;
            }
            if (!$list) {
                $list[]=['数量'=>0,'月份'=>'没有数据'];
            }
            return json(['list' => $list]);
        }
    }

    public function get_repairman_data()
    {
        if (request()->isGet()) {
            if(input('repairman')=='all'){
                $list[]=['数量'=>0,'时间'=>'请先选择维修人员'];
                return json(['list' => $list]);
            }
            $list =\app\admin\model\Assign::where('is_delete', 0)->where('openid2',input('repairman'));
            switch (input('date_range')) {
                case 1:
                $list=$list->whereTime('create_time', 'week')->group('date_format(create_time, \'%m-%d\')')
                ->field('count(*) as 数量,date_format(create_time, \'%m-%d\') as 时间')->select();
                break;
                case 2:
                $list=$list->whereTime('create_time', 'month')->group('date_format(create_time, \'%m-%d\')')
                ->field('count(*) as 数量,date_format(create_time, \'%m-%d\') as 时间')->select();
                break;
                case 3:
                $list=$list->whereTime('create_time', 'year')->group('date_format(create_time, \'%y-%m\')')
                ->field('count(*) as 数量,date_format(create_time, \'%y-%m\') as 时间')->select();
                break;
                case 4:
                $list=$list->whereTime('create_time', 'last year')->group('date_format(create_time, \'%y-%m\')')
                ->field('count(*) as 数量,date_format(create_time, \'%y-%m\') as 时间')->select();
                break;
                default:break;
            }
            if (!$list) {
                $list[]=['数量'=>0,'时间'=>'该时间段内该维修人员没有接单记录'];
            }
            return json(['list' => $list]);
        }
    }
}
