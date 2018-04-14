<?php
/**
 * Created by PhpStorm.
 * User: Annie
 * Date: 2017/11/15
 * Time: 9:59
 */

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller
{
    //ajax返回
    protected function aR($msg, $type = 1)
    {
        $aRT['res'] = true;
        $aRF['res'] = false;
        switch ($type) {
            case 1:
                $aRT['msg'] = $msg;
                $this->ajaxReturn($aRT);
                break;
            case 0:
                $aRF['msg'] = $msg;
                $this->ajaxReturn($aRF);
                break;
        }
        die;
    }


    public function index()
    {
        if (IS_POST) {
            $user = I('post.user');
            $pass = I('post.pass');

            $validate = array(
                //登录验证非空
                array('user','require','请填写账号',3),
                array('pass','require','请填写密码',3),
            );

            $admin = M("admin"); // 实例化User对象
            if (!$admin->create($validate)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->aR($admin->getError(),0);
            } else {
                if(!session('adminData')){
                    //开始验证账号密码
                    $where['user'] = $user;
                    $where['pass'] = encrypting($pass);
                    $adminData = $admin->where($where)->find();
                    if(!$adminData){
                        $this->aR('账号或者密码有误',0);
                    }
                    session('adminData',$adminData);
                    $this->aR('登录成功');
                }else{
                    $this->aR('已登录');
                }
            }

            //session('user',1);
            //$this->redirect("/Admin/Index");

        }else{
            $this->display('index/login');
        }
    }

    public function out()
    {
        session('adminData', null);
        $this->success('您已成功退出', __CONTROLLER__);
    }
}
