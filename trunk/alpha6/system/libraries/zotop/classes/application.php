<?php
defined('ZOTOP') OR die('No direct access allowed.');

/**
 * 系统的应用类 Application
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_application
{
	public static $group = 'admin';
    public static $module = 'zotop';
    public static $controller = 'index';
    public static $action = 'index';
    public static $arguments = array();
    /**
     * 应用程序初始化
     *
     */
    public static function init()
    {
        //错误及异常处理
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        set_error_handler(array('application', 'error'));
        set_exception_handler(array('application', 'exception'));
        
        //时区设置
        if ( function_exists('date_default_timezone_set') )
        {
            $timezone = zotop::config('system.locale.timezone');
            $timezone = empty($timezone) ? date_default_timezone_get() : $timezone;
            if( $timezone )
            {
                date_default_timezone_set($timezone);
            }
        }
        
        //输出头
        header("Content-Type: text/html;charset=utf-8");
        
    }
    
    /**
     * 应用程序启动
     *
     */
    public static function boot()
    {
        //app
        define('ZOTOP_APP_URL_THEME', ZOTOP_APP_URL.'/themes/'.application::theme());
        define('ZOTOP_APP_URL_COMMON', ZOTOP_APP_URL.'/common');                
        define('ZOTOP_APP_URL_CSS', ZOTOP_APP_URL_THEME.'/css');
        define('ZOTOP_APP_URL_IMAGE', ZOTOP_APP_URL_THEME.'/image');
        define('ZOTOP_APP_URL_JS', ZOTOP_APP_URL_COMMON.'/js');        
        //group
        define('ZOTOP_GROUP', application::group());        
        //module
        define('ZOTOP_MODULE', application::module());               
        define('ZOTOP_MODULE_URL', zotop::modules(ZOTOP_MODULE, 'url'));
        define('ZOTOP_MODULE_URL_GROUP', ZOTOP_MODULE_URL.'/'.ZOTOP_GROUP);
        define('ZOTOP_MODULE_PATH', zotop::modules(ZOTOP_MODULE,'path'));
        define('ZOTOP_MODULE_PATH_GROUP', ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP);
        //controller
        define('ZOTOP_CONTROLLER', application::controller());
        //action
        define('ZOTOP_ACTION', application::action());
        
    }    
    
    
    


    /**
     * 应用程序执行
     *
     *
     * @return null
     */
    public static function execute()
    {
		if( zotop::modules(ZOTOP_MODULE) === null ||  (int)zotop::modules(ZOTOP_MODULE,'status') < 0 )
		{
             msg::error(array(
            	'title' => '404 error',
                'content' => zotop::t('<h2>未能找到模块，模块可能尚未安装或者已经被禁用？</h2>'),
                'detail' => zotop::t('模块名称：{$module}', array('module' => ZOTOP_MODULE))
            ));   			
		}

        //controller classname
        $controllerName = ZOTOP_MODULE.'_controller_'.ZOTOP_CONTROLLER;
        $controllerPath = ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP.DS.ZOTOP_CONTROLLER.'.php';
                
        if ( !class_exists($controllerName, false) )
        {
            if ( !zotop::load($controllerPath) )
            {
                msg::error(array(
                	'title' => '404 error',
                    'content' => zotop::t('<h2>未能找到控制器，请检查控制器文件是否存在？</h2>'),
                    'detail' => zotop::t('文件名称：{$file}',array('file'=>$controllerPath))
                ));            
            }    
        }

        if ( class_exists($controllerName,false) )
        {
            //实例化控制器
            $controller = new $controllerName();
            
            //调用__execute方法
            $controller->execute(ZOTOP_ACTION, application::arguments());            
        }
        else
        {
             msg::error(array(
            	'title' => '404 error',
                'content' => zotop::t('<h2>未能找到控制器类，请检查控制器文件中是否存在控制器类？</h2>'),
                'detail' => zotop::t('类名称：{$className}', array('className' => $controllerName))
            ));            
        }
    }

    /**
     * 渲染输出内容
     *
     * @param string $output 待渲染输出的内容
     * @return string
     */
    public static function render($output)
    {
        $time = number_format(microtime(TRUE) - ZOTOP_START_TIME, 4);
        $memory = number_format((memory_get_usage() - ZOTOP_START_MEMORY)/1024/1024, 4);
        $output = str_ireplace
        (
            array('{#runtime}', '{#memory}', '{#include}', '{#queries}'),
            array($time.' S', $memory.' MB', count(get_included_files()), database::Q()),
            $output
        );
        return $output;
    }
    
    
    public static function reboot()
    {
        
    }
    /**
     * 错误控制器
     *
     * @param string $errno 	错误代码
     * @param string $message 	错误信息
     * @param string $file 		错误发生的文件名称
     * @param string $line 		错误行号
     * @return string
     */   
    public static function error($errno, $message='', $file='', $line=0, $extra=array())
    {
        switch ( $errno )
        {
            case E_ERROR:
            case E_USER_ERROR:
                $error = "<div>errno:[{$errno}]</div><div>message: {$message}</div><div>file: {$file} </div><div>line: {$line} </div>";
                exit('<div>'.$error.'</div>');
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                $error = "<div>errno:[{$errno}]</div><div>message: {$message}</div><div>file: {$file} </div><div>line: {$line} </div>";
                //exit('<div>'.$error.'</div>');
                break;
        }
    }
    
    /**
     * 异常处理，待完善
     *
     * @param $error
     * @param $message
     * @param $file
     * @param $line
     * @return null
     */
    public static function exception($error,$message='',$file='',$line=0)
    {
        echo '<div style="color:red;">error:'.$error.'('.$message.'////'.$file.$line.')</div>';
        exit();
    }
    
    /**
     * 404错误显示
     *
     */
    public static function show404($data)
    {
        msg::error(array(
        	'title' => '404 error',
            'content' => zotop::t('<h2>未能找到相应页面，请检查页面文件是否存在？</h2>'),
            'detail' => zotop::t('文件名称：{$filepath}',$data)
        ));
    }

    /**
     * 返回当前的名称空间
     *
     */
	public static function namespace()
	{
		return ZOTOP_GROUP.'://'.ZOTOP_MODULE.'.'.ZOTOP_CONTROLLER.'.'.ZOTOP_ACTION;
	}

    /**
     * 返回当前的组名称
     *
     */    
    public static function group()
    {
        $group = router::group();
        
        if ( empty($group) )
        {
            $group = application::$group;
        }
        
        return $group;
    }

    /**
     * 返回当前的模块名称
     *
     * @return string
     */
    public static function module()
    {
        $module = router::module();
                
        if ( empty($module) )
        {
            $module = application::$module;
             
        }          
        return $module;
    }
    
    /**
     * 返回当前URL路由的控制器名称，如果未能获取路由分发的控制器，则获取当前应用的默认路由
     *
     * @return string
     */
    public static function controller()
    {
        $controller = router::controller();
                      
        if ( empty($controller) )
        {
            $controller = application::$controller;
             
        }
        return $controller;
    }
    
    /**
     * 返回当前URL路由的动作名称，未能获取则返回当前应用的默认动作
     *
     * @return string
     */
    public static function action()
    {
        $action = router::action();
        
        if ( empty($action) )
        {
            $action = application::$action;
             
        }
        
        return $action;
    }
    
    
    public static function arguments()
    {
        return router::arguments();
    }
    
    public static function template($action='')
    {
        if ( empty($action) )
        {
            $action = ZOTOP_ACTION;
        }
        
        $template = ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP.DS.'template'.DS.ZOTOP_CONTROLLER.DS.$action.'.php';
        
        return $template;
    }

    public static function theme()
    {
        $theme = zotop::config('system.admin.theme');
        $theme = empty($theme) ? 'default' : $theme;
        
        return $theme;
    }
}