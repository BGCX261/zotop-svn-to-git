<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 树形操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class tree_base
{
    public $nodes = array(); //树形的元数据2维数组
    public $root = 0; //根元素
	public $icon = array('┃','┣','┗');
	public $string = '';
	public $data = array();
	public $childs = array();
    
	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      array('id'=>'1','parentid'=>'0','name'=>'一级栏目一'),
	*      array('id'=>'2','parentid'=>'0','name'=>'一级栏目二'),
	*      array('id'=>'3','parentid'=>'1','name'=>'二级栏目一'),
	*      array('id'=>'4','parentid'=>'1','name'=>'二级栏目二'),
	*      array('id'=>'5','parentid'=>'2','name'=>'二级栏目三'),
	*      array('id'=>'6','parentid'=>'3','name'=>'三级栏目一'),
	*      array('id'=>'7','parentid'=>'3','name'=>'三级栏目二')
	* )
	*/    
    public function __construct($trees, $root=0)
    {
        $nodes =array();

        if( is_array($trees) )
        {
            foreach($trees as $tree)
            {
                $nodes[$tree['id']] = $tree;
            }
        }

        $this->nodes = $nodes;
        $this->root = $root;
		$this->string = '';
    }
    
    /**
	* 获取父级节点数组
	* @param int|string
	* @return array
	*/
    public function getParent($id)
    {
        $parent = array();

        if( isset($this->nodes[$id]) )
        {
            $parentid = $this->nodes[$id]['parentid'];
            $parentid = $this->nodes[$parentid]['parentid'];
            foreach($this->nodes as $key=>$node)
            {
                if( $node['parentid'] == $parentid )
                {
                    $parent[$key] = $node; 
                }
            }
            return $parent;
        }

        return false;
    }
    
    /**
	* 获取子级节点数组
	* @param int|string
	* @return array
	*/    
    public function getChild($parentid)
    {
        $child = array();

        foreach($this->nodes as $key=>$node)
        {
            if( $node['parentid'] == $parentid )
            {
                $child[$key] = $node; 
            }
        }

        return $child;
    }

    /**
	* 获取全部子级节点数组
	* @param int|string
	* @return array
	*/    
    public function getChilds($parentid,&$childs=array())
    {
		$child = $this->getChild($parentid);
		
		if( is_array($child) && !empty($child) )
		{
			$childs = $childs + $child;

			foreach($child as $c)
			{
				$this->getChilds($c['id'],$childs);
			}
		}

        return $childs;
    }

    /**
	* 得到当前位置的节点数组
	* @param int|string
	* @return array
	*/    
    public function getPosition($id,&$pos=array())
    {
        $position = array();

        if( isset($this->nodes[$id]) )
        {
            $pos[] = $this->nodes[$id];            
            
			$parentid = $this->nodes[$id]['parentid'];
            
			if( isset($this->nodes[$parentid]) )
            {
                $this->getPosition($parentid,$pos);
            }
            
            if( is_array($pos) )
            {
                krsort($pos);//逆向排序
                foreach($pos as $node)
                {
                    $position[$node['id']] = $node;
                }
            }

        }
        return $position;        
    }
    
    /**
	 * 得到树型结构
	 * @param int id，表示获得这个id下的所有子级
	 * @param string 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
	 * @param int 被选中的ID，比如在做树型下拉框的时候需要用到
	 * @return string
	 */
	public function getList($rootid, $template, $selectid = 0, $repeat = '&nbsp;&nbsp;&nbsp;', $adds = '')
	{
		$str = '';
		$template = str_replace('"','\"',$template);

		$nodes = $this->getArray($rootid);
		
		foreach($nodes as $id=>$node)
		{
			$spacer = '';

			@extract($node);
			
			if ( $_level > 1 )
			{
				$spacer = str_repeat($repeat,$_level-1) . ($_last ? $this->icon[2] : $this->icon[1]) . $adds;
			}
			else
			{
				$spacer = $adds;
			}

			
			$selected = $id==$selectid ? 'selected' : '';
			
			eval("\$nstr = \"$template\";");	
			
			$str .= $nstr;
		}

		return $str;
	}

	public function getArray($rootid=0, $level=0)
	{
		$number = 1;
		
		$childs = $this->getChild($rootid);

		if ( is_array($childs) && !empty($childs) )
		{
			$level = $level + 1;

			foreach($childs as $child)
			{
				//$level = $child['parentid'] == 0 ? 1 : $level;

				$this->data[$child['id']] = $child;
				$this->data[$child['id']]['_child'] = count($this->getChild($child['id']));
				$this->data[$child['id']]['_level'] = $level;
				$this->data[$child['id']]['_last'] = ( $number == count($childs) ) ? 1 : 0;
				$this->getArray($child['id'], $level);
				$number++;
			}
		}


		return $this->data;
	}

	public function getOptionsArray($rootid=0, $key='id', $value='title', $repeat = '&nbsp;&nbsp;&nbsp;', $adds = ' ')
	{
		$options = array();
		
		$nodes = $this->getArray($rootid);
		
		foreach($nodes as $node)
		{
			$spacer = '';

			@extract($node);
			
			if ( $_level > 0 )
			{
				$spacer = str_repeat($repeat,$_level-1) . ($_last ? $this->icon[2] : $this->icon[1]) . $adds;
			}
			else
			{
				$spacer = $adds;
			}

			$options[$node[$key]] = $spacer.$node[$value];
		}

		return $options;		
	}

    /**
	* 得到树型结构ul结构
	* @param int $rootid，表示获得这个ID下的所有子级
	* @param string  $template 生成树型结构的基本代码，例如：'<a href="$url">$title</a>'
	* @param int 被选中的ID，自动展开
	* @return string
	*/
	public function getHtml($rootid, $temp , $selectid=0)
	{
		static $template='';
		
		$template = empty($template) ? str_replace('"','\"',$temp) : $template;

		$childs = $this->getChild($rootid);

		if ( is_array($childs) && !empty($childs) )
		{
			$this->string .= "\n<ul>\n";
			
			foreach($childs as $child)
			{
				@extract($child);

				$opened = ($id == $selectid) ? 'open' : '';
				
				$this->string .= empty($opened) ? '<li>' : "<li class=\"$opened\">";

				eval("\$nstr = \"$template\";");

				$this->string .= $nstr;
				$this->getHtml($id,$template,$selectid);
				$this->string .= "</li>\n";
			}

			$this->string .= "</ul>\n";
		}

		return $this->string;
	}
    
}
?>