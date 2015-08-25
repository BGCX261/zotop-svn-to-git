<?php
/**
 * title:栏目详细页面
 * description:通用详细内容页面
*/
?>
<?php $this->header();?>
<?php $this->top();?>
<script type="text/javascript">
$(function(){
	$('div.content').zoomImage(600,500,{valign:false});
})
</script>
<div id="position">
	现在位置：<a href="<?php echo zotop::url('site://');?>">首页</a> <cite>></cite>  <?php echo $content->title?>
</div>
<div class="grid grid-m-s">
	<div class="column-main">
	<div class="column-main-inner">
		<div class="detail">
			<div class="title clearfix">
				<h1 id="title"><?php echo $content->title;?></h1>
				<h2 id="info">作者：<?php echo $content->author;?> 发布时间：<?php echo time::format($content->createtime);?></h2>
			</div>
			<?php if ( $content->summary != '' ) : ?>
			<div class="summary"><?php echo $content->summary;?></div>
			<?php endif;?>
			<div id="content" class="content clearfix">
				<?php echo $content->content;?>
			</div>
		</div>
	</div>
	</div>

	<div class="column-sub">
	<div class="column-sub-inner">
	<?php box::header(array('title'=>'分类'));?>
	<div class="navbarlist">
		<ul>

		</ul>
	</div>
	<?php box::footer();?>
	<?php zotop::run('content.side',$content)?>
	</div>
	</div>
</div>
<?php $this->bottom();?>
<?php $this->footer();?>