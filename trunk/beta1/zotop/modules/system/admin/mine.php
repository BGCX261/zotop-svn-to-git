<?php
class system_controller_mine extends controller
{

    public function navbar()
    {
        return array(
			'index'=>array('id'=>'index','title'=>'个人中心','href'=>zotop::url('system/mine')),
			'info'=>array('id'=>'changeinfo','title'=>'修改资料','href'=>zotop::url('system/mine/info')),
			'password'=>array('id'=>'changepassword','title'=>'修改密码','href'=>zotop::url('system/mine/password')),
			'image'=>array('id'=>'changeinfo','title'=>'头像设置','href'=>zotop::url('system/mine/image')),
		);
    }

    public function actionIndex()
    {
 		$page = new page();        
		$page->title = zotop::t('个人中心');
		$page->set('user',$user);
		$page->set('navbar',$this->navbar());
		$page->display();         
    }

    public function actionPassword()
    {
		$user = zotop::model('system.user');

        $user->id = (int)zotop::user('id');
        $user->username = (string)zotop::user('username');

        if( form::isPostBack() )
		{

			$user->read();
							
			$password = zotop::post('password');
			$newpassword = zotop::post('newpassword');
						
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
		$page->title = zotop::t('个人中心');
		$page->set('user',$user);
		$page->set('navbar',$this->navbar());
		$page->display();      
    }

    public function actionInfo()
    {
		$user = zotop::model('system.user');
		$user->id = (int)zotop::user('id');
        
    	if( form::isPostBack() )
		{			
		    $post = form::post();
		    
		    $update = $user->update($post,$user->id);

		    if( $update )
		    {
		        msg::success('资料设置成功，正在刷新页面，请稍后……',url::location());   
		    }
		    msg::error();		    
		}
		$data = $user->read();
				
		$page = new page();
		$page->title = zotop::t('个人中心');
		$page->set('navbar',$this->navbar());
		$page->set('globalid',$user->globalid());
		$page->set('data',$data);
		$page->display();          
    }
}
?>