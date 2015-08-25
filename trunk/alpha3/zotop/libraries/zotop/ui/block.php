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
	public static function header($header=array())
	{
	    if( !is_array($header) )
		{
		    $header = array('title'=>$header);
		}
	    $html[] = '';
		$html[] = '<div class="block clearfix '.$header['class'].'"'.(isset($header['id']) ? ' id="'.$header['id'].'"':'').'>';
		if(isset($header['title']))
		{
		    $html[] = '	<div class="block-header">';
		    $html[] = '		<h2>'.$header['title'].'</h2>';
		    if( isset($header['action']) )
		    {
		        $html[] = '		<h3>'.$header['action'].'</h3>';
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
}
?>