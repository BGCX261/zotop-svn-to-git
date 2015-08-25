<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统控制器类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_controller
{
    public function __empty($method='',$arguments='')
    {
        msg::error('404 error',zotop::t('<h2>未能找到相应的动作，请检查控制器中动作是否存在？</h2>动作名称：{$method}',array('method'=>$method)));
    }
    
    public function __init()
    {
        
    }
}
?>