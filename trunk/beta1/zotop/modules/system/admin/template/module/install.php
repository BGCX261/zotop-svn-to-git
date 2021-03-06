<?php
$this->header();
?>
<script>
$(function(){
	dialog.setTitle('模块安装');
})
</script>
<?php 
	form::header();
?>
<div style="padding:15px;" class="clearfix">
	<div style="float:left;width:100px;text-align:center;padding-top:20px;"><?php echo (empty($module['icon']) ? '<div class="zotop-icon zotop-icon-module"></div>' : html::image($module['icon'], array('width'=>'48px')));?></div>
	<div class="clearfix" style="margin-left:120px;">
		<table class="table">
		<tr><td colspan="2"><b><?php echo $module['name'].' ( '.$module['id'].' )' ?></b><div style="line-height:22px;padding:5px 0px;"><?php echo $module['description'] ?></div></td></tr>
		<tr><td class="w80">模块版本：</td><td><?php echo $module['version'] ?></td></tr>
		<tr><td class="w80">模块设计：</td><td><?php echo $module['author'] ?></td></tr>		
		<tr><td class="w80">模块开发：</td><td><?php echo $module['email'] ?></td></tr>
		<tr><td class="w80">官方网站：</td><td><a href="<?php echo $module['homepage'] ?>" target="_blank"><?php echo $module['homepage'] ?></a></td></tr>
	</table>
	</div>
</div>
<?php
	form::buttons(
		array('type'=>'submit','value'=>'安 装'),
		array('type'=>'button','value'=>'取 消','class'=>'zotop-dialog-close' )
	);

	form::footer();
?>
<?php
$this->footer();
?>