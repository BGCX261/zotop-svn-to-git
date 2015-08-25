<?php
class blog_controller_index extends controller
{


	public function navbar($categoryid=0)
	{
		return array(
			'index' => array('id'=>'index','title'=>'日志列表','href'=>zotop::url('blog/index/index/'.$categoryid)),
			'add' => array('id'=>'add','title'=>'发表新日志','href'=>zotop::url('blog/index/add/'.$categoryid)),
			'edit' => array('id'=>'edit','title'=>'编辑日志'),
		);
	}

	public function actionIndex($categoryid=0, $status=null)
    {
		$blog = zotop::model('blog.blog');

		$blogs = $blog->getPage($categoryid,$status);

		$blogstatus = $blog->status();

		$category = zotop::model('blog.category');
		$categorys = $category->getAll();
		
	
		$pagination = new pagination();
		$pagination->page = $blogs['page'];
		$pagination->pagesize = $blogs['pagesize'];
		$pagination->total = $blogs['total'];
		$p = $pagination->render();


        $page = new page();
        $page->set('title', '日志管理');
		$page->set('navbar', $this->navbar($categoryid));
		$page->set('blogs', $blogs);
		$page->set('status',$status);
		$page->set('blogstatus',$blogstatus);
		$page->set('pagination', $p);
		$page->set('categoryid', $categoryid);
		$page->set('categorys', $categorys);
        $page->display();
    }


	public function actionAdd($categoryid=0)
	{
		$blog = zotop::model('blog.blog');

		$status = $blog->status();

		$category = zotop::model('blog.category');
		$categorys = $category->getAll();

		if ( form::isPostBack() )
		{
			$post = form::post();

			$id = $blog->add($post);

			if ( !$blog->error() )
			{
				msg::success('保存成功',form::referer());
			}
			msg::error($blog->msg());
		}
		
		//设置默认数据
		$data['status'] = 100;
		$data['categoryid'] = $categoryid;
		//$data['link'] = 1;

		//渲染页面
        $page = new page();
        $page->set('title', '添加日志');
		$page->set('navbar', $this->navbar($categoryid));
		$page->set('globalid', $blog->globalid());
		$page->set('data', $data);
		$page->set('status',$status);
		$page->set('categoryid',$categoryid);
		$page->set('categorys', $categorys);
        $page->display();
	}

	public function actionEdit($id)
	{
		$blog = zotop::model('blog.blog');
		$status = $blog->status();

		$category = zotop::model('blog.category');
		$categorys = $category->getAll();

		if ( form::isPostBack() )
		{
			$post = form::post();

			$blog->edit($post,$id);

			if ( !$blog->error() )
			{
				msg::success('保存成功',form::referer());
			}
			msg::error($blog->msg());
		}
		
		//读取数据
		$data = $blog->read($id);
		$categoryid = $data['categoryid'];

		//渲染页面
        $page = new page();
        $page->set('title', '编辑日志');
		$page->set('navbar', $this->navbar($categoryid));
		$page->set('globalid', $blog->globalid());
		$page->set('data', $data);
		$page->set('status',$status);
		$page->set('categoryid',$categoryid);
		$page->set('categorys', $categorys);
        $page->display();
	}

	public function actionDelete($id)
	{
		$blog = zotop::model('blog.blog');
		$blog->id = $id;
		$blog->delete();

		if ( !$blog->error() )
		{
			msg::success('删除成功',url::referer());
		}
		msg::error($blog->msg());
	}

	public function actionOperation()
	{
		$blog = zotop::model('blog.blog');

		$id = (array)$_POST['id'];
		$operation = $_POST['operation'];

		foreach($id as $i)
		{
			switch($operation)
			{
				case 'delete':
					$blog->delete($i);
					break;
				case 'status100':
					$blog->update(array('status'=>100), $i);
					break;
				case 'status-50':
					$blog->update(array('status'=>-50), $i);
					break;
				case 'status-1':
					$blog->update(array('status'=>-1), $i);
					break;
				case 'status0':
					$blog->update(array('status'=>0), $i);
					break;
				case 'order':
					if( !is_numeric($_POST['order']) )
					{
						$blog->error(1,'权重必须是数字');
					}
					else
					{
						$blog->update(array('order'=>$_POST['order']), $i);
					}
					break;
				case 'move':
					$blog->update(array('categoryid'=>$_POST['categoryid']), $i);
					break;					
				default:					
					break;
			}
		}

		if ( !$blog->error() )
		{
			msg::success('操作成功',url::referer());
		}
		msg::error($blog->msg());	
	}
}
?>