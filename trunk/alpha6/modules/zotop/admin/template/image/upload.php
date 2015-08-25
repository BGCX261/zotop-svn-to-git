<?php $this->header(); ?>
<script>

zotop.form.callback = function(msg){
	if( msg.type == 'success' ){
		if ( msg.url ) {
			location.href = msg.url;
		}
		return true;
	}
	zotop.msg.show(msg);
	return false;
}
</script>
<?php $this->navbar(); ?>
<?php

	form::header(array('enctype'=>'multipart/form-data'));
	
	
	form::field(array(
	   'type'=>'file',
	   'label'=>zotop::t('图片选择'),
	   'name'=>'file',
	   'value'=>'',
	   'valid'=>"{required:true,accept:'bmp|jpg|jpeg|gif|png',messages:{required:'请选择图片',accept:'不支持该格式文件'}}",
	   'description'=>'支持格式：<b>bmp|jpg|jpeg|gif|png</b> , 图片大小 <= <b>'.zotop::config('upload.maxsize').' KB</b>',
	));


	/*
	form::field(array(
	   'type'=>'html',
	   'label'=>zotop::t('缩略图大小'),
	   'name'=>'thumb_width_height',
	   'value'=>'宽：'.field::get('text',array('name'=>'thumb_width','value'=>300,'valid'=>'required:true,number:true','style'=>'width:50px;')).' px 　×　高：'.field::get('text',array('name'=>'thumb_height','value'=>300,'valid'=>'required:true,number:true','style'=>'width:50px;')).' px',
	   'description'=>'',
	));
	*/

	form::field(array(
	   'type'=>'select',
	   'options'=>array('1'=>'图片','2'=>'头像'),
	   'label'=>zotop::t('图片分类'),
	   'name'=>'folderid',
	   'value'=>'0',
	   'description'=>'',
	));

	form::field(array(
	   'type'=>'textarea',
	   'label'=>zotop::t('图片描述'),
	   'name'=>'description',
	   'value'=>'',
	   
	   'description'=>'请输入图片的描述信息',
	));

	form::buttons(
	   array('type'=>'submit','value'=>'上传图片'),
	   array(
		'type'=>'button',
		'value'=>zotop::t('取消'),
		'class'=>'zotop-dialog-close'
	   )
	);

	form::footer();
?>
<?php $this->footer(); ?>