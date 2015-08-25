<?php
class controller extends BaseController
{
    public $db = null;

	public function __construct()
    {

		$this->db = zotop::db(); //数据库
        if(!user::isLogin())//将权限判断写在这儿，加载的其他控制器都将从这个控制器继承，
        {
            //url::redirect('system/login');
        }

    }

    public function onDefault()
    {
        echo 'Hello Zotop Administrator!';
    }
}
?>