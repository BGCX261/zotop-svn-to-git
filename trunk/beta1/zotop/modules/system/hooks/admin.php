<?php
//设置默认的uri
zotop::add('zotop.uri','system_default_uri');

function system_default_uri($uri)
{
	if ( empty($uri) ) router::$uri = 'system';
}

//为控制中心增加记事本功能
zotop::add('system.main.side','notepad_box');
function notepad_box()
{
	box::header(array(
		'title'=>'记事本',
		'action'=>'<a class="dialog" href="'.zotop::url('system/notepad/add').'">新建记事</a>|<a class="more" href="'.zotop::url('system/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	box::footer();
}

//为控制中心增加个人收藏夹功能
zotop::add('system.main.main','favorite_box');

function favorite_box()
{
	box::header(array(
		'title'=>'我的收藏夹',
		'action'=>'<a class="dialog" href="'.zotop::url('system/quick/add').'">管理</a>|<a class="more" href="'.zotop::url('system/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	box::footer();
}

//为主框架增加系统设置快捷功能
zotop::add('system.quickbar','system_quickbar_settings');

function system_quickbar_settings()
{
	echo '<a href="'.zotop::url('system/setting').'" target="mainIframe">系统设置</a> <b>|</b> ';
}




//增加template字段，用于选择模板
zotop::add('system.ready','system_field_template');

function system_field_template()
{
	field::set('template','field_template');

	function field_template($attrs)
	{
		$html[] = field::text($attrs);
		$html[] = '<a class="dialog" href="'.zotop::url('system/template/select/'.$attrs['name']).'" title="选择模板"><span class="zotop-icon zotop-icon-template"></span></a>';
		return implode("\n",$html);
	}
}
?>