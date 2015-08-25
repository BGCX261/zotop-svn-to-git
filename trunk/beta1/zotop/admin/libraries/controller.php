<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 控制器操作类
 *
 * @package		zotop
 * @class		controller
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class controller extends controller_base
{
    public $page;
    public $user;
    
    public function __init()
    {
        $this->user = zotop::user();
    }
    
    public function __check()
    {                
        if( empty($this->user) )
        {
            zotop::redirect(zotop::url('system/login'));
        }        
    }

    public function navbar()
    {
        
    }
    

}
?>