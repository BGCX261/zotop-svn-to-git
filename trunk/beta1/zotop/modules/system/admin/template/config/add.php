<?php
$this->header();
?>
<script type="text/javascript">
	$(function(){
		$('select[name=type]').change(function(){
			var type = $(this).val();
			var href = "<?php echo zotop::url('system/config/attrs/__type__');?>";
				href = href.replace(/__type__/i, type);			
				$('#settings').load(href,function(){
					
				});		
		});
	});
</script>
<style type="text/css">
body.dialog .form-body{height:400px;overflow:auto;}
</style>
<?php
form::header(array('icon'=>'file','title'=>'添加控件','description'=>'新建一个控件'));


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
		'name'=>'title',
		'label'=>'节点名称',
		'value'=>$field['title'],
		'valid'=>'{required:true,maxlength:64}',
		'description'=>'请输入节点的标题名称',
	));

	form::field(array(
		'type'=>'select',
		'options'=>$types,
		'name'=>'type',
		'label'=>'控件类型',
		'onchange'=>'reselect();',
		'value'=>$field['type'],
		'valid'=>'{required:true}',
	));

	form::field('<div id="settings">');
	
	foreach($attrs as $attr)
	{
		form::field($attr);
	}

	form::field('</div>');

	form::field(array(
		'type'=>'css',
		'name'=>'settings[class]',
		'label'=>'控件CSS',
		'value'=>$field['settings']['class'],
		'valid'=>'',
		'description'=>'定义表单的CSS样式名',
	));

	form::field(array(
		'type'=>'css',
		'name'=>'settings[style]',
		'label'=>'控件样式',
		'value'=>$field['settings']['style'],
		'valid'=>'',
		'description'=>'定义表单的style样式，如：width:200px;',
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
		'name'=>'valid',
		'label'=>'验证规则',
		'value'=>$field['valid'],
		'valid'=>'',
		'description'=>'节点值有效性验证规则，如：必选字段 <b>required:true,number:true</b>',
	));

	form::field(array(
		'type'=>'text',
		'name'=>'description',
		'label'=>'节点说明',
		'value'=>$field['description'],
		'valid'=>'',
		'description'=>'',
	));


	form::buttons(
		array('type'=>'submit','value'=>'保 存'),
		array('type'=>'button','value'=>'关 闭','class'=>'zotop-dialog-close')	
	);
	
	form::footer();

$this->footer();
?>