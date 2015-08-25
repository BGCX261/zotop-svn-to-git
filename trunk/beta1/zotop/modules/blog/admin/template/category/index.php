<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<script type="text/javascript">
	
	//判断是否对页面进行了改变
	if( location.href.indexOf('hash') < 0 ){
		dialog.onClose = function(){
			dialog.opener.location.reload();
		}
	}
	
	//表单返回结果处理
	zotop.form.callback = function(msg){
		if( msg.type == 'success' &&  msg.url ){
			window.location.href = msg.url;
		}else{
			//$('form').submiting(true);
			$('form :submit').removeClass("disabled").removeClass("loading").disabled(false);
		}		
		zotop.msg.show(msg);
		return false;		
	}
</script>
<style type="text/css">
body.dialog{
	min-width:650px;
	width:650px;	
}
body.dialog form .form-body{
	height:300px;
	width:100%;
	overflow-y:auto;
	overflow-x:hidden;
}
table.list .extra{
	display:none;
	_display:block;
}
</style>
<?php
form::header();

	$column = array();
	$column['w30 center'] = '编号';
	$column['title'] = '标题';
	$column['manage edit'] = '编辑';
	$column['manage delete'] = '删除';
	$column['extra w10'] = '';
	table::header('list sortable',$column);

	foreach($dataset['data'] as $row)
	{
		$column = array();
		$column['w30 center'] = $row['id'];
		$column['title'] = '<input name="id[]" type="hidden" value="'.$row['id'].'"/><b>'.$row['title'].'</b><h5>'.$row['description'].'</h5>';
		$column['manage edit'] = '<a href="'.zotop::url('blog/category/edit/'.$row['id']).'" id="category'.$row['id'].'" class="dialog">修改</a>';
		$column['manage delete'] = '<a href="'.zotop::url('blog/category/delete/'.$row['id']).'" class="confirm">删除</a>';
		$column['extra w10'] = '';
		table::row($column);
	}
	table::footer();

form::buttons(
	array('type'=>'submit','value'=>'保存排序'),
	array('type'=>'button','value'=>'关闭','class'=>'zotop-dialog-close')
);
form::footer('<span class="zotop-icon zotop-icon-notice"></span>拖动并保存，改变顺序');
?>
<?php $this->bottom()?>
<?php $this->footer();?>