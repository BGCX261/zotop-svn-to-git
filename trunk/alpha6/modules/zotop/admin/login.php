<?php
class zotop_controller_login extends controller
{
    //重载此函数以免再次验证
    public function __check()
    {

    }

    public function actionIndex()
    {
        $user = zotop::model('zotop.user');
		
		if( form::isPostBack() )
        { 	
            
            $post = array();
			$post['username'] = request::post('username');
			$post['password'] = request::post('password');
			$post['logintime'] = time();
			
			zotop::cookie('admin.username',$post['username'],3600);
			
			if( empty($post['username']) )
			{
			    msg::error(zotop::t('登陆失败，请输入登陆账户名称'));   
			}
			if( empty($post['password']) )
			{
			    msg::error(zotop::t('登陆失败，请输入登陆账户密码'));   
			}            
			if( !$user->isValidUserName($post['username']) )
			{
			    msg::error(zotop::t('登陆失败，请输入有效的账户名称'));
			}
        	if( !$user->isValidPassword($post['password']) )
			{
			    msg::error(zotop::t('登陆失败，请输入有效的账户密码'));
			}
			//读取用户		
			$data = $user->read(array('username','=',$post['username']));
			//验证
			if( $data == false )
			{
				msg::error(zotop::t('账户名称`{$username}`不存在，请检查是否输入有误！',array('username'=>$post['username'])));
			}
			
		    if( $user->password($post['password']) != $data['password'] )
		    {
				msg::error(zotop::t('账户密码`{$password}`错误，请检查是否输入有误！',array('password'=>$post['password'])));
		    }
		    //用户登入
            $user->login();
            //跳转
			msg::success('登陆成功，系统正在加载中',zotop::url('zotop/index'),2);
        }		

        if ( !empty($this->user) )
        {
            zotop::redirect('zotop/index');
        }
        
        $page = new page();
        $page->set('title',zotop::t('系统管理登陆'));
		$page->set('body',array('class'=>'login'));
        $page->display('login');
        
    }
    
	public function actionLogout()
	{
	    $user = zotop::model('zotop.user');
		$user->logout();	
		msg::success('登出成功，系统正在关闭中',zotop::url('zotop/login'),2);
	}
	
	public function actionShortcut($title, $url)
	{
		$shortcut = "[InternetShortcut]\nURL=".$url."\nIDList= [{000214A0-0000-0000-C000-000000000046}] Prop3=19,2";
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$title.".url;");
		echo $shortcut;
	}
}
?>