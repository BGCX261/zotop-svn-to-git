<?php
$this->header();
$this->top();
$this->navbar();
?>
<script type="text/javascript">
$(function(){
	$('#userface').zoomImage(120,80);
});

$(function(){
    $("#userface").hover(function(){$(this).addClass("active")},function(){$(this).removeClass("active");})
})
</script>
<style type="text/css">
.grid-m-s{}
.grid-m-s .col-main .col-main-inner{margin-right:320px;}
.grid-m-s .col-sub{margin-left:-320px;width:320px;}

#user{padding:20px 0px;position:relative;}
#userface{position:relative;float:left;height:100px;width:170px;}
#userface span.image{width:120px;height:120px;display:block;position:absolute;top:5px;left:20px;z-index:100;}
#userface span.image img{border:solid 1px #ebebeb;-moz-border-radius: 4px; -webkit-border-radius: 4px;padding:4px;background:#fff;}
#userface span.image-more{
	display:none;
	left: 13px;
	position: absolute;
	top: 0px;
	z-index: 90;
	width: 450px;
}
#userface.active .image-more{display:block;}
#userface span.image-more div.image-more-main{
	background: white;
	border: 1px solid #d5d5d5;
	overflow: hidden;
	position: absolute;
	width: 360px;height:118px;
	z-index:100;padding: 5px 5px 5px 145px;
}
#userface span.image-more div.image-more-bg{
	background: #999;
	display: block;
	width: 512px;height: 130px;
	opacity: 0.4;
	position: absolute;
	top:3px;left:3px;	
	z-index: 90;
}


#userinfo{float:left;}


</style>
<div id="user" class="clearfix">
	<div id="userface">
		<span class="image"><?php echo html::image($user['image'],array('width'=>'80px')); ?></span>
		<span class="image-more">
			<div class="image-more-bg"></div>
			<div class="image-more-main">
				<h2><?php echo $user['name']?></h2>
				<h4><?php echo $user['sign']?></h4>

				<div id="login">					
					登录次数：<?php echo $user['loginnum']?> 
					登录IP：<?php echo $user['loginip']?>
					登录时间：<?php echo time::format($user['logintime'])?>
				</div>
				<a>修改资料</a> 
				<a>修改密码</a>
				<a>修改图像</a>
			</div>
		</span>
	</div>
	<div id="userinfo">
		<h2 id="welcome">欢迎您，<?php echo $user['name']?> <span id="sign"><?php echo $user['sign']?></span></h2>
		<div id="login">登录时间：<?php echo time::format($user['logintime'])?> 登录次数：<?php echo $user['loginnum']?> 登录IP：<?php echo $user['loginip']?></div>
		<div>
				
				<a href="<?php echo zotop::url('system/mine/changeinfo');?>">修改我的资料</a> 
				<a href="<?php echo zotop::url('system/mine/changepassword');?>">修改我的密码</a>
				<a href="<?php echo zotop::url('system/mine/changeimage');?>">修改我的头像</a>
		</div>
		<div id="action"><?php zotop::run('system.main.action') ?></div>
	</div>
</div>

<div class="grid-m-s clearfix">
<div class="col-main">
<div class="col-main-inner">
<?php zotop::run('system.main.main') ?>
</div>
</div>
<div class="col-sub">
<?php zotop::run('system.main.side') ?>
<div class="box clearfix ">
	<div class="box-header">
		<h2>系统信息</h2>
		<h3><a class="more" href="<?php echo zotop::url('system/system/info') ?>">详细</a></h3>
	</div>
	<div class="box-body clearfix">
		<table class="table">
			<tr><td class="w80">程序名称：</td><td><?php echo zotop::config('zotop.name') ?></td></tr>
			<tr><td class="w80">程序设计：</td><td><?php echo zotop::config('zotop.author') ?></td></tr>
			<tr><td class="w80">程序开发：</td><td><?php echo zotop::config('zotop.authors') ?></td></tr>
			<tr><td class="w80">官方网站：</td><td><a href="<?php echo zotop::config('zotop.homepage') ?>" target="_blank"><?php echo zotop::config('zotop.homepage') ?></a></td></tr>
			<tr><td class="w80">安装时间：</td><td><?php echo zotop::config('zotop.installed') ?></td></tr>
		</table>
	</div>
	<div class="box-footer"></div>
</div>

</div>
</div>
<?php
$this->bottom();
$this->footer();
?>