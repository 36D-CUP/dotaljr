<?php
namespace app\admin\controller;

use app\admin\controller\Allow;
use think\Db;

Class Articleico extends Allow
{
	//文章标签列表
	public function getArticleico()
	{
		//分页
		$pro = input('pro')==""?1:input('pro');							//获取分页传过来的当前页
		$showNum = input('data_num')?input('data_num'):'5';				//设定每一个显示的条数
		$str = (($pro-1)*$showNum).",".$showNum;						//分页

		$s_val  = input('search_value');								//获取搜索传过来的值
		$s_ty   = input('search_type');    								//获取搜索传过来的字段名
		$s_si   = input('search_size');									//判断是否搜索大小写
		$s_type = $s_val?input('type'):'其他';							//获取搜索传过来的搜索类型

		$table = 'my_article_ico';
		switch($s_type){
			case 1:
				if($s_ty != "id"){
					$where = $s_ty." like '%".$s_val."%'";
				}else{
					$where = $s_ty.'='.$s_val;
				}
				
				$num  = Db::table($table)->where($where)->count();							//数量
				$data = Db::table($table)->where($where)->limit($str)->select();			//获取所有管理员
			break;
			default:
				$num  = Db::table($table)->count();												//数量
				$data = Db::table($table)->limit($str)->select();								//获取所有管理员
			break;
		}



		//分页
		$arr['page'] = ceil($num/$showNum);//获取条数

		$arr['row']        = $data;																			//列表数据
		$arr['s_ty']       = $s_ty;																			//搜索保留值
		$arr['s_val']      = $s_val;																		//搜索保留值
		$arr['data_num']   = $showNum;																		//搜索保留值
		$arr['table']      = $table;																		//模板上使用
		$arr['tables']     = 'articleico';																	//模板上使用
		$arr['empty']      = "<tr><td colspan='6' style='text-align:center'>没有任何数据</td></tr>";		//显示条数
		$arr['title']	   = '文章管理';
		$arr['title_txt']  = '文章标签列表';
		return $this->fetch('Articleico/articleico',$arr);
	}



	//文章标签添加功能
	public function postArticleicodoadd()
	{
		$data['name'] 		 = input('name');
		$table 				 = 'my_article_ico';


		$bool = Db::table($table)->insertGetId($data);						//插入数据
		echo '添加成功';
	}

	//单击修改状态
	public function postClick_updata_status()
	{
		$id  	= input('id');					//获取ID
		$val 	= input('val');					//获取新值
		$key	= input('key');					//获取字段名
		$table  = input('table');			    //获取表名;

		$bool = Click_updata_status($id,$val,$key,$table);
		echo $bool;
	}

	//单击删除
	public function postClick_delete()
	{
		$id    = input('id');					//id
		$table = input('table');				//表名

		//删除图片
		delete_file($table , $id);
		//删除数据
		$bool = Db::table($table)->where('id',$id)->delete();

		echo $bool?"1":'no';
	}

	//双击修改名字
	public function postDouble_updata_txt()
	{
		$id    = input('id');					//获取ID
		$val   = input('new_txt');				//获取新值
		$key   = input('name');					//获取字段名
		$table = input('table');				//表

		$bool = Db::table($table)->where('id',$id)->update([$key  => $val]);
		echo $bool?'1':'2';
	}
}
