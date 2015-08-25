<?php
$this->header();
$this->top();
$this->navbar();
?>
<script type="text/javascript">

</script>
<style type="text/css">
div.navbar ul li.current a{background:#fff;}

.grid-m-s{*float:left;}
.grid-m-s .col-main .col-main-inner{margin-right:330px;}
.grid-m-s .col-sub{margin-left:-320px;width:320px;}

#module {float:left;width:100%;padding:20px 0px;margin-bottom:10px;display:;background-color:#fff;clear:both;}
#module #icon{float:left;width:80px;text-align:center}
#module #info{margin-left:80px;}
#module #title{font-size:14px;font-weight:bold;}

ul.dashboard li{line-height:22px;}


</style>
<div id="module" class="system clearfix">
	<div id="icon"><span class="image"><?php echo html::image($module['icon'],array('width'=>'42px')); ?></span></div>
	<div id="info">
		<div id="title"><?php echo $module['title']?> <span id="version">v<?php echo $module['version']?></span></div>
		<div id="description"><?php echo $module['description']?></div>
	</div>
</div>

<div class="grid-m-s clearfix">
	<div class="col-main">
	<div class="col-main-inner">
		<?php
			//box::header('快捷操作');
			table::header();

			foreach( $menus as $id=>$menu )
			{
				$column = array();
				$column['icon w30 center'] = '<div class="zotop-icon zotop-icon-folder"></div>';
				$column['title'] = '<b class="title"><a href="'.$menu['url'].'">'.$menu['title'].'</a></b><h5 class="description">'.$menu['description'].'</h5>';

				table::row($column);
			}

			table::footer();
			//box::footer();
		?>
	</div>
	</div>
	<div class="col-sub">
		<?php //box::header('关于');?>
		<table class="table">
			<tr><td colspan="2"><h2><?php echo $module['name'] ?></h2></td></tr>
			<tr><td colspan="2"><?php echo $module['description'] ?></td></tr>
			<tr><td class="w80">模块版本：</td><td><?php echo $module['version'] ?></td></tr>
			<tr><td class="w80">程序开发：</td><td><?php echo $module['author'] ?></td></tr>
			<tr><td class="w80">官方网站：</td><td><a href="<?php echo $module['homepage'] ?>" target="_blank"><?php echo $module['homepage'] ?></a></td></tr>
			<tr><td class="w80">安装时间：</td><td><?php echo time::format($module['installtime']) ?></td></tr>
		</table>
		<?php //box::footer();?>
	</div>
</div>
<?php
$this->bottom();
$this->footer();
?>