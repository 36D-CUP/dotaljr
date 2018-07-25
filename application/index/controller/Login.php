<?php
namespace app\index\controller;

use app\index\controller\Allow;

use think\Db;
use think\Session;

class Login extends Allow
{
    public function getLogin()
    {

// dump(Session::get());exit;
        return $this->fetch('Login/login');
    }

    public function postLogindo()
    {
        $data['admin_user'] = input('user');
        $data['user_pass']  = md5(input('pass'));
        $data['status']     = 1;

        $bool = Db::table('my_admin')->where($data)->find();
        if(empty($bool)){
            echo "失败";
        }else{
            Session::set('user_name',$bool['admin_name']);
            Session::set('user_id',$bool['id']);
            echo "1";
        }
    }

    //退出登录
    public function postLoginout()
    {
        Session::delete('user_name');
        Session::delete('user_id');
        echo "1";
    }
}
