<?php
$this->header();
$this->top();
$this->navbar();
?>
<div id="user" class="clearfix">
	<div id="userface"><span class="image"><img src="<?php echo $this->user['image']?>" /></span></div>
	<div id="userinfo">
	<h2 id="welcome">欢迎您， <?php echo $user['name']?> <span id="sign"><?php echo $user['sign']?></span></h2>
	<div id="login">登录时间：2009-12-11 00:45:22 登录次数：<?php echo $user['loginnum']?> 登录IP：<?php echo $user['loginip']?></div>
	<div id="action"><?php zotop::run('zotop.main.action') ?></div>
	</div>
</div>

<div class="grid-m-s">
<div class="col-main">
<div class="col-main-inner">
<?php zotop::run('zotop.main.main') ?>
</div>
</div>
<div class="col-sub">
<?php zotop::run('zotop.main.side') ?>
<div class="block clearfix ">
	<div class="block-header">
		<h2>网站信息</h2>
		<h3><a class="more" href="<?php echo zotop::url('zotop/site/info') ?>">详细</a></h3>
	</div>
	<div class="block-body clearfix">
		<table class="table">
			<tr>
				<td class="w80">网站名称：</td><td><?php echo zotop::config('site.name') ?></td>
			</tr>
			<tr>
				<td class="w80">空间占用：</td><td><?php echo zotop::config('site.size') ?></td>
			</tr>
			<tr>
				<td class="w80">已上传文件：</td><td><?php echo zotop::config('upload.size') ?></td>
			</tr>
			<tr>
				<td class="w80">数据库大小：</td><td><?php echo zotop::db()->size() ?></td>
			</tr>
		</table>
	</div>
	<div class="block-footer"></div>
</div>
<div class="block clearfix ">
	<div class="block-header">
		<h2>系统信息</h2>
		<h3><a class="more" href="<?php echo zotop::url('zotop/system/info') ?>">详细</a></h3>
	</div>
	<div class="block-body clearfix">
		<table class="table">
			<tr><td class="w80">程序名称：</td><td><?php echo zotop::config('zotop.name') ?></td></tr>
			<tr><td class="w80">程序设计：</td><td><?php echo zotop::config('zotop.author') ?></td></tr>
			<tr><td class="w80">程序开发：</td><td><?php echo zotop::config('zotop.authors') ?></td></tr>
			<tr><td class="w80">官方网站：</td><td><a href="<?php echo zotop::config('zotop.homepage') ?>" target="_blank"><?php echo zotop::config('zotop.homepage') ?></a></td></tr>
			<tr><td class="w80">安装时间：</td><td><?php echo zotop::config('zotop.install.time') ?></td></tr>
		</table>
	</div>
	<div class="block-footer"></div>
</div>

</div>
</div>
<?php
$this->bottom('<span class="zotop-tip">上次登录时间：'.time::format($this->user['logintime']).'</span>');
$this->footer();
?>