<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;



Class Allow extends Controller
{
	public $imgPath = DS.'public'.DS.'uploads'.DS;		//普通上传的图片路径

	protected function _initialize()
	{
        // $glo['user']['id'] = Session::get('user_id');
        // $glo['user']['name'] = Session::get('user_name');

		//获取公告
    	$notice    = Db::table('my_notice')->where('status','1')->select();	//全局头部
    	foreach($notice as $v){
    		switch($v['address']){
    			case 1:$glo['notice']['notice_top'] = $v['content'];break;
    			case 2:$glo['notice']['notice_right'] = $v['content'];break;
    		}
    	}

    	    	//获取标签
    	$icos  = Db::table('my_article_ico')->order('sort desc')->select();
		$glo['ico'] 	= $icos;		//获取标签
		$glo['article'] = $data;		//获取文章内容
		
    	$this->assign('glo',$glo);

	}

}
