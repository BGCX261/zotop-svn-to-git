<?php
class content_model_field extends model
{
	protected $_key = 'id';
	protected $_table = 'content_field';

	/* 
	 * 初始化当前模型，添加默认字段	
	 */
	public function init($modelid)
	{
		//$this->field(true);
		$fields = array(
			'title'=>array('name'=>'title','label'=>zotop::t('标题'),'field'=>'title','class'=>'long','valid'=>'required:true,maxlength:100','type'=>'VARCHAR','maxlength'=>100),
			'image'=>array('name'=>'image','label'=>zotop::t('标题图片'),'field'=>'image','class'=>'long','valid'=>'maxlength:160','type'=>'VARCHAR','maxlength'=>160),
			'url'=>array('name'=>'url','label'=>zotop::t('转向链接'),'field'=>'link','class'=>'long','valid'=>'maxlength:100','description'=>zotop::t('如果填写转向链接则点击标题就直接跳转而内容设置无效'),'type'=>'VARCHAR','maxlength'=>100),
			'keywords'=>array('name'=>'keywords','label'=>zotop::t('关键词'),'field'=>'keywords','class'=>'long','valid'=>'maxlength:50','type'=>'VARCHAR','maxlength'=>50),
			'summary'=>array('name'=>'summary','label'=>zotop::t('摘要'),'field'=>'summary,textarea','class'=>'long','valid'=>'maxlength:255','type'=>'VARCHAR','maxlength'=>255),		
			
		);

		//$this->delete(array(array('modelid','=',$modelid),'and',array('system','=',1)));
		$count = $this->db()->where(array(array('modelid','=',$modelid),'and',array('system','=',1)))->count();

		if ( $count == 0 )
		{
			$i = 0;
			foreach($fields as $field)
			{
				$i++;
				$field['modelid'] = $modelid;
				$field['id'] = time()+$i;
				$field['order'] = $i;
				$field['status'] = 1;
				$field['system'] = 1;
				$field['settings'] = array('class'=>$field['class'],'style'=>$field['style']);
				$field['required'] = strpos($field['valid'],'required:true') === false ? 0 : 1;				
				$this->insert($field);
			}
		}
		
		$this->cache($modelid,true);
	}

	/* 
	 * 获取当前模型的全部字段	
	 */
	public function getAll($modelid)
	{
		$data = $this->db()->where('modelid','=',$modelid)->orderby('order','asc')->getAll();

		return $data;
	}

	/* 
	 * 生成当前模型中字段的新编号	
	 */
	public function getNewOrder($modelid)
	{
		$order = $this->max('order',array('modelid','=',$modelid));

		if ( is_numeric($order) )
		{
			$order = $order + 1;
		}

		return (int)$order;
	}

	/* 
	 * 获取使用的字段，用于生成表单	
	 */
	public function getFields($modelid,$data=array())
	{
		$fields =array();
		$_fields = $this->cache($modelid);	
		foreach( $_fields as $f )
		{
			if ( $f['status']>0 )
			{
				$field = array('name'=>$f['name'],'type'=>$f['field'],'label'=>$f['label'],'value'=>$f['value'],'valid'=>$f['valid'],'description'=>$f['description']);
				$settings = json_decode($f['settings'],true);
				$fields[$f['name']] = array_merge($field,(array)$settings);
				//赋值
				if ( is_array($data) && isset($data[$f['name']]) )
				{
					$fields[$f['name']]['value'] = $data[$f['name']];
				}
			}

		}
		unset($_fields);
		return $fields;
	}

	public function count($modelid,$system=false)
	{
		if ( $system )
		{
			//计算全部的字段数目
			$count = $this->db()->where('modelid','=',$modelid)->count();
		}
		else
		{
			//只计算自定义的字段数目
			$count = $this->db()->where('modelid','=',$modelid)->where('system','=',0)->count();
		}
		return $count;	
	}

	public function controls()
	{

		$controls = array();

		$controls['text']['name'] = '单行文本输入控件';
		$controls['text']['attr']['type'] = array('type'=>'select','options'=>array('VARCHAR'=>'变长字符 VARCHAR(255)','CHAR'=>'定长字符 CHAR(10)','int'=>'整数 INT(10)','TINYINT'=>'整数 TINYINT(3)','SMALLINT'=>'整数 SMALLINT(5)','MEDIUMINT'=>'整数 MEDIUMINT(8)'),'label'=>'数据类型','name'=>'type','valid'=>'required:true','value'=>'varchar','description'=>'');
		$controls['text']['attr']['maxlength'] = array('type'=>'text','label'=>'字段长度','name'=>'maxlength','valid'=>'required:true,number:true,min:1,max:255','value'=>255,'description'=>'');

		$controls['textarea']['name'] = '多行文本输入控件';
		$controls['textarea']['attr']['type'] = array('type'=>'select','label'=>'数据类型','options'=>array('VARCHAR'=>'变长字符 VARCHAR(255)','MEDIUMTEXT'=>'MEDIUMTEXT','TEXT'=>'TEXT'),'name'=>'type','value'=>'MEDIUMTEXT','description'=>'');
		$controls['textarea']['attr']['maxlength'] = array('type'=>'text','label'=>'字段长度','name'=>'maxlength','value'=>'','valid'=>'number:true');

		$controls['select']['name'] = '单选下拉控件';
		$controls['select']['attr']['options'] = array('type'=>'textarea','label'=>'控件选项','value'=>'选项名称1|选项值1','description'=>'每行一个，选项名称和选项值使用<b>|</b>隔开');
		$controls['select']['attr']['type'] = array('type'=>'select','options'=>array('VARCHAR'=>'变长字符 VARCHAR(255)','CHAR'=>'定长字符 CHAR(10)','int'=>'整数 INT(10)','TINYINT'=>'整数 TINYINT(3)','SMALLINT'=>'整数 SMALLINT(5)','MEDIUMINT'=>'整数 MEDIUMINT(8)'),'label'=>'数据类型','name'=>'type','valid'=>'required:true','value'=>'varchar','description'=>'');
		$controls['select']['attr']['maxlength'] = array('type'=>'text','label'=>'字段长度','name'=>'maxlength','valid'=>'required:true,number:true,min:1,max:255','value'=>255,'description'=>'');
		
		$controls['radio']['name'] = '单选按钮控件';
		$controls['radio']['attr']['options'] = array('type'=>'textarea','label'=>'控件选项','value'=>'选项名称1|选项值1','description'=>'每行一个，选项名称和选项值使用<b>|</b>隔开');
		$controls['radio']['attr']['type'] = array('type'=>'select','options'=>array('VARCHAR'=>'变长字符 VARCHAR(255)','CHAR'=>'定长字符 CHAR(10)','int'=>'整数 INT(10)','TINYINT'=>'整数 TINYINT(3)','SMALLINT'=>'整数 SMALLINT(5)','MEDIUMINT'=>'整数 MEDIUMINT(8)'),'label'=>'数据类型','name'=>'type','valid'=>'required:true','value'=>'varchar','description'=>'');
		$controls['radio']['attr']['maxlength'] = array('type'=>'text','label'=>'字段长度','name'=>'maxlength','valid'=>'required:true,number:true,min:1,max:255','value'=>255,'description'=>'');

		$controls['checkbox']['name'] = '复选框控件';
		$controls['checkbox']['attr']['options'] = array('type'=>'textarea','label'=>'控件选项','value'=>'选项名称1|选项值1','description'=>'每行一个，选项名称和选项值使用<b>|</b>隔开');
		$controls['checkbox']['attr']['type'] = array('type'=>'select','options'=>array('VARCHAR'=>'变长字符 VARCHAR(255)','CHAR'=>'定长字符 CHAR(10)','int'=>'整数 INT(10)','TINYINT'=>'整数 TINYINT(3)','SMALLINT'=>'整数 SMALLINT(5)','MEDIUMINT'=>'整数 MEDIUMINT(8)'),'label'=>'数据类型','name'=>'type','valid'=>'required:true','value'=>'varchar','description'=>'');
		$controls['checkbox']['attr']['maxlength'] = array('type'=>'text','label'=>'字段长度','name'=>'maxlength','valid'=>'required:true,number:true,min:1,max:255','value'=>255,'description'=>'');

		$controls['image']['name'] = '图片上传控件';
		$controls['image']['attr']['upload'] = array('type'=>'radio','label'=>'图片上传','options'=>array(true=>'允许上传',false=>'不允许上传'));
		$controls['image']['attr']['type'] = array('type'=>'hidden','name'=>'type','value'=>'varchar');
		$controls['image']['attr']['maxlength'] = array('type'=>'hidden','name'=>'maxlength','value'=>255);

		$controls['editor']['name'] = '富文本编辑器';
		$controls['editor']['attr']['toolbar'] = array('type'=>'radio','label'=>'编辑器类型','options'=>array('basic'=>'简洁型','standard'=>'标准型','full'=>'全功能型'),'value'=>'standard');
		$controls['editor']['attr']['type'] = array('type'=>'select','label'=>'数据类型','options'=>array('TEXT'=>'TEXT','MEDIUMTEXT'=>'MEDIUMTEXT'),'name'=>'type','value'=>'MEDIUMTEXT','description'=>'');
		$controls['editor']['attr']['maxlength'] = array('type'=>'hidden','name'=>'maxlength','value'=>'');

		$controls['keywords']['name'] = '关键词控件';
		$controls['keywords']['attr']['type'] = array('type'=>'hidden','name'=>'type','value'=>'varchar');
		$controls['keywords']['attr']['maxlength'] = array('type'=>'hidden','name'=>'maxlength','value'=>255);

		$controls = zotop::filter('field.controls',$controls);

		return $controls;	
	}

	public function getControlTypes()
	{
		$types = array();
		$controls = $this->controls();

		foreach( $controls as $type=>$control )
		{
			$types[$type] = $control['name'].'('.$type.')';
		}
		
		return $types;
	}

	public function getControlAttrs($type,$data=array())
	{
		$controls = $this->controls();
		$attrs = (array)$controls[$type]['attr'];
		
		foreach( $attrs as $t=>$attr )
		{
			if ( is_array($attr) )
			{
				$a = array();

				if ( !isset($attr['name']) )
				{
					$a['name'] = "settings[$t]";

					if ( is_array($data) && isset($data['settings'][$t]) )
					{
						$a['value'] = $data['settings'][$t];
					}
				}
				elseif ( is_array($data) && isset($data[$t]) )
				{
					$a['value'] = $data[$t];
				}
				

				$attrs[$t] = array_merge($attr,$a);
			}
		}
		return $attrs;
	}

	public function settings($key='',$setting='')
	{
		static $settings = array();

		if ( empty($settings) )
		{
			$settings = $settings ?  $setting : $this->settings;		
			$settings = $settings ? json_decode($settings,true) : array();
		}

		if ( empty($key) )
		{
			return $settings;
		}
		
		return $settings[$key];
	}

	public function exists($modelid,$name)
	{
		$content = zotop::model('content.content');

		//获取表的字段信息
		$field = array_keys($content->field());
		
		//禁止与主表的字段信息重复的字段
		if (  in_array(strtolower($name),$field) )
		{
			return true;		
		}

		if ( $this->isExist(array(array('modelid','=',$modelid),'and',array('name','=',$name))))
		{
			return false;
		}

		return false;
	}

	public function add($data=array())
	{
		$this->bind($data);

		if ( strlen($this->name)== 0 )
		{
			$this->error(zotop::t('字段名称不能为空'));
			return false;
		}

		if ( strlen($this->label)== 0 )
		{
			$this->error(zotop::t('控件标签不能为空'));
			return false;
		}

		if ( strlen($this->field)== 0 )
		{
			$this->error(zotop::t('控件类型不能为空'));
			return false;
		}

		if ( strlen($this->type)== 0 )
		{
			$this->error(zotop::t('字段数据类型不能为空'));
			return false;
		}

		if ( $this->exists($this->modelid,$this->name) )
		{
			$this->error(zotop::t('字段已经存在，请尝试其它字段名称'));
			return false;
		}

		if ( strlen($this->id)== 0 )
		{			
			$this->id = time();
		}
		
		$this->status = (int)$this->status;
		$this->status = 1;
		$this->order = (int)$this->getNewOrder($this->modelid);
		

		if ( !$this->system )
		{
		
			$model = zotop::model('content.model');
			$model->id = $this->modelid;
			$model->read();
			
			//获取真实的扩展表名称
			$tablename = $model->tablename;
			
			//获取表对象
			$table = $this->db()->table($tablename);
			
			//获取字段对象
			$name = $this->db()->table($tablename)->field($this->name);
			
			//获取字段数据
			$field = array('name'=>$this->name,'type'=>$this->type,'length'=>$this->maxlength,'default'=>$this->value,'null'=>$this->required ? 0 : 1,'comment'=>$this->label);

			if ( $name->exists()  )
			{
				$this->error(zotop::t('字段{$name}已经存在,请尝试其它名称',array('name'=>$this->name)));
				return false;				
			}
			//数据表添加字段
			$table->add($field);
		}

		$this->insert();
		$this->cache($this->modelid,true);

		return $this->error() ? false : true;	
	}


	public function edit($data=array(), $id='')
	{
		$this->bind('id',empty($id) ? $this->id : $id );
		$this->bind($data);

		if ( !$this->system && strlen($this->name)== 0 )
		{
			$this->error(zotop::t('字段名称不能为空'));
			return false;
		}

		if ( strlen($this->label)== 0 )
		{
			$this->error(zotop::t('控件标签不能为空'));
			return false;
		}

		if ( !$this->system && strlen($this->field)== 0 )
		{
			$this->error(zotop::t('控件类型不能为空'));
			return false;
		}

		if ( !$this->system && strlen($this->type)== 0 )
		{
			$this->error(zotop::t('字段数据类型不能为空'));
			return false;
		}

		if ( !$this->system )
		{
		
			$model = zotop::model('content.model');
			$model->id = $this->modelid;
			$model->read();
			
			//获取真实的扩展表名称
			$tablename = $model->tablename;
			
			//获取表对象
			$table = $this->db()->table($tablename);
			
			//获取字段对象
			$oname = $this->db()->table($tablename)->field($this->oname);
			$name = $this->db()->table($tablename)->field($this->name);
			
			//获取字段数据
			$field = array('name'=>$this->name,'type'=>$this->type,'length'=>$this->maxlength,'default'=>$this->value,'null'=>$this->required ? 0 : 1,'comment'=>$this->label);

			if ( $oname->exists()  )
			{
				if ( $this->oname != $this->name )
				{
					if ( !$name->exists() )
					{
						$oname->rename($this->name);
					}
					else
					{
						$this->error(zotop::t('字段{$name}已经存在,请尝试其它名称',array('name'=>$this->name)));
						return false;				
					}
				}

				$table->modify($field);
			}
			else
			{
				$table->add($field);
			}
		}

		$this->update();
		$this->cache($this->modelid,true);

		return $this->error() ? false : true;	
	}

	public function drop($id='')
	{
		$this->bind('id',empty($id) ? $this->id : $id );		
		//读取字段信息
		$this->read();

		if ( $this->system )
		{
			$this->error(zotop::t('系统字段不允许被删除'));
			return false;		
		}
		
		//读取模型信息
		$model = zotop::model('content.model');
		$model->id = $this->modelid;
		$model->read();

		//获取真实的扩展表名称
		$tablename = $model->tablename;
		//字段对象
		$field = $this->db()->table($tablename)->field($this->name);
		//删除字段
		$field->drop();

		if ( !$field->exists() )
		{
			$this->delete(array('id','=',$this->id));
			$this->cache($this->modelid,true);
		}

		return true;
	}

	public function order($ids,$modelid)
	{
		if ( isset($ids['id']) )
		{
			$ids = $ids['id'];
		}
		
		foreach( (array)$ids as $i=>$id )
		{
			$this->update(array('order'=>$i+1),$id);
		}

		$this->cache($modelid,true);
		return true;
	}

	public function cache($modelid,$flush=false)
	{
		$name = $this->table().'-'.$modelid;
		
		//获取缓存
		$cache = zotop::cache($name);

		if ( empty($cache) or $flush )
		{
			//更新表字段数据
			$modelcontent = zotop::model('content.modelcontent');
			$modelcontent->modelid = $modelid;
			$modelcontent->field(true);
			
			//更新缓存数据
			$cache = $this->getAll($modelid);

			if ( is_array($cache) )
			{
				zotop::cache($name,$cache);
			}


		}
		
		return $cache;
	}

}
?>