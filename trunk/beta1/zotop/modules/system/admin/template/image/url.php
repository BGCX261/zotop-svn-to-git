<?php
$this->header();
$this->navbar();
?>
<script type="text/javascript">
	//图片预览
	function preview(image){
		if( image.length > 12 ){
			$('#image-preview').html('<img src="'+image+'">');
			$('#image-preview').zoomImage(500,200,true);
		}	
	}
	//插入
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

	//预览
	$(function(){
		preview($('#file').val());
	});

	$(function(){
		$('#file').change(function(){
			preview($(this).val());
		});
	});
</script>
<div id="image-preview" style="margin:10px;text-align:center;height:260px;overflow:hidden;text-align:center;border:solid 1px #ebebeb;background:#fff;">
	<div class="loader" style="line-height:260px;_margin-top:130px;">预览图片请先输入图片URL地址</div>
</div >
<?php

	form::header();
	
	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('图片地址'),
	   'name'=>'file',
	   'value'=> strtolower(substr($image,0,7)) == 'http://' ? $image : 'http://',
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