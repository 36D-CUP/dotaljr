<?php
namespace app\admin\controller;

use think\Controller;

Class Index extends Controller
{
	public function getIndex()
	{
		return $this->fetch('Index/index');
		// echo "2";
	}

	public function getAdminList()
	{
		return $this->fetch('index/adminlist');

		
	}

}
