<?php
class zotop_url
{
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
        $urls['base'] =empty($app) ? url::base() : url::application($app);
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
				$url .= '/'.$value;
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
	public static function current($complete=FALSE)
	{
		$current = $_SERVER['REQUEST_URI'];
		if($complete)
		{
		   $current = self::domain(true).$current;
		}
	    return $current;
	}

	/**
	 * 返回url中的基础部分，不含文件名称，如：http://www.zotop.com/system/admin/index.php 或者 /system/admin/index.php
	 *
	 * @param boolean|string $page 页面名称，默认 为 true 则为 'index.php'
	 * @param boolean $dir 是否只返回当前的目录名称
	 * @param $domain 是否含有域名
	 * @return string
	 */
	public static function base($page=true ,$domain=false)
	{
	    $base=url::scriptname();
	    if( $page !==true )
	    {
	       $base = dirname($base).$page;
	    }
	    if($domain==true)
	    {
	        $base=url::domain(true).$base;
	    }
	    return $base;
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
		return zotop::config('zotop.dir.root');
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
            return dirname(url::base(true));
        }
        $url = zotop::application($id , 'url');
        if( $url[0] !=='/' && strpos($url,'://')===false )
        {
            $url =  url::root().'/'.$url;
        }
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
     * @return string 如：<install>/system
     */
	public static function system()
	{
	    return url::root().'/system';
	}
    /**
     * 获取web的url信息
     *
     * @return string 如：<install>/system/admin/theme
     */
	public static function web()
	{
	    return url::root().'/web';
	}

    public static function module($id = '')
    {
	    $id = empty($id) ? router::module() : $id;
	    if($id)
	    {
	        return zotop::module($id,'url');

	    }
	    return '';
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
	 * 获取当前的主域名(含端口),返回www.zotop.com或者完整的部分：http://www.zotop.com
	 *
	 * @param boolean $complete //是否返回全部参数
	 * @param boolean $protocol //
	 * @return string
	 */
	public static function domain($complete=FALSE,$protocol=FALSE)
	{
		if($complete===FALSE)
		{
			return $_SERVER['HTTP_HOST'];
		}
		if($protocol == FALSE)
		{
			$protocol = zotop::config('site.protocol');
			$protocol = empty($protocol) ? self::protocol() : $protocol;
		}
		return $protocol.'://'.self::domain().'';
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
	 * 返回当前脚本名称 /system/admin/index.php , 如果参数为真，则只返回index.php
	 *
	 * @param $boolean $short 是否返回带目录格式的脚本名称，默认返回
	 * @return $string
	 */
	public static function scriptname($short=false)
	{
		if(!$short)
		{
	        return $_SERVER['SCRIPT_NAME'];

		}
		$scriptname = self::scriptname();
		return array_pop(explode('/',$scriptname));
	}

	public static function clean($url)
	{
        //替换掉多余的 / 符号，并且保护如：http:// 中的双斜杠，下面代码可能有错误
	    $url = str_replace('//','/',$url);
	    $url = str_replace('//','/',$url);
        $url = str_replace(':/','://',$url);
	    return $url;
	}

	//站内相对连接转化成绝对链接
	public static function abs($href)
	{
	    if( strpos($href,'://') === false )
	    {
	        //如果格式如 /system/admin/themes/default/css/system.css
	        if($href[0] === '/')
	        {
	            $root = url::root();
	            $href = $root.'/'.ltrim($href , $root);
	        }
	        else
	        {
	            $base = dirname(url::base());
	            $href = $base .'/'.$href;
	        }
	    }
	    return url::clean($href);
	}


}
?>