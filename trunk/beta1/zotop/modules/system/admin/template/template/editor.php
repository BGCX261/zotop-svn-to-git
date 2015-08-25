<?php $this->header();?>
<?php $this->top();?>
<?php $this->navbar();?>
<script>
	//设置按钮
	dialog.setTitle("<?php echo zotop::t('编辑').' &nbsp; <span>'.$file.'</span>';?>");

</script>
<style type="text/css">
body.dialog {width:750px;}
body.dialog textarea.textarea{width:100%;height:450px;padding:0px;border:0px;overflow:auto;white-space:nowrap;}
</style>
<?php 
form::header();

	form::field('<div>'.field::get(array(
		'type'=>'templateeditor,code,textarea',
		'name'=>'filecontent',
		'value'=>$filecontent,
		'height'=>'450px',
	)).'</div>');

	form::buttons(array('type'=>'submit','value'=>'保 存'),array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close'));

form::footer();
?>
<?php $this->bottom(); ?>
<?php $this->footer(); ?>