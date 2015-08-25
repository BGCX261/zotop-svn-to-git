<?php
class system_controller extends controller
{
    public function onSide()
    {

        $header['title'] = '系统管理';
		$header['js'][] = url::module().'/admin/js/side.js';
		$header['body']['class'] = 'side';

        page::header($header);

		block::header(array('title'=>'系统工具','class'=>'show'));

		echo '<ul id="applications" class="list">';
		echo '	<li><a href="'.zotop::url('zotop/system/reboot').'" target="mainIframe">系统关闭与重启</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/system/clearcahce').'" target="mainIframe">缓存清理</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/setting').'" target="mainIframe">系统设置</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/database').'" target="mainIframe">数据库备份与还原</a></li>';
		echo '	<li><a href="'.zotop::url('database').'" target="mainIframe">数据库管理</a></li>';
		echo '	<li><a href="'.zotop::url('filemanager').'" target="mainIframe">文件管理</a></li>';		
		echo '</ul>';

		block::footer();

		block::header(array('title'=>'模块管理','class'=>'show'));

		echo '<ul class="list">';
		echo '	<li><a href="'.zotop::url('zotop/module').'" target="mainIframe">模块管理</a></li>';
        echo '	<li><a href="'.zotop::url('zotop/module/add').'" target="mainIframe">模块添加</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/module/install').'" target="mainIframe">模块安装</a></li>';
		echo '</ul>';

		block::footer();
		
		block::header(array('title'=>'管理员设置','class'=>'show'));

		echo '<ul class="list">';
		echo '	<li><a href="'.zotop::url('zotop/user').'" target="mainIframe">系统用户管理</a></li>';
        echo '	<li><a href="'.zotop::url('zotop/usergroup').'" target="mainIframe">系统用户组管理</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/role').'" target="mainIframe">系统角色管理</a></li>';
		echo '</ul>';

		block::footer();

		//echo '<div style="height:600px;"></div>';

		page::footer();
	}
}
?>