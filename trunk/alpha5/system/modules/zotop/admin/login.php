<?php
class login_controller extends controller
{
    //重载此函数以免再次验证
    public function __check()
    {

    }
    
    public function indexAction()
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
			msg::success('登陆成功，系统正在加载中',url::current(),2);
        }
        if( !empty($this->user) )
        {
            $this->redirect('zotop/index');
        } 

		$data = $user->read(array('username','=','admin'));
		

        $page = new page();
        $page->title = '系统登陆';
        $page->body = array('class'=>'login');
        $page->addScript('$this/js/login.js');
        $page->display();
        
    }
    
	public function logoutAction()
	{
	    $user = zotop::model('zotop.user');
		$user->logout();	
		msg::success('登出成功，系统正在关闭中',zotop::url('zotop/login'),2);
	}    
}
?>