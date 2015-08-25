<?php
class BaseRouter
{
    public static $uri='';
    public static $application='';
	public static $module = 'system';
	public static $controller = 'index';
	public static $method = 'default';
	public static $arguments = array();


    /**
     * 获取当前的URl参数，如：index.php/cms/index/index/1/2，所有的URI最后不含有斜线
     *
     * @return string URI;
     */
    public static function findURI()
    {
		$uri='';
		if( isset($_GET['zotop']) )//最先获取的是唯一参数：index.php?zotop=cms/index/index/1/2
		{
			$uri = $_GET['zotop'];
			
			zotop::config('zotop.url.model',2); //将url模式重新设置为兼容模式
		}
		elseif( isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO'] ) //获取的是唯一参数：index.php/cms/index/index/1/2
		{
			$uri = $_SERVER['PATH_INFO'];
		}
		else
		{
			$uri = $_SERVER['PHP_SELF'];

			if( strpos($uri,APPBASE) !== FALSE)
			{
				$uri = (string) substr($uri,strpos($uri,APPBASE) + strlen(APPBASE));
			}
		}
        //URI别名，实现自定义路由功能
        $uri=router::alias($uri);

		//应用程序入口文件处理，去掉开始结尾的斜线
		$uri = trim($uri,'/');
		$uri = preg_replace('#//+#', '/', $uri);

		self::$uri = $uri;
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
     * 处理当前的URI，并将其分解。系统默认支持pathinfo模式，如果需要，请覆写一下函数
     *
     * @return null;
     */
    public static function execute()
    {
        self::$arguments = explode('/',self::$uri);
		self::$module = array_shift(self::$arguments);
		self::$controller = array_shift(self::$arguments);
		self::$method = array_shift(self::$arguments);
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
	    return empty(self::$module) ? 'system' : self::$module;
	}

	/**
	 * 获取当前的控制器的相关信息
	 *
	 * @return string
	 */
	public static function controller($type='name')
	{
		switch($type)
		{
		    case 'name':
		    case 'filename':
		        $return = empty(self::$controller) ? 'index' : self::$controller;
		        break;
		    case 'path':
		    case 'filepath':
		        $return = module::setting(Router::module(),'root').DS.router::application().DS.router::controller('filename').'.php';
		        break;
		    case 'class':
		    case 'classname':
		        $return = empty(self::$controller) ? 'IndexController' : ucfirst(self::$controller).'Controller';
		        break;
		}
		return $return;
	}

	/**
	 * 获取当前的方法名称
	 *
	 * @return string
	 */
	public static function method($prefix=true)
	{
		if($prefix)
		{
			return empty(self::$method) ? 'onDefault' : 'on'.ucfirst(self::$method);
		}
		return strtolower(self::$method);
	}

	/**
	 * 获取当前的参数
	 *
	 * @return array
	 */
	public static function arguments()
	{
		return (array)self::$arguments;
	}
}
?>