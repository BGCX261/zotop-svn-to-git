<?php
class database_controller_table extends controller
{
	public function navbar()
	{
		return array(
			'index'=>array('id'=>'index','title'=>'<span class="zotop-icon"></span>数据表管理','href'=>zotop::url('database/table')),
			'add'=>array('id'=>'add','title'=>'<span class="zotop-icon"></span>新建数据表','href'=>zotop::url('database/table/add'),'class'=>'dialog'),
			'edit'=>array('id'=>'edit','title'=>'数据表设置','href'=>''),
			'edit'=>array('id'=>'edit','title'=>'SQL执行','href'=>zotop::url('database/table/sql'),'class'=>'dialog'),
		);
	}
	    
    public function actionIndex()
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

	public function actionAdd()
	{
       $page = new dialog();
       $page->title = '数据库管理';
       $page->set('position',$database['database'].' @ '.$database['hostname'].'<i>></i>创建');       
	   $page->set('database',$database);
       $page->set('tables',$tables);       
       $page->display();	
	}
    
	public function actionCreate()
	{
		if( form::isPostBack() )
		{
    	    $tablename = zotop::post('tablename');
    		$comment = zotop::post('comment');
    		if( empty($tablename) )
    		{
    			msg::error('数据表名称不能为空');
    		}
    		$create = zotop::db()->table('#'.$tablename)->create();
    		
		    if( $comment !== NULL )
			{
				$comment = zotop::db()->table('#'.$tablename)->comment($comment);
			}    		
    
    		msg::success('操作成功,数据表创建成功',zotop::url('database/table'));
	    }
	}

	public function actionEdit($tablename)
	{
		if( form::isPostBack() )
		{
			$tablename =zotop::post('tablename');
			$name = zotop::post('name');
			$comment = zotop::post('comment');
			$primary = zotop::post('primary');


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

			msg::success('数据表设置成功，正在刷新页面，请稍后……',zotop::url('database/table'));
		}	    
	    
	    $db = zotop::db();
	    $database = $db->config();
	    $tables = $db->tables(true);
		$table = $tables[$tablename];

		if(!isset($table))
		{
			msg::error(zotop::t('数据表{$tablename}不存在',array('tablename'=>$tablename)) );
		}
		
       $page = new dialog();
       $page->title = '数据库管理：'.$database['database'].' @ '.$database['hostname'] .'<i>></i> 编辑：'.$tablename;
       $page->set('database',$database);
       $page->set('table',$table);       
       $page->display();
	}

	public function actionDelete($tablename)
	{
		$delete = zotop::db()->table($tablename)->drop();

		if( !$delete )
		{
			msg::error(zotop::t('删除数据表{$tablename}失败',array('tablename'=>$tablename)) );
		}
		msg::success('删除成功',zotop::url('database/table'));

	}

	public function actionSql()
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