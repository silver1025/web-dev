<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Api extends Model
{
    // public function get_userinfo($openid)
    // {
    //     $res = \app\admin\model\User::where('openid', $openid)->select();
    //     if (null != $res) {
    //         return $res[0];
    //     } else {
    //         return null;
    //     }
    // }

//     public function insert_woinfo(
//         $woid,
//         $openid1,
//         $name1,
//         $telephone1,
//         $alone,
//         $address,
//         $content,
//         $state
    //   ) {
//         // 插入数据
//         $data = ['woid' =>$woid, 'openid1' =>$openid1, 'name1' =>$name1, 'telephone1' =>$telephone1,
//     'alone' =>$alone, 'address' =>$address, 'content' =>$content, 'state' =>$state, 'assign'=> 0 ];
//         $success =\app\admin\model\RepairList::insert($data);
//         return  $success;
//     }

    // public function get_user_woinfo($openid1, $state)
    // {
    //     $res =\app\admin\model\RepairList::where('openid1', $openid1)->where('state', $state)->select();
    //     if (null != $res) {
    //         return $res;
    //     } else {
    //         return null;
    //     }
    // }

    // public function get_repairman_woinfo($openid2, $state, $accept)
    // {
    //     $res = \app\admin\model\Assign::alias('a')
    //   ->join('workorders w', 'a.woid = w.woid')
    //   ->where('openid2', $openid2)
    //   ->where('state', $state)
    //   ->where('accept', $accept)
    //   ->select();
    //     if (null != $res) {
    //         return $res;
    //     } else {
    //         return null;
    //     }
    // }

    // public function get_all_woinfo($state,$assign,$comment_state)
    // {
    //     $res =\app\admin\model\RepairList::alias('w')->join('assign a', 'w.woid = a.woid','left')
    //     ->join('users u', 'u.openid = a.openid2','left')->where('w.isdelete', 0)->where('w.assign', $assign)
    //     ->where('w.state', $state)->where('w.comment_state', $comment_state)->select();
    //     if (null != $res) {
    //         return $res;
    //     } else {
    //         return null;
    //     }
    // }

    public function get_people_list($type)
    {
        $res = \app\admin\model\User::where('type', $type)->select();
        if (null != $res) {
            return $res;
        } else {
            return null;
        }
    }


    public function repairman_accept($woid, $openid2)
    {
        $has = \app\admin\model\Assign::where('woid', $woid)->where('openid2', $openid2)->find();
        if (null != $has) {
            // 启动事务
            Db::startTrans();
            try {
                \app\admin\model\RepairList::where('woid', $woid)->update(['state' => 2]);
                \app\admin\model\Assign::where('woid', $woid)->where('openid2', $openid2)->update(['accept' => 1]);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return  false;
            }
            return  true;
        } else {
            return  false;
        }
    }

    public function repairman_error($woid, $openid2, $reason, $picnum)
    {
        $has = \app\admin\model\Assign::where('woid', $woid)->where('openid2', $openid2)->where('accept', 1)->find();
        if (null != $has) {
            if (\app\admin\model\RepairList::where('woid', $woid)->update(['state' => 3, 'comment' => $reason, 'picture2' => $picnum])) {
                return  true;
            }
        }
        return  false;
    }

    public function repairman_complete($woid, $openid2, $picnum)
    {
        $has = \app\admin\model\Assign::where('woid', $woid)->where('openid2', $openid2)->where('accept', 1)->find();
        if (null != $has) {
            if (\app\admin\model\RepairList::where('woid', $woid)->update(['comment_state' => 0,'state' => 4, 'picture2' => $picnum])) {
                return  true;
            }
        }
        return  false;
    }

    public function user_comment($woid, $star, $comment)
    {
        $state = 4;
        $has =\app\admin\model\RepairList::where('woid', $woid)->where('state', $state)->find();
        if (null != $has) {
            if (\app\admin\model\RepairList::where('woid', $woid)->update(['comment_state' => 1, 'star' => $star, 'comment'=> $comment])) {
                return  true;
            }
        }
        return  false;
    }
}
