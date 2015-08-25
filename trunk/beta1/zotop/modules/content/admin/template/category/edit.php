<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<script type="text/javascript">
$(function(){
	var model_url = "<?php echo zotop::url('content/model/json/__modelid__')?>";
	
	$('select[name=modelid]').change(function(){		
		var modelid = $(this).val();
		if( modelid )
		{
			zotop.loading.show('正在加载模型数据……');
			var url = model_url.replace(/__modelid__/i, modelid);
			$.get(url,function(model){
				model = model ||{};
				var settings = eval('('+model.settings+')');

				$('#template_index').val(settings.template_index);
				$('#template_list').val(settings.template_list);
				$('#template_detail').val(settings.template_detail);
				$('#template_print').val(settings.template_print);
				zotop.loading.hide();
			},'json');
		}else{
				$('#template_index').val('');
				$('#template_list').val('');
				$('#template_detail').val('');
				$('#template_print').val('');		
		}
	});

	$('input[name=title]').blur(function(){
		var title = $(this).val();
		if( title ){
			$('input[name=settings[meta_title]]').val(title);
			$('input[name=settings[meta_keywords]]').val(title);
		}
	});

});

zotop.form.callback = function(msg){
	if( msg.type == 'success' ){
		if ( msg.url ) {
			dialog.opener.location.href = dialog.opener.location.href;
			zotop.msg.show(msg);
			dialog.close();		
			return true;
		}
		else
		{
			$('form :submit').removeClass("disabled").removeClass("loading").disabled(false);
		}

	}
	zotop.msg.show(msg);
	return false;
}

</script>
<style type="text/css">
body.dialog {width:700px;}
body.dialog .form-body{height:400px;overflow:auto;}
</style>
<?php

form::header(array('icon'=>'category','title'=>zotop::t('编辑栏目'),'description'=>zotop::t('编辑栏目的名称，模型及相关属性')));

	box::header('基本信息');

	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('栏目编号'),
	   'name'=>'id',
	   'value'=>$data['id'],
	   'valid'=>'',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('上级栏目'),
	   'name'=>'parentid',
	   'value'=>$data['parentid'],
	));

	form::field(array(
	   'type'=>'disabled',
	   'label'=>zotop::t('上级栏目'),
	   'name'=>'parent_title',
	   'value'=>$data['parent_title'],
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('栏目名称'),
	   'name'=>'title',
	   'value'=>$data['title'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));


	form::field(array(
	   'type'=>'textarea',
	   'label'=>zotop::t('栏目说明'),
	   'name'=>'description',
	   'value'=>$data['description'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'select',
	   'options'=>array(''=>zotop::t('请选择栏目模型')) + arr::hashmap($models,'id','name'),
	   'label'=>zotop::t('栏目模型'),
	   'name'=>'modelid',
	   'class'=>'box',
	   'valid'=>'required:true',
	   'value'=>$data['modelid'],
	));	
	
	box::footer();

	box::header('模板设置');
	
	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('首页模板'),
	   'name'=>'settings[template_index]',
	   'id'=>'template_index',
	   'value'=>$data['settings']['template_index'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('列表页面模板'),
	   'name'=>'settings[template_list]',
	   'value'=>$data['settings']['template_list'],
	   'id'=>'template_list',
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('详细页面模板'),
	   'name'=>'settings[template_detail]',
	   'id'=>'template_detail',
	   'value'=>$data['settings']['template_detail'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'template',
	   'label'=>zotop::t('打印页面模板'),
	   'name'=>'settings[template_print]',
	   'id'=>'template_print',
	   'value'=>$data['settings']['template_print'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	box::footer();

	box::header('搜索优化');
	
	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('栏目标题'),
	   'name'=>'settings[meta_title]',
	   'value'=>$data['settings']['meta_title'],
	   'description'=>zotop::t('针对搜索引擎设置的标题(meta title)'),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('栏目关键词'),
	   'name'=>'settings[meta_keywords]',
	   'value'=>$data['settings']['meta_keywords'],
	   'description'=>zotop::t('针对搜索引擎设置的关键词(meta keywords)'),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('栏目描述'),
	   'name'=>'settings[meta_description]',
	   'value'=>$data['settings']['meta_description'],
	   'description'=>zotop::t('针对搜索引擎设置的网页描述(meta description)'),
	));

	box::footer();

	form::buttons(
		array('type'=>'submit','value'=>'保存'),
		array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
	);
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>