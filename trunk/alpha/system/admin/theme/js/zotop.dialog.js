// JavaScript Document
// 对话框传递进来的参数
// dialog.id 表示的是当前对话框的id编号
// dialog.opener 表示的是开启对话框的页面的对象(window)
var dialog = frameElement.dialog;
	
$(function(){
	//设置对话框的关闭
	$('.zotop-dialog-close').click(function(){
		zotop.dialog.hide(dialog.id,dialog.callback);
	});
	//设置焦点
	setTimeout("$('input:first').focus();",100);
});





