<?php
//为主导航增加内容管理功能
zotop::add('system.navbar','system_navabr_content');

function system_navabr_content()
{
	?>
	<li><a href="javascript:void(0);" onclick="top.go('<?php echo zotop::url('content/index/side') ?>','<?php echo zotop::url('content/index/index') ?>')"><span>内容管理</span></a></li>
	<?php
}

//link 字段
/*
zotop::add('system.ready','field_linkurl');

function field_linkurl()
{
	field::set('link','linkurl');

	function linkurl($attrs)
	{
		return field::text($attrs).' <span style="white-space:nowrap;"><input type="checkbox" name="link" id="linkurl" value=""/> <label for="linkurl">'.zotop::t('使用转向链接').'</label></span>';
	}
}
*/
?>