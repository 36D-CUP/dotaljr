<?php
namespace app\index\controller;

use app\index\controller\Allow;
use think\Db;
use think\Session;

class Article extends Allow
{
    public function getArticle()
    {
    	$id = input('pid');				//文章id
        $uid = Session::get();                     //用户id





        //获取文章信息
        $where['status'] = 1;
        $where['id']     = $id;
        $data = Db::table('my_article')->field('id,title,uid,content,start_time,read')->order('start_time desc')->where($where)->find();
        $data['admin_name']        = Db::table('my_admin')->where('id',$data['uid'])->find()['admin_name'];

        //获取访问量
        $reads['id'] = $id;
        $data['read'] = Db::table('my_article')->where('id',$id)->find()['read'];
        $read_num['read'] = $data['read']+1;
        Db::table('my_article')->where($reads)->update($read_num);

        //获取喜欢量
        $data['love'] = Db::table('my_article_love')->where('aid',$id)->count();


        //整理时间
        $data['start_time'] = date('Y-m-d',$data['start_time']);

        //组合公告图片
        $old_img['img_surface_name'] = 'my_notice';
        $old_img['img_surface_id']   = '3';
        $img = $this->imgPath.'my_notice'.DS.Db::table('my_img')->where($old_img)->find()['img_path'];   

        $row['notice_img'] = $img;
        $row['article'] = $data;
        return $this->fetch('Article/article',$row);
    }
}
