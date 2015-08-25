<?php
$this->header();
$this->top();
$this->navbar();
?>
<style type="text/css">
table.field td.field-side{width:80px;}
</style>
<?php
form::header();
	

	form::field(array(
		'type'=>'hidden',
		'name'=>'id',
		'label'=>'编号',
		'value'=>$data['id'],
		'description'=>''
	));	
	form::field(array(
		'type'=>'hidden',
		'name'=>'categoryid',
		'label'=>'栏目编号',
		'value'=>$data['categoryid'],
		'description'=>''
	));	
	form::field(array(
		'type'=>'hidden',
		'name'=>'modelid',
		'label'=>'模型编号',
		'value'=>$data['modelid'],
		'description'=>''
	));	


	foreach($fields as $field)
	{
		form::field($field);
	}

	form::field(array(
		'type'=>'template',
		'name'=>'template',
		'label'=>'模板设置',
		'value'=>$data['template'],
		'valid'=>'',
	));

	form::field(array(
		'type'=>'radio',
		'options'=>array(-50=>zotop::t('草稿'),0=>zotop::t('等待审核'),1=>zotop::t('通过审核'),100=>zotop::t('发布')),
		'name'=>'status',
		'label'=>'默认状态',
		'value'=>$data['status'],
		'valid'=>'',
	));

	form::field(array(
		'type'=>'radio',
		'options'=>array(0=>zotop::t('允许'),-1=>zotop::t('不允许')),
		'name'=>'comment',
		'label'=>'评论',
		'value'=>((int)$data['comment'] >= 0 ? 0 : -1),
		'valid'=>'',
	));

	form::field(array(
		'type'=>'time',
		'name'=>'createtime',
		'label'=>'发布时间',
		'value'=>$data['createtime'],
		'valid'=>'',
	));

	form::buttons(
		array('type'=>'submit','id'=>'save','value'=>zotop::t('发布')),
		array('type'=>'button','id'=>'savedraft','value'=>zotop::t('保存')),
		array('type'=>'back')	
	);
	
	form::footer();

$this->bottom();
$this->footer();
?>