<?php
class usergroup_controller extends controller
{
    public function navbar()
    {
        return array(
			array('id'=>'index','title'=>'用户组列表','href'=>zotop::url('zotop/usergroup/index')),
			array('id'=>'add','title'=>'添加新用户组','href'=>zotop::url('zotop/usergroup/add')),
			array('id'=>'edit','title'=>'编辑用户组','href'=>''),
			
		);
    }

    public function indexAction()
    {
		$usergroup = zotop::model('zotop.usergroup');
		
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


	public function addAction()
	{
		$usergroup = zotop::model('zotop.usergroup');

		if(form::isPostBack())
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
				$usergroup->cache();
		        msg::success('保存成功，正在返回列表页面，请稍后……',zotop::url('zotop/usergroup'));   
		    }		
		}

		
        $page = new page();
        $page->set('title','添加新用户组');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar()); 
        $page->display();  		
	}

	public function editAction($id)
	{
		$usergroup = zotop::model('zotop.usergroup');

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
				$usergroup->cache();
		        msg::success('保存成功，正在返回列表页面，请稍后……',zotop::url('zotop/usergroup'));   
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

	public function deleteAction($id)
    {
		$user = zotop::model('zotop.user');
		$usergroup = zotop::model('zotop.usergroup');
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

		zotop::run('zotop.usergroup.delete',$usergroup);

		if( $usergroup->delete() )
		{
			$usergroup->cache();
			msg::success('删除成功，正在重载数据，请稍后……',zotop::url('zotop/usergroup'));
		}
    }

    public function statusAction($id,$status=-1)
    {
        $usergroup = zotop::model('zotop.usergroup');
        
        $post = array(
            'id'=>(int)$id,
            'status'=>$status,
        );
        
        $update = $usergroup->update($post);
        if( $update )
        {
			$usergroup->cache();
            msg::success('操作成功，正在刷新页面，请稍后……',zotop::url('zotop/usergroup'));   
        }        
    }

}
?>