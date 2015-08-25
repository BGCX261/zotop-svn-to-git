<?php
class system_controller_login extends controller
{
    //重载此函数以免再次验证
    public function __check()
    {

    }

    public function actionIndex()
    {
        $user = zotop::model('system.user');
		
		if ( form::isPostBack() )
        { 	
            
            $post = form::post();
			$user->login($post);
			
			if ( !$user->error() )
			{
				msg::success(zotop::t('登陆成功，系统正在加载中'),zotop::url(),2);
			}

			msg::error($user->msg());
        }		

        if ( !empty($this->user) )
        {
			header("Location: ".zotop::url());
			exit();
        }
        
        $page = new page();
        $page->set('title',zotop::t('系统管理登陆'));
		$page->set('body',array('class'=>'login'));
        $page->display();
        
    }
    
	public function actionLogout()
	{
	    $user = zotop::model('system.user');
		$user->logout();	
		msg::success('登出成功，系统正在关闭中',zotop::url('system/login'),2);
	}
	
	public function actionShortcut()
	{
		$title = zotop::get('title');
		$url = zotop::get('url');
		$shortcut = "[InternetShortcut]\nURL=".$url."\nIDList= [{000214A0-0000-0000-C000-000000000046}] Prop3=19,2";
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$title.".url;");
		echo $shortcut;
	}
}
?>