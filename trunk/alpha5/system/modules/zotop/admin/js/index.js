zotop.namespace('zotop.url.frame');
zotop.namespace('zotop.url.msg');


//禁止页面被包含
(function(){
	if(top!= self){top.location = self.location;}
	$("html").css("overflow","hidden");
})();
//页面加载及重设
$(function(){
	$(window).bind('load.frame',resize);
	$(window).bind('resize.frame',resize);
})
//记录iframe的当前URL
$(function(){
	$('#mainIframe').load(function(){
		$(this).show();
		zotop.cookie.set('mainUrl',zotop.frame.main().location.href);
	});
	$('#sideIframe').load(function(){		
		$(this).show();
		zotop.cookie.set('sideUrl',zotop.frame.side().location.href);
		$('#side-body-inner').hide();
		$('html').css('overflow','hidden');
		
	})
})
//给iframe赋值
$(function(){
	var mainUrl=zotop.cookie.get('mainUrl');
	if( mainUrl == null || mainUrl == 'about:blank' ){
		mainUrl = zotop.url.frame.main;
	}
	var sideUrl=zotop.cookie.get('sideUrl');
	if( sideUrl == null || sideUrl == 'about:blank' ){
		sideUrl = zotop.url.frame.side;
	}
	setTimeout(function(){
		top.go(mainUrl,sideUrl);
	},500);
});

$(function(){
	$("#navbar li:first").addClass('current');	
	$("#navbar li").click(function(){
		$(this).parent().find('li').removeClass('current');
		$(this).addClass('current');
	});
});
//side loading
$(function(){
	$('a[target=sideIframe]').click(function(){
		$('#side-body-inner').show();
	});
});

//定时获取未读短消息数目
(function(){
	setInterval(msg,10000);
})();

//页面重新计算
function resize(){
	var height;
	//主框架的高度
	height = $(window).height()-$('#header').height()-$('#footer').height();
	$('#body').height(height);
	$('#main').height(height);
	$('#mainIframe').height(height);
	$('#side').height(height);
	//设置side部分的高度	
	height = height-$('#side-header').height()-$('#side-footer').height();
	$('#side-body').height(height);
	$('#sideIframe').height(height);
	$('#page').css('visibility','visible');
};
//主页面加载
function go(mainUrl,sideUrl){
	if(sideUrl){
		zotop.frame.side().location.href = sideUrl;
	}
	if(mainUrl){
		zotop.frame.main().location.href = mainUrl;
	}
}
//获取未读消息数目
function msg(){
	var msgUrl = zotop.url.msg.unread;
	if(msgUrl){
		$.get(msgUrl,'',function(msg){
			if(parseInt(msg.num) > 0)
			{
				$('#msg-unread').show();
				$('#msg-unread-num').html(msg.num);
			}else{
				$('#msg-unread').hide();
			}
		},'json');
	}
};
//页面刷新，强制只刷新mainiframe
function refresh(event) {
	event = event ? event : window.event;
	keycode = event.keyCode ? event.keyCode : event.charCode;
	if(keycode == 116 || (event.ctrlKey && keycode==82)) {
		zotop.frame.main().location.reload();
		if(document.all) {
			event.keyCode = 0;
			event.returnValue = false;
		} else {
			event.cancelBubble = true;
			event.preventDefault();
		}
	}
}
