<?php
class database_controller_field extends controller
{
    public function navbar($tablename)
	{
		return array(
		  array('id'=>'tables','title'=>'数据表管理','href'=>url::build('database/table')),
		  array('id'=>'index','title'=>'数据表结构','href'=>url::build('database/field/index',array('tablename'=>$tablename))),
		  array('id'=>'add','title'=>'新建字段','href'=>url::build('database/field/add',array('tablename'=>$tablename)),'class'=>'dialog'),
		  array('id'=>'edit','title'=>'字段修改','href'=>''),
	   );
	}
	    
    public function actionIndex($tablename)
    {
        $db = zotop::db();
        
        $database = $db->config();
        $database['version'] = $db->version();
        $database['size'] = $db->size();
	           
        $tables = $db->tables(true);
        $table = $tables[$tablename];
        $fields = array();
        if(isset($table))
        {
        	$fields = $db->table($tablename)->fields(true);
        }
        $indexes = $db->table($tablename)->index();
        
        $page = new page();
        $page->title = '数据库管理';
        $page->set('position','<a href="'.zotop::url('database/table').'">'.$database['database'].' @ '.$database['hostname'].'</a><i>></i> 数据表 : <b>'.$tablename. '</b>');         
        $page->set('navbar',$this->navbar($tablename));
        $page->set('database',$database);
        $page->set('tablename',$tablename);
        $page->set('table',$table);
        $page->set('fields',$fields);
        $page->set('indexes',$indexes);       
        $page->display();
    }

	public function actionAdd($tablename)
	{
		$db = zotop::db();
		
	    if(form::isPostBack())
		{
			$field = array();
			$field['name'] = request::post('name');
			$field['length'] = request::post('len');
			$field['type'] = request::post('type');
			$field['collation'] = request::post('collation');
			$field['null'] = request::post('null');
			$field['default'] = request::post('default');
			$field['attribute'] = request::post('attribute');
			$field['extra'] = request::post('extra');
			$field['comment'] = request::post('comment');
			$field['position'] = request::post('position');


			$result = $db->table($tablename)->add($field);

			if($result)
			{
				msg::success('字段创建成功',zotop::url('database/field/index',array('tablename'=>$tablename)));
			}

		}

		$tables = $db->tables(true);
		$table = $tables[$tablename];
		$fields = array();
		if(isset($table))
		{
			$fields = $db->table($tablename)->fields(true);
		}

		$positions = array();
		$positions[-1] = '位于表头';
		if( $fields )
		{
			foreach( $fields as $key=>$val )
			{
				$positions[$key] = '位于 '.$key.' 之后';
			}
		}
		$positions[0] = '位于表尾';

		$field['collation'] = 'utf8_general_ci';
			    
       $page = new dialog();
	   $page->title = '创建新字段';
       $page->set('database',$database);
       $page->set('tables',$tables); 
       $page->set('field',$field);
       $page->set('positions',$positions);       
       $page->display();	
	}
    

	public function actionEdit($tablename,$fieldname)
	{
       
		if(form::isPostBack())
		{
			$field = array();
			$field['name'] = request::post('name');
			$field['length'] = request::post('len');
			$field['type'] = request::post('type');
			$field['collation'] = request::post('collation');
			$field['null'] = request::post('null');
			$field['default'] = request::post('default');
			$field['attribute'] = request::post('attribute');
			$field['extra'] = request::post('extra');
			$field['comment'] = request::post('comment');
			$field['position'] = request::post('position');

			$fieldname = request::post('fieldname');

			if( $fieldname != $field['name'] )
			{
				$result = zotop::db()->table($tablename)->field($fieldname)->rename($field['name']);
			}

			$result = zotop::db()->table($tablename)->modify($field);

			if($result)
			{
				msg::success('字段修改成功',zotop::url('database/field/index',array('tablename'=>$tablename)));
			}

		}

		$tables = zotop::db()->tables(true);
		$table = $tables[$tablename];
		$fields = array();
		if(isset($table))
		{
			$fields = zotop::db()->table($tablename)->fields(true);
		}
		$field = $fields[$fieldname];
		if(!isset($field))
		{
			msg::error('字段不存在，请勿修改浏览器参数');
		}

		$positions = array();
		$positions[-1] = '位于表头';
		if( $fields )
		{
			foreach( $fields as $key=>$val )
			{
				$positions[$key] = '位于 '.$key.' 之后';
			}
		}
		$positions[0] = ' ';
			    
	   $page = new dialog();
	   $page->title = '编辑字段';
       $page->set('database',$database);
       $page->set('tables',$tables); 
       $page->set('field',$field);
       $page->set('positions',$positions);       
       $page->display();        
	}

	public function actionDelete($tablename, $fieldname)
	{
		$fields = zotop::db()->table($tablename)->fields();
		$field = $fields[$fieldname];

		if(!isset($field))
		{
			msg::error('参数错误，'.zotop::t('数据表{$tablename}中找不到字段{$fieldname}',array('tablename'=>$tablename,'fieldname'=>$fieldname)) );
		}

		$delete = zotop::db()->table($tablename)->field($fieldname)->drop();
		if(!$delete)
		{

		}
		msg::success('字段删除成功，正在刷新页面，请稍后……','reload');
	}
    
	public function actionPrimaryKey($tablename , $fieldname)
	{
		$result = zotop::db()->table($tablename)->primaryKey($fieldname);
		if($result)
		{
			msg::success('已经成功的将该字段设置成为主键',zotop::url('database/field/index',array('tablename'=>$tablename)));
		}
	}
	
	public function actionAddIndex($tablename , $fieldname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$fieldname]))
		{
			zotop::db()->table($tablename)->index($fieldname,'DROP');
		}
		$result = zotop::db()->table($tablename)->index($fieldname,'INDEX');
		if($result)
		{
			msg::success('操作成功，已经成功的索引该字段',zotop::url('database/field/index',array('tablename'=>$tablename)));
		}
	}
	
	public function actionDropIndex($tablename , $indexname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$indexname]))
		{
			$result = zotop::db()->table($tablename)->index($indexname,'DROP');
		}
		if($result)
		{
			msg::success('操作成功，已经成功的删除了索引',zotop::url('database/field/index',array('tablename'=>$tablename)));
		}
	}
		
	public function actionUnique($tablename , $fieldname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$fieldname]))
		{
			zotop::db()->table($tablename)->index($fieldname,'DROP');
		}
		$result = zotop::db()->table($tablename)->index($fieldname,'UNIQUE');
		if($result)
		{
			msg::success('操作成功，已经成功的将该字段设置为唯一索引',zotop::url('database/field/index',array('tablename'=>$tablename)));
		}
	}
	
	public function actionFulltext($tablename , $fieldname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$fieldname]))
		{
			zotop::db()->table($tablename)->index($fieldname,'DROP');
		}
		$result = zotop::db()->table($tablename)->index($fieldname,'FULLTEXT');
		if($result)
		{
			msg::success('操作成功，已经成功的将该字段设置为全文索引',zotop::url('database/field/index',array('tablename'=>$tablename)));
		}
	}
    
}
?>