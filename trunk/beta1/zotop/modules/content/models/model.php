<?php
class content_model_model extends model
{
	protected $_key = 'id';
	protected $_table = 'content_model';

	public function tablename($id='')
	{
		$id = $id ? $id : $this->id;
		$tablename = "content_model_{$id}";
		return $tablename;
	}

	public function getAll()
	{
		static $data = array();

		if ( empty($data) )
		{
			$data = $this->db()->orderby('order','asc')->getAll();
			$data = arr::hashmap($data,'id');
		}

		return $data;
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


	public function add($data=array())
	{
		$this->bind($data);

		if ( strlen($this->name)== 0 )
		{
			$this->error(zotop::t('模型名称不能为空'));
			return false;
		}

		if ( strlen($this->title)== 0 )
		{
			$this->error(zotop::t('模型标题不能为空'));
			return false;
		}

		if ( strlen($this->id)== 0 )
		{
			$this->error(zotop::t('模型数据表不能为空'));
			return false;
		}
		$this->tablename = $this->tablename($this->id);
		$this->status = (int) $this->status;
		$this->order = (int) $this->id;

		$tablename = $this->tablename;

		//返回数据表对象
		$table = $this->db()->table($tablename);

		if ( $table->exists() )
		{
			$this->error(zotop::t('模型数据表{$tablename}已经存在,请尝试其它名称',array('tablename'=>$this->tablename)));
			return false;		
		}
		else
		{
			//创建表
			$table->create(array(
				array('name'=>'id','type'=>'int','length'=>10,'comment'=>zotop::t('内容编号'))	
			),($this->description=='') ? $this->name : $this->description);

			//设置主键
			$table->primaryKey('id');
		}
		
		if ( $table->exists() )
		{
			$this->insert();
			$this->cache(true);
		}

		return $this->error() ? false : true;
	}

	public function edit($data=array(),$id='')
	{
		$this->bind('id',$id);
		$this->bind($data);		

		if ( strlen($this->name) == 0 )
		{
			$this->error(zotop::t('模型名称不能为空'));
			return false;
		}

		if ( strlen($this->title) == 0 )
		{
			$this->error(zotop::t('模型标题不能为空'));
			return false;
		}

		$tablename = $this->tablename($this->id);

		
		//返回数据表对象
		$table = $this->db()->table($tablename);
		
		if ( $table->exists() )
		{
			$table->comment(($this->description == '') ? $this->name : $this->description);
		}
		else
		{
			//重新创建表
			$table->create(array(
				array('name'=>'id','type'=>'int','length'=>10,'comment'=>zotop::t('内容编号'))	
			),($this->description == '') ? $this->name : $this->description);
			$table->primaryKey('id');
		}

		if ( $table->exists() )
		{
			$this->update();
			$this->cache(true);
		}
		return $this->error() ? false : true;		
	}
	

	public function drop($id='')
	{
		$this->bind('id',empty($id) ? $this->id : $id );
		$this->read();

		$field = zotop::model('content.field');
		$count = $field->count($this->id);
		if (  $count > 0  )
		{
			$this->error(zotop::t('无法删除该模型，删除之前请先删除字段'));
			return false;		
		}

		$table = $this->db()->table($this->tablename);

		//删除表
		$table->drop();
		
		if ( ! $table->exists() )
		{
			$field->delete(array('modelid','=',$this->id));
			$this->delete(array('id','=',$this->id));
			$this->cache(true);
			return true;
		}
		$this->error(zotop::t('删除失败'));
		return false;
	}

	public function order($ids)
	{
		if ( isset($ids['id']) )
		{
			$ids = $ids['id'];
		}

		foreach( (array)$ids as $i=>$id )
		{
			$this->update(array('order'=>$i+1),$id);			
		}
		$this->cache(true);
		return true;
	}

	
	public function cache($flush=false)
	{
		$name = $this->table();

		$data = zotop::cache($name);
		
		if ( $flush || empty($data) )
		{
			$data = $this->getAll();			
			
    		if( is_array($data) )
    		{
    		    zotop::cache($name, $data);
    		}
		}
		
		return $data;
	}

}
?>