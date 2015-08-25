<?php
class config_controller extends controller
{
    public function navbar($parentid='root')
	{
		$navbar = array(
			array('id'=>'index','title'=>'节点列表','href'=>zotop::url('zotop/config/index',array('parentid'=>$parentid))),
			array('id'=>'add','title'=>'添加节点','href'=>zotop::url('zotop/config/add',array('parentid'=>$parentid)),'class'=>'dialog'),
		);

		$navbar = zotop::filter('zotop.config.navbar',$navbar,$parentid);

		return $navbar;
	}
    
    public function indexAction($parentid='root')
    {
        $config = zotop::model('zotop.config');
		if(form::isPostBack())
		{
			$post = form::post();
			
			$save = $config->save($post);
			if( $save )
			{
				msg::success('保存成功，重新加载中，请稍后……',zotop::url('zotop/config/index',array('parentid'=>$parentid)));
			}

			
			msg::error(zotop::dump($post,true));
		}        
        $configs = $config->getAll(array(
            'where'=>array('parentid','=',$parentid),
            'orderby'=>array('order'=>'asc'),
        ));

		//zotop::dump($configs);

        $position = $config->position($parentid,' > <a href=\"'.zotop::url('zotop/config/index').'/$id\">$title</a>');
        $position = '<a href="'.zotop::url('zotop/config/index').'">注册表首页</a>'.$position;
        
        $page = new page();
        $page->set('title','注册表管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar($parentid));        
		$page->set('configs',$configs);        
        $page->display();        
    }

	public function addAction($parentid='root',$type='')
	{
		
        $config = zotop::model('zotop.config');
		

		if( form::isPostBack() )
		{
			$post = form::post();
            $post['settings'] = json_encode($post['settings']);
			$post['order'] = 9999;
            
			$config->id = $post['id'];
			if( $config->isExist() )
			{
				msg::error('节点键名已经存在，请使用其它键名！');
			}

			$insert = $config->insert($post);
			if( $insert )
			{
			    msg::success('保存成功，重新加载中，请稍后……',zotop::url('zotop/config/index',array('parentid'=>$parentid)));
			}
			msg::error(zotop::dump($post,true));
		}
		
		$field = array();
		$field['id'] = empty($parentid) ? '' : $parentid.'.name';
		$field['parentid'] = $parentid;
        $field['type'] = $type;

		//zotop::dump($field);
        
        $page = new dialog();
        $page->set('title','添加');
		$page->set('body',array('style'=>'min-width:600px;'));
		$page->set('field',$field);
		$page->set('type',$type);
		$page->set('types',$config->types());
		$page->set('controls',$config->controls());
		$page->set('attrs',$config->attrs($type));
        $page->display();   		
	}

	public function editAction($id,$type='')
	{
		$config = zotop::model('zotop.config');
		$config->id = $id;
		if( form::isPostBack() )
		{
			$post = form::post();
            $post['settings'] = json_encode($post['settings']);
            
			$update = $config->update($post,$id);
			if( $update )
			{
			    msg::success('保存成功，重新加载中，请稍后……',zotop::url('zotop/config/index',array('parentid'=>$post['parentid'])));
			}
			msg::error(zotop::dump($post,true));		
		}

		$field = $config->read();
		$field['settings'] = (array)json_decode($field['settings']);

		$type = empty($type) ? $field['type'] : $type;
		$field['type'] = $type;

		$page = new dialog();
		$page->set('title','编辑');
		$page->set('body',array('style'=>'min-width:600px;'));
		$page->set('field',$field);
		$page->set('type',$type);
		$page->set('types',$config->types());
		$page->set('controls',$config->controls());
		$page->set('attrs',$config->attrs($type));
		$page->display();  
	}
	
	public function deleteAction($id)
	{
	    $config = zotop::model('zotop.config');
	    $config->id = $id;
	    $config->read();
		
		if ( $config->childNum($id) > 0 )
		{
			msg::error('该项下面尚有子项，无法被删除！');
		}

	    $config->delete();
	    msg::success('删除成功，重新加载中，请稍后……',zotop::url('zotop/config/index',array('parentid'=>$config->parentid)));
	}
}
?>