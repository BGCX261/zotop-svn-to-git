$(function(){
	//图片对话框
	$("form input.upload-image").click(function(){
		var upload = {};
			upload.type = 'input'
			upload.field = $(this).prev('input').attr('name');
			upload.url = $(this).prev('input').attr('handle');			
		var dialog = zotop.upload.image.dialog(upload);		
	});
});
zotop.namespace('zotop.upload.image');
zotop.upload.image.dialog = function(upload){
	var _close = function(){
			$('input[name=' + upload.field + ']',dialog.opener.document).val('test.jpg').focus();
	}
	var dialog = zotop.dialog.open({
		id:'upload-image',
		width:600,
		height:200,
		type:'iframe',
		title:'上传图片',
		url:upload.url,
		args:{type:upload.type,field:upload.field},
		onClose:_close
	});	

	return dialog;
}
zotop.upload.image.insert = function(field,file){
	$('input[name='+ field +']').val(file).focus();
}