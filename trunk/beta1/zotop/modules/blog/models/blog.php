<?php
class blog_model_blog extends model
{
	protected $_key = 'id';
	protected $_table = 'blog';
	public $category;
	

	public function add($data)
	{
		$data['id'] = empty($data['id']) ? $this->max('id') + 1 : $data['id'];		
		$data['createtime'] = empty($data['createtime']) ? TIME : $data['createtime'];
		$data['updatetime'] = empty($data['updatetime']) ? TIME : $data['updatetime'];
		$data['order'] = (int) $data['order'];
		$data['userid'] = empty($data['userid']) ? (int)$this->user('id') : (int)$data['userid'];
		$data['link'] = (int)$data['link'];

		$color = arr::take('title_color',$data);
		$weight = arr::take('title_weight',$data);

		$data['style'] = (empty($color) ? '' : 'color:'.$color.';').(empty($weight) ? '' : 'font-weight:'.$weight.';');

		if ( empty($data['title']) )
		{
			$this->error('标题不能为空');
			return false;
		}
		
		if ( !$data['link'] && empty($data['content']) )
		{
			$this->error('内容不能为空');
			return false;
		}

		if ( $data['link'] && empty($data['url']) )
		{
			$this->error('转向链接不能为空');
			return false;
		}
		$this->id = $this->insert($data);
		
		//更新文件全局编号和状态
		$file = zotop::model('system.file');
		$file->refresh(zotop::post('_GLOBALID'),$this->globalid());

		return $this->id;
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
			$this->error('标题不能为空');
			return false;
		}
		
		if ( !$data['link'] && empty($data['content']) )
		{
			$this->error('内容不能为空');
			return false;
		}

		if ( $data['link'] && empty($data['url']) )
		{
			$this->error('转向链接不能为空');
			return false;
		}
		
		$this->update($data,$id);
		
		//更新文件全局编号和状态
		$file = zotop::model('system.file');

		$file->refresh($this->globalid($id));
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

	public function author($userid='')
	{
		$userid = empty($userid) ? $this->userid : $userid;
		$author = empty($author) ? $this->author : $author;

		if ( empty($author) && !empty($userid) )
		{
			$author = zotop::model('system.user')->getName($userid);
		}
		
		return empty($author) ? '' : $author;
	}

	public function getPage($categoryid=0, $status=100, $page=1, $pagesize=20, $totalnum=0)
	{
		if ( !empty($categoryid) )
		{
			$this->db()->where('categoryid','=',$categoryid);
		}

		if ( !is_null($status) )
		{
			$this->db()->where('status','=',$status);
		}
		
		$blogs = $this->db()->select('*')->orderby('order','desc')->orderby('updatetime','desc')->getPage($page,$pagesize,$totalnum);
		
		return $blogs;
	}

}
?>