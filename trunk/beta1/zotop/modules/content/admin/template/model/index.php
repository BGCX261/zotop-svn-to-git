<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<style type="text/css">
</style>
<?php
form::header();

	$column = array();
	$column['status w30 center'] = '状态';
	$column['title'] = '名称';
	$column['tablename'] = '数据表名称';
	$column['manage field'] = '字段管理';
	$column['manage status'] = '状态';
	$column['manage edit'] = '编辑';
	$column['manage delete'] = '删除';

	table::header('list sortable',$column);

	foreach($models as $row)
	{
		$column = array();
		$column['status w30 center'] = '<input name="id[]" type="hidden" value="'.$row['id'].'"/><span class="zotop-icon zotop-icon-status'.(int)$row['status'].'"></span>';
		$column['title'] .= '<a href="'.zotop::url('content/field/index/'.$row['id']).'"><b>'.$row['name'].'</b></a><h5>'.$row['description'].'</h5>';
		$column['tablename w300'] = $row['tablename'];
		$column['manage field'] = '<a href="'.zotop::url('content/field/index/'.$row['id']).'">字段管理</a>';
		$column['manage status'] = (int)$row['status'] ? '<a href="'.zotop::url('content/model/status/'.$row['id'].'/0').'"  class="confirm">禁用</a>' : '<a href="'.zotop::url('content/model/status/'.$row['id'].'/1').'"  class="confirm">启用</a>';
		$column['manage edit'] = '<a href="'.zotop::url('content/model/edit/'.$row['id']).'">修改</a>';
		$column['manage delete'] = '<a href="'.zotop::url('content/model/delete/'.$row['id']).'" class="confirm">删除</a>';
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