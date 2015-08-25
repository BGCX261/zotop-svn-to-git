<?php
class content_model_modelcontent extends model
{
	protected $_key = 'id';
	protected $_table = '';
	public $modelid = '';

	//重写table，根据modelid，获取表名称
	public function table()
	{
        if( empty($this->_table) )
        {
            $model = zotop::model('content.model');
			$models = $model->cache();	
			$this->_table = $models[$this->modelid]['tablename'];
			if ( empty($this->_table) )
			{
				$this->error(zotop::t('未找到模型数据表'));
				return false;		
			}
        }
        return $this->_table;	
	}

	public function getAll()
	{
		
	}

	public function add($data=array())
	{
		$this->bind($data);

		if ( (int)$this->id <= 0 )
		{
			$this->error(zotop::t('编号不能为空'));
			return false;
		}

		$this->insert();

		return $this->error() ? false : true;
	}

	public function edit($data=array(),$id='')
	{
		$this->bind('id',$id);
		$this->bind($data);

		$this->update();

		return $this->error() ? false : true;		
	}

}
?>