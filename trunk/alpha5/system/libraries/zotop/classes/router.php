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
    public static $application = '';
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
        if( PHP_SAPI === 'cli' )
        {
            //cli
        }
        elseif( isset($_GET['zotop']) ) //URL兼容模式，最先获取的是唯一参数，index.php?zotop=cms/index/index/1/2
        {
            $uri = $_GET['zotop'];
        }
        elseif( isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] ) //pathinfo模式：index.php/cms/index/index/1/2
        {
            $uri = $_SERVER['PATH_INFO'];
        }
        elseif( isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] )
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
     *
     */
    public static function execute()
    {
        if($uri = trim(router::$uri , '/') )
        {
            router::$arguments = explode('/',$uri);
            router::$module = array_shift(router::$arguments);
            router::$controller = array_shift(router::$arguments);
            router::$action = array_shift(router::$arguments);
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
     * 获取当前的application，如：admin,site
     *
     * @return string
     */
    public static function application()
    {
        return empty(self::$application) ? APP_NAME : self::$application;
    }

    /**
     * 获取当前的模块名称
     *
     * @return string;
     */
    public static function module()
    {
        return self::$module;
    }

    /**
     * 获取控制器的名称
     * 
     * @return string;
     */
    public static function controller()
    {
        return self::$controller;
    }

    /**
     * 获取动作名称
     *
     * @return string;
     */
    public static function action()
    {
        return self::$action;
    }

    /**
     * 获取参数
     * 
     * @return array;
     */
    public static function arguments()
    {
        $arguments = array();
        $args = (array)router::$arguments;
        foreach($args as $arg)
        {
           $arguments[] = url::decodeParam($arg);     
        }
        return $arguments;
    }    
}
?>