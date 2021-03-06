<?php
class side_controller extends controller
{
    public function onDefault()
    {

        $header['title'] = '控制面板';
		$header['js'][] = url::module().'/admin/js/side.js';
		$header['body']['class'] = 'side';

        page::header($header);

		block::header(array('title'=>'应用列表','action'=>'<a href="#" title="栏目管理">管理</a>'));

		echo '<ul id="applications" class="list">';
        echo '	<li><a href="'.zotop::url('zotop/main').'" target="mainIframe">控制中心</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/test').'" target="mainIframe">表单测试</a></li>';
		echo '	<li><a href="'.zotop::url('database').'" target="mainIframe">数据库管理</a></li>';
		echo '	<li><a href="'.zotop::url('filemanager').'" target="mainIframe">文件管理</a></li>';
		echo '</ul>';

		block::footer();


		block::header(array('title'=>'内容管理','action'=>'<a href="javascript:zotop.frame.side().location.reload();">刷新</a> <a href="#" title="栏目管理">管理</a>'));

		echo '<ul class="list">';
        echo '	<li><a href="'.zotop::url('zotop/main').'" target="mainIframe">控制中心</a></li>';
		echo '	<li><a href="'.zotop::url('zotop/test').'" target="mainIframe">表单测试</a></li>';
		echo '	<li><a href="'.zotop::url('database').'" target="mainIframe">数据库管理</a></li>';
		echo '	<li><a href="'.zotop::url('filemanager').'" target="mainIframe">文件管理</a></li>';
		echo '</ul>';

		block::footer();

		//echo '<div style="height:600px;"></div>';

		page::footer();
	}
}
?>