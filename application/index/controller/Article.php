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

    //筛选文章列表
    public function getArticlelist()
    {
        $id  = input('tid');
        $iid = input('iid');
        $where['status'] = 1;
        $table = 'my_article';
        if(empty($iid)){
            switch($id){
                case 1:
                    $where['uid'] = $id;
                    $data = Db::table($table)->field('id,title,uid,brief,start_time,top,read')->order('start_time desc')->where($where)->select();
                break;
                case 2:
                    $data = Db::table($table)->field('id,title,uid,brief,start_time,top,read')->order('start_time desc')->where('uid','<>','1')->where($where)->select();
                break;
                case 3:
                    $data = Db::table($table)->field('id,title,uid,brief,start_time,top,read')->order('start_time desc')->where($where)->select();
                break;
            }
        }else{
            $ico  = Db::table('my_article_ico_c')->where('iid',$iid)->select();
            $icos = array();
            for($i = 0 ;$i<count($ico) ; $i++){
                $icos[] = $ico[$i]['aid'];
            }
            $str = implode(',',$icos);
            $data = Db::table($table)->field('id,title,uid,brief,start_time,top,read')->order('start_time desc')->where('id','in',$str)->where($where)->select();

        }


        //组合数据
        $img  = Db::table('my_img')->where('img_surface_name',$table)->select();                                //获取所有文章缩略图片
        for($i = 0 ; $i<count($data) ; $i++){

            //获取喜欢数
            $data[$i]['love'] = Db::table('my_article_love')->where('aid',$data[$i]['id'])->count();


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

        $row['list'] = $data;           //列表
        return $this->fetch('Article/articlelist',$row);
    }
}
