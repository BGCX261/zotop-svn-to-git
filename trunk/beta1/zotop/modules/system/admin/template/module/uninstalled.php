<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php

echo html::msg(zotop::t('<h2>如何安装新模块？</h2><div>1，安装模块前，请确认将该模块文件夹上传至服务器上的模块目录下面目录下面（{$modules}）</div><div>2，上传完成后，<a class="zotop-reload" href="javascript:location.reload();">刷新本页面</a>，模块将会出现在下面的待安装模块列表中，点击模块后面的 <a>安装</a>，根据系统提示完成模块安装</div>', array('modules'=>ZOTOP_URL_MODULES)));

box::header('待安装模块');

	$column = array();
	$column['logo w40 center'] = '标识';
	$column['name'] = '名称';
	$column['version w50 center'] = '版本号';
	$column['manage edit'] = '安装';
	$column['manage delete'] = '删除';		
	
	table::header('list',$column);

	foreach($modules as $module)
	{
	
		$column = array();
		$column['logo center'] = empty($module['icon']) ? '<div class="zotop-icon zotop-icon-module"></div>' : html::image($module['icon'], array('width'=>'32px'));
		$column['name'] = '<a><b>'.$module['name'].' ( '.$module['id'].' )</b></a><h5>'.$module['description'].'</h5>';
		$column['version center'] = $module['version'];
		$column['manage edit'] = '<a href="'.zotop::url('system/module/install/'.$module['id']).'" class="dialog">'.zotop::t('安装').'</a>';
		$column['manage delete'] = html::a(zotop::url('system/module/delete/'.$module['id']),zotop::t('删除'),array('class'=>'confirm'));   		
		
		
		table::row($column);
	}
	table::footer();

box::footer();

?>

<?php
$this->bottom();
$this->footer();
?>