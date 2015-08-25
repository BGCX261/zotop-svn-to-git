<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php
//zotop::dump($users);
if(empty($users))
{
	echo('<div class="zotop-empty"><span>暂时没有数据</span></div>');
}
else
{
	//form::header();

	$column = array();
	$column['status w30 center'] = '状态';
	$column['username'] = '账号名称';	
	$column['usergroup'] = '用户组';
	$column['name'] = '姓名';	
	$column['loginnum'] = '登录次数';
	$column['loginip'] = '最后登录IP/登录时间';
	$column['manage lock'] = '锁定';
	$column['manage edit'] = '编辑';
	$column['manage delete'] = '删除';

    table::header('list',$column);
	foreach($users as $user)
	{            
            $column = array();
			$column['status w30 center'] = $user['status'] == -1 ? '<span class="zotop-icon zotop-icon-lock"></span>' : '<span class="zotop-icon zotop-icon-ok"></span>';
            $column['username'] = '<a><b>'.$user['username'].'</b></a><h5>'.$user['email'].'</h5>';			
        	$column['usergroup w100'] = $usergroups[$user['groupid']];
            $column['name w80'] = $user['name'];			
        	$column['loginnum w60'] = $user['loginnum'];
        	$column['loginip w140'] = $user['loginip'].'<h5>'.time::format($user['logintime']).'</h5>';        	
        	
        	if( $user['status'] == -1 )
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('system/user/lock/'.$user['id'].'/0').'">'.zotop::t('解锁').'</a>';
        	}
        	else
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('system/user/lock/'.$user['id']).'">'.zotop::t('锁定').'</a>';
        	}
        	$column['manage edit'] = '<a href="'.zotop::url('system/user/changeinfo/'.$user['id']).'">'.zotop::t('编辑').'</a>';
        	$column['manage delete'] = '<a href="'.zotop::url('system/user/delete/'.$user['id']).'" class="confirm">'.zotop::t('删除').'</a>';
            table::row($column);
		
	}
	table::footer();
	//form::footer();
}
?>

<?php
$this->bottom();
$this->footer();
?>