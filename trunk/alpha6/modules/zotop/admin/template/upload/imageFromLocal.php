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

$(function(){
	$('input[name=globalid]').val(dialog.args.globalid);
	$('input[name=field]').val(dialog.args.field);
});

</script>
<?php $this->navbar(); ?>
<?php

	form::header(array('enctype'=>'multipart/form-data','description'=>'<span class="zotop-icon zotop-icon-notice"></span>请从您的电脑中选择要上传的图片并设置图片标签后上传'));
	
	
	form::field(array(
	   'type'=>'file',
	   'label'=>zotop::t('图片选择'),
	   'name'=>'file',
	   'value'=>'',
	   'description'=>'请从您的电脑中选择要上传的图片',
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('图片描述'),
	   'name'=>'description',
	   'value'=>'',
	   'description'=>'请输入图片的描述信息',
	));

	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('唯一编号'),
	   'name'=>'globalid',
	   'value'=>'',
	   'description'=>'',
	));

	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('字段名称'),
	   'name'=>'field',
	   'value'=>'',
	   'description'=>'字段名称',
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