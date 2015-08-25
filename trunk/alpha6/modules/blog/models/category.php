<?php
class blog_model_category extends model
{
	protected $_key = 'id';
	protected $_table = 'blog_category';


	public function getAll()
	{
		$data = $this->db()->orderby('order','asc')->getAll();

		return $data;
	}

}
?>