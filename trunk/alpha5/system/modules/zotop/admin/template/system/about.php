<?php $this->header();?>
<?php $this->top();?>

<div style="padding:4px 15px;">
	<table class="table">
		<tr><td class="w80">程序名称：</td><td><?php echo zotop::config('zotop.name').' ('.zotop::config('zotop.version').') ' ?></td></tr>
		<tr><td class="w80">程序设计：</td><td><?php echo zotop::config('zotop.author') ?></td></tr>
		<tr><td class="w80">程序开发：</td><td><?php echo zotop::config('zotop.authors') ?></td></tr>
		<tr><td class="w80">官方网站：</td><td><a href="<?php echo zotop::config('zotop.homepage') ?>" target="_blank"><?php echo zotop::config('zotop.homepage') ?></a></td></tr>
		<tr><td class="w80">安装时间：</td><td><?php echo zotop::config('zotop.install.time') ?></td></tr>
	</table>
</div>
<div class="zotop-toolbar right"><button class="button zotop-dialog-close">关闭</button></div>
<?php $this->bottom();?>
<?php $this->footer(); ?>