<?php $this->header(); ?>
<script type="text/javascript">

	//图片预览
	$(function(){
		$('.image').zoomImage(80,60,true);
		
		//选择图片
		$('a.insert').click(function(event){
			event.preventDefault();

			if ( parent.dialog.args.type == 'input' )
			{
				parent.callback($(this).attr('href'));
			
			}else{
				var root = "<?php echo rtrim(ZOTOP_URL_ROOT,'/');?>";
				var href = root+'/'+$(this).attr('href');
				var link = root+'/'+$(this).attr('link');
				var title = $(this).attr('title');
				var html = '<span class="image"><a href="'+link+'" target="_blank" title="'+title+'"><img src="'+href+'" alt="'+title+'"/></a></span>';
				parent.callback(html);				
			}
		})
		
		$('ul.images li').mouseover(function(){
			$(this).addClass('mouseover');
		}).mouseout(function(){
			$(this).removeClass('mouseover');
		});
	});

</script>
<style type="text/css">
	ul.images{margin:8px 3px;border:solid 1px #ebebeb;border-bottom:none;}
	ul.images li{float:left;width:100%;text-align:left;position:relative;border-bottom:solid 1px #ebebeb;padding:5px 0px;line-height:20px;}
	ul.images li div.image{
		float:left;width:80px;height:60px;margin:0px 3px;text-align:center;
	}
	ul.images li div.image img{display:none;}
	ul.images li.mouseover{
		background:#f7f7f7;
	}
</style>
<?php $this->navbar(); ?>
<?php if( empty($images) ) : ?>
<div style="margin-top:180px;text-align:center;"><?php echo zotop::t('暂无图片，请先上传图片')?></div>
<?php else :?>
<ul class="images clearfix">
<?php foreach($images as $img) : ?>		
	<li<?php if ($image == $img['path']) echo ' class="select"';?>>
		<div class="image">
		<?php echo html::image($img['path'])?>
		</div>
		<div>名称：<?php echo $img['name'] ?></div>
		<div>宽高：<?php echo $img['width'].' px × '.$img['height'].' px'?></div>
		<div class="manage">
			<a href="<?php echo $img['path'] ?>" link="<?php echo $img['path'] ?>" title="<?php echo empty($img['description']) ? $img['name'] : $img['description'] ?>" class="insert">插入原图</a>	
			<a href="<?php echo zotop::url('system/image/edit/'.$img['id'])?>" class="dialog">预览&编辑</a>
			<a href="<?php echo zotop::url('system/image/delete/'.$img['id'])?>" class="confirm">删除</a>
		</div>
	</li>
	<?php endforeach;?>
	</ul>	
	<div class="clearfix"><?php echo $pagination?></div>
<?php endif;?>
<?php $this->footer(); ?>