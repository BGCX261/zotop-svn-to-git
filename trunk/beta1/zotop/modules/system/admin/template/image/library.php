<?php $this->header(); ?>
<style type="text/css">
	body.dialog{
		width:750px;
	}
	.grid{margin:5px;}
	.grid .col-main .col-main-inner{margin-left:160px;}
	.grid .col-sub{margin-left:-100%;width:148px;}
	
	.grid .col-main-inner{
		border:solid 1px #ebebeb;
		height:420px;
		background:#fff;
	}
	.grid .col-main-inner iframe{
		width:100%;height:100%;
	}
	.grid .col-sub{
		border:solid 1px #ebebeb;
		height:420px;overflow:auto;
		background:#fff;
		padding-left:5px;
	}
</style>
<?php $this->navbar(); ?>
<div class="grid clearfix">
<div class="col-main">
<div class="col-main-inner">
	<iframe src="<?php echo zotop::url('system/image/browser')?>" id="browserIframe" name="browserIframe" scrolling="auto" frameBorder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</div>
</div>
<div class="col-sub">
<div class="tree">
<div class="tree-root"><span class="zotop-icon zotop-icon-folder"></span><a href="<?php echo zotop::url('system/image/browser')?>" target="browserIframe">全部图片</a></div>
<?php echo $folders_tree; ?>
</div>
</div>
</div>
<div class="buttons">
	<?php echo field::get(array('type'=>'button','id'=>'insert','value'=>'插入选择图片'))?>
	<?php echo field::get(array('type'=>'button','id'=>'close','value'=>'关闭','class'=>'zotop-dialog-close'))?>
</div>
<?php $this->footer(); ?>