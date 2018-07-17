<?php
namespace app\admin\controller;

use app\admin\controller\Allow;
use think\Db;

Class Article extends Allow
{
	//文章列表
	public function getArticle()
	{
		//分页
		$pro = input('pro')==""?1:input('pro');							//获取分页传过来的当前页
		$showNum = input('data_num')?input('data_num'):'5';				//设定每一个显示的条数
		$str = (($pro-1)*$showNum).",".$showNum;						//分页

		$s_val  = input('search_value');								//获取搜索传过来的值
		$s_ty   = input('search_type');    								//获取搜索传过来的字段名
		$s_si   = input('search_size');									//判断是否搜索大小写
		$s_type = $s_val?input('type'):'其他';							//获取搜索传过来的搜索类型

		$table = 'my_article';
		switch($s_type){
			//普通搜索
			case 1:
				if($s_ty == 'ico'){
					$ico = Db::table('my_article_ico')->where('name','like',"%".$s_val."%")->select();					//获取所有标签

					//组合查询标签条件
					$t = array();
					for($k = 0 ; $k<count($ico) ; $k++){
						$t[] = $ico[$k]['id'];
					}
					$ico = implode(',',$t);

					$icos = Db::table('my_article_ico_c')->where('iid','in',$ico)->select();							//查询出文章id

					//组合查询文章条件
					$ico_in = array();
					for($i = 0 ; $i<count($icos) ; $i++){
						$ico_in[] = $icos[$i]['aid'];
					}
					$ini = implode(',',$ico_in);

	
					$num  = Db::table($table)->where('id','in',$ini)->count();										//数量
					$data = Db::table($table)->where('id','in',$ini)->limit($str)->select();						//获取所有文章
				}else{
					$num  = Db::table($table)->where($s_ty,'like',"%".$s_val."%")->count();							//数量
					$data = Db::table($table)->where($s_ty,'like',"%".$s_val."%")->limit($str)->select();			//获取所有文章
				}

			break;

			//时间格式搜索
			case 2:
				//获取此天的时间区
				$s_val = explode(' ', $s_val);
				$start = strtotime($s_val[0].' 00:00:00');
				$end   = strtotime($s_val[0].' 23:59:59');

				$num  = DB::table($table)->where($s_ty,'between',[$start,$end])->count();
				$data = DB::table($table)->where($s_ty,'between',[$start,$end])->limit($str)->select();
			break;

			//默认列表
			default:
				$num  = Db::table($table)->count();
				$data = Db::table($table)->limit($str)->select();
			break;
		}


		//组合数据
		$img  = Db::table('my_img')->where('img_surface_name',$table)->select();								//获取所有文章缩略图片
		for($i = 0 ; $i<count($data) ; $i++){
			$ico  = Db::table('my_article_ico_c')->where('aid',$data[$i]['id'])->select();						//获取此文章所有标签

			//组合标签
			for($o = 0 ; $o<count($ico) ; $o++){
				$data[$i]['ico'][] = Db::table('my_article_ico')->where('id',$ico[$o]['iid'])->find()['name'];
			}

			//组合图片
			for($k = 0 ; $k<count($img) ; $k++){
				if($data[$i]['id'] == $img[$k]['img_surface_id']){
					$data[$i]['img'] = $this->imgPath.$img[$k]['img_surface_name'].DS.$img[$k]['img_path'];
				}
			}

			//整理时间
			$data[$i]['start_time'] = date('Y-m-d H:i:s',$data[$i]['start_time']);
		}


		//分页
		$arr['page'] = ceil($num/$showNum);//获取条数

		$arr['row']        = $data;																			//列表数据
		$arr['s_ty']       = $s_ty;																			//搜索保留值
		$arr['s_val']      = $s_val;																		//搜索保留值
		$arr['data_num']   = $showNum;																		//显示条数保留值
		$arr['table']      = $table;																		//模板上使用
		$arr['tables']     = 'article';																		//模板上使用
		$arr['empty']      = "<tr><td colspan='8' style='text-align:center'>没有任何数据</td></tr>";		//为空时显示
		$arr['title']	   = '文章管理';
		$arr['title_txt']  = '文章列表';

		return $this->fetch('Article/article',$arr);
	}


	//管理员添加与修改页
	public function getArticleadd()
	{
		$id = input('id');

		//判定是否添加
		$table = 'my_article';
		if(empty($id)){
			$arr['title_txt']  = '文章添加';
		}else{
			$arr['title_txt']  = '文章修改';
			$arr['row']        = Db::table($table)->where('id',$id)->find();			//找出用户

			$old_img['img_surface_name'] = $table;										//组合图片条件
			$old_img['img_surface_id']   = $id;
			$img = Db::table('my_img')->where($old_img)->find();  						//查找图片

			if(!empty($img)){
				$arr['row']['img'] = $this->imgPath.$img['img_surface_name'].DS.$img['img_path'];
			}
		}
		
		//判断已有标签
		$arr['row']['ico'] = array();
		if(!empty($id)){
			$ico = Db::table('my_article_ico_c')->where('aid',$id)->select();

			for($i = 0 ; $i<count($ico) ; $i++){
				$arr['row']['ico'][] = $ico[$i]['iid'];
			}
		}

		//获取所有标签
		$arr['ico']        = Db::table('my_article_ico')->order('sort desc')->select();

		$arr['table']      = 'my_article';																	//模板上使用
		$arr['tables']     = 'article';																		//模板上使用
		$arr['title']	   = '文章管理';
		return $this->fetch('article/articleadd',$arr);
	}

	//管理员添加与修改功能
	public function postArticledoadd()
	{
		$id 				 = input('id');
		$data['title']       = input('title');
		$data['brief']   	 = input('brief');
		$data['uid']  	  	 = 1;
		$data['content']  	 = input('content');
		$data['start_time']  = time();
		$ico   	 		     = input()['ico'];


		$files 				 = request()->file('imgs');
		$table 				 = 'my_article';
		//判断是否修改或添加
		if(!empty($id)){
			Db::table('my_article_ico_c')->where('aid',$id)->delete();			//删除所有原标签

			//插入标签
			$icos['aid'] = $id;
			for($i = 0 ; $i<count($ico) ; $i++){
				$icos['iid'] = $ico[$i];
				Db::table('my_article_ico_c')->insert($icos);
			}

	        $bool = Db::table($table)->where('id',$id)->update($data);			//更新数据

			upload_file($files , $table , $id , 1);								//操作图片

		}else{
		    $bool = Db::table($table)->insertGetId($data);						//插入数据

			//插入标签
			$icos['aid'] = $bool;
			for($i = 0 ; $i<count($ico) ; $i++){
				$icos['iid'] = $ico[$i];
				Db::table('my_article_ico_c')->insert($icos);
			}


			upload_file($files , $table , $bool , 1);						    //操作图片
		}

		echo $id==""?'添加成功':'修改成功';
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

		Db::table('my_article_ico_c')->where('aid',$id)->delete();
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
