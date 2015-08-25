<?php
class system_controller_usergroup extends controller
{
    public function navbar()
    {
        return array(
			array('id'=>'index','title'=>'用户组列表','href'=>zotop::url('system/usergroup/index')),
			array('id'=>'add','title'=>'添加新用户组','href'=>zotop::url('system/usergroup/add')),
			array('id'=>'edit','title'=>'编辑用户组','href'=>''),
			
		);
    }

    public function actionIndex()
    {
		$usergroup = zotop::model('system.usergroup');
		
        $usergroups = $usergroup->getAll(array(
            'where'=>array('type','=','system'),
            'orderby'=>array('order'=>'asc'),
        ));

		$page = new page();
        $page->set('title','系统用户组管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('usergroups',$usergroups);
        $page->display();   
    }


	public function actionAdd()
	{
		$usergroup = zotop::model('system.usergroup');

		if ( form::isPostBack() )
		{
			$post = form::post();

			if( empty($post['id']) )
			{
				$post['id'] = $usergroup->max('id') + 1;
			}

			if( empty($post['title']) )
			{
				msg::error('用户组名称不能为空');
			}

			if( $usergroup->isExist('title',$post['title']) )
			{
				msg::error('用户组名称<b>'.$post['title'].'</b>已经存在');
			}

			$post['type'] = empty($post['type']) ? 'system' : $post['type'];
			$post['status'] = $post['status'] || 1;
			$post['order'] = $post['order'] || 0;

		    $insert = $usergroup->insert($post);

		    if( $insert )
		    {
				$usergroup->cache(true);
		        msg::success('保存成功，正在返回列表页面，请稍后……',zotop::url('system/usergroup'));   
		    }		
		}

		
        $page = new page();
        $page->set('title','添加新用户组');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar()); 
        $page->display();  		
	}

	public function actionEdit($id)
	{
		$usergroup = zotop::model('system.usergroup');

		if(form::isPostBack())
		{
			$post = form::post();


			if( empty($post['title']) )
			{
				msg::error('用户组名称不能为空');
			}


			$post['type'] = empty($post['type']) ? 'system' : $post['type'];
			$post['order'] = $post['order'] || 0;

		    $update = $usergroup->update($post,$id);

		    if( $update )
		    {
				$usergroup->cache(true);
		        msg::success('保存成功，正在返回列表页面，请稍后……',zotop::url('system/usergroup'));   
		    }		
		}

		$data = $usergroup->read($id);
		
        $page = new page();
        $page->set('title','添加新用户');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());
		$page->set('data',$data);
        $page->display();  		
	}

	public function actionDelete($id)
    {
		$user = zotop::model('system.user');
		$usergroup = zotop::model('system.usergroup');
		$usergroup->id = $id;
		$usergroup->read();

		if( $usergroup->id == 1 )
		{
			msg::error('超级管理员组无法被删除');
		}

		if( $user->countByGroupid($id)>0 )
		{
			msg::error('该用户组下面尚有用户，无法被删除');
		}

		zotop::run('system.usergroup.delete',$usergroup);

		if( $usergroup->delete() )
		{
			$usergroup->cache(true);
			msg::success('删除成功，正在重载数据，请稍后……',zotop::url('system/usergroup'));
		}
    }

    public function actionStatus($id,$status=-1)
    {
        $usergroup = zotop::model('system.usergroup');
        
        $post = array('status'=>$status);
        
        $update = $usergroup->update($post, $id);
        
        if( $update )
        {
			$usergroup->cache(true);
            msg::success('操作成功，正在刷新页面，请稍后……',zotop::url('system/usergroup'));   
        }        
    }

}
?>