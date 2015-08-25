<?php
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
	    if( !is_array($block) )
		{
		    $block = array('title'=>$block);
		}
	    $html[] = '';
		$html[] = '<div class="block clearfix '.$block['class'].'"'.(isset($block['id']) ? ' id="'.$block['id'].'"':'').'>';
		if(isset($block['title']))
		{
		    $html[] = '	<div class="block-header">';
		    $html[] = '		<h2>'.$block['title'].'</h2>';
		    if( isset($block['action']) )
		    {
		        $html[] = '		<h3>'.$block['action'].'</h3>';
		    }
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