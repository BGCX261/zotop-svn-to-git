<?php
$this->header();
$this->top();
$this->navbar();
?>
<script type="text/javascript">
	function content_postform_submit(type){
		var $form = $('form');
		var action = $form.attr('action');
		switch(type){
			case 'saveback':
				action = zotop.url.join(action,"return=<?php echo zotop::url('content/content/index/'.$data['categoryid']);?>");
				break;
			case 'savedraft':
				action = zotop.url.join(action,"status=-50");
				break;
		}
		//alert(action);
		$form.attr('action',action);
		$form.submit();
		return false;
	}

	$(function(){
		$('#saveback').click(function(){
			content_postform_submit('saveback');
		});
		$('#savedraft').click(function(){
			content_postform_submit('savedraft');
		});			
	});
</script>
<style type="text/css">
table.field td.field-side{}
</style>
<?php
form::header(array('action'=>zotop::url('content/content/save')));
	

	form::field(array(
		'type'=>'hidden',
		'name'=>'id',
		'label'=>'编号',
		'value'=>$data['id'],
		'description'=>''
	));	
	form::field(array(
		'type'=>'hidden',
		'name'=>'categoryid',
		'label'=>'栏目编号',
		'value'=>$data['categoryid'],
		'description'=>''
	));	
	form::field(array(
		'type'=>'hidden',
		'name'=>'modelid',
		'label'=>'模型编号',
		'value'=>$data['modelid'],
		'description'=>''
	));	


	foreach($fields as $field)
	{
		form::field($field);
	}

	form::field(array(
		'type'=>'template',
		'name'=>'template',
		'label'=>'模板设置',
		'value'=>$data['template'],
		'valid'=>'',
	));



	form::field(array(
		'type'=>'time',
		'name'=>'createtime',
		'label'=>'发布时间',
		'value'=>$data['createtime'],
		'valid'=>'',
	));

	form::field(array(
		'type'=>'radio',
		'options'=>array(-50=>zotop::t('草稿'),0=>zotop::t('等待审核'),1=>zotop::t('通过审核'),100=>zotop::t('发布')),
		'name'=>'status',
		'label'=>'默认状态',
		'value'=>$data['status'],
		'valid'=>'',
	));
	
	form::field(array(
		'type'=>'radio',
		'options'=>array(0=>zotop::t('允许'),-1=>zotop::t('不允许')),
		'name'=>'comment',
		'label'=>'评论',
		'value'=>((int)$data['comment'] >= 0 ? 0 : -1),
		'valid'=>'',
	));



	form::buttons(		
		array('type'=>'submit','id'=>'submit','value'=>zotop::t('保存')),		
		array('type'=>'submit','id'=>'savedraft','value'=>zotop::t('保存为草稿')),
		array('type'=>'submit','id'=>'saveback','value'=>zotop::t('保存并返回'))
	);
	
	form::footer();

$this->bottom();
$this->footer();
?>