<?php
/*
 * @Author: quqian
 * @Date: 2019-03-19 19:39:25
 * @Last Modified by: quqian
 * @Last Modified time: 2019-05-15 14:12:24
 */
namespace app\admin\controller;

class Repair extends Common
{
    public function show()
    {
        if (request()->isGet()) {
            $list = \app\admin\model\User::where('type', 3)->select();
            $this->assign('repairman', $list);
            $this->success('获取成功', '', $this->fetch());
        }
    }

    private function trans_info($info)
    {
        // 此处单独用了表名
        $info['repairman'] = \app\admin\model\Assign::alias('a')->join('repair_users u', 'a.openid2 = u.openid')
        ->where('a.woid', $info['woid'])->where('a.is_delete', 0)->column('name');
        $info['repairman'] = " ".join($info['repairman']);
        $info['type'] = \app\admin\model\Type::where('id', $info['type'])->value('type');
        $info['location'] = \app\admin\model\Location::where('id', $info['location'])->value('location');
        switch ($info['alone']) {
            case 0:
            $info['alone']='不接受';break;
            case 1:
            $info['alone']='接受';break;
            default:
            $info['alone']='N/A';break;
        }
        switch ($info['state']) {
            case 1:
            $info['state']='待处理';break;
            case 2:
            $info['state']='已处理';break;
            case 3:
            $info['state']='异常';break;
            case 4:
            $info['state']='已结单';break;
            case 5:
            $info['state']='被退回';break;
            default:
            $info['state']='N/A';break;
        }
        switch ($info['assign']) {
            case 0:
            $info['assign']='未指派';break;
            case 1:
            $info['assign']='已指派';break;
            default:
            $info['assign']='N/A';break;
        }
        switch ($info['comment_state']) {
            case 0:
            $info['comment_state']='未评价';break;
            case 1:
            $info['comment_state']='已评价';break;
            default:
            $info['comment_state']='N/A';break;
        }
        switch ($info['star']) {
            case 0:
            $info['star']='未评价';break;
            default:
            break;
        }
        return $info;
    }

    public function data()
    {
        if (request()->isPost()) {
            $state = input('state')=='all'?'1,2,3,4,5':input('state');
            $list = \app\admin\model\RepairList::where('state', 'in', $state)->where('is_delete', 0);
            $all = \app\admin\model\RepairList::where('state', 'in', $state)->where('is_delete', 0);

            if (input('repairman')!='all') {
                $woid_list = \app\admin\model\Assign::where('openid2', input('repairman'))->where('is_delete', 0)
                ->column('woid');
                $list = $list->where('woid', 'in', $woid_list);
                $all = $all->where('woid', 'in', $woid_list);
            }

            if (input('start_date')&&input('end_date')) {
                $start_date = strtotime(input('start_date'));
                $end_date = strtotime(input('end_date'));
                $start_date = date("Y-m-d", $start_date);
                $end_date = date("Y-m-d", $end_date+3600*24);
                $list = $list->whereTime('create_time', 'between', [$start_date, $end_date]);
                $all = $all->whereTime('create_time', 'between', [$start_date, $end_date]);
            }

            $all = $all->count();
            $list = $list->limit(input('offset'), input('limit'))->order(input('sortName').' '.input('sortOrder'))
            ->field('other1,other2,other3,update_time', true)->select();

            foreach ($list as &$info) {
                $info=$this->trans_info($info);
            }
            return json(['total' => $all, 'rows' => $list]);
        }
    }

    public function assign_wo()
    {
        if (request()->isGet()) {
            $list = \app\admin\model\User::where('type', 3)->select();
            $this->assign('repairman', $list);
            $this->assign('woid', input('woid'));
            $this->success('获取成功', '', $this->fetch());
        }
    }

    public function assign_wo_data()
    {
        if (request()->isPost()) {
            $openids = input('openids/a');
            $woid = input('woid');
            $tag=true;
            // 启动事务
            \think\Db::startTrans();
            try {
                \app\admin\model\RepairList::where('woid', $woid)->update(['assign' => 1]);
                \app\admin\model\Assign::where('woid', $woid)->where('is_delete', 0)->update(['is_delete' => 1]);
                foreach ($openids as &$info) {
                    \app\admin\model\Assign::create(['woid' => $woid, 'openid2' => $info]);
                }
                // 提交事务
                \think\Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                \think\Db::rollback();
                $tag=false;
            }
            $sms_result = true;
            foreach ($openids as &$info) {
                $mobile = \app\admin\model\User::where('openid', $info)->value('telephone');
                $sms_result = \sms_send(2, $mobile);
            }
            $result = json_decode($sms_result,true);
            if($result['code'] != 0) $sms_result = false;
            if ($tag) {
                return json(['msg' => '派单成功', 'sms_result'=>$sms_result, 'code' => 1]);
            } else {
                return json(['msg' => '失败', 'code' => 0]);
            }
        }
    }

    public function get_detail()
    {
        if (request()->isPost()) {
            $info = \app\admin\model\RepairList::get(input('woid'));
            $info = $this->trans_info($info);
            $this->assign('info', $info);

            if ($info['picture']!=0) {
                $pictures = \app\admin\model\Picture::where('woid', input('woid'))
                ->where('type', 1)->select();
                $this->assign('pictures', $pictures);
            }
            if ($info['picture2']!=0) {
                $pictures2 = \app\admin\model\Picture::where('woid', input('woid'))
                ->where('type', 2)->select();
                $this->assign('pictures2', $pictures2);
            }
            $this->success('获取成功', '', $this->fetch('detail'));
        }
    }

    public function delete_woid()
    {
        if (request()->isPost()) {
            $woids = input()['woids'];
            $tag=true;
            // 启动事务
            \think\Db::startTrans();
            try {
                foreach ($woids as &$info) {
                    \app\admin\model\RepairList::where('woid', $info)->update(['is_delete' => 1]);
                    \app\admin\model\Assign::where('woid', $info)->update(['is_delete' => 1]);
                }
                // 提交事务
                \think\Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                \think\Db::rollback();
                $tag=false;
            }
            if ($tag) {
                return json(['msg' => '删除成功！', 'code' => 1]);
            } else {
                return json(['msg' => '删除失败！', 'code' => 0]);
            }
        }
    }

    /*添加新维修任务*/
    public function add_wo()
    {
        if (request()->isGet()) {
            $type_arr = \app\admin\model\Type::where('is_delete', 0)->select();
            $this->assign('type', $type_arr);
            $location_arr = \app\admin\model\Location::where('is_delete', 0)->select();
            $this->assign('location', $location_arr);
            $this->success('获取成功', '', $this->fetch());
        }
    }

    /*添加新维修任务*/
    public function add_wo_data()
    {
        if (request()->isPost()) {
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
            $files = request()->file('files');
            $data['picture'] = $files?count($files):0;
            $paths = [];
            for ($i = 0; $i < $data['picture']; $i++) {
                $info = $files[$i]->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    // 成功上传后 获取上传信息
                    // 如输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    $paths[$i] = DS .'uploads/'.$info->getSaveName();
                } else {
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }
            $tag = true;
            //启动事务
            \think\Db::startTrans();
            try {
                foreach ($paths as &$path) {
                    \app\admin\model\Picture::insert(['woid' => $data['woid'], 'path' => $path]);
                }
                \app\admin\model\RepairList::insert($data);
                // 提交事务
                \think\Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                \think\Db::rollback();
                $tag=false;
            }
            if ($tag) {
                return json(['msg' => '成功！', 'code' => 1]);
            } else {
                return json(['msg' => '失败！', 'code' => 0]);
            }
        }
    }

    public function refuse_wo()
    {
        if (request()->isGet()) {
            $this->assign('woid', input('woid'));
            $this->success('获取成功', '', $this->fetch());
        }
    }

    public function refuse_wo_data()
    {
        if (request()->isPost()) {
            $comment = input('comment');
            $woid = input('woid');
            if (\app\admin\model\RepairList::update(['woid' => $woid,'comment'=> $comment,'state' => 5])) {
                return json(['msg' => '成功', 'code' => 1]);
            } else {
                return json(['msg' => '失败', 'code' => 0]);
            }
        }
    }

    public function repair_again()
    {
        if (request()->isPost()) {
            $woid = input('woid');
            if (\app\admin\model\RepairList::update(['woid' => $woid,'comment'=> '',
            'state' => 2,'comment_state'=> 0])) {
                return json(['msg' => '成功', 'code' => 1]);
            } else {
                return json(['msg' => '失败', 'code' => 0]);
            }
        }
    }

    /*打印表格*/
    public function print_excel_data()
    {
        if (request()->isPost()) {
            session('state', input('state'));
            session('repairman', input('repairman'));
            session('start_date', input('start_date'));
            session('end_date', input('end_date'));
        }
    }

    /*打印表格*/
    public function print_excel()
    {
        $state = session('state')=='all'?'1,2,3,4,5':session('state');
        $list = \app\admin\model\RepairList::where('state', 'in', $state)->where('is_delete', 0);

        if (session('repairman')!='all') {
            $woid_list = \app\admin\model\Assign::where('openid2', session('repairman'))->where('is_delete', 0)
            ->column('woid');
            $list = $list->where('woid', 'in', $woid_list);
        }

        if (session('start_date')&&session('end_date')) {
            $start_date = strtotime(session('start_date'));
            $end_date = strtotime(session('end_date'));
            $start_date = date("Y-m-d", $start_date);
            $end_date = date("Y-m-d", $end_date+3600*24);
            $list = $list->whereTime('create_time', 'between', [$start_date, $end_date]);
        }

        $list = $list->field('other1,other2,other3,update_time,is_delete', true)->select();

        foreach ($list as &$info) {
            $info=$this->trans_info($info);
        }
        // $list=\app\admin\model\RepairList::where('state', session('state'))->where('is_delete', 0)->select();
        // foreach ($list as &$info) {
        //     // 此处单独用了表名
        //     $name2 = \app\admin\model\Assign::alias('a')->join('repair_users u', 'a.openid2 = u.openid')
        //             ->where('a.woid', $info['woid'])->where('a.is_delete', 0)->column('name');
        //     $info['name2']=join(",", $name2);
        //     $info['type'] = \app\admin\model\Type::where('is_delete', 0)->where('id', $info['type'])->value('type');
        //     $info['location'] = \app\admin\model\Location::where('is_delete', 0)->where('id', $info['location'])->value('location');
        // }
        if (!count($list)) {
            $this->error('数据不足，无法生成');
        }
        $key_info=[
                    'woid'=>'工单号',
                    'openid1'=>'报修人id',
                    'name1'=>'报修人',
                    'telephone1'=>'电话号码',
                    'alone'=>'无人时维修',
                    'type'=>'维修类型',
                    'location'=>'楼栋',
                    'address'=>'地址',
                    'content'=>'报修内容',
                    'picture'=>'报修图片数量',
                    'picture2'=>'维修结果图片数量',
                    'state'=>'工单状态',
                    'assign'=>'派单状态',
                    'repairman'=>'维修人',
                    'accept'=>'接单状态',
                    'star'=>'评星',
                    'comment_state'=>'评价/异常/退回状态',
                    'comment'=>'评价/异常原因/退回原因',
                    'create_time'=>'创建时间',
                ];
        $key=array_keys($list[0]->toArray());
        $head=[];
        foreach ($key as &$item) {
            $head[$key_info[$item]]=$item;
        }
        $this->create_excel($head, $list);
    }

    private function create_excel($title, $list)
    {
        require_once VENDOR_PATH . "PHPExcel/PHPExcel.php";
        // 创建Excel文件对象
        $objPHPExcel = new \PHPExcel();
        // 设置文档信息，这个文档信息windows系统可以右键文件属性查看
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("后勤报修系统")
            ->setTitle("维修单")
            ->setSubject("维修单")
            ->setDescription("维修列表")
            ->setKeywords("维修列表")
            ->setCategory("设置文档的分类");
        ob_clean();
        //根据excel坐标，添加数据
        $cell=["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R",
         "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG",
          "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ"];
        $count=0;
        foreach ($title as $k=>$v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell[$count]."1", $k);
            $count++;
        }
        foreach ($list as $key=>$info) {
            $count=0;
            foreach ($title as $k=>$v) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell[$count].($key+2), $info->$v);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($cell[$count])->setWidth(20);
                ;
                $count++;
            }
        }
        // 重命名工作sheet
        $objPHPExcel->getActiveSheet()->setTitle('报修列表');
        // 设置第一个sheet为工作的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        header("Content-type:application/vnd.ms-excel");
        $date=date("YmdHis");
        header("Content-Disposition:attachment;filename=RepairList-{$date}.xlsx");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save('export.xlsx');
        $objWriter->save('php://output');
        exit();
    }
}
