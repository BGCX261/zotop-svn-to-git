<?php $this->header(); ?>
<script type="text/javascript">
	$(function(){
		$('#insert').click(function(){
			callback($('#image').val());
		});
	})

	//图片预览
	$(function(){
		$('#image-preview').zoomImage(500,300,true);
	});

</script>
<?php $this->navbar(); ?>
<div style="text-align:right;padding:3px;">
	<span class="zotop-icon zotop-icon-delete"></span><a href="<?php echo zotop::url('zotop/image/delete',array('image'=>url::encode($image['path']),'referer'=>url::encode(url::location())))?>" class="confirm">删除图片</a>
	<span class="zotop-icon zotop-icon-edit"></span><a href="<?php echo zotop::url('zotop/image/edit',array('image'=>url::encode($image['path'])))?>" class="dialog">编辑图片</a>
</div>

<div id="image-preview" style="margin:10px;text-align:center;height:300px;overflow:hidden;">
<?php echo html::image($image['path'],array('width'=>'100%')); ?>
</div>
<div style="text-align:center;padding:3px;">
	<span>名称：<?php echo $image['name']; ?> 大小：<?php echo format::byte($image['size']); ?> 宽高：<?php echo (int)$image['width']; ?>px × <?php echo (int)$image['height']; ?>px </span>
</div>
<div class="buttons">
	<?php echo field::get('hidden',array('id'=>'thumb','value'=>$image['path'] ))?>
	<?php echo field::get('hidden',array('id'=>'image','value'=>$image['path'] ))?>
	<?php echo field::get('button',array('id'=>'insert','value'=>'插入图片'))?>
	<?php echo field::get('button',array('id'=>'close','value'=>'关闭','class'=>'zotop-dialog-close'))?>
</div>
<?php $this->footer(); ?>