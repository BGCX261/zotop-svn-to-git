<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的应用类 Application
 *
 * @package		zotop
 * @class		application_base
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class application_base
{
	public static $uri = '';
	public static $module = '';
	public static $controller = '';
	public static $action = '';
	public static $arguments = array();
	public static $hooks = array();


	/**
     * 应用程序启动
     *
     */
    public static function boot()
    {
        //错误及异常处理
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
       // set_error_handler(array('application', 'error'));
      //  set_exception_handler(array('application', 'exception'));
        
        //时区设置
        if ( function_exists('date_default_timezone_set') )
        {
			defined('TIMEZONE') OR define('TIMEZONE', 'Etc/GMT-8'); //网站时区，Etc/GMT-8 实际表示的是 GMT+8,北京时间

            $timezone = zotop::config('system.locale.timezone');
            $timezone = empty($timezone) ? TIMEZONE : $timezone;

            if( $timezone )
            {
                date_default_timezone_set($timezone);
            }
        }
        //定义时间
		if ( !defined('TIME') )
		{
			define('TIME',time());
		}
		//网站字符集
		defined('CHARSET') OR define('CHARSET', 'utf-8'); 
        //输出网站头
        header("Content-Type:text/html;charset=".CHARSET); 
		
    }


    /**
     * 解析URI
     * 
     * URI 由模块名/控制器/动作/参数组成，采用如下的格式：
     *
     * @code php
     * module/controller/action/param1/param2
     * @endcode
     *
     */
    public static function route()
    {
		$uri = application::uri();
        
		//分解uri
		$uris = explode('/',trim($uri,'/'));

		//获取namespace 和 arguments
		$namespace = implode('/',array_slice($uris,0,3));
		$arguments = array_slice($uris,3);
		
		if ( $namespace )
		{
			list(application::$module,application::$controller,application::$action) = explode('/', $namespace);
		}

		//处理参数
		for ( $i = 0, $cnt = count($arguments); $i <$cnt; $i++ )
		{

			$arguments[$i] =  rawurldecode($arguments[$i]);
		}

		application::$arguments = $arguments;		
       
    }

	/**
     * 应用程序执行
     *
     *
     * @return null
     */
    public static function execute()
    {

		if( zotop::module(application::module()) === null ||  (int)zotop::module(application::module(),'status') < 0 )
		{
             msg::error(array(
            	'title' => '404 error',
                'content' => zotop::t('<h2>未能找到模块，模块可能尚未安装或者已经被禁用？</h2>'),
                'detail' => zotop::t('模块名称：{$module}', array('module' => application::$module))
            ));   			
		}

		define('ZOTOP_MODULE', application::module());
		define('ZOTOP_MODULE_PATH', zotop::module(application::module(),'path'));
		define('ZOTOP_MODULE_URL', zotop::module(application::module(),'url'));
		
		$controllerPath = ZOTOP_MODULE_PATH.DS.ZOTOP_APPLICATION.DS.application::controller().'.php';

		if ( zotop::load($controllerPath) )
		{

		}
		elseif( zotop::load(ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP.DS.'default.php') )
		{
			$controllerPath = ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP.DS.'default.php';
			application::$arguments = array_merge(array(application::$controller), array(application::$action), application::$arguments);
			application::$controller = 'default';	
			application::$action = '';
		}
		else
		{
			zotop::error(array(
				'title' => '404 error',
				'content' => zotop::t('<h2>未能找到控制器，请检查控制器文件是否存在？</h2>'),
				'detail' => zotop::t('文件名称：{$file}',array('file'=>$controllerPath))
			));            
		}

		define('ZOTOP_CONTROLLER', application::controller());
		
		$class = application::module().'_controller_'.application::controller();

        if ( class_exists($class,false) )
        {			
			//实例化控制器
            $controller = new $class();

			if( !method_exists($controller, 'action'.ucfirst(application::action())) )
			{
				if( strlen(application::action()) > 0 )
				{
					application::$arguments = array_merge(array(application::$action),application::$arguments);
				}
		
				application::$action = $controller->action;
			}
			
			define('ZOTOP_ACTION', application::action());

			if ( method_exists($controller, 'action'.ucfirst(application::action())) )
			{

				zotop::run("system.execute.before");

				call_user_func_array(array($controller, 'action'.ucfirst(application::action())), application::arguments());
				
				zotop::run("system.execute.after");
			}
			else
			{
				 call_user_func_array(array($controller, '__empty'), array(application::action(), application::arguments()));
			}            
          
        }
        else
        {
             zotop::error(array(
            	'title' => '404 error',
                'content' => zotop::t('<h2>未能找到控制器类，请检查控制器文件中是否存在控制器类？</h2>'),
                'detail' => zotop::t('类名称：{$className}', array('className' => $class))
            ));            
        }
		
	}

	/**
     * 应用程序重启
     *
     *
     * @return null
     */
    public static function reboot()
    {
        //清理运行时文件
		folder::clear(ZOTOP_PATH_RUNTIME);

		//加载全部配置
		zotop::config(@include(ZOTOP_PATH_DATA.DS.'config.php'));
		zotop::config('zotop.database',@include(ZOTOP_PATH_DATA.DS.'database.php'));
		zotop::config('zotop.application',@include(ZOTOP_PATH_DATA.DS.'application.php'));
		zotop::config('zotop.module',@include(ZOTOP_PATH_DATA.DS.'module.php'));
		zotop::config('zotop.router',@include(ZOTOP_PATH_DATA.DS.'router.php'));

		zotop::register(@include(ZOTOP_PATH_MODULES.DS.'system'.DS.'libraries.php'));
		zotop::register(@include(ZOTOP_PATH_APPLICATION.DS.'libraries.php'));
		
		//加载全部开启模块的hook以及注册类文件
        $modules = zotop::config('zotop.module');
		
        foreach( (array)$modules as $module)
        {
            if( (int)$module['status'] >= 0 && folder::exists($module['path']) )
            {
				//加载库文件
				zotop::register(@include(path::decode($module['path']).DS.'libraries.php'));
				//加载hook文件
				application::$hooks[] = $module['path'].DS.'hooks'.DS.ZOTOP_APPLICATION.'.php';
            }
        }
		
		//打包配置文件
		zotop::data(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.config.php',zotop::config());

		//打包hook文件
        file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.hooks.php', application::compile(application::$hooks),true);
		//加载hooks以便核心文件使用
        zotop::load(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.hooks.php');		
		
		//打包类文件
		file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.core.php', application::compile(zotop::register()), true);
		
	}

    /**
     * 文件打包
     */
    public static function compile($files)
    {
        $content = "<?php\n";
        foreach($files as $file)
        {
            $content .= file::compile($file);
        }
        $content .= "\n?>";
        
        return $content;
    } 

    /**
     * 渲染输出内容
     *
     * @param string $output 待渲染输出的内容
     * @return string
     */
    public static function render($output='')
    {
        $time = number_format(microtime(TRUE) - ZOTOP_START_TIME, 4);
        $memory = number_format((memory_get_usage() - ZOTOP_START_MEMORY)/1024/1024, 4);
        $output = str_ireplace
        (
            array('{#runtime}', '{#memory}', '{#include}', '{#queries}', '{#caches}'),
            array($time.' S', $memory.' MB', count(get_included_files()), database::Q(), cache::Q().' / '.cache::W()),
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
     * 路由初始化
     * 
     *
     */
    public static function uri()
    {
        $uri = '';
        
        if ( PHP_SAPI === 'cli' )
        {
            //cli
        }
        elseif ( isset($_GET['zotop']) )
        {
            //URL兼容模式，通过一个GET变量传递PATHINFO，默认为zotop，index.php?zotop=/zotop/index/index/1/2
            $uri = $_GET['zotop'];
        }
        elseif ( isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] ) 
        {
            //pathinfo模式：index.php/zotop/index/index/1/2
            $uri = $_SERVER['PATH_INFO'];
        }
        elseif ( isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] )
        {
            $uri = $_SERVER['ORIG_PATH_INFO'];
        }
                 
        $uri = trim($uri,'/');
        $uri = preg_replace('#//+#', '/', $uri);


		$uri = empty($uri) ? application::$uri : $uri;
		return $uri;
	}


    /**
     * 返回当前的模块名称
     *
     * @return string
     */
    public static function module()
    {
        $module = application::$module;
		return $module;
    }
    
    /**
     * 返回当前URL路由的控制器名称，如果未能获取路由分发的控制器，则获取当前应用的默认路由
     *
     * @return string
     */
    public static function controller()
    {
        $controller = application::$controller;
		$controller = empty($controller) ? 'index' : $controller;
		return $controller;

    }
    
    /**
     * 返回当前URL路由的动作名称，未能获取则返回当前应用的默认动作
     *
     * @return string
     */
    public static function action()
    {
        $action = application::$action; 
		$action = empty($action) ? 'index' : $action;
        return $action;
    }
    
    
    public static function arguments()
    {
		$arguments = application::$arguments; 

		if ( is_array($arguments) )
		{
			return $arguments;
		}

		return array();
    }
    
    public static function template($namespace='')
    {

        if ( empty($namespace) )
        {
			$namespace = application::module().'/'.application::controller().'/'.application::action();
        }

		$namespace = str_replace('.','/',$namespace);
		
		$namespaces = explode('/',$namespace);
		
		switch(count($namespaces))
		{
			case 1:
				$module = application::module();
				$namespace = $namespaces[0];
				break;
			default:
				$module = array_shift($namespaces);
				$namespace = implode('/',$namespaces);
				break;
		}
        
        $template = zotop::module($module,'path').DS.ZOTOP_APPLICATION.DS.'template'.DS.str_replace('/',DS,$namespace).'.php';
       
        return $template;
    }

    public static function theme()
    {
        $theme = zotop::config('admin.theme');
        $theme = empty($theme) ? 'default' : $theme;
        
        return $theme;
    }
}
?>