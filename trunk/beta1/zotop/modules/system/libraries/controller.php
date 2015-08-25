<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 控制器基类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class controller_base
{
	public $action = 'index';

    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        $this->__init();
        $this->__check();
    }

    /**
     * 初始化动作，当控制器被初始化的时候调用
     *
     */
    public function __init()
    {
        
    }
    /**
     * 初始化权限检查，当控制器被初始化的时候调用
     *
     */

    public function __check()
    {
        
    }
	
    /**
     * 动作触发之前调用
     *
     */
    public function __before()
    {
        
    }
    
    /**
     * 当动作被触发之后调用
     *
     */
    public function __after()
    {
        
    }
        
    
    public function __empty($action='', $arguments='')
    {
        zotop::error(array(
        	'title' => '404 error',
            'content' => zotop::t('<h2>未能找到相应的动作，请检查控制器中动作是否存在？</h2>'),
            'detail'=> zotop::t('动作名称：{$action}',array('action'=>$action))
        ));
    }
    

}