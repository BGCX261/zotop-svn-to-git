<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * URL操作类，完成对URL的操作
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_url
{
    /**
     * 根据参数生成完整的URL，如：url::build('zotop/index/default',array('id'=>'1'))
     *
     * @param string 		$uri 		一般由{module}/{controller}/{action}组成
     * @param array|string 	$params 	URL参数 ，一般为数组
     * @param string 		$extra	 	额外参数 ，一般为数组
     * @return string
     */
    public static function build($uri , $params = '' , $extra = '')
    {
        $app = '';
        $uri = trim($uri , '/');
        if( strpos($uri , '://') !== false )
        {
            $uris = explode('://',$uri);
            $app = $uris[0];
            $uri = $uris[1];
        }
        $urls = array();
        $urls['base'] =empty($app) ? url::scriptname() : url::application($app);
        if( $paths = explode('/',trim($uri,'/')) )
        {
            $urls['module'] = $paths[0];
            $urls['controller'] = $paths[1];
            $urls['action'] = $paths[2];
        }

        $url = $urls['base'];
        $url = $url.'/'.$urls['module'].'/'.$urls['controller'].'/'.$urls['action'];

		if( !empty($params) )
		{
			if( is_string($params) )
			{
			    $keyvalues = explode('&',$params);
			    $params = array();
			    foreach($keyvalues as $keyvalue)
			    {
			        list($key,$value) = explode('=',$keyvalue);
			        if($value)
			        {
			            $params[$key] = $value;
			        }
			    }
			}
		    foreach($params as $key=>$value)
			{
				$url .= '/'.url::encodeParam($value);
			}
		}
        if( is_string($extra) )
	    {
	        $url .= $extra;
	    }
	    return url::clean(rtrim($url,'/'));
    }

    public static function redirect($url,$time=0)
    {
        $url = url::build($url);
        header("Location: ".$url);
        exit();
    }

	/**
	 * 获取当前页面的URl
	 *
	 * @param	bool $complete	是否返回完整的地址
	 * @return	string	页面地址
	 */
	public static function current($complete=TRUE)
	{
		$current = $_SERVER['REQUEST_URI'];
		if($complete)
		{
		   $current = url::protocol().'://'.url::domain().$current;
		}
	    return $current;
	}

	/**
	 * 返回 URL 中的基础部分， 如：/zotop/admin/index.php
	 *
	 * @return string
	 */
	public static function base()
	{
	    $scriptname = url::scriptname();
		return $scriptname;
	}

	/**
	 * 返回url中的页面名称， 如：index.php
	 *
	 * @return string
	 */
	public static function basename()
	{
	    $scriptname=url::scriptname();
        $pathinfo = pathinfo($scriptname);
        return $pathinfo['basename'];
	}
	
	/**
	 * 返回url中的页面名称， 如：/zotop/admin
	 *
	 * @return string
	 */
	public static function dirname()
	{
	    $scriptname=url::scriptname();
        $pathinfo = pathinfo($scriptname);
        return $pathinfo['dirname'];
	}	

	/**
	 * 如果网站安装在目录或者目录（如：/test/cms）下面，则返回：'/test/cms',如果安装在根目录下则返回 '/'
	 * 在不同的应用中必须重写该函数
	 *
	 *
	 * @param $domain 是否包含网站域名
	 * @return string
	 */
	public static function root()
	{
		$root = ZURL_ROOT;
		$root = trim($root,'\\');
	    return $root;
	}
	
    /**
     * 获取当前应用的url
     *
     * @return string 如：<install>/system/admin
     */
    public static function application($id = '')
    {
        if( empty($id) )
        {
            return APP_URL;
        }
        $url = zotop::application($id , 'url');
        if( $url[0] !=='/' && strpos($url,'://')===false )
        {
            $url =  url::root().'/'.$url;
        }
        return $url;
    }
    
    public static function modules()
    {
        return url::system().'/modules';
    }
    
    public static function module($id='')
    {
        if( empty($id) )
        {
            $id = application::getModule();            
        }
        $url = url::system().'/modules/'.$id;
        return $url;
    }
    
    public static function controller()
    {
        $url = url::module();
        $url = $url.'/'.APP_NAME;
        return $url;
    }

    /**
     * 获取当前应用的主题信息
     *
     * @return string 如：<install>/system/admin/theme
     */
	public static function theme()
	{
	    return url::application().'/themes';
	}

    /**
     * 获取当前应用的公共目录
     *
     * @return string 如：<install>/system/admin/common
     */
	public static function common()
	{
	    return url::application().'/common';
	}
	
    /**
     * 获取系统的url地址
     *
     * @return string 如：<install>/zotop
     */
	public static function system()
	{
	    return url::root().'/'.basename(ZPATH_SYSTEM);
	}

	/**
	 * 返回当前的使用的协议，一般为 http 或者 https
	 *
	 * @return https|http
	 */
	public static function protocol()
	{
		return (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS']==='off') ? 'http':'https';
	}

	/**
	 * 获取当前的主域名(含端口),返回如：www.zotop.com
	 *
	 * @param boolean $complete //是否返回全部参数
	 * @param boolean $protocol //
	 * @return string
	 */
	public static function domain()
	{
		return $_SERVER['HTTP_HOST'];
	}

	/**
	 * 返回当前的pathinfo 如：REQUEST_URI='/hc/admin/index.php/cms/index/index/1'，返回：/cms/index/index/1
	 *
	 * @return string or bool
	 */
	public static function pathinfo()
	{
		if( isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO'] )
		{
			return $_SERVER['PATH_INFO'];
		}
		return false;
	}


	/**
	 * 返回当前脚本名称 /system/admin/index.php
	 *
	 * @return $string
	 */
	public static function scriptname()
	{
	    return $_SERVER['SCRIPT_NAME'];
	}

	public static function clean($url)
	{
        //替换掉多余的 / 符号，并且保护如：http:// 中的双斜杠，下面代码可能有错误
	    $url = str_replace('//','/',$url);
	    $url = str_replace('//','/',$url);
        $url = str_replace(':/','://',$url);
	    return $url;
	}


	public static function encode($url)
	{

	}

	public static function decode($url)
	{
    	$url = strtr($url,array(
		    '$root'=>url::root(),
		    '$system'=>url::system(),
		    '$theme'=>url::theme(),
		    '$modules'=>url::modules(),
			'$this'=>url::controller(),
    	    '$common'=>url::common()
		));
		return $url;
	}
	
	public static function encodeParam($param)
	{
	    $param = str_replace(array(DS),array('∕'),$param);
	    return $param;
	}
	
	public static function decodeParam($param)
	{
	    $param = str_replace(array('∕'),array(DS),$param);
	    return $param;
	}
}
?>