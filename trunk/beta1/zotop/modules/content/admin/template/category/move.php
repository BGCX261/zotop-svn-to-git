<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<script type="text/javascript">
$(function(){

});
</script>
<style type="text/css">
body.dialog {width:650px;}
body.dialog .form-body{height:300px;overflow:auto;border:solid 1px #ebebeb;margin:5px;background:#fff;}
body.dialog .treeview{padding:10px;}
</style>
<?php

form::header(array('icon'=>'category','title'=>zotop::t('移动栏目'),'description'=>zotop::t('将栏目 <b>{$title}</b> 移动到选择的栏目下',$categories[$id])));

	form::field('<div class="tree">');
	form::field('<div class="tree-root"><input type="radio" name="id" value="0" checked> <span class="zotop-icon"></span>根目录</div>');
	form::field($tree);
	form::field('</div>');

	form::buttons(
		array('type'=>'submit','value'=>'保存'),
		array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
	);
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>