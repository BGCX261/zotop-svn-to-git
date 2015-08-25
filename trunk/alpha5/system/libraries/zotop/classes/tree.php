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
class zotop_tree
{
    public $nodes = array(); //树形的元数据2维数组
    public $root = 'root'; //根元素
    
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
    public function __construct($trees=array(), $root='root')
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
            return $position;
        }
        return false;        
    }
    
    
    
}
?>