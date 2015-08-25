<?php
class blog_model_category extends model
{
	protected $_key = 'id';
	protected $_table = 'blog_category';


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

}
?>