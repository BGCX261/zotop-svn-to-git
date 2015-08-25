<?php
class blog_controller_default extends controller
{
	public $action = 'show';

	public function actionIndex($id)
	{

	}

	public function actionShow($id)
	{
		$blog = zotop::model('blog.blog');
		$blog->category = zotop::model('blog.category');
		$blog->read($id);
		$blog->category->read($blog->categoryid);
		$categorys = $blog->category->getAll();

		//渲染页面
        $page = new page();		
        $page->set('title',$blog->title.' '.$blog->category->title);
		$page->set('keywords',$blog->keywords.' '.$blog->category->keywords);
		$page->set('description',$blog->description.' '.$blog->category->description);
		$page->set('body',array('class'=>'detail'));
		$page->set('id',$id);
		$page->set('blog', $blog);
		$page->set('categorys', $categorys);
        $page->display('blog.show');	
	}

}
?>