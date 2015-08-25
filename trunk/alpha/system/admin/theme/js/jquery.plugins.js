/*
 * Metadata - jQuery plugin for parsing metadata from elements
 *
 * Copyright (c) 2006 John Resig, Yehuda Katz, J�örn Zaefferer, Paul McLanahan
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id: jquery.metadata.js 3620 2007-10-10 20:55:38Z pmclanahan $
 *
 */
(function($){$.extend({metadata:{defaults:{type:'class',name:'metadata',cre:/({.*})/,single:'metadata'},setType:function(type,name){this.defaults.type=type;this.defaults.name=name;},get:function(elem,opts){var settings=$.extend({},this.defaults,opts);if(!settings.single.length)settings.single='metadata';var data=$.data(elem,settings.single);if(data)return data;data="{}";if(settings.type=="class"){var m=settings.cre.exec(elem.className);if(m)data=m[1];}else if(settings.type=="elem"){if(!elem.getElementsByTagName)return;var e=elem.getElementsByTagName(settings.name);if(e.length)data=$.trim(e[0].innerHTML);}else if(elem.getAttribute!=undefined){var attr=elem.getAttribute(settings.name);if(attr)data=attr;}if(data.indexOf('{')<0)data="{"+data+"}";data=eval("("+data+")");$.data(elem,settings.single,data);return data;}}});$.fn.metadata=function(opts){return $.metadata.get(this[0],opts);};})(jQuery);

/* ie6 bug fixed
 * 
 */
(function($){
	$.fn.bgIframe = $.fn.bgiframe = function(s) {
		// This is only for IE6
		if ( $.browser.msie && /6.0/.test(navigator.userAgent) ) {
			s = $.extend({
				top     : 'auto', // auto == .currentStyle.borderTopWidth
				left    : 'auto', // auto == .currentStyle.borderLeftWidth
				width   : 'auto', // auto == offsetWidth
				height  : 'auto', // auto == offsetHeight
				opacity : true,
				src     : 'javascript:false;'
			}, s || {});
			var prop = function(n){return n&&n.constructor==Number?n+'px':n;},
				html = '<iframe class="bgiframe"frameborder="0"tabindex="-1"src="'+s.src+'"'+
						   'style="display:block;position:absolute;z-index:-1;'+
							   (s.opacity !== false?'filter:Alpha(Opacity=\'0\');':'')+
							   'top:'+(s.top=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderTopWidth)||0)*-1)+\'px\')':prop(s.top))+';'+
							   'left:'+(s.left=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderLeftWidth)||0)*-1)+\'px\')':prop(s.left))+';'+
							   'width:'+(s.width=='auto'?'expression(this.parentNode.offsetWidth+\'px\')':prop(s.width))+';'+
							   'height:'+(s.height=='auto'?'expression(this.parentNode.offsetHeight+\'px\')':prop(s.height))+';'+
						'"/>';
			return this.each(function() {
				if ( $('> iframe.bgiframe', this).length == 0 )
					this.insertBefore( document.createElement(html), this.firstChild );
			});
		}
		return this;
	};
})(jQuery);
/*
 * jQuery Center Plugin 
 * 
 */
(function($){
	$.fn.center=function(f){
		return this.each(function(){
			//设定父元素的position
			if($(this).parent().css("position")=="static"){
				$(this).parent().css("position","relative");
			};
			
			//设定元素的位置方式
			$(this).css("position","absolute");
			
			//如果参数为空,或者参数为水平居中
			if(!f || f == "horizontal" || f == "h" || f == "x") {
				var left=($(this).parent().width()-$(this).width())/2;
					left=(left<0)?0:left;
					$(this).css("left",left+"px");
			}
			if(!f || f == "vertical"|| f == "v" || f == "y") {
				var top=($(this).parent().height()-$(this).height())/2;
					top=(left<0)?0:top;
					$(this).css("top",top+"px");	
			}
			
		});
	}
})(jQuery);

/*
 * Jquery drag和resize插件扩展
 * 
 */
(function($){
	$.fn.drag=function(r){$.jqDnR.init(this,r,'d'); return this;};
	$.fn.resize=function(r){$.jqDnR.init(this,r,'r'); return this;};
	$.jqDnR={
		init:function(w,r,t){ r=(r)?$(r,w):w;
			r.bind('mousedown',{w:w,t:t},function(e){ var h=e.data; var w=h.w;
			hash=$.extend({oX:f(w,'left'),oY:f(w,'top'),oW:f(w,'width'),oH:f(w,'height'),pX:e.pageX,pY:e.pageY},h);
			$().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
			return false;});
		},
		drag:function(e) {var h=hash; var w=h.w[0];
			if(h.t == 'd') h.w.css({left:h.oX + e.pageX - h.pX,top:h.oY + e.pageY - h.pY});
			else h.w.css({width:Math.max(e.pageX - h.pX + h.oW,0),height:Math.max(e.pageY - h.pY + h.oH,0)});
			return false;},
		stop:function(){var j=$.jqDnR;$().unbind('mousemove',j.drag).unbind('mouseup',j.stop);},
		h:false};
		var hash=$.jqDnR.h;
		var f=function(w,t){return parseInt(w.css(t)) || 0};
})(jQuery);

/*
 * 图像自动缩放
 */

(function($){
	$.fn.ImageBox = function(maxWidth,maxHeight)
	{
		$("img",this).each(function()
		{
			var image = $(this);
			var realWidth=image.width();
			var realHeight=image.height();
   
			var rate=(maxWidth/realWidth < maxHeight/realHeight)?maxWidth/realWidth:maxHeight/realHeight;
			if(rate <= 1){   
				image.width(realWidth*rate).height(realHeight*rate).css('marginTop',((maxHeight-realHeight*rate)/2)+'px');
			}
			else {
				image.width(realWidth).height(realHeight).css('marginTop',((maxHeight-realHeight*rate)/2)+'px');
			}
			image.show();
		});
	}
})(jQuery);

/*
 * jQuery DropdownBox Plugin
 * 
 * 
 */
(function($){
	$.fn.dropdownBox = function(options){
		$(this).each(function(){
			options = $.extend({},options);
			controller = {};
			controller.value = $(this).find('option:selected').attr('value');
			controller.text = $(this).find('option:selected').text();
			controller.id	= $(this).attr('id');
			controller.name	= $(this).attr('name');
			controller.width	= $(this).width();
			controller.multiple = $(this).attr('multiple');
			if(!controller.multiple){
				controller.options = '<ul class="dropdownOptions">';
				$('option',this).each(function(){
					if($(this).attr("selected")){
						controller.options += '<li class="selected" rel="'+$(this).attr("value")+'">'+$(this).text()+'</li>';
					}else{
						controller.options += '<li rel="'+$(this).attr("value")+'">'+$(this).text()+'</li>';
					}					
				});
				controller.options +='</ul>';
				$box = $('<span class="dropdownBox inline-block"></span>').insertAfter($(this));
				$box.width(controller.width);
				$box.append('<input type="hidden" class="text" name="'+controller.name+'" value="'+controller.value+'" />');
				$box.append('<div class="dropdown">'+controller.text+'</div>');
				
				$box.append(controller.options).mouseover(function(){
					$(this).addClass('mouseover');
				}).mouseout(function(){
					$(this).removeClass('mouseover');
				});

				
				$box.find('.dropdown').click(function(){
					if($box.find('ul.dropdownOptions').is(':hidden')){
						$box.find('ul.dropdownOptions').slideDown("fast").width(controller.width);				
					}
					return false;
				});
		
				$box.find('li').click(function(){
					$box.find('li').removeClass('selected');
					$(this).addClass('selected');
					$box.find('input[name='+controller.name+']').val($(this).attr("rel"));
					$box.find('.dropdown').html($(this).html());
				}).mouseover(function(){
					$(this).addClass('mouseover');
				}).mouseout(function(){
					$(this).removeClass('mouseover');
				});

				$(this).remove();

				$(document).click(function(event){
					if($(event.target).attr('class')!='dropdownOptions'){
						$box.find('ul.dropdownOptions').slideUp("fast");
					};
				});
				$(window).scroll(function(){
					$box.find('ul.dropdownOptions').hide();					
				});				
				return $(this);
			}
		});		
	}
})(jQuery);



/**
 * jQuery's Dialog Plugin
*/
(function($){
	$.fn.dialog = function(options){
		//options = $.extend({},{width:450,height:200},options);
		$(this).click(function(){
			var settings = $(this).metadata()||{};
				settings = $.extend({},options,settings);
				settings.type = settings.type || 'iframe';
				settings.id = $(this).attr('id') || '';
				
			if(settings.type=='iframe' || settings.type=='ajax'){
				settings.url= $(this).attr('href');
				settings.title = settings.title || $(this).html();
			}else{
				settings.content = $(this).html();
			}
			zotop.dialog.show(settings);
			return false;
		});
	};
})(jQuery);
/**
 * jQuery's Confirm Plugin
*/
(function($){
	$.fn.confirm = function(options){
		options = $.extend({},{width:400,height:60},options);
		$(this).click(function(){
			var href= $(this).attr('href');
			var id = $(this).attr('id') || 'confirm';
			var submit = function(){
				zotop.dialog.tip(id,'操作正在执行中，请稍后……');
				$.get(href,function(msg){
				  if(msg){
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
			var settings = $(this).metadata()||{};
				settings = $.extend({},options,settings);			
				settings.id = id
				settings.title = settings.title || $(this).html();
				settings.content = settings.content || '<h1>您确定要执行 <b>'+settings.title+'</b> 操作？</h1>';
				settings.content = '<div class="zotop-msg zotop-msg-confirm"><div class="zotop-msg-icon"></div><div class="zotop-msg-content">'+settings.content+'</div></div>';
				settings.yes = settings.yes || ' 是 ';
				settings.no = settings.no || ' 否 ';
				settings.buttons = settings.buttons || [{text:settings.yes,callback:submit},{text:settings.no}];
				settings.tip = settings.tip || '确定要执行该操作？';
			zotop.dialog.show(settings);
			return false;
		});
	};
})(jQuery);
/**
 * jQuery's Timer Plugin
*/
(function($){
	$.fn.timer = function(options) {
		options = $.extend({},{second:10},options);
		if($(this).length == 0) return false;
		var obj = this;
		if(options.second < 0 || options.second == 'undefined')
		{
			if(options.callback && typeof(options.callback)=='function') options.callback();
			return null;
		}
		window.setTimeout(function() {
				options.second=options.second-1;
				$(obj).html(String(options.second));				
				$(obj).timer(options);
			},1000);
		return this;
	}
})(jQuery);