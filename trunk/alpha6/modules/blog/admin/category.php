<?php
class blog_controller_category extends controller
{
    
	public function navbar()
	{
		return array(
			'index' => array('id'=>'index','title'=>'分类管理','href'=>zotop::url('blog/category/index')),
			'add' => array('id'=>'add','title'=>'添加分类','href'=>zotop::url('blog/category/add'),'class'=>'dialog'),
		);
	}

	public function actionIndex()
    {
		$category = zotop::model('blog.category');

		if ( form::isPostBack() )
		{
			$post = form::post();

			foreach( (array)$post['id'] as $i=>$id )
			{
				$category->update(array('order'=>$i+1),$id);
			}
			
			if ( !$category->error() )
			{
				msg::success('保存成功',zotop::url('blog/category/index'));
			}
			msg::error($category->msg());
		}		
			
		$dataset = $category->db()->orderby('order','asc')->getPage();

        $page = new dialog();
        $page->set('title', '分类管理');
		$page->set('navbar', $this->navbar());
		$page->set('dataset', $dataset);
		$page->set('hash', $hash);
        $page->display();
    }

	public function actionAdd($referer='')
    {
        $category = zotop::model('blog.category');
		
		$referer = empty($referer) ? zotop::url('blog/category/index') : $referer;

		if ( form::isPostBack() )
		{
			$post = form::post();

			$category->insert($post);

			if ( !$category->error() )
			{
				msg::success('保存成功',$referer);
			}
			msg::error($category->msg());
		}
		
		$data['id'] = $category->max('id') + 1;
		$data['order'] = $data['id'];
		
		$page = new dialog();
        $page->set('title', '添加分类');
		$page->set('referer', $referer);
		$page->set('data', $data);
        $page->display();
    }

	public function actionEdit($id)
    {
		$category = zotop::model('blog.category');

		if ( form::isPostBack() )
		{
			$post = form::post();

			$category->update($post,$id);

			if ( !$category->error() )
			{
				msg::success('保存成功',zotop::url('blog/category/index'));
			}
			msg::error($category->msg());
		}
		
		$category->id = $id;
		$data = $category->read();

        $page = new dialog();
        $page->set('title', '编辑分类');
		$page->set('data', $data);
        $page->display();
    }

	public function actionDelete($id)
    {
		$category = zotop::model('blog.category');
		$category->id = $id;
		$category->delete();
		if ( !$category->error() )
		{
			msg::success('删除成功', zotop::url('blog/category/index'));
		}
		msg::error($category->msg());
    }
}
?>