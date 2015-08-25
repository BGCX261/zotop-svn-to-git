<?php $this->header();?>
<?php $this->top();?>
<?php $this->navbar();?>
<div  style="border:solid 1px #d5d5d5;padding:0px;margin:5px;background:#fff;float:left;width:600px;line-height:32px;">
	<div id="icon" style="float:left;margin:1px;border:solid 1px #ebebeb;"><div class="zotop-icon" style="background-position:top left;"></div></div><div id="position">background-position:0px 0px;</div>
</div>
<script>
$(function(){
	$('td div').click(function(){
		var position = $(this).attr('position');
		$('#icon').html('<div class="zotop-icon" style="background-position:'+position+'"></div>');
		$('#position').html($(this).attr('title'));
	});
})
</script>
<div class="zotop-icon" style="width:1400px;height:1200px;background-position:top left;margin:5px;">
<table style="border-collapse:collapse;">
<?php for($i=0;$i<32;$i++):?>
<tr>
	<?php for($j=0;$j<32;$j++):?>
	<td style="border:solid 1px #999;width:31px;height:31px;padding:0px;overflow:hidden;" >
		<?php
			$title = '-'.($j*32).'px -'.($i*32).'px;';
		?>
		<div title="background-position:<?php echo $title;?>" position="<?php echo $title;?>" style="height:100%;width:100%;font-size:2px;"></div>
	</td>
	<?php endfor;?>
</tr>
<?php endfor;?>
</table>
</div>
<?php $this->bottom();?>
<?php $this->footer(); ?>