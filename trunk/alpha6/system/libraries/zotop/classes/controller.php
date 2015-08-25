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
class zotop_controller
{
	public $_defaultAction = 'index';
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
    public function __before($arguments='')
    {
        
    }
    
    /**
     * 当动作被触发之后调用
     *
     */
    public function __after($arguments='')
    {
        
    }
        
    /**
     * 执行动作，当找不到对应动作时候触发，可以被重载 
     *
     */
    public function execute($action='',$arguments='')
    {
        //设置默认的动作
		$action = empty($action) ? $this->_defaultAction : $action;
		
		//取得方法名称
		$method = 'action'.ucfirst($action);
        
        if( method_exists($this, $method) )
        {
            call_user_func_array(array($this, '__before'), $arguments);
            call_user_func_array(array($this, $method), $arguments);
            call_user_func_array(array($this, '__after'), $arguments);
        }
        else
        {
             call_user_func_array(array($this, '__empty'), array($action, $arguments));
        }
    }
    
    public function __empty($action='', $arguments='')
    {
        msg::error(array(
        	'title' => '404 error',
            'content' => zotop::t('<h2>未能找到相应的动作，请检查控制器中动作是否存在？</h2>'),
            'detail'=> zotop::t('动作名称：{$action}',array('action'=>$action))
        ));
    }
    
	/*
    public function redirect($uri , $params=array() , $fragment='')
    {
        $url = zotop::url($uri,$params,$fragment);
        header("Location: ".$url);
        exit();
    }
    
    public function error($content='', $life=5)
    {
        msg::error($content, $life=5);
    }
    
    public function success($content='', $url='', $life=3, $extra='')
    {
        msg::success($content, $url, $life, $extra);
    }
	*/

}