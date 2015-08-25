<?php
class user_controller extends controller
{
   
    public function navbar()
    {
        return array(
			array('id'=>'default','title'=>'系统用户列表','href'=>url::build('zotop/user')),
			array('id'=>'add','title'=>'添加系统用户','href'=>url::build('zotop/user/add')),
			array('id'=>'changepassword','title'=>'修改密码'),
			array('id'=>'changeinfo','title'=>'修改资料'),
		);
    }
    
    public function onDefault()
    {
		$User = zotop::model('zotop.user');
		$list = $User->getAll(array(
		    'select'=>'*',
		    'where'=>array('modelid','=','system'),
		    'orderby'=>'logintime desc'
		));
                
        $page['title'] = '系统用户管理';

		page::header($page);
		page::top();
		page::navbar($this->navbar());
		
		form::header();
		
        $column = array();
    	$column['status w30 center'] = '状态';
    	$column['username'] = '账号名称';
    	$column['name'] = '姓名';
    	$column['loginnum'] = '登录次数';
    	$column['loginip w120'] = '登录IP';
    	$column['logintime w150'] = '登录时间';
    	$column['manage lock'] = '锁定';
    	$column['manage rename'] = '修改密码';
    	$column['manage edit'] = '编辑';
    	$column['manage delete'] = '删除';

        table::header('list',$column);
        foreach($list as $user)
        {
            $user['status-icon'] = $user['status'] == -1 ? html::image(url::theme().'/image/icon/lock.gif') : html::image(url::theme().'/image/icon/user.gif');
            
            $column = array();
            $column['status w30 center'] = $user['status-icon'];
            $column['username'] = '<a><b>'.$user['username'].'</b></a><h5>'.$user['email'].'</h5>';
        	$column['name w60'] = $user['name'];
        	$column['loginnum w60'] = $user['loginnum'];
        	$column['loginip w120'] = $user['loginip'];        	
        	$column['logintime w150'] = time::format($user['logintime']);
        	if( $user['status'] == -1 )
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/user/lock',array('id'=>$user['id'],'status'=>0)).'">解锁</a>';
        	}
        	else
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/user/lock',array('id'=>$user['id'])).'">锁定</a>';
        	}
        	$column['manage rename'] = '<a href="'.zotop::url('zotop/user/changepassword',array('id'=>$user['id'])).'">修改密码</a>';
        	$column['manage edit'] = '<a href="'.zotop::url('zotop/user/changeinfo',array('id'=>$user['id'])).'">编辑</a>';
        	$column['manage delete'] = '<a>删除</a>';
            table::row($column);
        }
        table::footer();
		form::buttons();
        form::footer();
		
		page::bottom();
		page::footer();        
    }
    
    public function onCheckUsername()
    {
        header("Cache-Control","no-store");
        header("Pragma","no-cache");
        header("Expires", "0");  
        $username = $_GET['username'];
        if( $username == 'chanlaye' )
        {
            echo 'true';
        }
        else
        {
            echo 'false';
        }
        exit();
    }
    
    public function onAdd()
    {
		
		
		if( form::isPostBack() )
		{
		    $user = zotop::model('zotop.user');
		    $post = form::post();		    
		    $update = $user->update($post,$user->id);
		    if( $update )
		    {
		        msg::success('保存成功','资料设置成功，正在刷新页面，请稍后……',zotop::url('zotop/user'));   
		    }		    
		}
		    
        $page['title'] = '修改我的资料';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header();
            
			block::header('账户信息');

			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('账户名称'),
			   'name'=>'username',
			   'value'=>$data['username'],
			   'valid'=>'required:true,username:true,minlength:2,maxlength:32,remote:"'.zotop::url('zotop/user/checkusername').'"',
			   'description'=>zotop::t('允许使用中文、数字、英文字符(不区分大小写)或者下划线，不允许使用其它特殊字符，2~32位'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('账户密码'),
			   'id'=>'newpassword',
			   'name'=>'newpassword',
			   'value'=>'',
			   'valid'=>'required:true,minlength:6,maxlength:32',
			   'description'=>zotop::t('请输入账户密码，6~32位之间'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('确认密码'),
			   'name'=>'newpassword2',
			   'value'=>'',
			   'valid'=>'required:true,equalTo:"#newpassword"',
			   'description'=>zotop::t('为确保安全，请再次输入账户密码'),
			));						
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('安全问题'),
			   'name'=>'question',
			   'value'=>$data['question'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('安全答案'),
			   'name'=>'answer',
			   'value'=>$data['answer'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));

			form::field(array(
			   'type'=>'select',
			   'label'=>zotop::t('用户组设定'),
			   'name'=>'groupid',
			   'value'=>$data['groupid'],
			   'valid'=>'',
			   'description'=>zotop::t('不同用户组所属角色不同，权限也不同'),
			));				
			
			block::footer();
			
			block::header('个人信息');
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('真实姓名'),
			   'name'=>'name',
			   'value'=>$data['name'],
			   'valid'=>'required:true',
			   'description'=>zotop::t(''),
			));			
			form::field(array(
			   'type'=>'radio',
			   'options'=>array('男'=>'男','女'=>'女'),
			   'label'=>zotop::t('性别'),
			   'name'=>'gender',
			   'value'=>$data['gender'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			form::field(array(
			   'type'=>'image',
			   'label'=>zotop::t('头像'),
			   'name'=>'image',
			   'value'=>$data['image'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
		    form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('电子邮件'),
			   'name'=>'email',
			   'value'=>$data['email'],
			   'valid'=>'required:true,email:true',
			   'description'=>zotop::t(''),
			));	
			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('个人签名'),
			   'name'=>'sign',
			   'value'=>$data['sign'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('更新时间'),
			   'name'=>'updatetime',
			   'value'=>$data['updatetime'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			
								
			block::footer();
			
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();          
    }
    
    public function onChangePassword($id)
    {
		$user = zotop::model('zotop.user');
        $user->id = (int)$id;
        $user->read();
        
        if(form::isPostBack())
		{							
			$newpassword = request::post('newpassword');
						
			if( $newpassword != request::post('newpassword2') )
			{
			    msg::error('输入错误',zotop::t('两次输入的新密码不一致，请确认'));
			}			

			if($newpassword != $user->password)
			{
			   $update = $user->update(array(
			       'id' => $user->id,
			       'password' => $user->password($newpassword),
			   ));

			}
            msg::success('修改成功',zotop::t('密码修改成功'),zotop::url('zotop/user'));			
		}
		$page['title'] = '修改用户密码';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header(array(
				'title'=>'修改密码',
				'description'=>'为确保账户安全，请不要使用过于简单的密码，并及时的更换密码',
			    'icon'=>''
			));
			
			form::field(array(
			   'type'=>'label',
			   'label'=>zotop::t('账户名称'),
			   'name'=>'username',
			   'value'=>$user->username,
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			

			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('新密码'),
			   'id'=>'newpassword',
			   'name'=>'newpassword',
			   'value'=>'',
			   'valid'=>'required:true,minlength:6,maxlength:32',
			   'description'=>zotop::t('请输入您的新密码，6~32位之间'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('确认新密码'),
			   'name'=>'newpassword2',
			   'value'=>'',
			   'valid'=>'required:true,equalTo:"#newpassword"',
			   'description'=>zotop::t('为确保安全，请再次输入您的新密码'),
			));
			
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();        
    }
    
    public function onChangeInfo($id)
    {
		$user = zotop::model('zotop.user');
		$user->id = (int)$id;
		
		if( form::isPostBack() )
		{
		    $post = form::post();
		    
		    $update = $user->update($post,$user->id);
		    if( $update )
		    {
		        msg::success('保存成功','资料设置成功，正在刷新页面，请稍后……',zotop::url('zotop/user'));   
		    }		    
		}
		
		$data = $user->read();
		$data['updatetime'] = TIME;
		    
        $page['title'] = '修改我的资料';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header();
            
			block::header('账户信息');

			form::field(array(
			   'type'=>'label',
			   'label'=>zotop::t('账户名称'),
			   'name'=>'username',
			   'value'=>$data['username'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
						
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('安全问题'),
			   'name'=>'question',
			   'value'=>$data['question'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('安全答案'),
			   'name'=>'answer',
			   'value'=>$data['answer'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			
			
			block::footer();
			
			block::header('个人信息');
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('真实姓名'),
			   'name'=>'name',
			   'value'=>$data['name'],
			   'valid'=>'required:true',
			   'description'=>zotop::t(''),
			));			
			form::field(array(
			   'type'=>'radio',
			   'options'=>array('男'=>'男','女'=>'女'),
			   'label'=>zotop::t('性别'),
			   'name'=>'gender',
			   'value'=>$data['gender'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			form::field(array(
			   'type'=>'image',
			   'label'=>zotop::t('头像'),
			   'name'=>'image',
			   'value'=>$data['image'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
		    form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('电子邮件'),
			   'name'=>'email',
			   'value'=>$data['email'],
			   'valid'=>'required:true,email:true',
			   'description'=>zotop::t(''),
			));	
			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('个人签名'),
			   'name'=>'sign',
			   'value'=>$data['sign'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('更新时间'),
			   'name'=>'updatetime',
			   'value'=>$data['updatetime'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			
								
			block::footer();
			
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();         
    }
    
    public function onLock($id,$status=-1)
    {
        $user = zotop::model('zotop.user');
        
        $post = array(
            'id'=>(int)$id,
            'status'=>$status,
        );
        
        $update = $user->update($post);
        if( $update )
        {
            msg::success('操作成功','正在刷新页面，请稍后……',zotop::url('zotop/user'));   
        }        
    }
}
?>