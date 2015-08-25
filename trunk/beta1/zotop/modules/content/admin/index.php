<?php
class content_controller_index extends controller
{
	public function navbar()
	{
		$navbar = array(
			'index'=>array('id'=>'index','title'=>'首 页','href'=>zotop::url('content')),
			'setting'=>array('id'=>'setting','title'=>'模块设置','href'=>zotop::url('content/setting')),
		);

		$navbar = zotop::filter('content.index.navbar',$navbar);

		return $navbar;
	}

	public function actionIndex()
    {
		$module = zotop::module('content');

		$menus = array(
			array('id'=>'category','icon'=>'','title'=>'内容管理','url'=>zotop::url('content/content'),'description'=>'内容管理，添加，编辑以及删除等操作'),
			array('id'=>'category','icon'=>'','title'=>'栏目管理','url'=>zotop::url('content/category'),'description'=>'栏目分类管理，新建栏目及调整栏目顺序'),
			array('id'=>'category','icon'=>'','title'=>'模型管理','url'=>zotop::url('content/model'),'description'=>'内容模型管理，可以自定义内容模型'),
			array('id'=>'category','icon'=>'','title'=>'模块设置','url'=>zotop::url('content/setting'),'description'=>'内容模块的相关设置'),
		);

        $page = new page();
        $page->set('title', $module['title']);
		//$page->set('description', $module['description']);
		$page->set('navbar', $this->navbar());
		$page->set('module', $module);
		$page->set('menus', $menus);
        $page->display();
    }

	public function actionSide()
	{
		$category = zotop::model('content.category');
		$tree = $category->getTree(0,'<span class="zotop-icon zotop-icon-$icon"></span><a href="'.zotop::url('content/content/index/$id').'" target="mainIframe">$title</a>');

		$page = new side();
		$page->set('title',zotop::t('文件管理'));
		$page->set('tree', $tree);
		$page->display();	
	}
}
?>