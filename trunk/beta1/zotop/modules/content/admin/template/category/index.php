<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<style type="text/css">


</style>

<?php
form::header();

	$column = array();
	$column['id w30 center'] = '编号';
	$column['w30 status center'] = '状态';	
	$column['title'] = '栏目名称';
	$column['w80 model center'] = '栏目模型';
	$column['w50 type'] = '栏目类型';
	$column['manage add'] = '添加子栏目';
	$column['manage edit'] = '编辑';
	$column['manage move'] = '移动';
	$column['manage delete'] = '删除';
	

	table::header('list sortable',$column);

	foreach($categorys as $row)
	{
		$column = array();
		$column['id w30 center'] = $row['id'].'<input name="id[]" type="hidden" value="'.$row['id'].'"/>';
		$column['w30 status center'] = (int)$row['status']<1 ? 
			'<a href="'.zotop::url('content/category/status/'.$row['id'].'/1').'" class="confirm" title="启用该栏目"><span class="zotop-icon zotop-icon-status-1"></span></a>' 
			:
			'<a href="'.zotop::url('content/category/status/'.$row['id'].'/-1').'" class="confirm" title="禁用该栏目"><span class="zotop-icon zotop-icon-status1"></span></a>';
		$column['title'] .= '<a href="'.zotop::url('content/category/index/'.$row['id']).'"><b>'.$row['title'].'</b></a>';
		$column['title'] .= '<div class="manage">';
		$column['title'] .= '	<a href="'.zotop::url('content/category/index/'.$row['id']).'">访问子栏目</a>';
		$column['title'] .= '	';
		$column['title'] .='</div>';
		$column['w80 model center'] = isset($models[$row['modelid']]) ? $models[$row['modelid']]['name'] : '---';
		$column['w50 type'] = $types[(int)$row['type']];
		$column['manage add'] = '<a href="'.zotop::url('content/category/add/'.$row['id']).'"  class="dialog">添加子栏目</a>';
		$column['manage edit'] = '<a href="'.zotop::url('content/category/edit/'.$row['id']).'"  class="dialog">编辑</a>';
		$column['manage move'] = '<a href="'.zotop::url('content/category/move/'.$row['id']).'"  class="dialog">移动</a>';
		$column['manage delete'] = '<a href="'.zotop::url('content/category/delete/'.$row['id']).'" class="confirm">删除</a>';
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