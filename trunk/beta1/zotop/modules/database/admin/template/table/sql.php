<?php
$this->header();
//$this->top();
//$this->navbar();

form::header(array('icon'=>'notice','title'=>'注意：','description'=>'SQL语句一旦执行，将无法逆转,请确保执行的SQL语句安全','template'=>'div'));

	form::field(array(
		'type'=>'textarea',
		'name'=>'sql',
		'label'=>'请输入SQL语句并执行',
		'value'=>$table['comment'],
		'valid'=>'{required:true}',
		'style'=>'width:98%;height:150px;',
		'description'=>'一旦执行将无法撤销，请确保执行的SQL语句安全',
	));

	form::buttons(array('type'=>'submit','value'=>'执行sql语句'),array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close'));

form::footer();

//$this->bottom();
$this->footer();
?>