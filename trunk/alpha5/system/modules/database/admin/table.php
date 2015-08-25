<?php
class table_controller extends controller
{
	public function navbar()
	{
		return array(
			array('id'=>'tables','title'=>'数据表管理','href'=>zotop::url('database/table')),
			array('id'=>'add','title'=>'新建数据表','href'=>zotop::url('database/table/add'),'class'=>'dialog zotop-icon add'),
			array('id'=>'edit','title'=>'数据表设置','href'=>''),
			array('id'=>'edit','title'=>'SQL执行','href'=>zotop::url('database/table/sql'),'class'=>'dialog'),
			//array('id'=>'backup','title'=>'备 份','href'=>zotop::url('database/table/backup')),
		);
	}
	    
    public function indexAction()
    {
	   $db = zotop::db();
	   
	   $database = $db->config();
	   $database['version'] = $db->version();
	   $database['size'] = $db->size();

       $tables = zotop::db()->tables(true);
       
       $page = new page();
       $page->title = '数据库管理';
       $page->set('position',$database['database'].' @ '.$database['hostname']);
       $page->set('navbar',$this->navbar());
       $page->set('database',$database);
       $page->set('tables',$tables);       
       $page->display();
    }

	public function addAction()
	{
       $page = new dialog();
       $page->title = '数据库管理';
       $page->set('position',$database['database'].' @ '.$database['hostname'].'<i>></i>创建');       
	   $page->set('database',$database);
       $page->set('tables',$tables);       
       $page->display();	
	}
    
	public function createAction()
	{
		if( form::isPostBack() )
		{
    	    $tablename = request::post('tablename');
    		$comment = request::post('comment');
    		if( empty($tablename) )
    		{
    			$this->error('数据表名称不能为空');
    		}
    		$create = zotop::db()->table('#'.$tablename)->create();
    		
		    if( $comment !== NULL )
			{
				$comment = zotop::db()->table('#'.$tablename)->comment($comment);
			}    		
    
    		$this->success('操作成功,数据表创建成功',zotop::url('database/table'));
	    }
	}

	public function editAction($tablename)
	{
		if( form::isPostBack() )
		{
			$tablename =request::post('tablename');
			$name = request::post('name');
			$comment = request::post('comment');
			$primary = request::post('primary');


			if( strtolower($tablename) !== strtolower($name) )
			{
				$rename = zotop::db()->table($tablename)->rename($name);
			}
			if( $comment !== NULL )
			{
				$comment = zotop::db()->table($name)->comment($comment);
			}
			if( $primary )
			{
				$primary = zotop::db()->table($name)->primary($primary);
			}

			$this->success('数据表设置成功，正在刷新页面，请稍后……',zotop::url('database/table'));
		}	    
	    
	    $db = zotop::db();
	    $database = $db->config();
	    $tables = $db->tables(true);
		$table = $tables[$tablename];

		if(!isset($table))
		{
			$this->error(zotop::t('数据表{$tablename}不存在',array('tablename'=>$tablename)) );
		}
		
       $page = new dialog();
       $page->title = '数据库管理：'.$database['database'].' @ '.$database['hostname'] .'<i>></i> 编辑：'.$tablename;
       $page->set('database',$database);
       $page->set('table',$table);       
       $page->display();
	}

	public function sqlAction()
	{

		if( form::isPostBack() )
		{
			msg::error('该功能已经被禁用，请进入设置开启');
		}
	   
		$page = new dialog();
		$page->title = '数据库管理：'.$database['database'].' @ '.$database['hostname'] .'<i>></i> 执行sql语句';
		$page->display();		
	}
    
    
}
?>