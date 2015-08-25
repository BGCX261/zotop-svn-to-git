<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<?php box::header()?>
<div class="tree">
	<div class="tree-root" style="display:;">
		<a style="float:right;cursor:pointer;font-size:12px;font-weight:normal;" onclick="top.go('<?php echo zotop::url('content/index/side')?>');">刷新</a>
		<span class="zotop-icon zotop-icon-root"></span><a href="<?php echo zotop::url('content/index')?>" target="mainIframe"><b>内容管理</b></a>		
	</div>
	<ul>
		<li><span class="zotop-icon zotop-icon-category"></span><a href="<?php echo zotop::url('content/category')?>" target="mainIframe">栏目管理</a></li>
		<li><span class="zotop-icon zotop-icon-model"></span><a href="<?php echo zotop::url('content/model')?>" target="mainIframe">模型管理</a></li>
		<li><span class="zotop-icon zotop-icon-refresh"></span><a href="<?php echo zotop::url('content/content/html')?>" target="mainIframe">生成HTML</a></li>	
		<li class="open">
			<span class="zotop-icon zotop-icon-folder"></span><a href="<?php echo zotop::url('content/content')?>" target="mainIframe"><span>内容库</span> </a>
			<?php echo $tree; ?>
		</li>
		<li><span class="zotop-icon zotop-icon-waiting"></span><a href="<?php echo zotop::url('content/content/waiting')?>" target="mainIframe">待审核</a></li>
		<li><span class="zotop-icon zotop-icon-draft"></span><a href="<?php echo zotop::url('content/content/draft')?>" target="mainIframe">草稿箱</a></li>
		<li><span class="zotop-icon zotop-icon-recycle"></span><a href="<?php echo zotop::url('content/content/recycle')?>" target="mainIframe">回收站</a></li>
	
	</ul>
</div>
<?php box::footer();?>
<?php box::header()?>
	<form class="smallsearch" target="mainIframe" method="get" action="<?echo zotop::url('content/content/index')?>">
		<input type="text" name="keywords" class="text" value="<?php echo zotop::get('keywords') ?>" title="请输入关键词进行搜索"/>
		<button type="submit"><span class="zotop-icon zotop-icon-search button-icon"></span></button>
	</form>
<?php box::footer();?>
<?php $this->bottom();?>
<?php $this->footer();?>