<?php
class role_controller extends controller
{
   
    public function navbar()
    {
        return array(
			array('id'=>'default','title'=>'系统角色列表','href'=>url::build('zotop/role')),
			array('id'=>'add','title'=>'添加角色','href'=>url::build('zotop/role/add')),
			array('id'=>'edit','title'=>'修改角色'),
		);
    }
    
    public function onDefault()
    {
		$role = zotop::model('zotop.role');
		$list = $role->getAll(array(
		    'select'=>'*',
		    'orderby'=>'order desc'
		));
                
        $page['title'] = '系统用户管理';
        
        //zotop::dump($role->db()->lastsql());

		page::header($page);
		page::top();
		page::navbar($this->navbar());
		
		form::header();
		
        $column = array();
    	$column['status w30 center'] = '状态';
    	$column['id w30 center'] = '编号';
    	$column['rolename'] = '角色名称';
    	$column['manage lock'] = '锁定';
    	$column['manage edit'] = '编辑';
    	$column['manage delete'] = '删除';

        table::header('list',$column);
        foreach($list as $role)
        {
            $role['status-icon'] = $role['status'] == -1 ? html::image(url::theme().'/image/icon/lock.gif') : html::image(url::theme().'/image/icon/ok.gif');
            
            $column = array();
            $column['status w30 center'] = $role['status-icon'];
            $column['id w30 center'] = $role['id'];
            $column['rolename'] = '<a><b>'.$role['name'].'</b></a><h5>'.$role['description'].'</h5>';
        	if( $role['status'] == -1 )
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/role/lock',array('id'=>$role['id'],'status'=>0)).'">解锁</a>';
        	}
        	else
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/role/lock',array('id'=>$role['id'])).'">锁定</a>';
        	}
        	$column['manage edit'] = '<a href="'.zotop::url('zotop/role/edit',array('id'=>$role['id'])).'">编辑</a>';
        	$column['manage delete'] = '<a>删除</a>';
            table::row($column);
        }
        table::footer();
		form::buttons();
        form::footer();
		
		page::bottom();
		page::footer();        
    }
    
    public function onCheckname()
    {
        header("Cache-Control","no-store");
        header("Pragma","no-cache");
        header("Expires", "0");

        $role = zotop::model('zotop.role');       
        $role->name = $_GET['name'];
        if( !$role->isExist() )
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
		    $role = zotop::model('zotop.role');
		    $post = form::post();
		    $post['id'] = $role->max()+1;
            $post['order'] = $role->max('order')+1;
            $post['status'] = 0;		    	    
		    $insert = $role->insert($post);
		    if( $insert )
		    {
		        msg::success('操作成功','保存成功，正在刷新页面，请稍后……',zotop::url('zotop/role'));   
		    }		    
		}
        $page['title'] = '添加系统角色';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header();
            

			form::field(array(
			   'type'=>'text',
			   'label'=>'角色名称',
			   'name'=>'name',
			   'value'=>$fields['name'],
			   'valid'=>'required:true,maxlength:50',
			   'description'=>'',
			));

			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('角色说明'),
			   'name'=>'description',
			   'value'=>$fields['description'],
			   'valid'=>'',
			   'description'=>'',
			));
	
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();          
    }
    
    public function onEdit($id)
    {
	    $role = zotop::model('zotop.role');
	    $role->id = (int)$id;
	    	
		if( form::isPostBack() )
		{

		    $post = form::post();
		    $result = $role->update($post);
		    if( $result )
		    {
		        msg::success('操作成功','保存成功，正在刷新页面，请稍后……',zotop::url('zotop/role'));   
		    }		    
		}
        
		$fields = $role->read();
		
		$page['title'] = '编辑系统角色';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header();
            

			form::field(array(
			   'type'=>'text',
			   'label'=>'角色名称',
			   'name'=>'name',
			   'value'=>$fields['name'],
			   'valid'=>'required:true,maxlength:50',
			   'description'=>zotop::t(''),
			));

			form::field(array(
			   'type'=>'textarea',
			   'label'=>'角色说明',
			   'name'=>'description',
			   'value'=>$fields['description'],
			   'valid'=>'',
			   'description'=>'',
			));
	
										
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
        $role = zotop::model('zotop.role');
        
        $post = array(
            'id'=>(int)$id,
            'status'=>$status,
        );
        
        $update = $role->update($post);
        if( $update )
        {
            msg::success('操作成功','正在刷新页面，请稍后……',zotop::url('zotop/role'));   
        }        
    }
}
?>