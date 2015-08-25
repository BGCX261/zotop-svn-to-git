<?php
class content_controller_content extends controller
{


	public function navbar($categoryid=0)
	{
		$navbar = array(
			'index'=>array('id'=>'index','title'=>zotop::t('内容列表'),'href'=>zotop::url("content/content/index/$categoryid")),
			'add'=>array('id'=>'add','title'=>zotop::t('添加内容'),'href'=>zotop::url("content/content/add/$categoryid")),
			'edit'=>array('id'=>'edit','title'=>zotop::t('编辑内容')),
			'category'=>array('id'=>'category','title'=>zotop::t('栏目设置'),'href'=>zotop::url("content/category/edit/$categoryid"),'class'=>'dialog'),
		);

		$navbar = zotop::filter('content.content.navbar',$navbar);

		return $navbar;
	}

	public function actionIndex($categoryid=0,$status=null)
    {
		$content = zotop::model('content.content');
		//获取模型信息
		$model = zotop::model('content.model');
		$models = $model->cache();
		//获取栏目信息
		$category = zotop::model('content.category');
		$categories = $category->cache();
		
		$db = $content->db()->select('content.*,user.username,user.name,user.email')->join('user','user.id','content.userid','left')->orderby('content.order','desc')->orderby('content.updatetime','desc');

		if ( $categoryid )
		{
			if ( isset($categories[$categoryid]) )
			{
				$childids = explode(',',$categories[$categoryid]['childids']);
				
				if ( is_array($childids) )
				{
					$db->where('content.categoryid','in',$childids);
				}
			}
		}

		if ( is_numeric($status) )
		{
			$db->where('content.status','=',$status);
		}
		else
		{
			//默认不显示回收站的内容
			$db->where('content.status','>=',-50);
		}

		if ( $keywords = zotop::get('keywords') )
		{
			$db->where(array(
				array('content.title','like',$keywords),'or',array('content.keywords','like',$keywords),'or',array('user.name','like',$keywords),'or',array('user.username','like',$keywords)
			));
		}

		$contents = $db->getPage();


		$position = array(
			zotop::url('content') => zotop::t('内容管理'),
			zotop::url('content/content') => zotop::t('内容库'),			
		);
		
		if ( $categoryid )
		{
			$pos = $category->getPosition($categoryid);		
			foreach($pos as $p)
			{
				$position[zotop::url('content/content/index/'.$p['id'])] = $p['title'];
			}
		}

		if ( $keywords )
		{
			$position[] = zotop::t('搜索结果');
		}
		else
		{
			$position[] = zotop::t('内容列表');
		}
		
		$pagination = new pagination();
		$pagination->page = $contents['page'];
		$pagination->pagesize = $contents['pagesize'];
		$pagination->total = $contents['total'];
		$p = $pagination->render();


		$statuses = $content->status();

        $page = new page();
        $page->set('title', zotop::t('内容管理'));
		$page->set('position',$position);
		$page->set('navbar', $this->navbar($categoryid));
		$page->set('categoryid',$categoryid);
		$page->set('contents', $contents['data']);
		$page->set('pagesize', $contents['pagesize']);
		$page->set('total', $contents['total']);
		$page->set('page', $contents['page']);
		$page->set('pagination', $p);
		$page->set('categories', $categories);
		$page->set('models', $models);	
		$page->set('statuses', $statuses);	
        $page->display();
    }

	public function actionPreview($id)
	{
		$content = zotop::model('content.content');
		$content->read($id);
		
		if ( $content->link )
		{
			zotop::redirect($content->url);
		}
		else
		{
			zotop::redirect(zotop::url('site://content/detail/'.$id));
		}
	}

	public function actionAdd($categoryid=0)
	{		
		$content = zotop::model('content.content');
		
		//实例化模块、栏目及字段
		$model = zotop::model('content.model');
		$field = zotop::model('content.field');
		$category = zotop::model('content.category');
		
		//读取栏目信息
		if ( $categoryid )
		{
			$categorydata = $category->read($categoryid);
			$modelid = $category->modelid;
			$position = $category->getPosition($categoryid);		
			foreach($position as $p)
			{
				$pos[zotop::url('content/content/index/'.$p['id'])] = $p['title'];
			}
		}
		
		$fields = array();

		//读取模块设置
		if ( $modelid )
		{			
			$modeldata = $model->read($modelid);

			if ( $modeldata )
			{
				$fields = $field->getFields($modelid);
			}
		}
		
		$data = array();
		$data['id'] = $content->getNewID();
		$data['categoryid'] = $categoryid;
		$data['modelid'] = $modelid;
		$data['template'] = $category->settings['template_detail'];
		$data['createtime'] = time::format();
		
        $page = new page();
        $page->set('title', zotop::t('内容管理'));
		$page->set('position',array(
			zotop::url('content') => zotop::t('内容管理'),
			zotop::url('content/content') => zotop::t('内容库'),
		) + (array)$pos + array('添加内容'));
		$page->set('navbar', $this->navbar($categoryid));
		$page->set('categorydata', $categorydata);
		$page->set('fields', $fields);
		$page->set('modeldata', $modeldata);
		$page->set('data', $data);
        $page->display('content.content.post');		
	}

	public function actionEdit($id)
    {
		$content = zotop::model('content.content');
		
		//读取并设置内容
		$data = $content->read($id);
		$data['createtime'] = time::format($data['createtime']);
		$data['url']  = $data['link'] == 1 ? $data['url'] : '';

		//获取栏目
		$categoryid = $content->categoryid;
		
		//实例化模块、栏目及字段
		$model = zotop::model('content.model');
		$field = zotop::model('content.field');
		$category = zotop::model('content.category');
		
		//读取栏目信息
		if ( $categoryid )
		{
			$categorydata = $category->read($categoryid);
			$modelid = $category->modelid;
			$position = $category->getPosition($categoryid);		
			foreach($position as $p)
			{
				$pos[zotop::url('content/content/index/'.$p['id'])] = $p['title'];
			}
		}
		
		$fields = array();

		//读取模块设置
		if ( $modelid )
		{			
			$modeldata = $model->read($modelid);

			if ( $modeldata )
			{
				$fields = $field->getFields($modelid,$data);
			}
		}

        $page = new page();
        $page->set('title', zotop::t('内容管理'));
		$page->set('position',array(
			zotop::url('content') => zotop::t('内容管理'),
			zotop::url('content/content') => zotop::t('内容库'),
		) + (array)$pos + array('编辑内容'));
		$page->set('navbar', $this->navbar($content->categoryid));
		$page->set('categorydata', $categorydata);
		$page->set('fields', $fields);
		$page->set('modeldata', $modeldata);
		$page->set('data', $data);
        $page->display('content.content.post');	

    }

	public function actionSave()
	{
		$content = zotop::model('content.content');		

		if ( form::isPostBack() )
		{
			$post = form::post();
			$return = zotop::get('return');
			$status = zotop::get('status');
			if ( is_numeric($status) )
			{
				$post['status'] = $status;
			}
			$content->save($post);

			if ( !$content->error() )
			{
				msg::success('保存成功',$return);
			}
			msg::error($content->msg());
		}
		
		return true;
	}

	public function actionMove($id)
	{

	}

	public function actionStatus($id,$status=-1)
	{
		$content = zotop::model('content.content');
		$content->status($id,$status);
		if ( !$content->error() )
		{
			msg::success('操作成功',url::referer());
		}
		msg::error($content->msg());	
	}

	public function actionDelete($id)
	{
		$content = zotop::model('content.content');
		$content->id = $id;
		$content->delete();
		
		if ( !$content->error() )
		{
			msg::success('删除成功',url::referer());
		}
		msg::error($content->msg());		

	}

}
?>