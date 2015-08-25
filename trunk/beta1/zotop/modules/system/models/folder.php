<?php
class system_model_folder extends model
{
	protected $_key = 'id';
	protected $_table = 'folder';

	public function save($data=array())
	{
		$data = array_merge($this->bind(),$data);

		if ( empty($data['id']) )
		{
			if ( empty($data['title']) )
			{
				$this->error(zotop::t('分类名称不能为空'));
				return false;
			}

			
			$data['id'] = $this->max('id') + 1;
			$data['parentid'] = empty($data['parentid']) ? 0 : (int)$data['parentid'];
			$data['order'] = $data['id'];

			
			return $this->insert($data);
		}
	
	}

	public function getAll()
	{
		$data = $this->db()->orderby('order','asc')->getAll();
		$data = arr::hashmap($data,'id');
		return $data;
	}
}
?>