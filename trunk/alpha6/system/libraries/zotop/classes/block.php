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
class zotop_block
{
	/**
	 * 区块的头部输出
	 * 
	 *	
	 * @param array|string $header  头部参数
	 * @return null 输出头部的代码
	 */
	public static function header($block=array())
	{
		$block = is_array($block) ? $block : array('title'=>$block);

		$icon = isset($block['icon']) ? '<span class="zotop-icon zotop-icon-'.(empty($block['icon']) ? 'empty' : $block['icon']).'"></span>' : '';

		$action = isset($block['action']) ? '<div class="block-action">'.$block['action'].'</div>' : '';

		$class = isset($block['class']) ? ' '.$block['class'] : '';

		$id = isset($block['id']) ? ' id="'.$block['id'].'"' : '';

	    $html[] = '';
		$html[] = '<div'.$id.' class="block clearfix'.$class.'">';
		if(isset($block['title']))
		{
		    $html[] = '	<div class="block-header clearfix">';
			$html[] = '		'.$action.'<div class="block-title">'.$icon.$block['title'].'</div>';
		    $html[] = '	</div>';
		}
		$html[] = '	<div class="block-body clearfix">';
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
	    $html[] = '	<div class="block-footer">'.$footer.'</div>';
	    $html[] = '</div>';

		echo implode("\n",$html);
	}
	
	public static function add($str)
	{
	    echo $str;    
	}
}
?>