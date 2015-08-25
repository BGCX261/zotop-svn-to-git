<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的路由类，完成对url的解析
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_router
{
    public static $uri = '';
    public static $group = '';
    public static $module = '';
    public static $controller = '';
    public static $action = '';
    public static $arguments = array();
    
    
    /**
     * 路由初始化
     * 
     *
     */
    public static function init()
    {
        $uri = '';
        
        if ( PHP_SAPI === 'cli' )
        {
            //cli
        }
        elseif ( isset($_GET['zotop']) )
        {
            //URL兼容模式，通过一个GET变量传递PATHINFO，默认为zotop，index.php?zotop=/zotop,index,index/id,1/parentid,2
            $uri = $_GET['zotop'];
        }
        elseif ( isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] ) 
        {
            //pathinfo模式：index.php/zotop,index,index/id,1/parentid,2
            $uri = $_SERVER['PATH_INFO'];
        }
        elseif ( isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] )
        {
            $uri = $_SERVER['ORIG_PATH_INFO'];
        }
                 
        router::$uri = trim($uri,'/');
        router::$uri = preg_replace('#//+#', '/', router::$uri);
        router::$uri = router::alias(router::$uri);
        
    }
    
    /**
     * URI别名，实现自定义路由，如果需要请覆写此函数
     *
     * @param string $uri 当前的URI
     * @return string 处理过的URI
     */
    public static function alias($uri)
    {
        return $uri;
    }

    /**
     * 解析URI
     * 
     * URI 由模块名/控制器/动作/参数组成，采用如下的格式：
     *
     * @code php
     * module/controller/action/param1/vlaue1/param2/value2
     * @endcode
     *
     */
    public static function execute()
    {
          
        if ( $uri = trim(router::$uri , '/') )
        {
            router::$arguments = explode('/',$uri);
            
            //分配module、controller、action
            router::$module = array_shift(router::$arguments);            
            router::$controller = array_shift(router::$arguments);
            router::$action = array_shift(router::$arguments);
            
            //处理参数
			
            $arguments = array();
            
            for ( $i = 0, $cnt = count(router::$arguments); $i <$cnt; $i++ )
            {

				$arguments[$i] =  rawurldecode(router::$arguments[$i]);
            }
            
            router::$arguments = $arguments;
           


            //unset($_GET['zotop']);            
            //$_GET = array_merge($_GET, array('module'=>router::$module,'controller'=>router::$controller,'action'=>router::$action), $arguments);
        }
        else
        {
            //当$uri 为空，则尝试Query_string模式
            router::$arguments = $_GET;
            router::$module = arr::take('module',router::$arguments);
            router::$controller = arr::take('controller',router::$arguments);
            router::$action = arr::take('action',router::$arguments);
        }
    }

    /**
     * 获取当前的模块名称
     *
     * @return string;
     */
    public static function group()
    {
        return router::$group;
    }    
    
    /**
     * 获取当前的模块名称
     *
     * @return string;
     */
    public static function module()
    {
        return router::$module;
    }

    /**
     * 获取控制器的名称
     * 
     * @return string;
     */
    public static function controller()
    {
        return router::$controller;
    }

    /**
     * 获取动作名称
     *
     * @return string;
     */
    public static function action()
    {
        return router::$action;
    }

    /**
     * 获取参数
     * 
     * @return array;
     */
    public static function arguments()
    {
        return router::$arguments;
    }     
}