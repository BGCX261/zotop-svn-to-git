if(top!= self){top.location = self.location;}

zotop.namespace('zotop.url.frame');
zotop.namespace('zotop.url.msg');

/*主页面加载*/
function go(mainUrl,sideUrl){
	if(sideUrl){
		zotop.frame.side().location.href = sideUrl;
	}
	if(mainUrl){
		zotop.frame.main().location.href = mainUrl;
	}
}

function notepad(){
	var id = 'notepad';
	var href = location.href;
	var submit = function(){
		zotop.dialog.tip(id,'操作正在执行中，请稍后……');
		$.get(href,function(msg){
		  if(msg && typeof(msg)=='object'){
				if(msg.code==0){
					//操作成功,解析返回的信息
					zotop.dialog.hide(id,function(){
						
					});
				}else{
					//操作返回失败信息
					zotop.dialog.tip(id,msg.content);
				}
		  }else{
				zotop.dialog.tip(id,'操作失败,未能返回正确数据类型');
		  };
		});
		return false;
	}
	zotop.dialog.show({
		id:id,
		title:'记事本',
		width:500,
		height:200,
		timer:0,
		tip:'输入信息并保存',
		content:'<textarea style="width:600px;margin:5px;height:200px;border:0px;padding:5px;overflow:auto;border:1px solid #a7c5e2;"></textarea>',
		buttons:[
			{text:'保存信息',callback:submit},
			{text:'关闭'}
		],
		callback:function(id){
			var $dialog=zotop.dialog.get(id);
				if(zotop.string.length($dialog.find('textarea').val())>0)
				{
					return confirm('已经输入内容，点击确定关闭对话框放弃放弃已经输入的内容？');
				}
			return true;
		}
	});
}

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

(function(){
	setInterval(msg,10000);
})();

(function(){
	function size(){
		var height;
		//主框架的高度
		height = $(window).height()-$('#header').height()-$('#footer').height();
		$('#body').height(height);
		//设置side部分的高度	
		height = height-$('#side-header').height()-$('#side-footer').height()-$('#side-body').height();
		$('#side-extra').height(height);
	};
	zotop.window.change(size);
})();

//记录iframe的当前URL
$(function(){
	$('#mainIframe').load(function(){
		zotop.cookie.set('mainUrl',zotop.frame.main().location.href);
	});
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
	},500)
});

$(function(){
	$("#navbar li:first").addClass('current');	
	$("#navbar li").click(function(){
		$(this).parent().find('li').removeClass('current');
		$(this).addClass('current');
	});
});



$(function(){
	/*notepad();
	
	zotop.dialog.show({
		type:'iframe',
		title:'iframe',
		width:450,
		height:200,
		url:'/zotop/system/admin/index.php/system/about',
		tip:'测试一下窗口tip',
		buttons:[{text:'关闭'}]
	});
	zotop.dialog.show({
		
	});
	*/

	//zotop.msg.success('提示信息','<h1>操作成功，页面将返回前页</h1>如果系统未能自动返回，请点击该链接');
});
