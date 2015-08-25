<?php $this->header();?>
<?php $this->top();?>
<style type="text/css">
</style>
<script type="text/javascript">
$(function(){
  $('div.content').zoomImage(600,500,{valign:false});
})
</script>
<div id="position">
  现在位置：<a href="<?php echo zotop::url('site://');?>">首页</a> <cite>></cite> <?php echo html::a(zotop::url('blog/list'),zotop::t('博客'))?> <cite>></cite> <?php echo html::a(zotop::url('blog/list/'.$blog->category->id),$blog->category->title)?> <cite>></cite> <?php echo $blog->title?>
</div>
<div class="grid grid-m-s">
  <div class="column-main">
  <div class="column-main-inner">
    <div class="detail">
      <div class="title clearfix">
        <h1 id="title"><?php echo $blog->title;?></h1>
        <h2 id="info">作者：<?php echo $blog->author();?> 发布时间：<?php echo time::format($blog->createtime);?></h2>
      </div>
      <?php if (!empty($blog->description)) : ?>
      <div class="description"><?php echo $blog->description;?></div>
      <?php endif;?>
      <div id="content" class="content clearfix">
        <?php echo $blog->content;?>
      </div>
    </div>
  </div>
  </div>

  <div class="column-sub">
  <div class="column-sub-inner">
  <?php box::header(array('title'=>'分类'));?>
  <div class="navbarlist">
    <ul>
      <?php foreach($categorys as $c){?>
      <li<?php echo $blog->categoryid == $c['id'] ? ' class="selected"' : '';?>><a class="textflow" href="<?php echo zotop::url('blog/list/'.$c['id'])?>"><span class="zotop-icon zotop-icon-folder"></span><?php echo $c['title']?></a></li>
      <?php }?>
    </ul>
  </div>
  <?php box::footer();?>
  <?php zotop::run('blog.side',$blog)?>
  </div>
  </div>
</div>
<?php $this->bottom();?>
<?php $this->footer();?>