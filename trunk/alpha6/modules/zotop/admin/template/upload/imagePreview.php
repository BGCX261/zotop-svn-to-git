<?php $this->header(); ?>
<script type="text/javascript">
	$(function(){
		$('#insert').click(function(){
			callback($('#image').val());
		});
	})

	//图片预览
	$(function(){
		$('#preview').zoomImage(500,300,true);
	});

</script>
<?php $this->navbar(); ?>
<div style="text-align:right;padding:3px;">
	<a href="<?php echo zotop::url('zotop/upload/imageDelete',array( 'image'=>$image['id'] ))?>" class="confirm">删除图片</a>
	<a href="#">编辑图片</a>
</div>
<div id="preview" style="margin:10px;text-align:center;height:300px;overflow:hidden;">
<?php echo html::image($image['path'],array('width'=>'100%')); ?>
</div>
<div class="buttons">
	<?php echo field::get('hidden',array('id'=>'id','value'=>$image['id'] ))?>
	<?php echo field::get('hidden',array('id'=>'image','value'=>$image['path'] ))?>
	<?php echo field::get('button',array('id'=>'insert','value'=>'插入图片'))?>
	<?php echo field::get('button',array('id'=>'close','value'=>'关闭','class'=>'zotop-dialog-close'))?>
</div>
<?php $this->footer(); ?>