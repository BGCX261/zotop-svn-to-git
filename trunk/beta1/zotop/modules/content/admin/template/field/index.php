<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<style type="text/css">
table.list td.title span.zotop-icon{
	float:left;
	margin:0px 5px;
}
</style>
<?php
form::header();
	
	$column = array();
	
	$column['title'] = '名称';
	$column['type w160'] = '控件类型';
	$column['system w40 center'] = '系统字段';
	$column['type w40 center'] = '字段类型';
	$column['maxlength w40 center'] = '字段长度';
	$column['manage status'] = '状态';
	$column['manage edit'] = '编辑';
	$column['manage delete'] = '删除';

	table::header('list sortable',$column);

	foreach($fields as $row)
	{
		$column = array();
		
		$column['title'] .= '<span class="zotop-icon zotop-icon-status'.(int)$row['status'].'"></span>';
		$column['title'] .= '<input name="id[]" type="hidden" value="'.$row['id'].'"/>';
		$column['title'] .= '<a href="'.zotop::url('content/field/index/'.$row['id']).'"><b>'.$row['label'].'</b></a><h4>'.$row['name'].'</h4>';
		$column['type w160'] = ''.isset($types[$row['field']]) ? $types[$row['field']] : $row['field'].'';
		$column['system w40 center'] = $row['system'] ? '<span class="zotop-icon zotop-icon-status'.$row['system'].'"></span>' : '';
		$column['type w40 center'] = $row['type'];
		$column['maxlength w40 center'] = $row['maxlength']>0 ? $row['maxlength'] : '--';
		$column['manage status'] = (int)$row['status']>0 ? '<a href="'.zotop::url('content/field/status/'.$row['id'].'/0').'" class="confirm">禁用</a>' : '<a href="'.zotop::url('content/field/status/'.$row['id'].'/1').'" class="confirm">启用</a>';
		$column['manage edit'] = '<a href="'.zotop::url('content/field/edit/'.$row['id']).'">修改</a>';
		$column['manage delete'] = (int)$row['system'] ? '<span class="disabled">删除</span>' : '<a href="'.zotop::url('content/field/delete/'.$row['id']).'" class="confirm">删除</a>';
		table::row($column);
	}
	table::footer();

form::buttons(
	array('type'=>'submit','value'=>'保存排序'),
	array('type'=>'back')
);
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>