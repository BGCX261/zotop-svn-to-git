<?php
class zotop_controller_user extends controller
{
    public function navbar()
    {
        return array(
			array('id'=>'index','title'=>'用户列表','href'=>zotop::url('zotop/user/index')),
			array('id'=>'add','title'=>'添加新用户','href'=>zotop::url('zotop/user/add')),
		);
    }

    public function actionIndex()
    {
        $user = zotop::model('zotop.user');
		$usergroup = zotop::model('zotop.usergroup');
		
        $users = $user->getAll(array(
            //'where'=>array('modelid','=','system'),
            //'orderby'=>array('logintime'=>'desc'),
        ));
		//获取用户组
		$usergroups = $usergroup->getIndex();
        
        $page = new page();
        $page->set('title','系统用户管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('users',$users);
		$page->set('usergroups',$usergroups);
        $page->display();   
    }

    public function actionLock($id,$status=-1)
    {
        $user = zotop::model('zotop.user');
        $user->id = $id;
		$user->read();

		if( $user->id == 1 || $user->groupid === 0 )
		{
			msg::error('系统管理员无法被锁定');
		}
		       
        $post = array(
            'status'=>(int)$status,
        );
        
        $update = $user->update($post,(int)$id);
        if( $update )
        {
            msg::success('操作成功，正在刷新页面，请稍后……',zotop::url('zotop/user'));   
        }        
    }

    public function actionCheckUsername()
    {
        header("Cache-Control","no-store");
        header("Pragma","no-cache");
        header("Expires", "0"); 

		$username = $_GET['username'];
		
		$user = zotop::model('zotop.user');
		
		if( $user->isExist('username',$username) )
        {
            echo 'false';
        }
        else
        {
            echo 'true';
        }
        exit();
    }

	public function actionAdd()
	{
        $user = zotop::model('zotop.user');
		$usergroup = zotop::model('zotop.usergroup');

		if(form::isPostBack())
		{
			$post = form::post();

			if( empty($post['id']) )
			{
				$post['id'] = $user->max('id') + 1;
			}

			if( empty($post['username']) )
			{
				msg::error('帐户名称不能为空');
			}

			if( empty($post['password']) )
			{
				msg::error('帐户密码不能为空');
			}
			
			if( $post['password'] != $_POST['_password'] )
			{
				msg::error('两次输入的密码不一致');
			}

			if( $user->isExist('username',$post['username']) )
			{
				msg::error('帐户名称<b>'.$post['username'].'</b>已经存在');
			}

			if( $user->isExist('email',$post['email']) )
			{
				msg::error('电子邮件<b>'.$post['email'].'</b>已经存在');
			}

			$post['password'] = $user->password($post['password']);
			$post['loginnum'] = $post['loginnum'] || 0;
			$post['createtime'] = $post['createtime'] || TIME;
			$post['modelid'] = 'system';

		    $insert = $user->insert($post);

		    if( $insert )
		    {
		        msg::success('保存成功，正在返回列表页面，请稍后……',zotop::url('zotop/user'));   
		    }		
		}
		//获取用户组
		$usergroups = $usergroup->getIndex('system');
		
        $page = new page();
        $page->set('title','添加新用户');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('data',$data);   
		$page->set('usergroups',$usergroups);   
        $page->display();  		
	}

	public function actionDelete($id)
    {

		$user = zotop::model('zotop.user');
		$user->id = $id;
		$user->read();

		if( $user->id == 1 || $user->groupid === 0 )
		{
			msg::error('系统管理员无法被删除');
		}

		zotop::run('zotop.user.delete',$user);

		if( $user->delete() )
		{
			msg::success('删除成功，正在重载数据，请稍后……',zotop::url('zotop/user'));
		}
    }

}
?>