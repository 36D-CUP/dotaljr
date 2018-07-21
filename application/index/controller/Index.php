<?php
namespace app\index\controller;

use app\index\controller\Allow;
use think\Db;
class Index extends Allow
{
    public function Index()
    {
    	$table = 'my_article';
    	$data = Db::table('my_article')->field('id,title,uid,brief,start_time,top')->order('start_time desc')->where('status','1')->select();

    	$icos  = Db::table('my_article_ico')->order('sort desc')->select();

    	$hot = array();		//置顶数据
		//组合数据
		$img  = Db::table('my_img')->where('img_surface_name',$table)->select();								//获取所有文章缩略图片
		for($i = 0 ; $i<count($data) ; $i++){

			//获取喜欢数
			$data[$i]['love'] = Db::table('my_article_love')->where('aid',$data[$i]['id'])->count();
			//获取阅读数
			$data[$i]['read'] = Db::table('my_article_read')->where('aid',$data[$i]['id'])->count();

			//置顶数据
			if($data[$i]['top'] == '1'){
				$hot[] = $data[$i];
			}


			//获取此文章所有标签
			$ico  = Db::table('my_article_ico_c')->where('aid',$data[$i]['id'])->select();						

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

			$data[$i]['admin_name']        = Db::table('my_admin')->where('id',$data[$i]['uid'])->find()['admin_name'];
			//整理时间
			$data[$i]['start_time'] = date('Y-m-d',$data[$i]['start_time']);
		}

		//排序数组
		foreach ($hot as $key => $row)
		    {
		        $volume[$key]  = $row['love'];
		    }
		array_multisort($volume, SORT_DESC, $hot);
		//截取数组
		$hots = array_slice($hot,0,5);



		$row['hot'] = $hots;			//热度
		$row['ico'] = $icos;			//标签
		$row['list'] = $data;			//列表
        return $this->fetch('Index/index',$row);
    }

    public function postLove()
    {
    	$data['aid'] = input('aid');			//文章id
    	$data['uid'] = input('uid');			//用户id

    	//查询是否已经添加
    	$row = Db::table('my_article_love')->where($data)->select();
    	if(empty($row)){
    		$bool = Db::table('my_article_love')->insert($data);
    		//获取新数据
    		$val  = Db::table('my_article_love')->where('aid',$data['aid'])->count();
    	}else{
    		$bool = '';
    	}

    	if(empty($bool)){
    		echo "no";
    	}else{
    		echo $val;
    	}
    }
}
