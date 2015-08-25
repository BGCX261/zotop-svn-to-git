<?php
class content_model_category extends model
{
	protected $_key = 'id';
	protected $_table = 'content_category';
	protected $_category = array(); //分类数据

	public function __construct()
	{
		parent::__construct();

		//初始化分类数据
		$this->_category = $this->cache();
	}
	/*
	 * 获取全部节点数据，并且格式化成标准的数组
	 *
	 *
	 */
	public function getAll()
	{
		$this->_category = $this->db()->where('status','>',0)->orderby('order','asc')->getAll();
		$this->_category = arr::hashmap($this->_category,'id');
		return $this->_category;
	}

	/*
	 * 循环分类数组获取子分类的编号字符串，如：2,3,4,5
	 *
	 *
	 */	
	public function getChildID($id)
	{
		$childid = array();
		
		$data = $this->_category;

		foreach((array)$data as $key=>$category)
		{
			if ( $category['parentid'] == $id )
			{
				$childid[] = $key;
			}
		}

		return implode(',',$childid);
	}


	/*
	 * 获取下一级的子分类数组，不包含该分类的第三级分类
	 *
	 *
	 */	
	public function getChild($id)
	{
		$child = array();
		
		if ( $id )
		{
			$childids = $this->_category[$id]['childid'];			
		}
		else
		{
			//处理id为0时候的状态
			$childids = $this->getChildID(0);
		}

		$childids = explode(',',$childids);

		foreach( $childids as $childid )
		{
			if ( isset($this->_category[$childid]) )
			{
				$child[$childid] = $this->_category[$childid];
			}
		}
		return $child;
	}



	/*
	 * 获取全部的子分类的编号字符串（包含自身编号），如：1,2,3,4
	 *
	 *
	 */	
	
	public function getChildIDs($id)
	{
		$childids = array();

		foreach((array)$this->_category as $key=>$category)
		{
			$parentids = explode(',', $category['parentids']);

			if(in_array($id, $parentids))
			{
				$childids[] = $key;
			}
		}

		return implode(',',$childids);
	}

	/*
	 * 获取全部的子节点数据
	 *
	 *
	 */	
	public function getChilds($id)
	{
		$childs = array();

		$childids = $this->_category[$id]['childids'];
		$childids = explode(',',$childids);
		
		foreach( $childids as $childid )
		{
			if ( isset($this->_category[$childid]) )
			{
				$childs[$childid] = $this->_category[$childid];
			}
		}

		return $childs;
	}



	/*
	 * 获取全部父分类的编号字符串，如：1,2,3,4,6,7
	 *
	 *
	 */
	public function getParentIDs($id, &$parentids=array())
	{
		//将自身加入输入
		if ( empty($parentids) )
		{
			$parentids[] = $id;
		}

		$parentid = (int)$this->_category[$id]['parentid'];

		if ( $parentid )
		{
			$parentids[] = $parentid;
			$this->getParentIDs($parentid, &$parentids);			
		}

		return implode(',', array_reverse($parentids,true));
	}

	/*
	 * 获取全部父分类数据，返回分类数组
	 *
	 *
	 */
	public function getParents($id)
	{
		$parents = array();

		$parentids = $this->_category[$id]['parentids'];
		$parentids = explode(',',$parentids);
		
		foreach( $parentids as $parentid )
		{
			if ( isset($this->_category[$parentid]) )
			{
				$parents[$parentid] = $this->_category[$parentid];
			}
		}

		return $parents;
	}



	public function getPosition($id)
	{
		$position = array();
		$data = $this->_category;
		$parentids = $data[$id]['parentids'];
		$parent = explode(',',$parentids);
		foreach($parent as $i)
		{
			if ($i)
			{
				$position[$i] = $data[$i];
			}
		}
		return $position;
	}

    /**
	* 得到树型结构ul结构
	* @param int $rootid，表示获得这个ID下的所有子级
	* @param string  $template 生成树型结构的基本代码，例如：'<a href="$url">$title</a>'
	* @param int 被选中的ID，自动展开
	* @return string
	*/
	public function getTree($rootid=0, $loop='<a href="$url">$title</a>', $selectid=0, &$string='')
	{
		static $template='';
		
		$template = empty($template) ? str_replace('"','\"',$loop) : $template;
		
		if ( $rootid == 0 )
		{
			$childs = $this->getChild($rootid);
		}
		else
		{
			$childids = $this->_category[$rootid]['childid'];
			$childids = explode(',',$childids);
			foreach( (array)$childids as $id)
			{
				if ($id)
				{
					$childs[$id] = $this->_category[$id];
				}
			}			
		}
		
		if ( is_array($childs) && !empty($childs) )
		{
			$string .= "\n<ul>\n";
			foreach( $childs as $id=>$child )
			{
				if ( !empty($child) )
				{
					@extract($child);
					$icon = empty($icon) ? (empty($childid) ? 'item' : 'folder') : $icon;
					$checked = ($id == $selectid || in_array($id,(array)$selectid)) ? 'checked' : '';
					$string .= ( in_array($selectid, explode(',',$this->_category[$id]['childids'])) ) ? '<li class="open">' : '<li>';
					eval("\$nstr = \"$template\";");
					$string .= $nstr;
					$this->getTree($id,$template,$selectid,&$string);
					$string .= "</li>";
					$icon = '';
				}
			}
			$string .= "</ul>\n";
		}

		return $string;
	}

	/*
	 * 获取下拉选项数据
	 *
	 *
	 */

	public function getOptions($rootid=0, $key='id', $value='title', $repeat = '┃&nbsp;&nbsp;&nbsp;', $adds='', $icon = array('┃','┣','┗'))
	{
		$options = array();

		$categories = $this->getArray($rootid);
		
		foreach( $categories  as $id=>$category )
		{
			$spacer = '';

			@extract($category);
			
			if ( $_level > 0 )
			{
				$spacer = str_repeat($repeat,$_level-1) . ($_last ? $icon[2] : $icon[1]) . $adds;
			}
			else
			{
				$spacer = $adds;
			}

			$options[$category[$key]] = $spacer.$category[$value];			
		}

		return $options;
	}

	public function getArray($rootid=0, &$data=array())
	{
		if ( $rootid == 0 )
		{
			$childs = $this->getChild($rootid);
		}
		else
		{
			$childids = $this->_category[$rootid]['childid'];
			$childids = explode(',',$childids);
			foreach( (array)$childids as $id)
			{
				if ( $id )
				{
					$childs[$id] = $this->_category[$id];
				}
			}			
		}
		
		if ( is_array($childs) && !empty($childs) )
		{
			$number = 1;
			foreach( $childs as $id=>$child )
			{
				if ( $id )
				{
					$data[$id] = $child;
					$data[$id]['_folder'] = empty($child['childids']) ? true : false;
					$data[$id]['_level'] = count(explode(',',$child['parentids']));
					$data[$id]['_last'] = ( $number == count($childs) ) ? true : false;				
					$number ++;
					$this->getArray($id,&$data);
				}				
			}
		}

		return $data;
	}

	/*
	 * 分类类型预设
	 *
	 *
	 */	
	public function types()
	{
		$types = array(
			'0'=>zotop::t('内部分类'),
			'1'=>zotop::t('外部链接'),
		);

		return $types;
	}

	/*
	 * settings 字段处理
	 *
	 *
	 */	
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
			return (array)$settings;
		}
		
		return $settings[$key];
	}

	/*
	 * 读取数据
	 *
	 *
	 */	
	public function read($id='')
	{
		$this->_bind = parent::read($id);

		if ( !$this->error() )
		{
			$this->_bind['settings'] = $this->settings(); 
			return $this->_bind;
		}

		return false;		
	}

	/*
	 * 保存数据
	 *
	 *
	 */	
	public function add($data=array())
	{
		@set_time_limit(6000);

		$this->bind($data);

		if ( $this->title == '' )
		{
			$this->error(zotop::t('分类名称不能为空'));
			return false;
		}

		if ( $this->id == '' )
		{			
			$this->id = $this->max('id') + 1;
		}

		$this->parentid = is_numeric($this->parentid) ? $this->parentid : 0;
		$this->parentids = $this->getParentIDs($this->parentid).','.$this->id;
		$this->parentids = trim($this->parentids,',');
		$this->childid = '';
		$this->childids = $this->id;
		$this->order = $this->id;
		$this->status = 1;		
		$this->insert();
		$this->cache(true);

		//更新父分类的childid和childids
		if ( $this->parentid > 0 )
		{
			$this->update(array('childid'=>$this->getChildID($this->parentid)), $this->parentid);
			
			//更新父分类数据
			$parentids = explode(',', $this->parentids);

			foreach ( $parentids as $parentid )
			{
				if ( $parentid && $parentid != $this->id )
				{
					$this->update(array('childids'=>$this->getChildIDs($parentid)),$parentid);
				}
			}
		}
		
		$this->cache(true);
		return $this->error() ? false : true;
	}
	
	/*
	 * 修改数据
	 *
	 *
	 */	
	public function edit($data=array(),$id='')
	{
		@set_time_limit(6000);

		if ( !empty($id) )
		{
			$this->bind('id',$id);
		}
		
		$this->bind($data);
		

		if ( $this->title == '' )
		{
			$this->error(zotop::t('分类名称不能为空'));
			return false;
		}

		$this->update();
		$this->cache(true);

		return $this->error() ? false : true;
	}

	/*
	 * 移动分类节点
	 *
	 *
	 */	

	public function move($id, $newparentid)
	{
		@set_time_limit(6000);

		$oldparentid = $this->_category[$id]['parentid'];

		$oldparentids = $this->_category[$id]['parentids'];
		$oldparentids = explode(',',$oldparentids);

		$oldchildids = $this->_category[$id]['childids'];		
		$oldchildids = explode(',',$oldchildids);

		//目标分类没有变化，则直接返回
		if ( $oldparentid == $newparentid )
		{
			return true;
		}
		
		//目标分类为当前分类或者当前分类的子分类，则返回错误信息
		if ( in_array($newparentid,(array)$oldchildids) )
		{
			$this->error(zotop::t('无法将分类 <b>{$title}</b> 移动到 <b>{$target}</b> 下面，因为 <b>{$target}</b> 是 <b>{$title}</b> 的子类别',array('title'=>$this->_category[$id]['title'],'target'=>$this->_category[$newparentid]['title'])));
			return false;
		}

		//重设当前缓存中的分类的父分类
		$this->_category[$id]['parentid'] = $newparentid;

		//重新计算分类子分类数据，只需更新子分类的parentiids，其他无需更新
		if ( is_array($oldchildids) && !empty($oldchildids) )
		{
			foreach($oldchildids as $childid )
			{
				$parentids = $this->getParentIDs($childid); //父节点的id字符串
				$this->_category[$childid]['parentids'] = $parentids; //更新缓存中的数据
				$this->update(array('parentids'=>$parentids),$childid); //更新数据库的数据
			}
		}

		//重新计算当前分类的全部父分类的 childid 和 childids
		if ( is_array($oldparentids) && !empty($oldparentids) )
		{
			foreach( $oldparentids as $parentid )
			{
				$newchild = $this->getChildID($parentid);
				$this->_category[$parentid]['childid'] = $newchild; //更新缓存中的数据

				$newchildids = $this->getChildIDs($parentid);
				$this->_category[$parentid]['childids'] = $newchildids; //更新缓存中的数据
				
				$this->update(array('childid'=>$newchild,'childids'=>$newchildids),$parentid); //更新数据库的数据
			}
		}

		$newparentids = $this->_category[$newparentid]['parentids'];
		$newparentids = (array)explode(',', $newparentids);

		//$this->error($newparentids[1]);
		//return false;
		//重新计算目标分类的全部父分类数据,只需更新childid 和childids

		if ( is_array($newparentids) && !empty($newparentids) )
		{
			foreach( $newparentids as $parentid )
			{
				$newchild = $this->getChildID($parentid);
				$this->_category[$parentid]['childid'] = $newchild; //更新缓存中的数据

				$newchildids = $this->getChildIDs($parentid);
				$this->_category[$parentid]['childids'] = $newchildids; //更新缓存中的数据
				
				$this->update(array('childid'=>$newchild,'childids'=>$newchildids),$parentid); //更新数据库的数据
			}
		}


		//更新分类的数据
		$this->update(array('parentid'=>$newparentid),$id);
		$this->cache(true);
	}

	public function repair($id=0)
	{
		
	}

	public function status($id,$status)
	{
		$status = (int)$status;

		if ( !in_array($status,array(-1,1)) )
		{
			$status = 1;
		}
		
		$childids = $this->_category[$id]['childids'];

		if ( empty($childids) )
		{
			$this->read($id);
			$childids = $this->childids;
		}

		//禁用或者启用将同时被执行到全部子分类
		$childids = explode(',',$childids);
		
		if ( !empty($childids) )
		{
			$this->update(array('status'=>$status),array('id','in',$childids));
		}

		$this->cache(true);
		return true;
	}

	/*
	 * 删除节点并更新缓存
	 *
	 *
	 */	
	public function delete($id)
	{
		$child = $this->getChildID($id);

		if ( empty($child) )
		{
			$r = parent::delete($id);
			if ( $r )
			{
				$this->cache(true);
			}
			return true;
		}
		
		$this->error(zotop::t('无法删除，删除前请先删除子类别'));
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
	
	/*
	 * 缓存数据和设置缓存
	 *
	 *
	 */	
	public function cache($flush=false)
	{
				
		$name = $this->table();

		$cache = zotop::cache($name);
		
		if ( empty($cache) || $flush )
		{
			
			$cache = $this->getAll();
						
			if( is_array($cache) )
			{	
				zotop::cache($name,$cache, 3000);
			}
		}
		
		return $cache;
	}

}
?>