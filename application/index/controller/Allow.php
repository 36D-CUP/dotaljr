<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

Class Allow extends Controller
{
	public $imgPath = DS.'public'.DS.'uploads'.DS;		//普通上传的图片路径

	protected function _initialize()
	{

	}

}
