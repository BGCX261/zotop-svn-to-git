$(function(){
	$("#UploadImage").click(function(){
		var image = $("#image").val();
		//赋值
		dialog.opener.zotop.upload.insert(dialog.args.type,dialog.args.name,image);
		//关闭对话框
		zotop.dialog.hide(dialog.id);
	});
});