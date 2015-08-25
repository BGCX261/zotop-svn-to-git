<?php $this->header();?>
<div id="main">
<div id="main-inner">
<?php $this->top()?>
<?php $this->navbar()?>
<?php 
form::header(array('class'=>'list','action'=>zotop::url('database/table/action')));

	$column['select'] = html::checkbox(array('name'=>'table','class'=>'selectAll'));
	$column['name'] = '数据表名称';
	$column['size  w60'] = '大小';
	$column['Rows  w60'] = '记录数';
	$column['Engine  w60'] = '类型';
	$column['Collation  w100'] = '整理';
	$column['manage view w60'] = '浏览';
	$column['manage delete'] = '删除';

	table::header('list',$column);
	foreach($tables as $table)
	{
		$column = array();
		$column['select'] = html::checkbox(array('name'=>'table[]','value'=>$table['name'],'class'=>'select'));
		$column['name'] = '<a href="'.zotop::url('database/field/index',array('tablename'=>$table['name'])).'"><b>'.$table['name'].'</b></a><h5>'.$table['comment'].'</h5>';
		$column['size w60'] = (string)format::byte($table['size']);
		$column['Rows  w60'] = $table['rows'];
		$column['Engine  w60'] = $table['engine'];
		$column['collation  w100'] = $table['collation'];
		$column['manage view w60'] = '<a href="'.zotop::url('database/table/edit',array('tablename'=>$table['name'])).'" class="dialog">设置</a>';
		$column['manage delete'] = '<a href="'.zotop::url('database/table/delete',array('tablename'=>$table['name'])).'" class="confirm">删除</a>';
		table::row($column,'select');
	}
	table::footer();
	
	form::buttons(
		array('type'=>'select','name'=>'operation','style'=>'width:180px','options'=>array('optimize'=>'优化','delete'=>'删除'),'value'=>'check'),
		array('type'=>'submit','value'=>'执行操作')
	);
		
form::footer();
?>
<?php $this->bottom()?>
</div>
</div>
<div id="side">
<?php 
block::header('数据库基本信息');
	table::header();
	table::row(array('w60'=>'数据库主机','2'=>''.$database['hostname'].''));
	table::row(array('w60'=>'数据库名称','2'=>''.$database['database'].''));
	table::row(array('w60'=>'数据库版本','2'=>''.$database['version'].''));
	table::row(array('w60'=>'数据库大小','2'=>'<b>'.$database['size'].'</b> '));
	table::row(array('w60'=>'数据表个数','2'=>'<b>'.count($tables).'</b> 个'));
	table::footer();
block::footer();

/*
block::header('创建数据表');
	form::header(array('action'=>zotop::url('database/table/create'),'template'=>'div'));
	form::field(array(
		'type'=>'text',
		'name'=>'tablename',
		'label'=>'数据表名称',
		'style'=>'width:180px',
		'valid'=>'{required:true}',
		'description'=>'不含前缀,系统会自动加上前缀',
	));
	form::field(array(
		'type'=>'text',
		'name'=>'comment',
		'label'=>'数据表注释',
		'style'=>'width:180px',
		'valid'=>'{required:true}',
		'description'=>'',
	));	
	form::buttons(array('type'=>'submit','value'=>'创建'));
	form::footer();
block::footer();
*/
?>
</div>
<?php $this->footer();?>