<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<?php

form::header();

	box::header('基本信息');

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('模型名称'),
	   'name'=>'name',
	   'value'=>$data['name'],
	   'valid'=>'required:true',
	   'description'=>zotop::t('模型名称为模型的标识名称，如 <b>新闻模型</b>'),
	));



	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('模型数据表'),
	   'name'=>'id',
	   'value'=>$data['tablename'],
	   'valid'=>'required:true,alphanumeric:true,maxlength:32',
	   'description'=>zotop::t('不含数据表的前缀，如 <b>新闻模型</b> 的数据表可以是：<b>news</b>。只允许英文字符、数字或者下划线'),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('模型标题'),
	   'name'=>'title',
	   'value'=>$data['title'],
	   'valid'=>'required:true',
	   'description'=>zotop::t('显示标题，如 <b>新闻模型</b> 的显示标题可以是：<b>新闻</b>'),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('模型单位'),
	   'name'=>'unit',
	   'value'=>$data['unit'],
	   'valid'=>'required:true',
	   'description'=>zotop::t('模型单位，如 <b>篇</b>、<b>个</b>、 <b>条</b> 等'),
	));

	form::field(array(
	   'type'=>'textarea',
	   'label'=>zotop::t('说明'),
	   'name'=>'description',
	   'value'=>$data['description'],
	   'valid'=>'',
	   'description'=>zotop::t(''),
	));
	
	box::footer();

	box::header('模板设置');
	
	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('首页模板'),
	   'name'=>'settings[template_index]',
	   'value'=>$data['settings']['template_index'],
	  // 'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('列表页面模板'),
	   'name'=>'settings[template_list]',
	   'value'=>$data['settings']['template_list'],
	  // 'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('详细页面模板'),
	   'name'=>'settings[template_detail]',
	   'value'=>$data['settings']['template_detail'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('打印页面模板'),
	   'name'=>'settings[template_print]',
	   'value'=>$data['settings']['template_print'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	box::footer();
	


	form::buttons(
		array('type'=>'submit','value'=>'保存'),
		array('type'=>'back')
	);
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>