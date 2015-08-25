<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<script type="text/javascript">

</script>
<?php

form::header();

	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('分类编号'),
	   'name'=>'id',
	   'value'=>$data['id'],
	   'valid'=>'',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('分类名称'),
	   'name'=>'title',
	   'value'=>$data['title'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));
	form::field(array(
	   'type'=>'select',
	   'options'=>array('0'=>'根目录')+$data['parentid_options'],
	   'label'=>zotop::t('上级分类'),
	   'name'=>'parentid',
	   'value'=>$data['parentid'],
	));	
	form::field(array(
	   'type'=>'textarea',
	   'label'=>zotop::t('分类说明'),
	   'name'=>'description',
	   'value'=>$data['description'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));
	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('分类排序'),
	   'name'=>'order',
	   'value'=>$data['order'],
	   'description'=>zotop::t(''),
	));

	form::buttons(
		array('type'=>'submit','value'=>'保存'),
		array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
	);
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>