<?php $this->header();?>
<?php $this->top();?>
<div id="position">
	现在位置：<a href="<?php echo zotop::url('site://');?>">首页</a> <cite>></cite> <?php echo html::a(zotop::url('blog/list'),zotop::t('博客'))?> <?php echo strlen($blog->category->title)==0 ? '' : '<cite>></cite> '.$blog->category->title?>
</div>
<div class="grid grid-m-s">
	<div class="column-main">
	<div class="column-main-inner">
		<div class="clearfix">
			<div class="list">
				<ul>
				<?php foreach($blogs['data'] as $blog) : ?>
					<li>
						<div class="title">
							<h2><?php echo html::a(zotop::url('blog/'.$blog['id']),$blog['title'],array('style'=>$blog['style']))?></h2>
							<h3>
								类别：<a href="<?php echo zotop::url("blog/list/{$blog['categoryid']}")?>"><?php echo $categorys[$blog['categoryid']]['title'];?></a>
								时间：<?php echo time::format($blog['createtime']);?>
								<?php echo (int)$blog['comment']>=0 ? html::a('',zotop::t('评论').':('.(int)$blog['comment'].')') : '';?>
							</h3>
						</div>						
						<?php if(!empty($blog['description'])):?>
						<div class="summary"><?php echo $blog['description']?></div>
						<?php endif;?>

					</li>
				<?php endforeach;?>
				</ul>
			</div>
			<?php echo $pagination?>
		</div>
	</div>
	</div>

	<div class="column-sub">
	<div class="column-sub-inner">
	<?php box::header(array('title'=>'日志分类'));?>
	<div class="navbarlist">
		<ul>
			<?php foreach($categorys as $c){?>
			<li<?php echo $categoryid==$c['id'] ? ' class="selected"' : '';?>><a class="textflow" href="<?php echo zotop::url('blog/list/'.$c['id'])?>"><span class="zotop-icon zotop-icon-folder"></span><?php echo $c['title']?></a></li>
			<?php }?>
		</ul>
	</div>
	<?php box::footer();?>
	<?php zotop::run('blog.side',$blog)?>
	</div>
	</div>
</div>
<?php $this->bottom()?>
<?php $this->footer();?>