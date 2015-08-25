<?php $this->header();?>
<?php echo html::script('$common/js/swfobject.js');?>

<div class="" style="width:100%;height:460px;overflow:hidden;">
	<div id="SourceEditorLoading" style="height:460px;line-height:460px;text-align:center;">正在加载编辑器，请稍后……</div>
	<div id="SourceEditorPannel"></div>
	<?php echo field::get('textarea',array('name'=>'source', 'value'=>$content))?>
	<script type="text/javascript">
		//显示按钮
		dialog.setTitle('编辑：<?php echo $file;?>').setWidth(800).setTip('小提示：快捷键 Ctrl+S 可以快速保存').setButtons([{text:'保 存',callback:save},{text:'关 闭'}]);
		//加载编辑器
		var so = new SWFObject("<?php echo url::decode('$common/swf/ScriptEditor.swf');?>", "SourceEditor", "100%", "460", "9", "#ffffff");
			so.addVariable("Language","<?php echo file::ext($file); ?>");
			so.addVariable("AfterInit","setContent");
			so.addParam("wmode", "Opaque");
			so.write("SourceEditorPannel");
		
		function setContent(){
			content = $("textarea[name=source]").val();					
			$("#SourceEditor").get(0).setText(content);
			$('#SourceEditorLoading').hide();
		}

		function save(dialog,button){
			var content = $("#SourceEditor").get(0).getText().replace(' ',' ');

			$(button).disabled();

			$.post(
				zotop.url.current(),
				{
					'source':content,
					'_FORMHASH':'<?php echo form::hash();?>'
				},
				function(data){
					msg = zotop.msg.parse(data);
					
					zotop.msg.show(msg);

					$(button).disabled(false);

					return true;
					
				}
			)
			return false;
		}

		$(function(){
			$(document).bind('keydown', 'ctrl+s', save);
		})
	</script>
</div>
<?php $this->footer();?>