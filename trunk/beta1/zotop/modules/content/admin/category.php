<?php
class content_controller_category extends controller
{


	public function navbar($parentid=0)
	{
		$navbar = array(
			'index'=>array('id'=>'index','title'=>zotop::t('栏目列表'),'href'=>zotop::url("content/category/index/$parentid")),
			'add'=>array('id'=>'add','title'=>zotop::t('添加子栏目'),'href'=>zotop::url("content/category/add/$parentid"),'class'=>'dialog {width:650}'),
		);

		$navbar = zotop::filter('content.category.navbar',$navbar);

		return $navbar;
	}

	public function actionIndex($parentid=0)
    {
		$category = zotop::model('content.category');

		if ( form::isPostBack() )
		{
			$ids = zotop::post('id');

			$category->order($ids);

			if ( !$category->error() )
			{
				msg::success('保存成功');
			}
			msg::error($category->msg());
		}

		$categorys = $category->db()->where('parentid','=',$parentid)->orderby('order','asc')->getAll();
		$types = $category->types();

		$model = zotop::model('content.model');
		$models = $model->cache();

		$position = $category->getPosition($parentid);
		
		foreach($position as $p)
		{
			$pos[zotop::url('content/category/index/'.$p['id'])] = $p['title'];
		}
		
        $page = new page();
        $page->set('title', zotop::t('栏目管理'));
		$page->set('position',array(
			zotop::url('content') => zotop::t('内容管理'),
			zotop::url('content/category') => zotop::t('栏目管理'),
		) + (array)$pos + array('栏目列表'));
		$page->set('navbar', $this->navbar($parentid));
		$page->set('categorys', $categorys);
		$page->set('models', $models);
		$page->set('types', $types);
        $page->display();
    }

	public function actionAdd($parentid=0)
	{
		$category = zotop::model('content.category');
		
		if ( form::isPostBack() )
		{
			$category->add(form::post());

			if ( !$category->error() )
			{
				msg::success('保存成功',zotop::url('content/category/index/'.$parentid));
			}
			msg::error($category->msg());
		}

		if ( $parentid > 0 )
		{
			$data =  $category->read($parentid);
		}
		else
		{
			$data = array(
				'parentid'=>0,
				'title'=>zotop::t('根栏目')	
			);
		}

		$types = $category->types();
		$models = zotop::model('content.model')->cache();
		
		
        $page = new dialog();
        $page->set('title', '添加栏目');
		$page->set('categorys', $categorys);
		$page->set('types', $types);
		$page->set('models', $models);
		$page->set('data', $data);
        $page->display();		
	}

	public function actionEdit($id)
    {
		$category = zotop::model('content.category');

		if ( form::isPostBack() )
		{
			$post = form::post();

			if( $post['id'] == $post['parentid'] )
			{
				msg::error('上级分类不能和当前分类相同');
			}

			$category->edit($post,$id);

			if ( !$category->error() )
			{
				msg::success('保存成功',zotop::url('content/category/index/'.$post['parentid']));
			}
			msg::error($category->msg());
		}
		
		$categories = $category->cache();

		$data = $category->read($id);

		if ( isset($categories[$category->parentid]) )
		{
			$data['parent_title'] = $categories[$category->parentid]['title']; 
		}
		else
		{
			$data['parentid'] = 0;
			$data['parent_title'] = zotop::t('根栏目'); 
		}

		$models = zotop::model('content.model')->cache();

        $page = new dialog();
        $page->set('title', '编辑栏目');
		$page->set('categorys',$categories);
		$page->set('models', $models);
		$page->set('data', $data);
        $page->display();
    }

	public function actionMove($id)
	{
		$category = zotop::model('content.category');
		$categories = $category->cache();
		
		if ( form::isPostBack() )
		{
			$post = form::post();

			$category->move($id,$post['id']);

			if ( !$category->error() )
			{
				msg::success('移动成功',zotop::url('content/category/index/'.$categories[$id]['parentid']));
			}
			msg::error($category->msg());
		}

		

		$tree = $category->getTree(0,'<input type="radio" name="id" id="id_$id" value="$id" $checked/> <label for="id_$id"><span class="zotop-icon zotop-icon-$icon"></span><span class="title">$title</span></label>',$categories[$id]['parentid']);

        $page = new dialog();
        $page->set('title', zotop::t('移动栏目'));
		$page->set('id',$id);
		$page->set('parentid',$categories[$id]['parentid']);
		$page->set('categories',$categories);
		$page->set('tree',$tree);
        $page->display();
	}

	public function actionSelect($id=0)
	{
		$category = zotop::model('content.category');
		$tree = $category->getTree(0,'<input type="radio" name="id" id="id_$id" value="$id" $checked/> <label for="id_$id"><span class="zotop-icon zotop-icon-$icon"></span><span class="title">$title</span></label>',$id);
        $page = new dialog();
        
		$page->set('title', zotop::t('选择栏目'));
		$page->set('id',$id);
		$page->set('tree',$tree);
        $page->display();		
	}

	public function actionStatus($id,$status=-1)
	{
		$category = zotop::model('content.category');
		$category->status($id,$status);
		if ( !$category->error() )
		{
			msg::success('操作成功',url::referer());
		}
		msg::error($category->msg());	
	}

	public function actionDelete($id)
	{
		$category = zotop::model('content.category');
		$category->id = $id;
		$category->delete();
		
		if ( !$category->error() )
		{
			msg::success('删除成功',url::referer());
		}
		msg::error($category->msg());		

	}

}
?>