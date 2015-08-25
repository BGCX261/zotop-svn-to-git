$(function(){
	$('form.ajax').submit(function(){
		var $submit = $(":submit",this);
			$submit.blur().addClass("loading").addClass("disabled").get(0).disabled=true;
		$(this).ajaxSubmit({
			success:function(msg){
				zotop.msg.success('提交成功','<h1>操作成功，正在刷新页面，请稍后</h1>',function(id){
					location.href = location.href;
				});
				$submit.removeClass("disabled").removeClass("loading").get(0).disabled=false;
			}
		});		
		return false;
	});	
	
	//图片对话框
	$("form .upload-image").click(function(){
		var $this = $(this);
		var fieldtype = 'input';
		var fieldname = $(this).prev('input').attr('name');
		var $dialog =zotop.upload.image(fieldtype,fieldname);		
	});
});

zotop.namespace('zotop.upload');
zotop.upload.image = function(fieldtype,fieldname){
	var _callback = function(){
			$('input[name='+fieldname+']').focus();
	}
	var upload = zotop.dialog.show({
		id:'upload-image',
		width:600,
		height:200,
		type:'iframe',
		title:'上传图片',
		url:zotop.url.build('/system/upload/image'),
		args:{type:fieldtype,name:fieldname},
		callback:_callback
	});	

	return upload;
}
zotop.upload.insert = function(fieldtype,fieldname,file)
{
	$('input[name='+fieldname+']').val(file).focus();
}