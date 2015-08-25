<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php

echo html::msg('<h2>什么是模块（module）？</h2><div>模块是对系统现有功能的扩展。如：内容发布用的内容管理模块(content)，会员系统(member)，日志模块(blog)等，获取最新模块请登陆官方网站</div>');

block::header('系统模块列表');
if(empty($modules))
{
	echo('<div class="zotop-empty"><span>暂时没有数据</span></div>');
}
else
{
	$column = array();
	$column['status w30 center'] = '状态';
	$column['logo w40 center'] = '标识';
	$column['name'] = '模块名称 ( 模块ID )';
	//$column['path'] = '安装目录';
	$column['varsion w60 center'] = '版本号';
	$column['updatetime'] = '更新时间';
	$column['manage rename'] = '设置';
	$column['manage edit'] = '权限';
	$column['manage lock'] = '禁用';
	$column['manage delete'] = '卸载';

	table::header('list',$column);
	foreach($modules as $module)
	{
		
		$column = array();
		$column['status w30 center'] = $module['status'] == -1 ? '<span class="zotop-icon zotop-icon-lock"></span>' : '<span class="zotop-icon zotop-icon-ok"></span>';
		$column['logo center'] = html::image(empty($module['icon']) ? '$theme/image/skin/none.png' : $module['icon'], array('width'=>'32px'));
		$column['name'] = '<a class="dialog" href="'.zotop::url('zotop/module/about',array('id'=>$module['id'])).'"><b>'.$module['name'].' ( '.$module['id'].' )</b></a><h5>'.$module['description'].'</h5>';
		
		//$column['loginnum w60'] = $module['path'];
		$column['loginip w60 center'] = $module['version'];        	
		$column['logintime w130'] = time::format($module['updatetime']);

		$column['manage setting'] = file::exists($module['path'].DS.'admin'.DS.'setting.php') ? '<a href="'.zotop::url($module['id'].'/setting').'">设置</a>' : '<span class="disabled">设置</span>';
		$column['manage priv'] = file::exists($module['path'].DS.'admin'.DS.'priv.php') ? '<a href="'.zotop::url($module['id'].'/priv').'">权限</a>' : '<span class="disabled">权限</span>';
		if( $module['type'] == 'core' )
		{
			$column['manage lock'] = '<span class="disabled">禁用</span>';
		}
		else
		{
			if( $module['status'] == -1 )
			{
				$column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/module/lock',array('id'=>$module['id'],'status'=>0)).'">启用</a>';
			}
			else
			{
				$column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/module/lock',array('id'=>$module['id'])).'">禁用</a>';
			}
		}

		$column['manage delete'] = $module['type'] == 'system' ? '<span class="disabled">卸载</span>' : '<a class="confirm" href="'.zotop::url('zotop/module/uninstall',array('id'=>$module['id'])).'">卸载</a>';
		table::row($column);
	}
	table::footer();
}
block::footer();

?>

<?php
$this->bottom();
$this->footer();
?>