<?php
class content_model_content extends model
{
	protected $_key = 'id';
	protected $_table = 'content';

	
	/**
	 *	获取一个新的编号，并插入编号数据，以待编辑
	 *
	 *
	 */
	public function getNewID()
	{
		$userid = $this->user('id');
		
		$id = $this->db()->select('id')->where('userid','=',$userid)->where('status','=',-100)->getOne();
		
		if ( $id === false )
		{
			$id = $this->max('id') + 1;
			$insert = $this->insert(array('id'=>$id,'userid'=>$userid,'status'=>'-100','createtime'=>TIME));
			if ( $insert )
			{
				return $id;
			}
		}

		return (int)$id;
	}

	public function read($id)
	{
		$this->_bind = parent::read($id);
		$modelcontent = zotop::model('content.modelcontent');
		$modelcontent->modelid = $this->modelid;
		$modelcontent->read($this->id);

		$this->bind($modelcontent->_bind);
		
		return $this->bind();
	}


	public function save($data=array())
	{
		$this->bind($data);

		if ( strlen($this->title)== 0 )
		{
			$this->error(zotop::t('标题不能为空'));
			return false;
		}

		if ( (int)$this->id <= 0 )
		{
			$this->id = $this->max('id') + 1;
		}

		if ( (int)$this->userid <=0 )
		{
			$this->userid = $this->user('id');
		}

		$this->link = $this->url == '' ? 0 : 1;	

		$this->status = (int)$this->status;
		$this->order = (int)$this->order;
		$this->createtime = $this->createtime == '' ? time() : strtotime($this->createtime);
		$this->updatetime = time();

		$this->update();
		
		$modelid = $this->modelid;
		
		if ( $modelid )
		{
			$modelcontent = zotop::model('content.modelcontent');
			$modelcontent->modelid = $modelid;
			$modelcontent->delete($this->id);
			$modelcontent->insert($this->bind());
		}

		return $this->error() ? false : true;
	}	

	public function delete($where=array())
	{
		$modelcontent = zotop::model('content.modelcontent');

		if ( empty($where) )
		{
			$where = array('id','=',$this->id);
		}

		$data = $this->db()->select('id','modelid','globalid')->where($where)->getAll();

		foreach($data as $item)
		{
			//删除模型内容数据
			if ( $item['modelid'] )
			{
				$modelcontent->modelid = $item['modelid'];
				$modelcontent->delete();
			}
			//删除内容数据
			parent::delete(array('id','=',$item['id']));			
			//删除附件
			
			//删除钩子，可以调用该钩子删除其他模块的相关数据
			zotop::run('content.delete',$item['id']);
		}

		return true;
	}
		
	public function status()
	{
		$status = array(
			'100' => zotop::t('通过审核并发布'),
			'1' => zotop::t('通过审核'),
			'0' => zotop::t('等待审核'),
			'-1' => zotop::t('未通过审核'),
			'-50' => zotop::t('草稿'),			
			'-100' => zotop::t('垃圾箱'),			
		);

		$status = zotop::filter($this->table().'.status',$status);

		return $status;
	}

	public function url($id='')
	{
		$id = empty($id) ? $this->id : $id;
		
		
	}

	public function cache($flush=false)
	{
		return false;
	}

}
?>