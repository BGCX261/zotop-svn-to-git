<?php
$this->header();
//$this->top();
//$this->navbar();

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
		array('type'=>'submit','value'=>'创建字段'),
		array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
	);

form::footer();
//$this->bottom();
$this->footer();
?>