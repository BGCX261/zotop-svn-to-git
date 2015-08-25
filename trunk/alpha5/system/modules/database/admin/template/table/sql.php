<?php
$this->header();
//$this->top();
//$this->navbar();

form::header(array('template'=>'div'));

	form::field(array(
		'type'=>'textarea',
		'name'=>'sql',
		'label'=>'请输入SQL语句并执行',
		'value'=>$table['comment'],
		//'valid'=>'{required:true}',
		'style'=>'width:98%;height:150px;',
		'description'=>'一旦执行将无法撤销，请确保执行的SQL语句安全',
	));

form::footer(array(array('type'=>'submit','value'=>'执行sql语句'),array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')));

//$this->bottom();
$this->footer();
?>