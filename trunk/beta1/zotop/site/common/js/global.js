//修正ie缓存问题
$(function(){   
	if($.browser.msie && $.browser.version == 6){
		try {
			document.execCommand("BackgroundImageCache",false,true);
		} catch(e) {};
	}		
});

//常见的绑定
$(function(){
	$('a.dialog,li.dialog,span.dialog').dialog();
	$('a.confirm').confirm();
})

//列表颜色及选择项
$(function(){
	$('table.list').each(function(){
		$(this).find('.title th:first').addClass('first');
	});
	$('ul.list').each(function(){
		$(this).find('li:last').addClass('last');
	});	
});	

$(function(){
	$("div.tree").treeview({
		animated: "fast",
		persist: "location",		
		unique: true,
		collapsed: true
	});
});
