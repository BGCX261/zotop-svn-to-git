<?php
$this->header();
$this->navbar();
?>
<script>
	$(function(){
		$('#insert').click(function(){
			var url = $('input[name=file]').val();
			var download = $('input[name=download]:checked').val();
			if( download == 0 ){
				callback(url);
			}else{
				$('form.form').submit();
			}
		});
	});
</script>
<?php

	form::header(array('description'=>'<span class="zotop-icon zotop-icon-notice"></span>请输入一个图片地址，并插入该图片'));
	
	
	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('图片地址'),
	   'name'=>'file',
	   'value'=>'http://',
	   'description'=>'请输入一个完整的图片地址，如：http://www.zotop.com/logo.png',
	));

	form::field(array(
	   'type'=>'radio',
	   'options'=>array('1'=>'将图片从远程自动上传到服务器','0'=>'不自动获取，直接插入图片地址'),
	   'label'=>zotop::t('远程获取'),
	   'name'=>'download',
	   'value'=>'0',
	   'class'=>'block',
	   'description'=>'',
	));

	form::buttons(
	   array('type'=>'button','value'=>'插入图片','id'=>'insert'),
	   array(
		'type'=>'button',
		'value'=>zotop::t('取消'),
		'class'=>'zotop-dialog-close'
	   )
	);
	form::footer();

$this->footer();
?>