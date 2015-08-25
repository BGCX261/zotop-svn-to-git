var zotop = window.zotop || {};
/*zotop namespace*/
zotop.namespace = function() {
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
zotop.NULL = '__NULL__';
/*zotop config*/
zotop.config = {};


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

/*zotop browser*/
zotop.browser = {
	ie6:function(){
		return navigator.userAgent.indexOf('MSIE')>0&&navigator.userAgent.indexOf('6')>0;
	},
	ie: /msie/.test(window.navigator.userAgent.toLowerCase()),
	moz: /gecko/.test(window.navigator.userAgent.toLowerCase()),
	opera: /opera/.test(window.navigator.userAgent.toLowerCase())	
};

/*zotop domain*/
zotop.domain = {
	get:function(){
	
	},
	set:function(domain){
		domain = domain || window.location.host.match(/[^.]+\.[^.]+$/)[0];
		document.domain = domain;
	}
};


/*zotop ip*/
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
zotop.json = {
	get:function(msg)
	{
		var type = $('#msg-type',msg).html()||'error';
		var title = $('#msg-title',msg).html()||'unknown';
		var content = $('#msg-content',msg).html()||msg;
			content = content || '<b>未知错误！</b>';
		var life = $('#msg-life',msg).html()||0;
		var url = $('#msg-url',msg).html()||'';		
			url = url.replace(/&amp;/g, "&");
		return {'type':type,'title':title,'content':content,'url':url,'life':life};
	},
	encode:function(str){
		
	},
	decode:function(arr){
	
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
	},
	format:function(str, arr) {
		//格式化字符串,支持array和object两种数据源
		var tmp;
		if (arr.constructor == Array) {
			for (var i = 0; i < arr.length; i++) {
				var re = new RegExp('\\{' + (i) + '\\}', 'gm');
				tmp = String(arr[i]).replace(/\$/g, "$$$$");
				str = str.replace(re, tmp);
			}
		} else {
			for (var elem in arr) {
				var re = new RegExp('\\{' + elem + '\\}', 'gm');
				tmp = String(arr[elem]).replace(/\$/g, "$$$$");
				str = str.replace(re, tmp);
			}
		}
		return str;
	},
	cut:function(str,len,addon){
		//得到字符串的前n个字符（1个汉字相当于两个字符，一个英文字母相当于1个字符）
		var leftStr = str;
		var curLen  = 0;
		for(var i=0;i<str.length;i++){
			curLen += str.charCodeAt(i)>255 ? 2 : 1;
			if(curLen > len){
				leftStr = str.substring(0,i);
				break;
			}else if(curLen == len){
				leftStr = str.substring(0,i + 1);
				break;
			}
		}
		if(addon){
			if(leftStr != str){
				leftStr += "..."; 
			}
		}
		return leftStr;		
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
	},
	parse:function(str){
		//将字符串表示的日期转化为Date类型
		var tmpArr		= str.split(" ");
		var dateStr		= tmpArr[0];
		var tmpDateArr	= dateStr.split(".");
		var iYear		= tmpDateArr[0];
		var iMonth		= tmpDateArr[1];
		var iDate		= tmpDateArr[2];
		var timeStr		= tmpArr[1];
		var	tmpTimeArr	= timeStr.split(":");
		var iHour		= tmpTimeArr[0];
		var iMinute		= tmpTimeArr[1];
		return new Date(iYear,iMonth - 1,iDate,iHour,iMinute);
	},
	daydiff:function(date1,date2){
		//判断天数的差值
		var t = date2.getTime() - date1.getTime(); 	//相差毫秒
		var day=Math.round(t/1000/60/60/24);
		if(day==0 || day==1){
			day=date1.getDate()==date2.getDate()?0:1;
		}
		return day;
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
	validator:null,
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
	},
	result:function(msg){
		if(msg.type == 'success'){
			zotop.url.redirect(msg.url,msg.life);
		}
		zotop.msg.show(msg);
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

/*loading*/
zotop.loading ={
	show:function(text){
		text = text || 'loading……';
		var $loading = $('#zotop-loading');
		if( $loading.length == 0 )
		{
			$loading=$('<div id="zotop-loading"><b class="loading"></b><span>'+text+'</span></div>').appendTo("body");
		}
		$loading.find('span').html(text).end().show();
	},
	hide:function(){
		$("#zotop-loading").hide();		
	}
};
/*msg*/
zotop.msg = {
	timer:null,
	exist:false,
	show:function(message,callback,life,type)
	{
		if (self!=top && top.zotop && top.zotop!=undefined){
			return top.zotop.msg.show(message,callback,life,type);
		}
		if( zotop.msg.exist ) zotop.msg.hide();

		//设置
		var settings = {};
		if (typeof (message) == "object") {
			settings = message;
		} else {
			settings.content = message;
			settings.onClose = callback;
			settings.life = life;
			settings.type = type;
		}			
		settings.type = settings.type || 'alert';
		settings.life = settings.life || 3;
		settings.onClose = settings.onClose || true;

		var $msg = $('<div id="zotop-msg" class="'+settings.type+'"><b class="icon"></b><p class="text">'+settings.text+'</p></div>').appendTo(document.body);
			$msg.find('p.text').html(settings.content);
			$msg.show();
		var x = ($(window).width()-$msg.width())/2;
			$msg.css('left',x+'px');

		if(settings.life && settings.life>0){
			zotop.msg.timer = setTimeout(function(){
				 zotop.msg.hide(settings.onClose);
			},settings.life*1000);
		}
		
		zotop.msg.exist = true;
	},
	hide:function(callback)
	{
		if (top!=undefined && self!=top && top.zotop && top.zotop!=undefined){
			return top.zotop.msg.hide(callback);
		}
		//关闭函数
		var close = true;			
		if(callback && typeof(callback)=='function')
		{
			close = callback();
		}
		if(close==false || close=='undefined')
		{
			return false;
		}
		$('#zotop-msg').remove();
		if(zotop.msg.timer) {
			window.clearTimeout(zotop.msg.timer);
			zotop.msg.timer=null;
		}		
		zotop.msg.exist = false;
	},
	success:function(message,callback,life){
		zotop.msg.show({
			content : message,
			onClose : callback,
			life:life || 3,
			type : 'success'
		});
	},
	error:function(message,callback,life){
		zotop.msg.show({
			content : message,
			onClose : callback,
			life:life || 3,
			type : 'error'
		});
	},
	notice:function(message,callback,life){
		zotop.msg.show({
			content : message,
			onClose : callback,
			life:life || 3,
			type : 'notice'
		});
	}	
}

zotop.data = {};
zotop.data.collection = function(){
	this.map = {};
	this.types = {};
	this.keys = [];

	//add
	zotop.data.collection.prototype.add = function(id,value,type){
		this.map[id] = value;
		this.keys.push(id);
		if(type){
			this.types[id] = type;
		}
		else{
			this.types[id] = 'string';
		}
	
	};
	//get
	zotop.data.collection.prototype.get = function(id){
		if(typeof(id)=="number") {
			return this.map[this.keys[id]]
		}
		return this.map[ID];
	}
	//getKey
	zotop.data.collection.prototype.getKey = function(index){
		if(typeof(index)=="number") {
			return this.keys[index];
		}
	}
	//size
	zotop.data.collection.prototype.size=function () {
		return this.keys.length;
	};
	//remove
	zotop.data.collection.remove=function (id) {
		if(typeof(id)=="number") {
			this.map[this.keys[id]]=null;
			this.keys[id];
		}
		return this.map[id];
	};
	//toQueryString
	zotop.data.collection.prototype.toQueryString = function(){
		var arr = [];
		for(var i=0;i<this.keys.length;i++){
			if(this.map[this.keys[i]]==null||this.map[this.keys[i]]=="") {
				continue;
			}
			if( i!=0 ){
				arr.push('&');
			}
			arr.push(this.keys[i]+"="+this.map[this.keys[i]]);
		}
		return arr.join("");
	};
	//toXML
	zotop.data.collection.prototype.toXML = function(){
		var arr = [];
			arr.push('<?xml version="1.0" encoding="UTF-8"?>');
			arr.push("<collection>");
			for(var id in this.map){
				try{
					var value = this.map[id];
					var type = this.types[id];
					//
					arr.push('<element id="'+id+'" type="'+type+'">');
					if(type == 'string'){
						if( value==null || typeof(value)=="undefined")
						{
							value = zotop.NULL;
						}
						arr.push("<![CDATA["+value+"]]>")
					}else
					{
						arr.push(value);
					}
					arr.push('</element>');
				}catch(ex){
				
				}
			}
			arr.push("</collection>");	
		return arr.join("");		
	};
	zotop.data.collection.prototype.toJSON = function(){
		var json;
		var arr = [];
			for(var id in this.map){
				try{
					var value = this.map[id];
					if( value==null || typeof(value)=="undefined" )
					{
						value ='';
					}
					arr.push('"'+id+'":"'+value+'"');
				}catch(ex){
				
				}
			}
			
		 json = '{'+arr.join(',')+'}';

		return json;
	}
}


/*
 * zotop.dialog
 *
 */

zotop.dialog = function(options){
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
		zindex:99908,
		buttons:false,
		tip:'',
		width:450,
		height:120,
		opener:window,
		onClose:true, //关闭时的回调函数
		onShow:false
	};
	if(options) {
		$.extend(settings, options);
	};

	//dialog
	var html  = '';		
	html += '<div class="zotop-dialog '+settings.classname+' clearfix" style="position:absolute;left:0px;top:0px;">';
	html += '<table class="zotop-dialog-main" style="width:'+settings.width+'px;">';
	html += '		<tr class="zotop-dialog-header">';
	html += '			<td>';
	html += '				<div class="zotop-dialog-top clearfix">';
	html += '					<div class="zotop-dialog-close"></div>';
	html += '					<div class="zotop-dialog-title"><span class="zotop-dialog-icon"></span><b>'+settings.title+'</b></div>';			
	html += '				</div>';		
	html += '			</td>';
	html += '		</tr>';
	html += '		<tr class="zotop-dialog-body">';
	html += '			<td>';
	html += '				<div class="zotop-dialog-content clearfix" style="height:'+settings.height+'px;">';
	html += '					<div class="zotop-dialog-loading"></div>';
	html += '				</div>';		
	html += '			</td>';
	html += '		</tr>';
	html += '		<tr class="zotop-dialog-footer">';
	html += '			<td>';
	html += '				<div class="zotop-dialog-bottom clearfix">';
	html += '					<div class="zotop-dialog-buttons"></div>';
	html += '					<div class="zotop-dialog-tip"></div>';		
	html += '				</div>';		
	html += '			</td>';
	html += '		</tr>';
	html += '</table>';
	html += '</div>';

	var self = this;
	var $dialog = $(html);

	this.id = settings.id || Math.ceil(Math.random()*1000);
	this.id = 'zotop-dialog-' + this.id;
	this.title = settings.title;
	this.content = settings.content;
	this.tip = settings.tip;
	this.width =  settings.width;
	this.height =  settings.height;
	this.buttons = settings.buttons;
	this.onShow = settings.onShow;
	this.onClose = settings.onClose;
	this.zindex = settings.zindex;
	this.zindex = ($(".zotop-dialog:last").css('z-index')||this.zindex) + 10;//获取真实的zindex.最后一个dialog的zindex加1即可
	this.timer = settings.timer;
	this.opener = settings.opener || window;
	this.args = settings.args || {};
	this.self = $dialog;

	this.setTitle = function(str){
		$dialog.find('.zotop-dialog-title').find('b').html(str);
	};
	
	this.setContent = function(html){
        if(html){
			if (typeof (html) == "string") {
				$dialog.find('.zotop-dialog-content').html(html);
			} else if (typeof (html) == "object") {
				$dialog.find('.zotop-dialog-content').append(html);
			}
		}
	};

	this.setArgs = function(args){
		this.args = args;
	};

	this.setTip = function(tip,className){
		if(tip){
			$dialog.find('.zotop-dialog-footer').show();
			$dialog.find(".zotop-dialog-tip").html('<span class="zotop-tip '+(className||'')+'">'+tip+'</span>');
		}
	};

	this.setButtons = function(buttons){
		//设置按钮
		if (buttons){			
			$.each(buttons,function(i,button){			
				$('<input type="button"/>')
				.addClass('button zotop-dialog-button').addClass(button.classname)
				.val(button.text)
				.click(function(e){
					self.close(button.callback,this);
					e.stopPropagation();
				})
				.appendTo($dialog.find('.zotop-dialog-buttons'));
			})
			$dialog.find('.zotop-dialog-footer').show();
		}		
	};

	this.setWidth = function(width){
		if( width ){
			$dialog.find('.zotop-dialog-main').css('width',width+'px');
			$dialog.find('.zotop-dialog-content').css('width',width+'px');
			$dialog.find('.zotop-dialog-content iframe').css('width',width+'px');
		}
	};
	this.setHeight = function(height){
		if( height ){
			$dialog.find('.zotop-dialog-body').css('height',height+'px');
			$dialog.find('.zotop-dialog-content').css('height',height+'px');
			$dialog.find('.zotop-dialog-content iframe').css('height',height+'px');
		}
	};
	this.setSize = function(width,height,position){
		this.setWidth(width);
		this.setHeight(height);
		this.setPosition(position);
	};

	this.setPosition = function(position){
		if(position){
			var top=$(window).scrollTop()+($(window).height()-$dialog.height())/2;
			var left = ($(window).width() - $dialog.width()) / 2;
			if (window != window.top) {
				try {
					var offset = $(frameElement).offset();
					top -= offset.top / 2;
					top -= offset.left / 2;
				} catch (e) { }
			}
			if (top < 0) top = 0;
			if (left < 0) left = 0;		
			$dialog.css({ top: top, left: left });
		}
	};
	this.setTimer = function(timer){
		
		if( timer > 0 ){
			
			zotop.dialog.timer[this.id] = setTimeout(function(){
				self.close();
			},timer*1000);
			
			$dialog.find('.zotop-dialog-timer').timer(timer);
		}
	};

	this.flashTitle = function(opacity,times,interval,flag){
        if(times>0){
            flag=!flag;
            op=flag?opacity:1;
            $dialog.find('.zotop-dialog-top').css('opacity',op);
            setTimeout(function(){
                self.flashTitle(opacity,times-1,interval,flag)
            },interval);
        }
	};

	this.show = function(){		
		//确保对话框只加载一次
		if( zotop.dialog.global[this.id] ) return false;	
		//显示遮罩
		var $mask=$('<div class="zotop-dialog-mask"></div>');
			$mask.css({
				position: 'absolute',
				top: '0px',
				left: '0px',
				zIndex : this.zindex,
				width : $(document).width(),
				height : $(document).height()
			});			
			$mask.appendTo(document.body).show();			
			$mask.click(function(){
				self.flashTitle(0.6,4,60);
			});

		//显示对话框
		$dialog.appendTo(document.body);
		$dialog.attr({
			id : this.id
		}).css({
			zIndex : this.zindex + 1	
		});
		
		$dialog.bgiframe();

		$dialog.draggable({
			addClasses: false,
			//scroll :false,
			handle : '.zotop-dialog-header',			
			iframeFix : true,
			containment : 'parent',
			cursor : 'move'
		});

		$dialog.find('.zotop-dialog-close').focus().click(function(){
			self.close();
		});		
		this.setTitle(this.title);
		this.setContent(this.content);
		this.setTip(this.tip);
		this.setButtons(this.buttons);
		this.setPosition(true);
		this.setTimer(this.timer);

		$dialog.css('visibility','visible').show();

        zotop.dialog.last = zotop.dialog.current;
        zotop.dialog.current = this;
		zotop.dialog.global[this.id] = this;
		//自动计算位置
		$(window).bind('resize.dialog.'+this.id,function(){			
			self.setPosition(true);				
		});

        if (this.onShow) { this.onShow() };
	};

	this.hide = function() {
		$dialog.prev('.zotop-dialog-mask').hide();
		$dialog.hide();		
    };

	this.remove = function(){
		this.isRemoved = true;
		this.self.draggable('destroy');
		$dialog.prev('.zotop-dialog-mask').remove();
		$dialog.remove();		
	}

    this.close = function(callback,button) {
		if(typeof(callback) != 'function'){
			callback = this.onClose;
		}
		var close = true;			
		if(callback && typeof(callback)=='function')
		{
			try{
				close = callback(this,button);
			}catch(e){}
		}
		if(close==false || close=='undefined')
		{
			return false;
		}
        
		this.hide();        
		this.remove();
		$(window).unbind('resize.dialog.'+this.id);
		zotop.dialog.global[this.id] = null;
        if ( zotop.dialog.last &&  zotop.dialog.current !=  zotop.dialog.last && ! zotop.dialog.last.isRemoved) {
             zotop.dialog.current =  zotop.dialog.last;
             zotop.dialog.last = null;
        }
        try {
            $('input:enabled:visible:first',this.opener.document).focus().blur();
        } catch (e) { }
    };

}
//Dialog扩展
zotop.dialog.last = null;
zotop.dialog.current = null;
zotop.dialog.timer = {};
zotop.dialog.global = {};
zotop.dialog.show = function(settings){
	settings.opener = settings.opener || window;
	if (window != window.top && top.zotop ){
		return window.top.zotop.dialog.show(settings);
	} 
	var dialog = new zotop.dialog(settings);
		dialog.show();
	return dialog;

};
zotop.dialog.alert = function(message,callback){
	var settings = {
		id : 'alert',
		title : '系统提示', 
		content : '<div class="zotop-msg zotop-msg-alert"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+message+'</div></div>', 
		buttons : [{text:'确 定'}],
		onClose : callback
	}
	return zotop.dialog.show(settings);
};
zotop.dialog.confirm = function(message,ok,cancel,tip){
	var settings = {
		id : 'confirm',
		title : '确认信息', 
		content : '<div class="zotop-msg zotop-msg-confirm"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+message+'</div></div>', 
		buttons : [{text:'确 定',callback:ok},{text:'取 消',callback:cancel}],
		tip : tip || '您确定要执行该操作？'
	}		
	return zotop.dialog.show(settings);
};
zotop.dialog.msg = function(settings){
		settings.title = settings.title||'系统提示';
		settings.content = '<div class="zotop-msg zotop-msg-'+settings.type+'"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+settings.content+'</div></div>';
		settings.width	= 400;
		settings.height	= 100;
		settings.timer	= settings.timer || 6;
		if( settings.timer>0 ){
			settings.tip	= '窗口将于<span class="zotop-dialog-timer">'+settings.timer+'</span>秒后自动关闭';
		}
		settings.buttons = [{text:'确 定'}];
		
	return zotop.dialog.show(settings);
};

zotop.dialog.open = function(settings){
	settings.opener = settings.opener || window;
	if ( window != window.top && top.zotop ){
		return window.top.zotop.dialog.open(settings);
	};
	//创建对话框
	var dialog = new zotop.dialog(settings);
		dialog.show();	
	//处理iframe问题
	var $iframe = $('<iframe src="about:blank;" class="zotop-dialog-iframe" frameBorder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>');
		$iframe.attr({
			src : zotop.url.join(settings.url,'hash='+new Date().getTime()) //处理url缓存问题
		});
		$iframe.load(function(){
			$(this).prev('.zotop-dialog-loading').hide();
			$(this).show();
			//重置dialog的宽高
			var width  = $(this).contents().find('body').width() || settings.width;
			var height  = $(this).contents().find('body').height() || settings.height;
			//重置dialog的位置
			dialog.setSize(width,height,true);					
		});
		//传递自身作为参数
		$iframe.get(0).dialog = dialog;
		//设置内容
		dialog.setContent($iframe);		
	return dialog;	
};