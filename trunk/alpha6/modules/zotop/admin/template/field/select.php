<?php
$this->header();
//$this->top();
//$this->navbar();
?>
<script>
	$(function(){
		$("#type").change(function(){
			var type = $(this).val();
			location.href = "<?php echo zotop::url('zotop/field/select')?>/"+type;
		});
	});

	$(function(){
		$('.zotop-dialog-submit').click(function(){
			var $form = $('.form');
			var dc = new zotop.data.collection();
				dc.add("width",$form.find('#width').val());
				dc.add("height",$form.find('#height').val());
			alert(dc.toJSON());
		})
	})
</script>
<?php
form::header();


	form::field(array(
		'type'=>'select',
		'options'=>$types,
		'name'=>'type',
		'label'=>'控件类型',
		'value'=>$type,
		'valid'=>'{required:true}',
	));


	foreach($controls as $key=>$control)
	{
		if( in_array($key,explode(',',$attrs)) )
		{
			form::field($control);		
		}
	}

	
	form::buttons(
		array('type'=>'button','value'=>'提交','class'=>'zotop-dialog-submit'),
		array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
	);

	form::footer();

//$this->bottom();
$this->footer();
?>