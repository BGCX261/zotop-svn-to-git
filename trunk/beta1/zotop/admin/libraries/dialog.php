<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 对话框页面类
 *
 * @package		zotop
 * @class		dialog
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class dialog extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'dialog'));
        $this->addScript('$common/js/zotop.dialog.js');
        parent::header();
    }

	public function top()
	{}

	public function bottom()
	{}

	public function footer()
	{
		 parent::footer();
	}
}
?>