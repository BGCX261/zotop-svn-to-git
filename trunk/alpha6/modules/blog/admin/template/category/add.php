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
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('分类标题'),
	   'name'=>'title',
	   'value'=>$data['title'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));
	form::field(array(
	   'type'=>'image',
	   'label'=>zotop::t('分类图片'),
	   'name'=>'image',
	   'value'=>$data['image'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
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
	   'valid'=>'required:true',
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