<?php $this->header(); ?>
<script type="text/javascript">
$(function(){
	$('#image-preview').zoomImage(500,300);

	zotop.form.callback = function(msg,$form){
		zotop.msg.show(msg);
		if( msg.type == 'success' ){
			dialog.opener.location.reload();
			dialog.close();
			return true;
		}	
		return false;
	}
})
</script>
<div id="image-preview" style="margin:10px;text-align:center;height:300px;overflow:hidden;">
<?php echo html::image($image['path'],array('style'=>'display:none'))?>
</div>
<?
	form::header();


	
	form::field(array(
	   'type'=>'select',
	   'options'=>$categorys,
	   'label'=>zotop::t('图片分类'),
	   'name'=>'folderid',
	   'value'=>$image['folderid'],
	   'description'=>'',
	));	

	form::field(array(
		'type'=>'text',
		'name'=>'name',
		'label'=>zotop::t('图片名称'),
		'value'=>$image['name'],
		'valid'=>'required:true',
	));
	
	form::field(array(
		'type'=>'textarea',
		'name'=>'description',
		'label'=>zotop::t('图片描述'),
		'value'=>$image['description'],
		'valid'=>'',
	));


	form::buttons(
		array('type'=>'submit','id'=>'submit','value'=>zotop::t('保存')),
		array('type'=>'button','id'=>'close','value'=>zotop::t('关闭'),'class'=>'zotop-dialog-close')
	);

	form::footer();
?>
<?php $this->footer(); ?>