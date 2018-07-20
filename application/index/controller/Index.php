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
			//置顶数据
			if($data[$i]['top'] == '1'){
				if($i<=5){
					$hot[] = $data[$i];
				}
			}

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
			$data[$i]['start_time'] = date('Y-m-d',$data[$i]['start_time']);
		}
		$row['hot'] = $hot;
		$row['ico'] = $icos;
		$row['list'] = $data;
        return $this->fetch('Index/index',$row);
    }
}
