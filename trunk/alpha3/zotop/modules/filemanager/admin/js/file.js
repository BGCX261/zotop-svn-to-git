$(function(){
	$('form.sourceEditor').submit(function(){
		var content = $("#SourceEditor").get(0).getText().replace(' ',' ');	
		var $submit = $(":submit",this);
			$submit.blur().addClass("loading").addClass("disabled").disabled(true);
		$.post(
			zotop.url.current(),
			{
				'source':content
			},
			function(data){
				msg = zotop.msg.get(data);
				
				$submit.removeClass("disabled").removeClass("loading").disabled(false);
			}
		)
		return false;		
	});	
});

var so = new SWFObject(zotop.url.common+"/swf/ScriptEditor.swf", "SourceEditor", "100%", "460", "9", "#ffffff");
	so.addVariable("Language","html");
	so.addVariable("AfterInit","setContent");
	so.addParam("wmode", "Opaque");
	so.write("SourceEditorPannel");

function setContent(){
	content = $("textarea[name=source]").val();
	content = content.replace(/\r\n/gi,"\n");
	//content = zotop.html.decode(content);
	$("#SourceEditor").get(0).setText(content);
}
