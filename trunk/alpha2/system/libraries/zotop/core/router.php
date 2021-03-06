<?php
class zotop_router
{
    public static $uri = '';
    public static $application = '';
	public static $module = '';
	public static $controller = '';
	public static $action = '';
	public static $arguments = array();

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
        elseif( isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF' ])
        {
            $uri = $_SERVER['PHP_SELF'];
        }

    	if (($pos = strpos($uri, ZOTOP_APP_BASE)) !== FALSE)
		{
			$uri = (string) substr($uri, $pos + strlen(ZOTOP_APP_BASE));
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
		return empty(self::$application) ? 'admin' : self::$application;
	}

	/**
	 * 获取当前的模型名称
	 *
	 * @return string;
	 */
	public static function module()
	{
	    return empty(self::$module) ? 'zotop' : self::$module;
	}

    public static function controller()
    {
        return empty(self::$controller) ? 'index' : self::$controller;
    }

    public static function action()
    {
        return empty(self::$action) ? 'default' : self::$action;
    }

    public static function arguments()
    {
        return (array)router::$arguments;
    }



    public static function controllerName()
    {
        $controller = router::controller();
        if($controller)
        {
            return $controller.'_controller';
        }
        return 'index_controller';
    }

    public static function controllerPath()
    {
        $controller = router::controller();
        if($controller)
        {
            return zotop::module(router::module(),'root').DS.router::application().DS.router::controller().'.php';
        }
        return '';
    }

    public static function controllerMethod()
    {
        $action = router::action();
        if($action)
        {
            return 'on'.ucfirst($action);
        }
        return 'onDefault';
    }
}
?>