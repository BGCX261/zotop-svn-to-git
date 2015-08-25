<?php
$this->header();
$this->top();
$this->navbar();
?>
<script type="text/javascript">
$(function(){
	$('#userface').zoomImage(80,80);
});
</script>
<style type="text/css">
.grid-m-s{}
.grid-m-s .col-main .col-main-inner{margin-right:320px;}
.grid-m-s .col-sub{margin-left:-320px;width:320px;}

#user{padding:20px 0px;position:relative;}
#userface{float:left;}
#userface span.image{width:80px;height:80px;/*overflow:hidden;text-align:center;*/display:block;margin:0px 30px;}
#userface span.image img{border:solid 1px #ebebeb;-moz-border-radius: 4px; -webkit-border-radius: 4px;padding:4px;background:#fff;}
#userinfo{float:left;}
</style>
<div id="user" class="clearfix">
	<div id="userface"><span class="image"><?php echo html::image($user['image'],array('width'=>'80px')); ?></span></div>
	<div id="userinfo">
		<h2 id="welcome">欢迎您，<?php echo $user['name']?> <span id="sign"><?php echo $user['sign']?></span></h2>
		<div id="login">登录时间：<?php echo time::format($user['logintime'])?> 登录次数：<?php echo $user['loginnum']?> 登录IP：<?php echo $user['loginip']?></div>
		<div id="action"><?php zotop::run('zotop.main.action') ?></div>
	</div>
</div>

<div class="grid-m-s clearfix">
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
$this->bottom();
$this->footer();
?>