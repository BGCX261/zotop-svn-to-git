<?php
$this->header();
//$this->top();
//$this->navbar();

form::header();

	form::field(array(
		'type'=>'hidden',
		'name'=>'tablename',
		'label'=>'数据表名称',
		'value'=>$table['name'],
		'valid'=>'{required:true}',
		'description'=>'已经包含表名称前缀'
	));
	form::field(array(
		'type'=>'text',
		'name'=>'name',
		'label'=>'数据表名称',
		'value'=>$table['name'],
		'valid'=>'{required:true}'
	));
	form::field(array(
		'type'=>'textarea',
		'name'=>'comment',
		'label'=>'数据表注释',
		'value'=>$table['comment'],
		'valid'=>''
	));

	form::buttons(array('type'=>'submit'),array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close'));

form::footer();

//$this->bottom();
$this->footer();
?>