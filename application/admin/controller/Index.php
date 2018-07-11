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
		$id = input('id');
		$id = input('id');
		$id = input('id');
		$id = input('id');

		if(empty($id)){
			$arr['title_txt']  = '管理员添加';
		}else{
			$arr['title_txt']  = '管理员修改';
		}


		$arr['title']	   = '管理员管理';
		return $this->fetch('Index/adminadd',$arr);
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
