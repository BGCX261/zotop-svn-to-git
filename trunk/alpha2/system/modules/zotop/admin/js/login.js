if(top!= self){top.location = self.location;}
$(function(){
	$("div.block").show().center().drag(".block-header");
	window.onresize=function(){
		$("div.block").center();
	};
});
