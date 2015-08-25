// JavaScript Document
// 对话框传递进来的参数
// dialog.id 表示的是当前对话框的id编号
// dialog.opener 表示的是开启对话框的页面的对象(window)
var dialog = frameElement ? frameElement.dialog : window.top.zotop.dialog.current;

// callback 为回调函数，默认调用dialog的回调函数，并传值，返回真时自动关闭对话框
var callback = function(v){
	var callback = dialog.callback(v);
	if (callback)
	{
		dialog.close();
	}
	
}

zotop.form.callback = function(msg){
	if( msg.type == 'success' ){
		if ( msg.url ) {
			dialog.opener.location.href = msg.url;
			zotop.msg.show(msg);
			dialog.close();		
			return true;
		}
		else
		{
			$('form :submit').removeClass("disabled").removeClass("loading").disabled(false);
		}

	}
	zotop.msg.show(msg);
	return false;
}

//防止焦点丢失
$(function(){
	if($.browser.msie){
		setTimeout(function(){
			try{
				$('input:enabled:visible:first').focus().blur();
			}catch(e){}
		}, 20);
	}
});

//关闭对话框
$(function(){
	$('.zotop-dialog-close').css('cursor','pointer').attr('title','关闭对话框').click(function(){
		dialog.close();
	});
});

//卸载时候显示loading
$(function(){
	$(window).unload(function(){
		dialog.self.find('.zotop-dialog-loading').show();
		dialog.self.find('.zotop-dialog-content iframe').hide();
	});
});

//清空buttons
$(function(){
	dialog.setButtons(null);
})