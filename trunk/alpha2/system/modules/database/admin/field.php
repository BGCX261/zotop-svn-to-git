<?php
class field_controller extends controller
{

    public function navbar($tablename)
	{
		return array(
		  array('id'=>'tables','title'=>'数据表管理','href'=>url::build('database/table')),
		  array('id'=>'fields','title'=>'数据表结构','href'=>url::build('database/field/default',array('tablename'=>$tablename))),
		  array('id'=>'create','title'=>'新建字段','href'=>url::build('database/field/create',array('tablename'=>$tablename))),
		  array('id'=>'edit','title'=>'字段修改','href'=>''),
	   );
	}

	public function onDefault($tablename)
    {

		$tables = zotop::db()->tables(true);
		$table = $tables[$tablename];
		$fields = array();
		if(isset($table))
		{
			$fields = zotop::db()->table($tablename)->fields(true);
		}
		$indexes = zotop::db()->table($tablename)->index();


		$header['title'] = '数据库管理 <i>></i> 表：'.$tablename. ' <h6>'.$table['comment'].'</h6>' ;

		page::header($header);

		page::add('<div id="page" class="clearfix">');
		page::add('<div id="side">');

		   block::header('数据表信息');
				table::header();
				table::row(array('w60 bold'=>'名称','2'=>''.$table['name'].''));
				table::row(array('w60 bold'=>'大小','2'=>''.format::byte($table['size']).''));
				table::row(array('w60 bold'=>'记录数','2'=>'<b>'.$table['rows'].'</b> '));
				table::row(array('w60 bold'=>'整理','2'=>''.$table['collation'].''));
				table::row(array('w60 bold'=>'创建时间','2'=>''.$table['createtime'].''));
				table::row(array('w60 bold'=>'更新时间','2'=>''.$table['updatetime'].''));
				//table::row(array('w60 bold'=>'注释','2'=>''.$table['comment'].''));
				table::footer();
		   block::footer();

		   block::header('索引信息');
				$column = array();
				$column['i w10'] = '';
				$column['field'] = '字段';
				$column['type w30'] = '类型';
				$column['manage dropindex'] = '删除';
				table::header('list',$column);
				foreach($indexes as $index)
				{
					$column = array();
					$column['i w10 center'] = '>';
					$column['field'] = '<b>'.$index['field'].'</b>';
					$column['type w30'] = $index['type'];
					$column['manage dropindex'] = '<a href="'.url::build('database/field/dropindex',array('table'=>$tablename,'index'=>$index['name'])).'" class="confirm">删除</a>';
					table::row($column,'select');
				}
				table::footer();
		   block::footer();


		page::add('</div>');
		page::add('<div id="main">');

		page::top();
	    page::navbar($this->navbar($tablename),'fields');

				form::header(array('class'=>'list'));
					$column = array();
					$column['select'] = html::checkbox(array('name'=>'id','class'=>'selectAll'));
					//$column['key w30 center'] = '索引';
					$column['name'] = '字段名称';
					$column['type w150'] = '字段类型';
					//$column['null w50'] = '空值';
					//$column['default w100'] = '默认值';
					//$column['comment'] = '注释';
					$column['manage pri'] = '主键';
					$column['manage index'] = '索引';
					$column['manage unique'] = '唯一';
					$column['manage fulltext'] = '全文';
					$column['manage edit'] = '修改';
					$column['manage delete'] = '删除';


					table::header('list',$column);
					foreach($fields as $field)
					{
						$column = array();
						$column['select'] = html::checkbox(array('name'=>'id[]','class'=>'select'));
						//$column['key w30 center'] = '<span class="'.$field['key'].'">'.$field['key'].'</span>';
						$column['name'] = '<a href="'.url::build('database/field/edit',array('table'=>$tablename,'field'=>$field['name'])).'" title="注释：'.$field['comment'].'&#13;默认：'.$field['default'].'&#13;空值：'.$field['null'].'&#13;整理：'.$field['collation'].'"><b class="'.$field['key'].'">'.$field['name'].'</b></a><h5>'.$field['comment'].'</h5>';
						$column['type w150'] = $field['type'];
						//$column['null w50'] = $field['null'];
						//$column['default w100'] = $field['default'];
						//$column['comment'] = $field['comment'];
						$column['manage pri'] = '<a href="'.url::build('database/field/primary',array('table'=>$tablename,'field'=>$field['name'])).'" class="confirm {content:\'<h2>确定要将该字段设置为主键？</h2>\'}">主键</a>';
						$column['manage index'] = '<a href="'.url::build('database/field/index',array('table'=>$tablename,'field'=>$field['name'])).'" class="confirm {content:\'<h2>确定要索引该字段？</h2>\'}">索引</a>';
						$column['manage unique'] = '<a href="'.url::build('database/field/unique',array('table'=>$tablename,'field'=>$field['name'])).'" class="confirm {content:\'<h2>确定要将该字段设置为唯一？</h2>\'}">唯一</a>';

						if( stripos((string)$field['type'],'varchar')!==false || stripos((string)$field['type'],'text')!==false )
						{
							$column['manage fulltext'] = '<a href="'.url::build('database/field/fulltext',array('table'=>$tablename,'field'=>$field['name'])).'" class="confirm {content:\'<h2>确定要将该字段设置为全文索引？</h2>\'}">全文</a>';
						}
						else
						{
							$column['manage fulltext'] = '<a class="disabled">全文</a>';
						}
						$column['manage edit'] = '<a href="'.url::build('database/field/edit',array('table'=>$tablename,'field'=>$field['name'])).'">修改</a>';
						$column['manage delete'] = '<a href="'.url::build('database/field/delete',array('table'=>$tablename,'field'=>$field['name'])).'" class="confirm">删除</a>';
						table::row($column,'select');
					}
					table::footer();


					form::buttons(
						array('type'=>'submit','value'=>'浏览选中项')
						);
				form::footer();

       page::bottom();
	   page::add('</div>');
	   page::add('</div>');
       page::footer();
	}

	public function onPrimary($tablename , $fieldname)
	{
		$result = zotop::db()->table($tablename)->primary($fieldname);
		if($result)
		{
			msg::success('操作成功','已经成功的将该字段设置成为主键','reload');
		}
	}
	public function onIndex($tablename , $fieldname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$fieldname]))
		{
			zotop::db()->table($tablename)->index($fieldname,'DROP');
		}
		$result = zotop::db()->table($tablename)->index($fieldname,'INDEX');
		if($result)
		{
			msg::success('操作成功','已经成功的索引该字段','reload');
		}
	}
	public function onUnique($tablename , $fieldname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$fieldname]))
		{
			zotop::db()->table($tablename)->index($fieldname,'DROP');
		}
		$result = zotop::db()->table($tablename)->index($fieldname,'UNIQUE');
		if($result)
		{
			msg::success('操作成功','已经成功的将该字段设置为唯一索引','reload');
		}
	}
	public function onFulltext($tablename , $fieldname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$fieldname]))
		{
			zotop::db()->table($tablename)->index($fieldname,'DROP');
		}
		$result = zotop::db()->table($tablename)->index($fieldname,'FULLTEXT');
		if($result)
		{
			msg::success('操作成功','已经成功的将该字段设置为全文索引','reload');
		}
	}
	public function onDropindex($tablename , $indexname)
	{
		$indexes = zotop::db()->table($tablename)->index();
		if(isset($indexes[$indexname]))
		{
			$result = zotop::db()->table($tablename)->index($indexname,'DROP');
		}
		if($result)
		{
			msg::success('操作成功','已经成功的删除了索引','reload');
		}
	}
	public function onCreate($tablename, $position = 0)
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


			$result = zotop::db()->table($tablename)->add($field);

			if($result)
			{
				msg::success('创建成功','字段创建成功',form::referer());
			}

		}

		$tables = zotop::db()->tables(true);
		$table = $tables[$tablename];
		$fields = array();
		if(isset($table))
		{
			$fields = zotop::db()->table($tablename)->fields(true);
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

		$header['title'] = '数据库管理 <i>></i> 表：<a href="'.zotop::url('database/field/default',array('table'=>$tablename)).'">'.$tablename. '</a>  <i>></i> 字段修改' ;

		page::header($header);
		page::top();
	    page::navbar($this->navbar($tablename));


				form::header();

					form::field(array(
						'type'=>'text',
						'name'=>'name',
						'label'=>'字段名称',
						'value'=>$field['name'],
						'valid'=>'{required:true}',
						'description'=>'请输入字段的名称，3到32位，请勿使用特殊字符'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'type',
						'label'=>'字段类型',
						'value'=>$field['type'],
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'len',
						'label'=>'长度/值',
						'value'=>$field['length'],
						'valid'=>'{number:true,min:1}',
						'description'=>'请输入字段的长度,如果字段无须定义长度，请保持空值'
					));
					form::field(array(
						'type'=>'hidden',
						'name'=>'collation',
						'label'=>'整理',
						'value'=>$field['collation'],
						'valid'=>'',
						'description'=>'默认使用 <b>utf8_general_ci</b>： Unicode (多语言), 不区分大小写'
					));
					form::field(array(
						'type'=>'select',
						'options'=>array(''=>' ','UNSIGNED'=>'UNSIGNED','UNSIGNED ZEROFILL'=>'UNSIGNED ZEROFILL','ON UPDATE CURRENT_TIMESTAMP'=>'ON UPDATE CURRENT_TIMESTAMP'),
						'name'=>'attribute',
						'label'=>'属性',
						'value'=>$field['attribute'],
						'valid'=>''
					));
					form::field(array(
						'type'=>'select',
						'options'=>array(''=>'NULL','NOT NULL'=>'NOT NULL'),
						'name'=>'null',
						'label'=>'null',
						'value'=>$field['null'],
						'valid'=>''
					));
					form::field(array(
						'type'=>'text',
						'name'=>'default',
						'label'=>'默认值',
						'value'=>$field['default'],
						'valid'=>'',
						'description'=>'如果需要可以为字段设置一个默认值'
					));
					form::field(array(
						'type'=>'select',
						'options'=>array(''=>'','AUTO_INCREMENT'=>'AUTO_INCREMENT'),
						'name'=>'extra',
						'label'=>'额外',
						'value'=>$field['extra'],
						'valid'=>'',
						'description'=>'设置为自动增加:<b>AUTO_INCREMENT</b>时，该字段必须为数字类型'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'comment',
						'label'=>'注释',
						'value'=>$field['comment'],
						'valid'=>''
					));
					form::field(array(
						'type'=>'select',
						'name'=>'position',
						'options'=>$positions,
						'label'=>zotop::t('字段位置'),
						'value'=>$position,
						'description'=>'',
					));
				   form::buttons(
					   array('type'=>'submit'),array('type'=>'reset')
				   );
				form::footer();

       page::bottom();
       page::footer();
	}

	public function onEdit($tablename,$fieldname)
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

			$result = zotop::db()->table($tablename)->field($fieldname)->rename($field['name']);

			$result = zotop::db()->table($tablename)->modify($field);

			if($result)
			{
				msg::success('修改成功','<h2>字段修改成功</h2>',form::referer());
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
			zotop::error(-10,'字段不存在，请勿修改浏览器参数');
		}
		if(!isset($field['length']))
		{
			if (strpos($field['type'], '(')) {
				$field['length'] = chop(substr($field['type'], (strpos($field['type'], '(') + 1), (strpos($field['type'], ')') - strpos($field['type'], '(') - 1)));
				$field['type'] = chop(substr($field['type'], 0, strpos($field['type'], '(')));
			} else {
				$field['length'] = '';
			}
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

		$header['title'] = '<a href="'.zotop::url('system/database').'">数据库管理</a> <i>></i> <a href="'.zotop::url('system/database/fields/',array('table'=>$tablename)).'">表：'.$tablename. '</a>  <i>></i> 字段修改' ;

		page::header($header);
		page::top();
	    page::navbar($this->navbar($tablename),'edit');


				form::header();
					form::field(array(
						'type'=>'hidden',
						'name'=>'fieldname',
						'label'=>'字段名称',
						'value'=>$field['name'],
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'name',
						'label'=>'字段名称',
						'value'=>$field['name'],
						'valid'=>'{required:true}',
						'description'=>'请输入字段的名称，3到32位，请勿使用特殊字符'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'type',
						'label'=>'字段类型',
						'value'=>$field['type'],
						'valid'=>'{required:true}'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'len',
						'label'=>'长度/值',
						'value'=>$field['length'],
						'valid'=>'{number:true,min:1}',
						'description'=>'请输入字段的长度,如果字段无须定义长度，请保持空值'
					));
					form::field(array(
						'type'=>'hidden',
						'name'=>'collation',
						'label'=>'整理',
						'value'=>$field['collation'],
						'valid'=>'',
						'description'=>'默认使用 <b>utf8_general_ci</b>： Unicode (多语言), 不区分大小写'
					));
					form::field(array(
						'type'=>'select',
						'options'=>array(''=>' ','UNSIGNED'=>'UNSIGNED','UNSIGNED ZEROFILL'=>'UNSIGNED ZEROFILL','ON UPDATE CURRENT_TIMESTAMP'=>'ON UPDATE CURRENT_TIMESTAMP'),
						'name'=>'attribute',
						'label'=>'属性',
						'value'=>$field['attribute'],
						'valid'=>''
					));
					form::field(array(
						'type'=>'select',
						'options'=>array(''=>'NULL','NOT NULL'=>'NOT NULL'),
						'name'=>'null',
						'label'=>'null',
						'value'=>$field['null'],
						'valid'=>''
					));
					form::field(array(
						'type'=>'text',
						'name'=>'default',
						'label'=>'默认值',
						'value'=>$field['default'],
						'valid'=>'',
						'description'=>'如果需要可以为字段设置一个默认值'
					));
					form::field(array(
						'type'=>'select',
						'options'=>array(''=>'','AUTO_INCREMENT'=>'AUTO_INCREMENT'),
						'name'=>'extra',
						'label'=>'额外',
						'value'=>$field['extra'],
						'valid'=>'',
						'description'=>'设置为自动增加:<b>AUTO_INCREMENT</b>时，该字段必须为数字类型'
					));
					form::field(array(
						'type'=>'text',
						'name'=>'comment',
						'label'=>'注释',
						'value'=>$field['comment'],
						'valid'=>''
					));
					form::field(array(
						'type'=>'select',
						'name'=>'position',
						'options'=>$positions,
						'label'=>zotop::t('字段位置'),
						'value'=>$position,
						'description'=>'',
					));
				   form::buttons(
					   array('type'=>'submit'),array('type'=>'reset')
				   );

				form::footer();

       page::bottom();
       page::footer();
	}



	public function onDelete($tablename, $fieldname)
	{
		$fields = zotop::db()->table($tablename)->fields();
		$field = $fields[$fieldname];

		if(!isset($field))
		{
			msg::error('参数错误' , zotop::t('数据表{$tablename}中找不到字段{$fieldname}',array('tablename'=>$tablename,'fieldname'=>$fieldname)) );
		}

		$delete = zotop::db()->table($tablename)->field($fieldname)->drop();
		if(!$delete)
		{

		}
		msg::success('操作成功','<h2>字段删除成功</h2>正在刷新页面，请稍后……','reload');
	}
}
?>