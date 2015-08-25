<?php
defined('ZOTOP') OR die('No direct access allowed.');

/**
 * 页面辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class box_base
{
	/**
	 * 区块的头部输出
	 * 
	 *	
	 * @param array|string $header  头部参数
	 * @return null 输出头部的代码
	 */
	public static function header($box=array())
	{
		$box = is_array($box) ? $box : array('title'=>$box);

		$icon = isset($box['icon']) ? '<span class="zotop-icon zotop-icon-'.(empty($box['icon']) ? 'empty' : $box['icon']).'"></span>' : '';

		$action = isset($box['action']) ? '<div class="box-action">'.$box['action'].'</div>' : '';

		$class = isset($box['class']) ? ' '.$box['class'] : '';

		$id = isset($box['id']) ? ' id="'.$box['id'].'"' : '';

	    $html[] = '';
		$html[] = '<div'.$id.' class="box clearfix'.$class.'">';
		if(isset($box['title']))
		{
		    $html[] = '	<div class="box-header clearfix">';
			$html[] = '		'.$action.'<div class="box-title">'.$icon.$box['title'].'</div>';
		    $html[] = '	</div>';
		}
		$html[] = '	<div class="box-body clearfix">';
		$html[] = '';
		echo implode("\n",$html);

	}
	
	
	/**
	 * 区块的尾部输出，闭合区块代码
	 * 
	 * @return null 
	 */
	public static function footer($footer=null)
	{
		$html[] = '';
	    $html[] = '	</div>';
	    $html[] = '	<div class="box-footer">'.$footer.'</div>';
	    $html[] = '</div>';

		echo implode("\n",$html);
	}
	
	public static function add($str)
	{
	    echo $str;    
	}
}
?>