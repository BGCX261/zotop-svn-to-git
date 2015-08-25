<?php $this->header();?>
<?php $this->top();?>
<?php $this->navbar();?>
<div id="system" class="system clearfix" style="padding:10px 20px;margin-top:3px;">
	<h2 style="padding-top:10px;font-size:16px;"><?php echo zotop::config('zotop.title')?></h2>
	<h5 style=""><?php echo zotop::config('zotop.name').' ('.zotop::config('zotop.version').') ' ?></h5>
</div>
<form class="form">
<div style="padding:4px 15px;">
	<table class="table">
		<tr><td colspan="2"><?php echo zotop::config('zotop.copyright') ?></td></tr>
		<tr><td class="w80">程序设计：</td><td><?php echo zotop::config('zotop.author') ?></td></tr>
		<tr><td class="w80">程序开发：</td><td><?php echo zotop::config('zotop.authors') ?></td></tr>
		<tr><td class="w80">官方网站：</td><td><a href="<?php echo zotop::config('zotop.homepage') ?>" target="_blank"><?php echo zotop::config('zotop.homepage') ?></a></td></tr>
		<tr><td class="w80">安装时间：</td><td><?php echo zotop::config('zotop.installed') ?></td></tr>
	</table>
</div>
</form>
<?php $this->bottom(); ?>
<?php $this->footer(); ?>