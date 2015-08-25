<?php
class table_controller extends controller
{

	public function navbar()
	{
		return array(
			array('id'=>'tables','title'=>'数据表管理','href'=>url::build('database/table')),
			array('id'=>'edit','title'=>'数据表设置','href'=>''),
			//array('id'=>'edit','title'=>'SQL执行','href'=>url::build('database/table/sql'),'class'=>'dialog'),
			array('id'=>'backup','title'=>'备 份','href'=>url::build('database/table/backup')),
		);
	}

	public function onDefault()
    {

	   $db = zotop::db();
	   $host =  $db->config('hostname');
	   $dbName =  $db->config('database');
	   $dbVersion = $db->version(true);
	   $dbSize = $db->size();

       $tables = zotop::db()->tables(true);

       $header['title'] = '数据库管理：'.$dbName;

       page::header($header);

	   page::add('<div id="page" class="clearfix">');
	   page::add('<div id="side">');

		   block::header('数据库基本信息');
				table::header();
				table::row(array('w60'=>'数据库主机','2'=>''.$host.''));
				table::row(array('w60'=>'数据库名称','2'=>''.$dbName.''));
				table::row(array('w60'=>'数据库版本','2'=>''.$dbVersion.''));
				table::row(array('w60'=>'数据库大小','2'=>'<b>'.$dbSize.'</b> '));
				table::row(array('w60'=>'数据表个数','2'=>'<b>'.count($tables).'</b> 个'));
				table::footer();
		   block::footer();

		   block::header('创建数据表');
				form::header(array('action'=>zotop::url('database/table/create'),'template'=>'div'));
				form::field(array(
					'type'=>'text',
					'name'=>'tablename',
					'label'=>zotop::t('表名称'),
					'style'=>'width:180px',
					'valid'=>'{required:true}',
					'description'=>'不含前缀,系统会自动加上前缀',
				));
				form::buttons(array('type'=>'submit','value'=>'创建'));
				form::footer();
		   block::footer();

	   page::add('</div>');
	   page::add('<div id="main">');

	   page::top();
	   page::navbar($this->navbar(),'tables');

		//zotop::dump($tables);

				form::header(array('class'=>'list','action'=>zotop::url('database/table/action')));

					$column['select'] = html::checkbox(array('name'=>'table','class'=>'selectAll'));
					$column['name'] = '数据表名称';
					$column['size  w60'] = '大小';
					$column['Rows  w60'] = '记录数';
					$column['Engine  w60'] = '类型';
					$column['Collation  w100'] = '整理';
					$column['manage view w60'] = '浏览';
					$column['manage delete'] = '删除';

					page::add('<div style="height:400px;">');
					table::header('list',$column);
					foreach($tables as $table)
					{
						$column = array();
						$column['select'] = html::checkbox(array('name'=>'table[]','value'=>$table['name'],'class'=>'select'));
						$column['name'] = '<a href="'.url::build('database/field/default',array('tablename'=>$table['name'])).'"><b>'.$table['name'].'</b></a><h5>'.$table['comment'].'</h5>';
						$column['size w60'] = (string)format::byte($table['size']);
						$column['Rows  w60'] = $table['rows'];
						$column['Engine  w60'] = $table['engine'];
						$column['collation  w100'] = $table['collation'];
						$column['manage view w60'] = '<a href="'.url::build('database/table/edit',array('tablename'=>$table['name'])).'">设置</a>';
						$column['manage delete'] = '<a href="'.url::build('database/table/delete',array('tablename'=>$table['name'])).'" class="confirm">删除</a>';
						table::row($column,'select');
					}
					table::footer();
					page::add('</div>');
					form::buttons(
						array('type'=>'select','name'=>'operation','style'=>'width:180px','options'=>array('optimize'=>'优化','delete'=>'删除'),'value'=>'check'),
						array('type'=>'submit','value'=>'执行操作')
						);
				form::footer();

       page::bottom();
	   page::add('</div>');
	   page::add('</div>');
       page::footer();
    }

	public function onAction()
	{
		$tables = request::post('table');
		$operation = request::post('operation');
		switch(strtolower(optimize))
		{
			case 'optimize':
				foreach($tables as $table)
				{
					zotop::db()->table($table)->optimize();
				}
				break;
			case '':
				break;
		}
		msg::success('操作成功','<h2>操作执行成功</h2>','reload');
	}

	public function onCreate()
	{
		$tablename = request::post('tablename');
		if( empty($tablename) )
		{
			msg::error('验证错误','数据表名称不能为空');
		}
		$create = zotop::db()->table('#'.$tablename)->create();

		msg::success('操作成功','数据表创建成功','reload');
	}

	public function onEdit($tablename)
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

			msg::success('操作成功','<h2>数据表设置成功</h2>正在刷新页面，请稍后……',form::referer());
		}

		$tables = zotop::db()->tables(true);
		$table = $tables[$tablename];

		if(!isset($table))
		{
			msg::error('参数错误' , zotop::t('数据表{$tablename}不存在',array('tablename'=>$tablename)) );
		}

		$header['title'] = '数据库管理 <i>></i> 数据表设置：'.$tablename. ' ' ;

		page::header($header);
		page::top();
	    page::navbar($this->navbar(),'edit');


				form::header();

					form::field(array(
						'type'=>'hidden',
						'name'=>'tablename',
						'label'=>'数据表名称',
						'value'=>$table['name'],
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'name',
						'label'=>'数据表名称',
						'value'=>$table['name'],
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'comment',
						'label'=>'数据表注释',
						'value'=>$table['comment'],
						'valid'=>''
					));
				   form::buttons(
					   array('type'=>'submit'),array('type'=>'button','value'=>'返回前页','class'=>'back','onclick'=>'history.go(-1);')
				   );
				form::footer();

       page::bottom();
       page::footer();
	}

	public function onDelete($tablename)
	{
		$tables = zotop::db()->tables(true);
		$table = $tables[$tablename];

		if(!isset($table))
		{
			msg::error('参数错误' , zotop::t('数据表{$tablename}不存在',array('tablename'=>$tablename)) );
		}

		$delete = zotop::db()->table($tablename)->drop();

		msg::success('操作成功','<h2>数据表删除成功</h2>正在刷新页面，请稍后……','reload');
	}

	public function onBackup()
	{
		$header['title'] = '数据库管理 <i>></i> 数据表设置：'.$tablename. ' ' ;

		page::header($header);
		page::top();
		page::navbar($this->navbar(),'backup');


				form::header();


					form::field(array(
						'type'=>'select',
						'options'=>array('all'=>'全部数据','custom'=>'自定义备份'),
						'name'=>'type',
						'label'=>'备份类型',
						'value'=>'all',
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'length',
						'label'=>'分卷长度',
						'value'=>'2048',
						'description'=>'分卷备份时文件长度限制，单位：<b>KB</b>',
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'filename',
						'label'=>'备份文件名',
						'value'=>'',
						'valid'=>'{required:true}'
					));
				   form::buttons(
					   array('type'=>'submit'),array('type'=>'button','value'=>'返回前页','class'=>'back','onclick'=>'history.go(-1);')
				   );
				form::footer();

		page::bottom();
		page::footer();
	}

}
?>