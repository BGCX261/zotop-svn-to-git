<?php
$this->header();
//$this->top();
//$this->navbar();

form::header(array('action'=>zotop::url('database/table/create')));

	form::field(array(
		'type'=>'text',
		'name'=>'tablename',
		'label'=>'数据表名称',
		'valid'=>'{required:true}',
		'description'=>'不含前缀,系统会自动加上前缀',
	));
	form::field(array(
		'type'=>'textarea',
		'name'=>'comment',
		'label'=>'数据表注释',
		'valid'=>'{required:true}',
		'description'=>'',
	));	

	form::buttons(array('type'=>'submit','value'=>'创建'),array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close'));

form::footer();

//$this->bottom();
$this->footer();
?>