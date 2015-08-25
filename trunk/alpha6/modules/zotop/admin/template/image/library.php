<?php $this->header(); ?>
<script type="text/javascript">
	$(function(){
		$('#insert').click(function(){
			var img = $('#image').val();
			if (img){
				callback(img);
			}else{
				zotop.msg.error('请选择要插入的图片');
			}
			
		});
	})

	//图片预览
	$(function(){
		$('.image').zoomImage(130,100,true);

		//设置默认图片
		selectImage();
		
		//选择图片
		$('ul.imagelist li').click(function(){
			$(this).closest('ul').find('li').removeClass('select');
			$(this).addClass('select');
			selectImage();
		})
	});

	function selectImage()
	{
		var img = $('ul.imagelist li.select input').val() || '';
		$('#image').val(img);
	}

</script>
<style type="text/css">
	body.dialog{min-width:650px;width:650px;}
	div.area{position:relative;text-align:center;width:100%;height:300px;overflow:auto;padding-top:20px;}
	ul.imagelist{float:left;margin-left:20px;_margin-left:10px;}
	ul.imagelist li{margin:8px;float:left;}
	div.pagination{background:#f7f7f7;}
</style>
<?php $this->navbar(); ?>
<div class="area">
	<ul class="imagelist clearfix">
	<?php foreach($images as $img){?>		
		<li<?php if ($image == $img['path']) echo ' class="select"';?>>
			<?php echo field::get('hidden',array('name'=>'image','value'=>$img['path'] ))?>
			<?php echo field::get('hidden',array('name'=>'thumb','value'=>$img['path'] ))?>
			<div class="image" title="名称：<?php echo $img['name'] ?>&#13宽高：<?php echo $img['width'].' px × '.$img['height'].' px'?>&#13路径：<?php echo $img['path'] ?>"><?php echo html::image($img['path'])?><span class="zotop-icon zotop-icon-ok"></span></div>
			<div class="title">
				<a href="<?php echo zotop::url('zotop/image/preview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($img['path'])))?>">预览</a>
				<a href="<?php echo zotop::url('zotop/image/edit',array('image'=>url::encode($img['path'])))?>" class="dialog">编辑</a>
				<a href="<?php echo zotop::url('zotop/image/delete',array('image'=>url::encode($img['path']),'referer'=>url::encode(url::location())))?>" class="confirm">删除</a>
			</div>
		</li>
	<?php }?>
	</ul>
</div>
<div class="clearfix"><?php echo $pagination?></div>
<div class="buttons">
	<?php echo field::get('hidden',array('id'=>'image','value'=>$image ))?>
	<?php echo field::get('button',array('id'=>'insert','value'=>'插入图片'))?>
	<?php echo field::get('button',array('id'=>'close','value'=>'关闭','class'=>'zotop-dialog-close'))?>
</div>
<?php $this->footer(); ?>