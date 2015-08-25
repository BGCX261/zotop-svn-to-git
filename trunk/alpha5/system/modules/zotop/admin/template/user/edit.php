<?php
$this->header();
//$this->top();
//$this->navbar();
?>
<script>
	function Reselect(){
		var type = $('#type').val();
		location.href = "<?php echo zotop::url('zotop/config/edit');?>/<?php echo $field['id'];?>/"+type;
	}
</script>
<?php
form::header();

	block::header('控件设置');

	form::field(array(
		'type'=>'select',
		'options'=>$types,
		'name'=>'type',
		'label'=>'控件类型',
		'onchange'=>'Reselect();',
		'value'=>$field['type'],
		'valid'=>'{required:true}',
	));

	foreach($controls as $key=>$control)
	{
		if( in_array($key,explode(',',$attrs)) )
		{			
			$control['value'] = $field['settings'][$key];
			form::field($control);		
		}
	}

	block::footer();
	block::header('节点设置');
	form::field(array(
		'type'=>'text',
		'name'=>'id',
		'label'=>'节点键名',
		'value'=>$field['id'],
		'valid'=>'{required:true,maxlength:64,minlength:3}',
		'description'=>'3到64位，允许使用英文、数字，英文点号和下划线，请勿使用特殊字符'
	));

	form::field(array(
		'type'=>'hidden',
		'name'=>'parentid',
		'label'=>'父节点编号',
		'value'=>$field['parentid'],
		'valid'=>'',
		'description'=>''
	));

	form::field(array(
		'type'=>'text',
		'name'=>'value',
		'label'=>'默认数值',
		'value'=>$field['value'],
		'valid'=>'',
		'description'=>'',
	));

	form::field(array(
		'type'=>'text',
		'name'=>'title',
		'label'=>'节点名称',
		'value'=>$field['title'],
		'valid'=>'{required:true,maxlength:64}',
		'description'=>'请输入节点的标题名称',

	));

	form::field(array(
		'type'=>'text',
		'name'=>'description',
		'label'=>'节点说明',
		'value'=>$field['description'],
		'valid'=>'',
		'description'=>'',
	));

	block::footer();


	
form::footer(array(
    array('type'=>'submit','value'=>'保 存'),
    array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
));

//$this->bottom();
$this->footer();
?>