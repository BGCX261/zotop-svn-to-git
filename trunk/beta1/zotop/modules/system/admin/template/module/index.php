<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php

echo html::msg('<h2>什么是模块（module）？</h2><div>模块是对系统现有功能的扩展。如：内容发布用的内容管理模块(content)，会员系统(member)，日志模块(blog)等，获取最新模块请登陆官方网站</div>');

box::header('模块列表');

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
		$column['logo center'] = empty($module['icon']) ? '<div class="zotop-icon zotop-icon-module"></div>' : html::image($module['icon'], array('width'=>'32px'));
		$column['name'] = '<a class="dialog" href="'.zotop::url('system/module/about/'.$module['id']).'"><b>'.$module['name'].' ( '.$module['id'].' )</b></a><h5>'.$module['description'].'</h5>';
		
		//$column['loginnum w60'] = $module['path'];
		$column['loginip w60 center'] = $module['version'];        	
		$column['logintime w130'] = time::format($module['updatetime']);

		$column['manage setting'] = file::exists($module['path'].DS.'admin'.DS.'setting.php') ? '<a href="'.zotop::url($module['id'].'/setting').'">设置</a>' : '<span class="disabled">设置</span>';
		$column['manage priv'] = file::exists($module['path'].DS.'admin'.DS.'priv.php') ? '<a href="'.zotop::url($module['id'].'/priv').'">权限</a>' : '<span class="disabled">权限</span>';
		
		if( $module['type'] == 'core')
		{
			$column['manage lock'] = '<span class="disabled">禁用</span>';
		}
		else
		{
			if( $module['status'] == -1 )
			{
				$column['manage lock'] = '<a class="confirm" href="'.zotop::url('system/module/status/'.$module['id'].'/0').'">启用</a>';
			}
			else
			{
				$column['manage lock'] = '<a class="confirm" href="'.zotop::url('system/module/status/'.$module['id'].'/-1').'">禁用</a>';
			}
		}

		$column['manage delete'] = $module['type'] == 'core' ? '<span class="disabled">卸载</span>' : '<a class="confirm" href="'.zotop::url('system/module/uninstall/'.$module['id']).'">卸载</a>';
		table::row($column);
	}
	table::footer();

box::footer();

?>

<?php
$this->bottom();
$this->footer();
?>