<?php
class mine_controller extends controller
{
    public function navbar()
    {
        return array(
			array('id'=>'changeinfo','title'=>'修改我的资料','href'=>zotop::url('zotop/mine/changeinfo')),
			array('id'=>'changepassword','title'=>'修改我的密码','href'=>zotop::url('zotop/mine/changepassword')),
		);
    }

    public function indexAction()
    {
        
    }

    public function changePasswordAction()
    {
		$user = zotop::model('zotop.user');
        $user->id = (int)zotop::user('id');
        $user->username = (string)zotop::user('username');
        if( form::isPostBack() )
		{

			$user->read();
							
			$password = request::post('password');
			$newpassword = request::post('newpassword');
						
			if( $user->password($password) != $user->password )
			{
			    msg::error(zotop::t('您输入的原密码:<b>{$password}</b>错误，请确认',array('password'=>$password)));
			}			
			if( $newpassword != request::post('newpassword2') )
			{
			    msg::error(zotop::t('两次输入的新密码不一致，请确认'));
			}			

			if($newpassword != $password)
			{
			   $update = $user->update(array(
			       'id' => $user->id,
			       'password' => $user->password($newpassword),
			   ));

			}
            msg::success(zotop::t('密码修改成功，请记住您的新密码'),url::current());			
		}
		
		$page = new page();        
		$page->title = '修改我的密码';
		$page->set('user',$user);
		$page->set('navbar',$this->navbar());
		$page->display();      
    }

    public function changeInfoAction()
    {
		$user = zotop::model('zotop.user');
		$user->id = (int)zotop::user('id');
        
    	if( form::isPostBack() )
		{

			
		    $post = form::post();
		    
		    $update = $user->update($post,$user->id);
		    if( $update )
		    {
		        msg::success('资料设置成功，正在刷新页面，请稍后……',url::current());   
		    }
		    msg::error();		    
		}
		$data = $user->read();     
		
		$page = new page();
		$page->title = '修改我的基本信息';
		$page->set('navbar',$this->navbar());
		$page->set('data',$data);
		$page->display();          
    }
}
?>