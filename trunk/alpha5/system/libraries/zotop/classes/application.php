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
    /**
     * 应用程序初始化
     *
     * @return null
     */
    public static function init()
    {
        //错误及异常处理
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        set_error_handler(array('application', 'error'));
        set_exception_handler(array('application', 'exception'));
        
        //时区设置
        if (function_exists('date_default_timezone_set'))
        {
            $timezone = zotop::config('zotop.locale.timezone');
            $timezone = empty($timezone) ? date_default_timezone_get() : $timezone;
            if($timezone)
            {
                date_default_timezone_set($timezone);
            }
        }
        //输出头
        header("Content-Type: text/html;charset=utf-8");
    }
    
    public static function reboot()
    {
        
    }
    
    /**
     * 应用程序执行
     *
     *
     * @return null
     */
    public static function run()
    {
        $className = application::getControllerName();
        $classPath = application::getControllerPath();
        $method = application::getControllerMethod();
        $arguments = router::arguments();        
        
        //加载controller
        if( file_exists($classPath) )
        {
            zotop::load($classPath);
        }
        else
        {
           zotop::run('system.404',array('filepath'=>$classPath));
           return false; 
        }
        if( class_exists($className,false) )
        {
            $controller=new $className();
            if(method_exists($controller,$method) && $method{0}!='_')
            {
                call_user_func_array(array($controller,'__before'),$arguments);
                call_user_func_array(array($controller,$method),$arguments);
                call_user_func_array(array($controller,'__after'),$arguments);
                return true;
            }
            //当方法不存在时，默认调用类的_empty()函数，你可以在控制器中重写此方法
            return call_user_func_array(array($controller,'__empty'),array($method,$arguments));
        }
        return false;
    }
    
    /**
     * 渲染输出内容
     *
     * @param string $output 待渲染输出的内容
     * @return string
     */
    public static function render($output)
    {
        $time = number_format(microtime(TRUE) - ZOTOP_START_TIME , 4);
        $memory = number_format((memory_get_usage() - ZOTOP_START_MEMORY)/1024/1024 , 4);
        $output=str_ireplace
        (
            array('{#runtime}','{#memory}','{#include}'),
            array($time.' S',$memory.' MB',count(get_included_files())),
            $output
        );
        return $output;
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
        
        switch ($errno)
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
        	'title'=>'404 error',
            'content'=>zotop::t('<h2>未能找到相应页面，请检查页面文件是否存在？</h2>{$filepath}',$data)
        ));
    }    

    public static function getApplication()
    {
        return router::application();
    }
    
    /**
     * 返回当前的模块名称
     *
     * @return string
     */
    public static function getModule()
    {
        $module = router::module();
        if( empty($module) )
        {
            $module = zotop::application(APP_NAME,'module');
             
        }       
        return empty($module) ? 'zotop' : $module;
    }

    /**
     * 返回当前URL路由的控制器名称，如果未能获取路由分发的控制器，则获取当前应用的默认路由
     *
     * @return string
     */
    public static function getController()
    {
        $controller = router::controller();
              
        if( empty($controller) )
        {
            $controller = zotop::application(APP_NAME,'controller');
             
        }
        return empty($controller) ? 'index' : $controller;
    }

    /**
     * 返回当前URL路由的动作名称，未能获取则返回当前应用的默认动作
     *
     * @return string
     */
    public static function getAction()
    {
        $action = router::action();
        if( empty($action) )
        {
            $action = zotop::application(APP_NAME,'action');
             
        }
        return empty($action) ? 'index' : $action;
    }
    
    
    /**
     * 返回当前的控制器的真实名称，含“_controller”
     *
     * @return string
     */
    public static function getControllerName()
    {
        $controller = application::getController();
        if( empty($controller) )
        {
            $controller = zotop::application(APP_NAME,'controller');
            $controller = empty($controller) ? 'index' : $controller;
        }
        return $controller.'_controller';
    }

    /**
     * 返回当前的控制器的真实路径
     *
     * @return string
     */
    public static function getControllerPath()
    {
        $controller = application::getController();
        $module = application::getModule();
        $path = zotop::module($module,'path');

        if( empty($path)  )
        {
             zotop::error(array(
            	'title'=>'系统错误',
                'content'=>zotop::t('<h2>未能找到相应模块，请检查模块是否未安装或者已被禁用？</h2>模块名称：{$module}',array('module'=>$module))
             ));            
        }            
        $path = $path.DS.router::application().DS.$controller.'.php';
        return $path;
    }
    
    /**
     * 返回当前触发的控制器的真实方法名称，一般如“onEdit”
     *
     * @return string
     */
    public static function getControllerMethod()
    {
        $action = application::getAction();
        if($action)
        {
            return $action.'Action';
        }
        return 'indexAction';
    }
    
    
}
?>