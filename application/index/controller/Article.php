<?php
namespace app\index\controller;

use app\index\controller\Allow;
use think\Db;
class Article extends Allow
{
    public function getArticle()
    {
    	$id = input('pid');				//文章id
        $uid = '1';                     //用户id

        $readdata['uid'] = $uid;
        $readdata['aid'] = $id;
        $read = Db::table('my_article_read')->where($readdata)->select();
        if(empty($read)){
            Db::table('my_article_read')->insert($readdata);
        }


        //获取文章信息
        $where['status'] = 1;
        $where['id']     = $id;
        $data = Db::table('my_article')->field('id,title,uid,content,start_time')->order('start_time desc')->where($where)->find();
        $data['admin_name']        = Db::table('my_admin')->where('id',$data['uid'])->find()['admin_name'];

        //获取访问量
        $data['read'] = Db::table('my_article_read')->where('aid',$id)->count();

        //获取喜欢量
        $data['love'] = Db::table('my_article_love')->where('aid',$id)->count();


        //整理时间
        $data['start_time'] = date('Y-m-d',$data['start_time']);

    	//获取标签
    	$icos  = Db::table('my_article_ico')->order('sort desc')->select();
		$row['ico'] 	= $icos;		//获取标签
		$row['article'] = $data;		//获取文章内容



        return $this->fetch('Article/article',$row);
    }
}
