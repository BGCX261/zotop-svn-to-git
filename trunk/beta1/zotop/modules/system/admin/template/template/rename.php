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
}
</script>
<style type="text/css">
body.dialog {width:530px;}
body.dialog .form-body{padding:30px 0px;}
body.dialog table.field{background:none;}
body.dialog table.field td.field-side{width:80px;}
</style>
<?php 
form::header(array('icon'=>'newfile','title'=>zotop::t('重命名模板'),'description'=>zotop::t('请输入一个新的文件名称，名称不能包含中文以及字符:<b>\/:*?"<>|</b>')));

	form::field(array(
		'type'=>'hidden',
		'name'=>'name',
		'label'=>zotop::t('原名称'),
		'value'=>file::name($file),
		'valid'=>'required:true',
	));

	form::field(array(
		'type'=>'text',
		'name'=>'newname',
		'label'=>zotop::t('新名称'),
		'value'=>file::name($file),
		'valid'=>'required:true',
		'description'=>zotop::t('名称不能包含中文以及字符:<b>\/:*?"<>|</b>'),
	));

	form::buttons(array('type'=>'submit','value'=>zotop::t('保存')),array('type'=>'button','value'=>zotop::t('关闭'),'class'=>'zotop-dialog-close'));

form::footer();
?>
<?php $this->bottom(); ?>
<?php $this->footer(); ?>