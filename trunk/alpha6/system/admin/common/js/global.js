zotop.loading.show('页面加载中，请稍候……');

$(function(){
	//关闭加载进度
	zotop.loading.hide();
	//关闭消息
	window.setTimeout(function(){zotop.msg.hide();},1000);
});

//页面底部信息
$(function(){
	$("body").mouseover(function(){
		top.window.status = zotop.html.clean(document.title);					 
	});			
});


//修正ie缓存问题
$(function(){   
	if($.browser.msie && $.browser.version == 6){
		try {
			document.execCommand("BackgroundImageCache",false,true);
		} catch(e) {};
	}		
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

$(function(){
	$('.block.expanded,.block.collapsed').each(function(){
		var $this = $(this);
		$this.find('.block-title').css('cursor','pointer').click(function(){
			$this.find('.block-body').slideToggle('fast',function(){
				$this.swapClass('expanded','collapsed');
			});			
		});
	});
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
		
	$('table.list').each(function(){
		$(this).find('.title th:first').addClass('first');
	});
	$('ul.list').each(function(){
		$(this).find('li:last').addClass('last');
	});	
});	

//sortable
$(function(){
	$("table.sortable").sortable({
		items: "tr.item",
		axis: "y",
		placeholder:".placeholder ui-sortable-placeholder",		
		containment:'document',
		forceHelperSize:true,
		forcePlaceholderSize:true,
		helper: function(e, tr){
			tr.children().each(function(){
				$(this).width($(this).width());
			});
			return tr;
		}
	});			
});


//修正f5的刷新问题
$(function(){
	$(document).bind('keydown', 'F5', function(event){		
		
		event.preventDefault();

		var win = top.mainIframe ? top.mainIframe : top;
		win.location.reload();

	});
})