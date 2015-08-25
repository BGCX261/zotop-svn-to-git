<?php
class content_controller_model extends controller
{


	public function navbar()
	{
		$navbar = array(
			'index'=>array('id'=>'index','title'=>'模型列表','href'=>zotop::url('content/model')),
			'add'=>array('id'=>'add','title'=>'添加模型','href'=>zotop::url('content/model/add')),
			'edit'=>array('id'=>'edit','title'=>'编辑模型','href'=>''),
		);

		$navbar = zotop::filter('content.model.navbar',$navbar);

		return $navbar;
	}

	public function actionIndex()
    {
		$model = zotop::model('content.model');

		if ( form::isPostBack() )
		{
			$post = form::post();
			
			$model->order($post);

			if ( !$model->error() )
			{
				msg::success('保存成功');
			}
			msg::error($model->msg());
		}
		
		$models = $model->cache();

		
        $page = new page();
        $page->set('title', zotop::t('模型管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::t('模型列表')
		));	
		$page->set('description', '');
		$page->set('navbar', $this->navbar());
		$page->set('model', $model);
		$page->set('models', $models);
        $page->display();
    }

	public function actionAdd()
	{
		$model = zotop::model('content.model');
		
		if ( form::isPostBack() )
		{
			$post = form::post();
			$model->add($post);
			if ( !$model->error() )
			{
				msg::success('保存成功',zotop::url('content/model/index/'.$parentid));
			}
			msg::error($model->msg());
		}

		
        $page = new page();
        $page->set('title', zotop::t('模型管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::t('添加模型')
		));	
		$page->set('navbar', $this->navbar());
		$page->set('models', $models);
		$page->set('types', $types);
		$page->set('data', $data);
        $page->display();		
	}

	public function actionEdit($id)
    {
		$model = zotop::model('content.model');

		if ( form::isPostBack() )
		{
			$post = form::post();

			$model->edit($post,$id);

			if ( !$model->error() )
			{
				msg::success('保存成功',zotop::url('content/model/index/'));
			}
			msg::error($model->msg());
		}
		
		$data = $model->read($id);
		$data['settings'] = $model->settings();

        $page = new page();
        $page->set('title', zotop::t('模型管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::t('编辑模型')
		));	
		$page->set('navbar', $this->navbar());
		$page->set('data', $data);
        $page->display();
    }

    public function actionStatus($id,$status=0)
    {
        $model = zotop::model('content.model');
		$model->id = $id;

		$field = zotop::model('content.field');

		if ( $status == 1 && $field->count($id) == 0 )
		{
			msg::error('无法启用该模型，因为该尚无字段，请先进入字段管理并添加字段');
		}
        
		$model->status = (int)$status;		
        $model->update();
		$model->cache(true);

        if( !$model->error() )
        {
            msg::success('操作成功',zotop::url('content/model'));   
        }        
    }

	public function actionDelete($id)
	{
        $model = zotop::model('content.model');
		$model->id = $id;
		$model->drop();

		if ( !$model->error() )
		{
			msg::success('删除成功', zotop::url('content/model'));
		}
		msg::error($model->msg());	
	}

	public function actionJson($id)
	{

		$model = zotop::model('content.model');
		$model->id = $id;
		$model->read();
		
		$json = json_encode($model->bind());

		//输出json格式
		exit($json);
	}

}
?>