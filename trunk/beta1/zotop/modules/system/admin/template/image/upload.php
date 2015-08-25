<?php $this->header(); ?>
<script type="text/javascript">
//上传成功
zotop.form.callback = function(msg,$form){
	zotop.msg.show(msg);
	if( msg.type == 'success' ){
		$('#location').attr('src',function(index,value){
			return value;
		});
		$form.get(0).reset();
		$('input[name=globalid]').val(dialog.args.globalid);
		$('input[name=field]').val(dialog.args.field);
		$('select[name=folderid]').val($.cookie('select.name.folderid')||0);
		$(':submit',$form).removeClass("disabled").removeClass("loading").disabled(false);
		return true;
	}	
	return false;
};


//赋值
$(function(){
	if ( !dialog.args.globalid )
	{
		location.href = "<?php echo zotop::url('system/image/library');?>"
	}
	$('input[name=globalid]').val(dialog.args.globalid);
	$('input[name=field]').val(dialog.args.field);
	$('select[name=folderid]').change(function(){
		$.cookie('select.name.folderid',$(this).val());
	});
});

$(function(){
	var iframeUrl = "<?php echo zotop::url('system/image/location/__globalid__')?>";
		iframeUrl = iframeUrl.replace(/__globalid__/i, dialog.args.globalid);
	//处理iframe问题
	var $iframe = $('<iframe src="about:blank;" id="location" scrolling="auto" frameBorder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>');
		$iframe.attr({
			src : iframeUrl
		});
		$iframe.load(function(){
			$(this).prev('.loader').hide();
			$(this).show();				
		});
		$('#uploaded').append($iframe);
})

</script>
<style type="text/css">
	body.dialog{
		width:750px;
	}
</style>
<?php $this->navbar(); ?>
<div id="uploaded" style="margin:10px;text-align:center;height:180px;overflow:hidden;text-align:center;border:solid 1px #ebebeb;background:#fff;">
	<div class="loader" style="line-height:180px;_margin-top:100px;"><span class="zotop-icon zotop-icon-loading"></span>正在加载已上传文件</div>
</div >
<?php
	

	form::header(array('id'=>'upload','enctype'=>'multipart/form-data'));
	
	form::field(array(
		'type'=>'hidden',
		'name'=>'globalid',
		'label'=>'globalid',
		'value'=>'',
		'valid'=>'required:true',
	));
	
	form::field(array(
		'type'=>'hidden',
		'name'=>'field',
		'label'=>'field',
		'value'=>'',
		'valid'=>'required:true',
	));

	form::field(array(
	   'type'=>'file',
	   'label'=>zotop::t('图片选择'),
	   'name'=>'file',
	   'value'=>'',
	   'valid'=>"{required:true,accept:'$alowexts',messages:{required:'请选择图片',accept:'不支持该格式文件'}}",
	   'description'=>'支持格式：<b>'.$allowexts.'</b> , 图片大小 <= <b>'.format::byte($maxsize*1024).'</b>',
	));

	form::field(array(
		'type'=>'checkbox',
		'options'=>array('watermark'=>zotop::t('原图水印'),'thumb'=>zotop::t('生成缩略图')),		
		'name'=>'field',
		'label'=>zotop::t('图片设置'),
		'value'=>'',
		//'class'=>'block',
		//'valid'=>'required:true',
	));

	form::field(array(
	   'type'=>'select',
	   'options'=>array('0'=>'选择分类') + $categorys,
	   'label'=>zotop::t('图片分类'),
	   'name'=>'folderid',
	   'value'=>'0',
	   //'valid'=>'required:true',
	   'description'=>'<a href="'.zotop::url('system/folder/add').'" class="dialog">新建分类</a> <a href="'.zotop::url('system/folder').'" class="dialog">管理分类</a>',
	));

	form::field(array(
	   'type'=>'textarea',
	   'label'=>zotop::t('图片描述'),
	   'name'=>'description',
	   'value'=>'',
	   'style'=>'height:20px;',
	   'description'=>'请输入图片的描述信息',
	));



	form::buttons(
	   array('type'=>'submit','value'=>'上传图片'),
	   array(
		'type'=>'button',
		'value'=>zotop::t('关闭'),
		'class'=>'zotop-dialog-close'
	   )
	);

	form::footer();
?>
<?php $this->footer(); ?>