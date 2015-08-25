<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php

	$column = array();
	$column['status w30 center'] = '状态';
	$column['id w30 center'] = '编号';
	$column['usergroupname'] = '用户组名称';
	$column['manage p'] = '权限设定';
	$column['manage status'] = '状态';
	$column['manage edit'] = '编辑';
	$column['manage delete'] = '删除';

	table::header('list',$column);
	foreach($usergroups as $usergroup)
	{
		$column = array();
		$column['status w30 center'] = $usergroup['status'] == -1 ? '<span class="zotop-icon zotop-icon-lock"></span>' : '<span class="zotop-icon zotop-icon-ok"></span>';
		$column['id w30 center'] = $usergroup['id'];
		$column['usergroupname'] = '<a><b>'.$usergroup['title'].'</b></a><h5>'.$usergroup['description'].'</h5>';
		$column['manage p'] = '<a href="'.zotop::url('system/usergroup/edit/'.$usergroup['id']).'">权限设定</a>';
		if( $usergroup['status'] == -1 )
		{
			$column['manage status'] = '<a class="confirm" href="'.zotop::url('system/usergroup/status/'.$usergroup['id'].'/0').'">解锁</a>';
		}
		else
		{
			$column['manage status'] = '<a class="confirm" href="'.zotop::url('system/usergroup/status/'.$usergroup['id']).'">锁定</a>';
		}
		$column['manage edit'] = '<a href="'.zotop::url('system/usergroup/edit/'.$usergroup['id']).'">编辑</a>';
		$column['manage delete'] = '<a href="'.zotop::url('system/usergroup/delete/'.$usergroup['id']).'" class="confirm">删除</a>';
		table::row($column);
	}
	table::footer();

?>

<?php
$this->bottom();
$this->footer();
?>