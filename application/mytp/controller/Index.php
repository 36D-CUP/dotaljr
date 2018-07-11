<?php
namespace app\mytp\controller;

use think\Db;
use app\mytp\controller\Allow;

//与public/static/js/my.js关联
class Index extends Allow
{
	//单击修改状态
	public function postClick_updata_status(){
		$id  = request()->param('id');				//获取ID
		$val = request()->param('sta');				//获取新值
		$key = request()->param('staKey');			//获取字段名
		$dbs = request()->param('dbs');			    //获取字段名
		if(Db::table($dbs)->where('id',$id)->update([$key  => $val])){
			echo $val;
		}else{
			echo '失败';
		}
	}

}