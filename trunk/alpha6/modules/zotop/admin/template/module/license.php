<?php
$this->header();
?>
<script>
$(function(){
	dialog.setTitle('许可协议');

	$('input[name=agreement]').change(function(){
		value = $(this).val();
		if( value == 1 ){
			$('#next').disabled(false);
		}else{
			$('#next').disabled(true);
		}
	});

	$('#next').click(function(){
		location.href = '<?php echo $next ?>';
	});
})
</script>
<div style="display:block;margin:5px;height:200px;overflow:auto;border:solid 1px #e4e4e4;padding:5px;background:#fff;">
	<?php echo $license ?>
</div>
<div style="padding:5px;">
	<?php echo field::radio(array('name'=>'agreement','options'=>array('1'=>'同意以上协议并安装模块','0'=>'我不同意以上许可协议'),'value'=>'0','class'=>'block'))?>
</div>
<div class="buttons">
	<?php echo field::button(array('id'=>'next','value'=>'下一步','disabled'=>'disabled'))?>
</div>
<?php
$this->footer();
?>