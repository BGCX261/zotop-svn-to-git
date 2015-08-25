<?php
class blog_controller_index extends controller
{
	public function actionIndex($categoryid=0,$page=1)
	{
		$blog = zotop::model('blog.blog');
		$blog->category = zotop::model('blog.category');
		

		$blogs = $blog->getPage($categoryid,100,$page,15);

		if( !empty($categoryid) )
		{
			$blog->category->read($categoryid);
		}		

		$categorys = $blog->category->getAll();

		$pagination = pagination::output($blogs['total'],$blogs['page'],$blogs['pagesize'],zotop::url("blog/list/$categoryid/{#page}"));

		
		//渲染页面
        $page = new page();		
        $page->set('title',$blog->category->title);
		$page->set('keywords',$blog->category->keywords);
		$page->set('description',$blog->category->description);
		$page->set('body',array('class'=>'list'));
		$page->set('categoryid',$categoryid);
		$page->set('blog', $blog);
		$page->set('blogs', $blogs);
		$page->set('categorys', $categorys);
		$page->set('pagination', $pagination);
        $page->display('blog.list');	
	}
}
?>