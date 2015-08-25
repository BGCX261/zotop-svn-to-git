<?php $this->header();?>
<?php $this->top();?>
<?php $this->navbar();?>
<script type="text/javascript">
zotop.form.callback = function(msg,$form){
	zotop.msg.show(msg);
	if( msg.type == 'success' ){
		dialog.opener.location.reload();
		dialog.close();
		return true;
	}	
	return false;
};

$(function(){
	$('#filename').html(function(){
		return $('input[name=name]').val();
	});
	$('input[name=name]').change(function(){
		var name = $('input[name=name]').val();
		$('#filename').html(name);		
	});
})
</script>
<style type="text/css">
body.dialog {width:530px;}
body.dialog .form-body{padding:10px 0px 0px 0px;}
body.dialog table.field{}
body.dialog table.field td.field-side{width:80px;}
</style>
<?php 
form::header(array('icon'=>'newfile','title'=>zotop::t('新建模板'),'description'=>zotop::t('在 <b>{$dir}</b> 下新建模板文件 <b id="filename" class="w120 textflow"></b>',array('dir'=>empty($dir)?zotop::t('根目录'):$dir))));
	
	form::field(array(
		'type'=>'text',
		'name'=>'name',
		'label'=>zotop::t('文件名称'),
		'value'=>file::name($file),
		'valid'=>'required:true',
		'description'=>zotop::t('名称不能包含中文以及字符:<b>\/:*?"<>|</b>'),
	));
	form::field(array(
		'type'=>'text',
		'name'=>'title',
		'label'=>zotop::t('文件标题'),
		'value'=>'',
		'valid'=>'required:true',
		'description'=>'',
	));
	form::field(array(
		'type'=>'textarea',
		'name'=>'description',
		'label'=>zotop::t('文件描述'),
		'value'=>'',
		'description'=>'',
	));

	form::buttons(array('type'=>'submit','value'=>zotop::t('保存')),array('type'=>'button','value'=>zotop::t('关闭'),'class'=>'zotop-dialog-close'));

form::footer();
?>
<?php $this->bottom(); ?>
<?php $this->footer(); ?>