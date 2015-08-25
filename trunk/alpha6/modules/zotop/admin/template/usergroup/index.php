<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php
if(empty($usergroups))
{
	echo('<div class="zotop-empty"><span>暂时没有数据</span></div>');
}
else
{
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
			$column['manage p'] = '<a href="'.zotop::url('zotop/usergroup/edit',array('id'=>$usergroup['id'])).'">权限设定</a>';
			if( $usergroup['status'] == -1 )
        	{
        	    $column['manage status'] = '<a class="confirm" href="'.zotop::url('zotop/usergroup/status',array('id'=>$usergroup['id'],'status'=>0)).'">解锁</a>';
        	}
        	else
        	{
        	    $column['manage status'] = '<a class="confirm" href="'.zotop::url('zotop/usergroup/status',array('id'=>$usergroup['id'])).'">锁定</a>';
        	}
        	$column['manage edit'] = '<a href="'.zotop::url('zotop/usergroup/edit',array('id'=>$usergroup['id'])).'">编辑</a>';
        	$column['manage delete'] = '<a href="'.zotop::url('zotop/usergroup/delete',array('id'=>$usergroup['id'])).'" class="confirm">删除</a>';
            table::row($column);
        }
        table::footer();
}
?>

<?php
$this->bottom();
$this->footer();
?>