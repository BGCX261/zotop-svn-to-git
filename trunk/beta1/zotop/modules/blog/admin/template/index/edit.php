<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<?php

form::header($globalid);

	form::field(array(
	   'type'=>'hidden',
	   'label'=>zotop::t('日志编号'),
	   'name'=>'id',
	   'value'=>$data['id'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'html',
	   'label'=>'',
	   'name'=>'option',
	   'value'=>' '.html::checkbox(array('name'=>'link','label'=>zotop::t('转向链接'),'value'=>'1','checked'=>(bool)$data['link'])).' ',
	));

	form::field(array(
	   'type'=>'title',
	   'label'=>zotop::t('日志标题'),
	   'name'=>'title',
	   'class'=>'long',
	   'value'=>$data['title'],
	   'style'=>$data['style'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'image',
	   'label'=>zotop::t('标题图片'),
	   'name'=>'image',
	   'class'=>'long',
	   'value'=>$data['image'],
	   //'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('转向链接'),
	   'name'=>'url',
	   'class'=>'long',
	   'value'=>$data['url'],
	   'display'=>'none',
	   'description'=>zotop::t('填写此项后，页面将直接跳转到链接地址页面，链接地址必须以<b>http://</b>开头'),
	));	


	form::field(array(
	   'type'=>'editor',
	   'label'=>zotop::t('日志内容'),
	   'name'=>'content',
	   'class'=>'long',
	   'value'=>$data['content'],
	   //'valid'=>'required:true',
	   //'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'textarea',
	   'label'=>zotop::t('日志摘要'),
	   'name'=>'description',
	   'class'=>'long',
	   'value'=>$data['description'],
	   'valid'=>'maxlength:255',
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'keywords',
	   'label'=>zotop::t('日志标签'),
	   'name'=>'keywords',
	   'class'=>'long',
	   'value'=>$data['keywords'],
	   'valid'=>'',
	   'description'=>zotop::t('多个标签请用空格分开'),
	));

	form::field(array(
	   'type'=>'select',
	   'options'=>arr::hashmap($categorys,'id','title'),
	   'label'=>zotop::t('日志类别'),
	   'name'=>'categoryid',
	   'value'=>(int)$data['categoryid'],
	   'valid'=>'required:true',
	   'description'=>zotop::t(''),
	));

	box::header(array('title'=>'<span class="zotop-icon"></span>高级设置','class'=>'collapsed'));

	form::field(array(
	   'type'=>'text',
	   'label'=>zotop::t('日志权重'),
	   'name'=>'order',
	   'value'=>(int)$data['order'],
	   'valid'=>'required:true,number:true',
	   'description'=>zotop::t('日志权重参数，数字较大者排在较前位置'),
	));

	form::field(array(
	   'type'=>'radio',
	   'options'=>$status,
	   'label'=>zotop::t('日志状态'),
	   'name'=>'status',
	   'value'=>(int)$data['status'],
	   'description'=>zotop::t(''),
	));

	form::field(array(
	   'type'=>'radio',
	   'options'=>array(0=>'允许评论',-1=>'禁止评论'),
	   'label'=>zotop::t('日志评论'),
	   'name'=>'comment',
	   'value'=>(int)$data['comment'],
	   'description'=>zotop::t(''),
	));

	box::footer();

	form::buttons(
		array('type'=>'submit','value'=>'保存'),
		array('type'=>'back')
	);

form::footer();
?>
<script type="text/javascript">
//转向链接
$(function(){	
	showlink($("input[name=link]").is(":checked"));
	$("input[name=link]").click(function(){
		showlink($(this).is(":checked"))														 
	})   
})

function showlink(bool){
	if(bool){
		$("textarea[name=content]").parents('.field').hide()
		$("input[name=url]").parents('.field').show().css("color","red")
		$("input[name=url]").removeClass("disabled").disabled(false);
	}else{
		$("textarea[name=content]").parents('.field').show()
		$("input[name=url]").parents('.field').hide()
		$("input[name=url]").addClass("disabled").disabled(true);
	}	
}
</script>
<?php $this->bottom()?>
<?php $this->footer();?>