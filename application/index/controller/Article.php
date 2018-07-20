<?php
namespace app\index\controller;

use app\index\controller\Allow;
use think\Db;
class Article extends Allow
{
    public function getArticle()
    {
    	$id = input('pid');				//文章id

    	//获取文章信息
    	$where['status'] = 1;
    	$where['id'] 	 = $id;
    	$data = Db::table('my_article')->field('id,title,uid,content,start_time')->order('start_time desc')->where($where)->find();

    	//获取标签
    	$icos  = Db::table('my_article_ico')->order('sort desc')->select();
		$row['ico'] 	= $icos;		//获取标签
		$row['article'] = $data;		//获取文章内容
        return $this->fetch('Article/article',$row);
    }
}
