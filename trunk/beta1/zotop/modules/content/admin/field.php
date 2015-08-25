<?php
class content_controller_field extends controller
{

	public function navbar($modelid)
	{
		$navbar = array(
			'index'=>array('id'=>'index','title'=>'字段列表','href'=>zotop::url("content/field/index/$modelid")),
			'add'=>array('id'=>'add','title'=>'添加字段','href'=>zotop::url("content/field/add/$modelid")),			
			'edit'=>array('id'=>'edit','title'=>'编辑字段','href'=>''),
			'preview'=>array('id'=>'preview','title'=>'预览','href'=>zotop::url("content/field/preview/$modelid")),
		);

		$navbar = zotop::filter('content.field.navbar',$navbar);

		return $navbar;
	}

	public function actionIndex($modelid)
    {
		$field = zotop::model('content.field');
		$model = zotop::model('content.model');

		if ( form::isPostBack() )
		{
			$post = form::post();
			
			$field->order($post['id'],$modelid);
			
			if ( !$field->error() )
			{
				msg::success('保存成功',zotop::url('content/field/index/'.$modelid));
			}
			msg::error($field->msg());
		}
		
		$field->init($modelid);		
		$fields = $field->cache($modelid);
		$types = $field->getControlTypes();

		$model->id = $modelid;
		$model->read();

        $page = new page();
        $page->set('title', zotop::t('字段管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::url('content/field/index/'.$modelid)=>$model->name,
			zotop::t('字段列表')
		));	
		$page->set('navbar', $this->navbar($modelid));
		$page->set('field', $field);
		$page->set('types', $types);
		$page->set('fields', $fields);
		$page->set('modelid', $modelid);
        $page->display();
    }

	public function actionPreview($modelid)
	{
		$field = zotop::model('content.field');	
		$model = zotop::model('content.model');

		if ( form::isPostBack() )
		{
			msg::success('表单提交成功(数据未真实保存)');
		}

		$model->id = $modelid;
		$model->read();

		$fields = $field->getFields($modelid);

        $page = new page();
        $page->set('title', zotop::t('字段管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::url('content/field/index/'.$modelid)=>$model->name,
			zotop::t('预览')
		));
		$page->set('navbar', $this->navbar($modelid));
		$page->set('field', $field);
		$page->set('fields', $fields);
        $page->display();	
	}

	public function actionAttrs($type='')
	{
		if ( empty($type) )
		{
			return false;
		}
		$field = zotop::model('content.field');
		$attrs = $field->getControlAttrs($type);
		foreach($attrs as $attr)
		{
			form::field($attr);
		}
	}

	public function actionAdd($modelid,$type='text')
	{
		$field = zotop::model('content.field');
		
		if ( form::isPostBack() )
		{
			$post = form::post();

			$field->add($post);

			if ( !$field->error() )
			{
				msg::success('保存成功',zotop::url('content/field/index/'.$modelid));
			}
			msg::error($field->msg());
		}

		$attrs = $field->getControlAttrs($type);

		$model = zotop::model('content.model');
		$model->id = $modelid;
		$model->read();

		$data['modelid'] = $modelid;
		$data['type'] = $type;
		$data['types'] = $field->getControlTypes();
		
        $page = new page();
        $page->set('title', zotop::t('字段管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::url('content/field/index/'.$modelid)=>$model->name,
			zotop::t('添加字段')
		));
		$page->set('navbar', $this->navbar($modelid));
		$page->set('data', $data);
		$page->set('attrs', $attrs);
        $page->display();		
	}

	public function actionEdit($id)
    {
		$field = zotop::model('content.field');

		if ( form::isPostBack() )
		{
			$post = form::post();

			$field->edit($post,$id);

			if ( !$field->error() )
			{
				msg::success('保存成功',zotop::url('content/field/index/'.$post['modelid']));
			}
			msg::error($field->msg());
		}
		
		$data = $field->read($id);
		$data['types'] = $field->getControlTypes();
		$data['settings'] = $field->settings();
		$attrs = $field->getControlAttrs($field->field,$data);

		$model = zotop::model('content.model');
		$model->id = $field->modelid;
		$model->read();

        $page = new page();
        $page->set('title', zotop::t('字段管理'));
		$page->set('position',array(
			zotop::url('content')=>zotop::t('内容管理'),
			zotop::url('content/model')=>zotop::t('模型管理'),
			zotop::url('content/field/index/'.$model->id)=>$model->name,
			zotop::t('编辑字段')
		));
		$page->set('navbar', $this->navbar($field->modelid));
		$page->set('data', $data);
		$page->set('attrs', $attrs);
        $page->display();
    }

	public function actionDelete($id)
	{
		$field = zotop::model('content.field');
		$field->id = $id;
		$field->drop();
		if ( !$field->error() )
		{
			msg::success('删除成功', zotop::url('content/field/index/'.$field->modelid));
		}
		msg::error($field->msg());	
	}

    public function actionStatus($id,$status=0)
    {
        $field = zotop::model('content.field');
        $field->id = $id;
		$field->read();		
		$field->status = (int)$status;		
        $field->update();

        if( !$field->error() )
        {
            msg::success('操作成功',zotop::url('content/field/index/'.$field->modelid));   
        }        
    }

}
?>