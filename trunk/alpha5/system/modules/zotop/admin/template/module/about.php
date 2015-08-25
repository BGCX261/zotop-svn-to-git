<?php
$this->header();
?>
<script>
$(function(){
	dialog.setButtons([{text:'确 定'}]);
})
</script>
<div style="padding:15px;" class="clearfix">
	<div style="float:left;width:100px;text-align:center;padding-top:20px;"><?php echo html::image($module['icon'],array('width'=>'48px'));?></div>
	<div style="margin-left:120px;">
		<table class="table">
		<tr><td class="w80">模块名称：</td><td><b><?php echo $module['name'].' ( '.$module['id'].' )' ?></b></td></tr>
		<tr><td class="w80">模块简介：</td><td><div style="line-height:22px;"><?php echo $module['description'] ?></div></td></tr>
		<tr><td class="w80">模块版本：</td><td><?php echo $module['version'] ?></td></tr>
		<tr><td class="w80">模块设计：</td><td><?php echo $module['author'] ?></td></tr>		
		<tr><td class="w80">模块开发：</td><td><?php echo $module['email'] ?></td></tr>
		<tr><td class="w80">官方网站：</td><td><a href="<?php echo $module['homepage'] ?>" target="_blank"><?php echo $module['homepage'] ?></a></td></tr>
	</table>
	</div>
</div>
<?php
$this->footer();
?>