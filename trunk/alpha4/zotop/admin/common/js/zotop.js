var zotop = window.zotop || {};

zotop={
	boot:function(){
		//全局的加载效果
		top.zotop.loading.show('页面加载中,请稍后……');
		$(function(){
			top.zotop.loading.hide();	   
		});

		//页面底部信息
		$(function(){
			$("body").mouseover(function(){
				top.window.status = zotop.html.clean(document.title);					 
			});			
		});

		$(function(){
			//top.$('#footer').find('td').html(zotop.html.clean(document.title)+' ip:'+zotop.ip.current()+',from:'+zotop.ip.province()+'/'+zotop.ip.city());
		});

		//修正ie横向滚动条的问题
		$(function(){   
			if($.browser.msie&&$.browser.version=="6.0"&&$("html")[0].scrollHeight>$("html").height())   
				$("html").css("overflowY","scroll");   
		});	
		
		//常见的绑定
		$(function(){
			$('a.dialog,li.dialog,span.dialog').dialog();
			$('a.confirm').confirm();
			$('div.dropdown').dropdown();
		})
		
		//修正f5的刷新问题
		$(document).keydown(function(e){
			if(top.refresh){
				top.refresh(e);
			}			
		});
		
		//列表颜色及选择项
		$(function(){
			$(".list .item").mouseover(function(){$(this).addClass("mouseover");}).mouseout(function(){$(this).removeClass("mouseover");});
			$(".list input.select").click(function(){
				var flag=$(this).attr("checked");
				if(flag){
					$(this).parents(".item").addClass("selected");
				}else{
					$(this).parents(".item").removeClass("selected");	
				}											  
			});		
			$(".selectAll").click(function(){
				var flag=$(this).attr("checked");
				if(!flag){flag=$(this).attr("flag")}
				//alert(flag)
				$(this).parents("form").find("input.select").each(function(){
					if(flag==true||flag=="true"){
						$(this).attr("checked","checked");
						$(this).parents(".item").addClass("selected")
					}else{
						$(this).attr("checked","");	
						$(this).parents(".item").removeClass("selected")
					}			
				})								 
			});		
		});		
	},
	namespace:function() {
		//defaine namespace from YUI
	    var a=arguments, o=null, i, j, d;
	    for (i=0; i<a.length; i=i+1) {
	        d=(""+a[i]).split(".");
	        o=zotop;
	        
	        for (j=(d[0] == "zotop") ? 1 : 0; j<d.length; j=j+1) {
	            o[d[j]]=o[d[j]] || {};
	            o=o[d[j]];
	        }
	    }	
	    return o;
	}
	
};
zotop.frame={
	top:function(){
		return top.topIframe ? top.topIframe : top;
	},
	side:function(){
		return top.sideIframe ? top.sideIframe : top;
	},
	main:function(){
		return top.mainIframe ? top.mainIframe : top;
	},
	bottom:function(){
		return top.bottomIframe ? top.bottomIframe : top;
	},
	get:function($name){
		return top.$name ? top.$name : top;
	}
};

zotop.window={
	load:function(callback){
		if(typeof callback == 'function'){
			window.onload = callback;
		}
		return true;
	},
	resize:function(callback){
		if(typeof callback == 'function'){
			window.onresize = callback;
		}
		return true;
	},
	change:function(callback){
		if(typeof callback == 'function'){
			window.onload = window.onresize = callback;
		}
		return true;
	},
	beforeunload:function(callback){		
		window.onbeforeunload = function(){
			var tips = '';
			if(typeof callback == 'function'){
				tips = callback();//传入值是一个函数，且有返回值，提示返回值，无则不提示
			}else{
				tips = callback; //传入是字符串，直接提示字符串
			}
			if(tips){
				if(zotop.browser.ie){
					event.returnValue = tips;
				}else{
					return tips;
				}
			}
		};
		return true;
	}		
};
zotop.browser={
	ie6:function(){
		return navigator.userAgent.indexOf('MSIE')>0&&navigator.userAgent.indexOf('6')>0;
	},
	ie: /msie/.test(window.navigator.userAgent.toLowerCase()),
	moz: /gecko/.test(window.navigator.userAgent.toLowerCase()),
	opera: /opera/.test(window.navigator.userAgent.toLowerCase())	
};
zotop.ip={
	data:function(){
		var data = zotop.cookie.get('ipdata')
		if(!data){
			zotop.js.load('http://fw.qq.com:80/ipaddress',function(){
				if (typeof IPData != 'undefined')
				{
					$data = IPData[0]+','+IPData[1]+','+IPData[2]+','+IPData[3];
					zotop.cookie.set('ipdata', $data);
				}					
			},'gb2312');
		}
		return data.split(',');
	},
	current:function(){
		var data = zotop.ip.data();
		return data[0];
	},
	province:function(){
		var data = zotop.ip.data();
		return data[2];		
	},
	city:function(){
		var data = zotop.ip.data();
		return data[3];		
	}
	
}
//Cookie类
zotop.cookie = {
	set:function(name, value, expires, path, domain, secure){
			if(expires)	{							//改成分钟
				var date=new Date();
				var ms=expires * 60 * 1000;         //每分钟有60秒，每秒1000毫秒
				date.setTime(date.getTime()+ms);
				expires=date;
			}
		  document.cookie =  name + "=" + escape(value) +
		   ((expires) ? "; expires=" + expires.toGMTString() : "") +
		   ((path) ? "; path=" + path : "; path=/") +
		   ((domain) ? "; domain=" + domain : "") +
		   ((secure) ? "; secure" : "");
	 },
	 get : function(name){
		  var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
		  if (arr != null){
		   return unescape(arr[2]);
		  }
		  return null;
	 },
	 clear : function(name, path, domain){
		  if (zotop.cookie.get(name)!= null){
			document.cookie = name + "=" +
			((path) ? "; path=" + path : "; path=/") +
			((domain) ? "; domain=" + domain : "") +
			";expires=Fri, 02-Jan-1970 00:00:00 GMT";
		  }
	 }
};
zotop.html={
		encode:function(s){
			return (s == null)?s:s.replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&amp;/g,"&").replace(/&quot;/g,"\"");
		},
		decode:function(s){
			return (s == null)?s:s.replace(/&/g,"&amp;").replace(/\"/g,"&quot;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
		},
		clean:function(str){
			//return str.replace(/</?[^>]+>/gi,"");
			var div=document.createElement("div");
				div.innerHTML=str;
			var text="";
			if(document.all){
				text=div.innerText;
			}else{
				text=div.textContent;
			}
			div=null;
			return text;				
		}
	};
zotop.string={
		trim:function(str){
			return str.replace(/(^\s+)|(\s+$)/ig,"");
		},
		length:function(str){
			//var arr=s.match(/[^\x00-\xff]/ig);
			//return s.length+(arr==null?0:arr.length);
			var charset;
			if(zotop.browser.moz){
				charset = document.characterSet;
			}
			else{
				charset = document.charset;
			}
			if(charset.toLowerCase() == 'utf-8'){
				return str.replace(/[\u4e00-\u9fa5]/g, "***").length;
			}
			else{
				return str.replace(/[^\x00-\xff]/g, "**").length;
			}
		},
		left:function(s,num,mode){
			if(!/\d+/.test(num)){return s};
			var str = s.substr(0,num);
			if(!mode) return str;
			var n = zotop.string.length(str) - str.length;
				num = num - parseInt(n/2);
			return s.substr(0,num);	
		},
		right:function(s,num,mode){
			if(!/\d+/.test(num))return s;
			var str = s.substr(s.length-num);
			if(!mode) return str;
			var n = zotop.string.length(str) - str.length;
				num = num - parseInt(n/2);
			return s.substr(this.length-num);
		},
		indent:function(orders){
			var i=(orders.split(',')).length-1;
			document.write("<span class=\"Indent\" style=\"width:"+(40*i)+"px;\" ></span>")
		}		
	};
zotop.time={
		current:function(){
			var c=new Date();
			var f=c.getHours();
			var b=c.getMinutes();
			var d=c.getSeconds();
			var a=[];
			a.push(f>9?f:"0"+f);
			a.push(b>9?b:"0"+b);
			a.push(d>9?d:"0"+d);
			return a.join(":")			
		}
};
zotop.valid={
	isNumber:function (a) {
		return this.regex(a,/^\d+$/);
	},
	isNaturalNumber:function (a) {
		return this.regex(a,/^[0-9]+$/);
	},
	isInteger:function (a) {
		return this.regex(a,/^(\+|-)?\d+$/);
	},
	isFloat:function (a) {
		return this.regex(a,/^(\+|-)?\d+($|\.\d+$)/);
	},
	isZH:function (a) {
		return this.regex(a,/^[\u4e00-\u9fa5]+$/);
	},
	isLowercase:function (a) {
		return this.regex(a,/^[a-z]+$/);
	},
	isUppercase:function (a) {
		return this.regex(a,/^[A-Z]+$/);
	},
	isLetter:function (a) {
		return this.regex(a,/^[A-Za-z]+$/);
	},
	isEmail:function (a) {
		return this.regex(a,/^([-_A-Za-z0-9\.]+)@([-_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/);
	},
	regex:function (b,a) {
		if(a.test(b)) {
			return true;
		}else {
			return false;
		}
	},
	isArray:function(a) {
		if(!a) {
			return false
		}if(a.constructor.toString().indexOf("Array")==-1) {
			return false
		}else {
			return true
		}
	}
};
zotop.page={};
zotop.user={
	id:0,username:'',name:''
};
zotop.form={
		select:function(obj){
			if(obj.checked){
				$(obj).parents("tr").addClass("selected");
			}else{
				$(obj).parents("tr").removeClass("selected");	
			}
		},
		disabled:function(bool){
			if(bool){
				$("input[type=submit]").attr("disabled","");
			}else{
				$("input[type=submit]").attr("disabled","disabled");
			}
		}	
	};
zotop.css={
	load:function(href,id){
		var head = 	document.getElementsByTagName('head')[0];
		if(head){	
		    var css = document.createElement("link");
		    	css.rel = "stylesheet";
		    	css.type = "text/css";
		    	css.href = href;      
		    	css.id = id;
		    head.appendChild(css);
		}
	}	
};
zotop.js={
	load:function(url,callback,charset){
		var head = 	document.getElementsByTagName('head')[0];
		if(head){
			var script = document.createElement('script');
				script.type = 'text/javascript';
				script.src = url;
				script.charset = charset||'UTF-8'; 
				
				head.appendChild(script);
			
				if (zotop.browser.ie)
				{
					script.onreadystatechange = function()
					{
						if (this.readyState=='loaded' || this.readyState=='complete')
						{
							callback();
						}
					};
				}
				else if (zotop.browser.moz)
				{
					script.onload = function()
					{
						callback();
					};
				}
				else
				{
					callback();
				}
		}		
	}	
};
zotop.image ={
	resize:function(){
	
	},
	maxsize:function(){
	
	}
};
zotop.url={
	current:function(){
		return location.href;
	},
	join:function(url,strings){
		if(url.indexOf(strings)==-1){
			if(url.indexOf('?')>-1){
				url+="&"+strings;
			}else{
				url+="?"+strings;	
			}
		}
		return url;
	},
	build:function(uri,querystring,fragment){
		var url = zotop.url.base ;
		if(uri){
			url = url+'/'+uri;
		}
		if(querystring){
			url = zotop.url.join(url,querystring);
		}
		if(fragment){
			url = url+'#'+fragment;
		}
		return url;
	},
	redirect:function(url,timer){
		if(url){
			switch(url.toLowerCase()){
				case 'reload':
				case 'refresh':
					url = location.href;
					break;
				case 'back':
					url = document.referer;
					break;
			}
			if(timer && timer>0){
				setTimeout(function(){location.href = url;},timer)
			}else{
				location.href = url;
			}
		}
		return false;
	}
};
zotop.loading={
	show:function(str){
		str = str || 'loading……';
		var	$loading=$('<div id="zotop-loading"><b class="loading"></b><span>'+str+'</span></div>');
			$loading.appendTo("body");
	},
	hide:function(){
		$("#zotop-loading").remove();		
	}
};


zotop.msg={
	get:function(msg)
	{
		var type = $('#msg-type',msg).html()||'error';
		var title = $('#msg-title',msg).html()||'error';
		var content = $('#msg-content',msg).html()||msg;
			content = content || '<b>未知错误！</b>';
		var life = $('#msg-life',msg).html()||0;
		var url = $('#msg-url',msg).html()||'';		
			url = url.replace(/&amp;/g, "&");		
		return {'type':type,'title':title,'content':content,'url':url,'life':life};
	},
	success:function(title,content,callback,timer){
		var settings={};
			settings.title = title||'操作成功';
			settings.content = content||'';
			settings.content = '<div class="zotop-msg zotop-msg-success"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+settings.content+'</div></div>';
			settings.width	= 400;
			settings.height	= 60;
			settings.timer	= timer||6;
			settings.callback = callback||true;
			settings.tip	= '窗口将于<span class="zotop-dialog-timer">'+settings.timer+'</span>秒后自动关闭';
			settings.buttons = [{text:'确 定'}];
			return zotop.dialog.show(settings);
	},
	error:function(title,content,callback,timer){
		var settings={};
			settings.title = title||'操作失败';
			settings.content = content||'';
			settings.content = '<div class="zotop-msg zotop-msg-error"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+settings.content+'</div></div>';
			settings.width	= 450;
			settings.height	= 120;
			settings.timer	= timer||6;
			settings.callback = callback||true;
			settings.tip	= '窗口将于<span class="zotop-dialog-timer">'+settings.timer+'</span>秒后自动关闭';
			settings.buttons = [{text:'确 定'}];
			return zotop.dialog.show(settings);
	},
	arert:function(title,content,callback,timer){
		var settings={};
			settings.title = title||'提示信息';
			settings.content = content||'';
			settings.content = '<div class="zotop-msg zotop-msg-alert"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+settings.content+'</div></div>';
			settings.width	= 450;
			settings.height	= 120;
			settings.timer	= timer||9;
			settings.callback = callback||true;
			settings.tip	= '提示信息将于<span class="zotop-dialog-timer">'+settings.timer+'</span>秒后自动关闭';
			settings.buttons = [{text:'确 定'}];
			return zotop.dialog.show(settings);
	}
};

zotop.dialog = {
	show:function(options){
		var settings = {
			id:'common',
			title:'zotop',
			type:'html',
			content:'',
			url:'',
			classname:'',
			position:true,
			close:true,
			timer:0,
			mask:true,
			zindex:1000,
			buttons:false,
			tip:'',
			callBack:true, //关闭时的回调函数
			opener:window
		};		
		if(options) {
  			$.extend(settings, options);
 		};		
		if (self!=top && top.zotop && top.zotop!=undefined){
			return top.zotop.dialog.show(settings);
		}
		settings.id = settings.id || Math.ceil(Math.random()*1000);
		settings.width = settings.width || 450;
		settings.height = settings.height || 200;
		settings.zindex = ($(".zotop-dialog:last").css('z-index')||settings.zindex) + 1;//获取真实的zindex.最后一个dialog的zindex加1即可
		//加载mask
		var $mask=$('<div id="zotop-dialog-'+settings.id+'-mask" class="zotop-dialog-mask"></div>');
			$mask.appendTo("body")
			$mask.css({zIndex:settings.zindex,width:$(document).width(),height:$(document).height()});
			$mask.css({background:settings.mask.background,opacity:settings.mask.opacity});
			$mask.bgiframe();
		
		var dialog  = '';		
			dialog += '<div class="zotop-dialog '+settings.classname+'" id="zotop-dialog-'+settings.id+'" style="z-index:'+settings.zindex+'">';
			dialog += '<div class="zotop-dialog-inner">';
			dialog += '		<div class="zotop-dialog-header">';
			dialog += '			<div class="zotop-dialog-header-close"></div>';
			dialog += '			<div class="zotop-dialog-header-title"><span class="zotop-dialog-icon"></span>'+settings.title+'</div>';		
			dialog += '		</div>';
			dialog += '		<div class="zotop-dialog-body"><div class="zotop-dialog-loading" style="width:'+settings.width+'px;height:'+settings.height+'px"></div></div>';
			dialog += '		<div class="zotop-dialog-footer clearfix">';
			dialog += '			<div class="zotop-dialog-buttons"></div>';
			dialog += '			<div class="zotop-dialog-tip"></div>';	
			dialog += '		</div>';
			dialog += '</div>';
			dialog += '</div>';
 		
		//凡是jQuery对象，请在变量名称前加$
		var $dialog =  $(dialog);
			$dialog.find(".zotop-dialog-header-close").click(function(){
				zotop.dialog.hide(settings.id,settings.callback);												 
			});		
			$dialog.drag(".zotop-dialog-header");
			$dialog.appendTo("body");

		//设置内容
		switch (settings.type){
			case 'html':
				$dialog.find('.zotop-dialog-body').html(settings.content);				
				break;
			case 'iframe':
				settings.url = zotop.url.join(settings.url,'now='+new Date().getTime());
				$iframe = $('<iframe id="zotop-dialog-'+settings.id+'-iframe" class="zotop-dialog-iframe" src="'+settings.url+'" frameBorder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>');
				$iframe.css({width:settings.width+'px',height:settings.height+'px'}).hide();
				$iframe.appendTo($dialog.find('.zotop-dialog-body'));
				$iframe.get(0).dialog = {
					id:settings.id,opener:settings.opener,args:settings.args,callback:settings.callback
				};
				$iframe.load(function(){
					$(this).prev('.zotop-dialog-loading').hide();
					$(this).show();
					//重置dialog的宽高
					var width  = $(this).contents().find('body').width() || settings.width;
					var height  = $(this).contents().find('body').height() || settings.height;
					
					$(this).css({width:width+'px',height:height+'px'});
					//重置dialog的位置
					zotop.dialog.resize(settings.id,width,height,true);
				});				
				break;				
			case 'ajax':
				settings.url = zotop.url.join(settings.url,'rand='+Math.random());
				$dialog.find('.zotop-dialog-body').load(settings.url,function(){
					zotop.dialog.resize(settings.id,$(this).width(),$(this).height(),true);
				})
				break;
		}

		//设置按钮
		if (settings.buttons){			
			$.each(settings.buttons,function(i,button){
				if(typeof(button.callback)!='function')
				{
					button.callback = settings.callback;
				}					
				$('<input type="button"/>')
				.addClass('button zotop-dialog-button').addClass(button.classname)
				.val(button.text)
				.click(function(){
					zotop.dialog.hide(settings.id,button.callback);
				})
				.appendTo($dialog.find('.zotop-dialog-buttons'));
			})
			$dialog.find('.zotop-dialog-footer').css('display','block');
		}		 
		//设置tip
		if(settings.tip){
			zotop.dialog.tip(settings.id,settings.tip);
		}
		//设置自动关闭
		if(settings.timer && settings.timer>0){
			 setTimeout(function(){
				 zotop.dialog.hide(settings.id,settings.callback)
			 },settings.timer*1000);
			 $dialog.find('.zotop-dialog-timer').timer(settings.timer);
		};
		//设置宽高和位置
		zotop.dialog.resize(settings.id,settings.width,settings.height,true);
		//显示对话框
		$dialog.css('visibility','visible');
		//设置相关信息
		return $dialog;
	},
	hide:function(id,callback){
		$dialog = zotop.dialog.get(id);
		if($dialog.length>0){
			//callback可以传入函数或者bool
			//当回调为false或者返回false的时候不关闭对话框，否则关闭
			var hide = true;
			if(callback && typeof(callback)=='function')
			{
				hide = callback(id);
			}
			if(hide==false || hide=='undefined')
			{
				return false;
			}		
			$dialog.prev('.zotop-dialog-mask').remove();
			$dialog.remove();			
			return true;
		}
		return false;
	},
	tip:function(id,tip,className){
		if(id){
			$dialog=zotop.dialog.get(id);
			$dialog.find('.zotop-dialog-footer').css('display','block');
			$dialog.find(".zotop-dialog-tip").html('<span class="zotop-tip '+(className||'')+'">'+tip+'</span>');
			return $dialog;
		}
		return false;
	},
	resize:function(id,width,height,position){
		if(id){
			$dialog=zotop.dialog.get(id);
			$dialog.find('.zotop-dialog-body').css('minWidth',width+'px');
			$dialog.find('.zotop-dialog-body').css('minHeight',height+'px');
			$dialog.find(".zotop-dialog-header,.zotop-dialog-footer,zotop-dialog-body").css({width:$dialog.find(".zotop-dialog-body").width()});

			if(position)
			{
				var left=($(window).width()-$dialog.width())/2;		
				var top=$(window).scrollTop()+($(window).height()-$dialog.height())/2;
				$dialog.css({left:left,top:top});
			}
			return $dialog;
		}
		return false;
	},
	get:function(id){
		if (!id) return false;
		if (top!=undefined && self!=top && top.zotop && top.zotop!=undefined){
			return top.zotop.dialog.get(id);
		}
		return $("#zotop-dialog-"+id);
	}	
}
//系统启动
zotop.boot();