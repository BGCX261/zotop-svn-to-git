<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>

<?php box::header()?>
<div class="tree">
	<div class="tree-root">
		<span class="zotop-icon zotop-icon-root"></span><a href="<?php echo zotop::url('system/file')?>" target="mainIframe"><span>文件管理</span></a>
	</div>
	<ul>
			<li><?php echo '<span class="zotop-icon zotop-icon-cagetory"></span><a href="'.zotop::url('system/folder').'" target="mainIframe">'.zotop::t('分类管理').'</a>'?></li>

		<li class="open">
			<span class="zotop-icon zotop-icon-folder"></span><a href="<?php echo zotop::url('system/file')?>" target="mainIframe"><span>文件库</span></a>
			<?php echo $folders_tree; ?>
		</li>
		<li><?php echo '<span class="zotop-icon zotop-icon-cagetory"></span><a href="'.zotop::url('system/folder').'" target="mainIframe">'.zotop::t('分类管理').'</a>'?></li>
	</ul>
</div>
<?php box::footer();?>
<?php box::header('<span class="zotop-icon zotop-icon-search"></span>'.'文件搜索')?>
<?php box::footer();?>
<?php $this->bottom();?>
<?php $this->footer();?>