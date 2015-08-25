<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_format
{
	public static function byte($size,$len=2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$pos=0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return number_format($size,$len).' '.$units[$pos];
	}
	
	/**
	 * 表情转换，可以通过hook(zotop.smiles)扩展
	 * 
	 *
	 */
	public static function smiles($str)
	{
	    $smiles = array(
	        ':)'=>url::theme().'/image/smiles/smile.gif',
	    	':-)'=>url::theme().'/image/smiles/cool.gif',
	    );
	    
	    $smiles = zotop::filter('zotop.smiles',$smiles);
	    
	    foreach($smiles as $key=>$val)
	    {
	        $str = str_replace($key,'<img src='.$val.' class="zotop-smile"/>',$str);
	    }
	    
	    return $str;
	}
	
	public static function email()
	{
	    
	}
	
	public static function link()
	{
	    
	}


}
?>