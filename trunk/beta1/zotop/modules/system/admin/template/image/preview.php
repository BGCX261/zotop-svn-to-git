<?php $this->header(); ?>
<script type="text/javascript">
$(function(){
	$('#image-preview').zoomImage(600,300);
})
</script>
<div style="text-align:right;padding:3px;display:none;">
	<span class="zotop-icon zotop-icon-delete"></span><a href="<?php echo zotop::url('system/image/delete/'.$image['id'])?>" class="confirm">删除图片</a>
	<span class="zotop-icon zotop-icon-edit"></span><a href="#" class="dialog">编辑图片</a>
</div>

<div id="image-preview" style="margin:10px;text-align:center;height:300px;overflow:hidden;">
<?php echo html::image($image['path'],array('style'=>'display:none'))?>
</div>
<div style="text-align:center;padding:3px;">
	<span>
		名称：<span id="image-name"><?php echo $image['name']?></span> 
		大小：<span id="image-size"><?php echo $image['size']?></span>  
		宽高：<span id="image-width"><?php echo $image['width']?></span> px × <span id="image-height"><?php echo $image['height']?></span> px </span>
</div>
<div class="buttons">
	<?php echo field::get(array('type'=>'button','id'=>'close','value'=>'关闭','class'=>'zotop-dialog-close'))?>
</div>
<?php $this->footer(); ?>