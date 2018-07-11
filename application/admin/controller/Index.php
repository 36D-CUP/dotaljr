<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

Class Index extends Controller
{
	//首页
	public function getIndex()
	{
		return $this->fetch('Index/index');
	}

	//管理员列表
	public function getAdminList()
	{
		$data = Db::table('my_admin')->select();

		$arr['admin']      = $data;
		$arr['title']	   = '管理员管理';
		$arr['title_txt']  = '管理员列表';
		return $this->fetch('Index/adminlist',$arr);
	}

	//管理员添加与修改页
	public function getAdminadd()
	{
		$id = input('id');
		$data['row']['admin_user'] = input('admin_user');
		$data['row']['admin_name'] = input('admin_name');
		$data['row']['level'] 	   = input('level');
		$data['row']['id']		   = $id;

		//判定是否添加
		if(empty($id)){
			$arr['title_txt']  = '管理员添加';
		}else{
			$arr['title_txt']  = '管理员修改';
		}


		$arr['title']	   = '管理员管理';
		return $this->fetch('Index/adminadd',$arr);
	}

	//管理员添加与修改功能
	public function postAdmindoadd()
	{
		$id 				 = input('id');
		$data['admin_user']  = input('admin_user');
		$data['admin_pass']  = md5(input('admin_pass'));
		$data['admin_name']  = input('admin_name');
		$data['level']  	 = input('level');

		$files 				 = request()->file('imgs');
		$table = 'my_admin';
		//判断是否修改或添加
		if(!empty($id)){
	        $bool = Db::table($table)->where('id',$id)->update($data);			//更新数据

			upload_file($files , $table , $id , 1);								//操作图片

		}else{
		    $bool = Db::table($table)->insertGetId($data);						//插入数据

			upload_file($files , $table , $bool , 1);						    //操作图片
		}

		echo $bool?'1':'2';
	}

	//单击修改状态
	public function postClick_updata_status()
	{
		$id  = input('id');					//获取ID
		$val = input('val');				//获取新值
		$key = input('key');				//获取字段名
		$table = input('table');			    //获取表名;

		Click_updata_status($id,$val,$key,$table);
	}

}
