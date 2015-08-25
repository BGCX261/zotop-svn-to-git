<?php
class blog_model_blog extends model
{
	protected $_key = 'id';
	protected $_table = 'blog';
	

	public function add($data)
	{
		$data['id'] = empty($data['id']) ? $this->max('id') + 1 : $data['id'];		
		$data['createtime'] = empty($data['createtime']) ? TIME : $data['createtime'];
		$data['updatetime'] = empty($data['updatetime']) ? TIME : $data['updatetime'];
		$data['order'] = (int) $data['order'];
		$data['userid'] = $data['userid'];
		$data['link'] = (int)$data['link'];

		$color = arr::take('title_color',$data);
		$weight = arr::take('title_weight',$data);

		$data['style'] = (empty($color) ? '' : 'color:'.$color.';').(empty($weight) ? '' : 'font-weight:'.$weight.';');

		if ( empty($data['title']) )
		{
			$this->error(1,'标题不能为空');
			return false;
		}
		
		if ( !$data['link'] && empty($data['content']) )
		{
			$this->error(1,'内容不能为空');
			return false;
		}

		if ( $data['link'] && empty($data['url']) )
		{
			$this->error(1,'转向链接不能为空');
			return false;
		}
		
		$this->insert($data);
	}

	public function edit($data,$id)
	{
		$data['updatetime'] = TIME;
		$data['link'] = (int)$data['link'];

		$color = arr::take('title_color',$data);
		$weight = arr::take('title_weight',$data);

		$data['style'] = (empty($color) ? '' : 'color:'.$color.';').(empty($weight) ? '' : 'font-weight:'.$weight.';');

		if ( empty($data['title']) )
		{
			$this->error(1,'标题不能为空');
			return false;
		}
		
		if ( !$data['link'] && empty($data['content']) )
		{
			$this->error(1,'内容不能为空');
			return false;
		}

		if ( $data['link'] && empty($data['url']) )
		{
			$this->error(1,'转向链接不能为空');
			return false;
		}
		
		$this->update($data);
	}

	public function status()
	{
		$status = array(
			'100' => zotop::t('通过审核并发布'),
			'0' => zotop::t('等待审核'),
			'-1' => zotop::t('未通过审核'),
			'-50' => zotop::t('草稿'),			
			//'-100' => zotop::t('垃圾箱'),			
		);

		$status = zotop::filter($this->table().'.status',$status);

		return $status;
	}

}
?>