<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<script type="text/javascript">
$(function(){
	$('.ok').click(function(){
		var id = $('input[name=id]:checked').val();
		var title = $('input[name=id]:checked').next().text();
		dialog.callback(id,title);
		dialog.close();
	});
});
</script>
<style type="text/css">
body.dialog {}
body.dialog .form-body{height:300px;overflow:auto;border:solid 1px #ebebeb;margin:5px;background:#fff;}
body.dialog .treeview{padding:10px;}
</style>
<?php

form::header(array('icon'=>'category','title'=>zotop::t('选择栏目'),'description'=>zotop::t('选择栏目并确定')));

	form::field('<div class="tree">');
	form::field('<div class="tree-root"><input type="radio" name="id" id="id_0" value="0" checked> <label for="id_0"><span class="zotop-icon"></span>根目录</label></div>');
	form::field($tree);
	form::field('</div>');

	form::buttons(
		array('type'=>'button','value'=>'确定','class'=>'ok'),
		array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
	);
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>