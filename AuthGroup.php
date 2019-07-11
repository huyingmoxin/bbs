<?php
namespace app\admin\controller;
use think\Request;//请求
use think\View;
use think\Db;
class AuthGroup extends Common
{
    
    //删除分组的时候删除用户-用户组表中的对应分组id的数据
    public function _after_delete($id){
        $res=Db::name('auth_group_access')->where(['group_id'=>$id])->delete();
    }

    //分类首页
    public function addGroup(){
        $id=input('id');
        $html=model('auth_group')->html($id);
        result(1,$html);
    }
    public function addGroupHander(){
        $id=input('id');
        $title=$param['title']=input('title');
        $illustration=$param['illustration']=input('illustration');
        if($id==''){
            //新增
            //验证
            $result = $this->validate($param, 'AuthGroup');
            if ($result !== true) {
                result(3, $result);
            }
            $res=DB::name('auth_group')->insert(['title'=>$title,'illustration'=>$illustration]);
            if($res!==false){
                result(1, '');
            }else{
                result(2, '角色添加失败'); 
            }
        }else{
            //修改
            $res=DB::name('auth_group')->where('id',$id)->update(['title'=>$title,'illustration'=>$illustration]);
            if($res!==false){
                result(6, '');
            }else{
                result(7, '角色编辑失败'); 
            }
        }
    }

    //改变角色状态
    public function changStatus(){
        $status=input('status');
        $id=input('id');
        if($id==1){
            result(2,'','不允许禁止超级管理员');
        }
        if($status==1){
            $data['status']=0;
        }elseif($status==0){
            $data['status']=1;
        }
        $res=model('AuthGroup')->where(['id'=>$id])->setField('status', $data['status']);
        if($res){
            result(1, '状态修改成功');
        }else{
            result(2, '状态修改失败');
        }
    }

    //给角色分配成员页面显示
    public function showUser(){
        $id=input('id');
        $showuser_html=model('AuthGroup')->showUserHtml($id);
        result(1,$showuser_html);
    }

    //保存角色用户的分配
    public function groupUserHander(){
        $uid=input('uid');
        $uid=explode(',',$uid);//转化成数组
        $group_id=input('group_id');
        //查找出当前管理员表拥有的该角色所有用户对比uid,没有的就去数据库删除
        $group_uid=DB::name('AuthGroupAccess')->where(['group_id'=>$group_id])->select();
        $need_del_uid=[];
        if(!empty($group_uid)){
            foreach ($group_uid as $key => $value) {
                //如果找出来的有不存在于$uid中的就是需要删除的,保存他的id
                if(!in_array($value['uid'],$uid)){
                    $need_del_uid[]=$value['id'];
                }
            } 
        }
        if(!empty($need_del_uid)){
            $num=0;
            foreach ($need_del_uid as $key => $value) {
                $num+=db('AuthGroupAccess')->delete($value);
            }
            if($num==false){
                result(3,'','角色用户配置没能成功，请重新配置');
            }
        }
        //插入的数据
        $data=[];
        foreach ($uid as $key => $value) {
            //查找AuthGroupAccess是否存在此用户，存在就清除
            $one_access=DB::name('AuthGroupAccess')->where(['group_id'=>$group_id,'uid'=>$value])->find();
            if(empty($one_access)){
               $data[$key]['group_id']=$group_id;
               $data[$key]['uid']=$value;
            }
        }
        //判断是否用户全部都存在
        if(!empty($data)){
            $res=DB::name('AuthGroupAccess')->insertAll($data);
        }
        result(1,'','成功配置角色管理员');
        
    } 
}
