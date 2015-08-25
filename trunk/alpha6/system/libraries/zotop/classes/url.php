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
     * @param string 		$uri 		一般由{app}://{module}/{controller}/{action}组成
     * @param array|string 	$params 	动作参数 ，一般为数组
	 * @param array|string 	$arguments 	URL参数 ，一般为数组
     * @param string 		$fragment	 	锚点
     * @return string
     */    
    public static function build($uri, $params=null, $arguments='' ,$fragment='')
    {
        $uri = trim($uri , '/');
        
        $app = '';
        
        if ( strpos($url,'://')!== false )
        {
            $u = explode('://', $uri);
            $app = $u[0];
            $uri = $u[1]; 
        }
        
        //获取入口文件地址
        if ( empty($app) )
        {
            $base = url::scriptname();
        }
        else
        {
            $base = zotop::applicationes($app,'url').'/'.zotop::applicationes($app,'base');;
        }
                
        //获取module/controller/action
        if ( $u= explode('/',trim($uri,'/')) )
        {
			$namespace = implode('/',array_slice($u,0,3));
        }
        
        //处理id/5/n/6 形式的参数
        if ( !is_array($params) )
        {
            $args = array();
            $array = explode('/', $params);            
            while ($key = array_shift($array))
            {
                $args[$key] = array_shift($array);
            }
            $params = $args;     
        }
        
        //合并参数
        $str = '';
        foreach($params as $key=>$value)
        {
			$str .= '/'.rawurlencode($value);
        }
        
        //组装url
        $url = $base.'/'.$namespace.$str.$fragment;
        $url = url::clean($url);
        return $url;
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
		$url = rawurlencode($url);

		return $url;
	}

	public static function decode($url)
	{
    	$url = strtr($url,array(
		    '$root' => ZOTOP_URL_ROOT,
		    '$system' => ZOTOP_URL_SYSTEM,
    		'$common' => ZOTOP_APP_URL_COMMON,
		    '$theme' => ZOTOP_APP_URL_THEME,
		    '$modules' => ZOTOP_URL_MODULES,
    	    '$module' => ZOTOP_MODULE_URL,
    	    '$group' => ZOTOP_MODULE_URL.'/'.ZOTOP_GROUP
		));
		return $url;
	}
	
    public static function redirect($url, $params=null, $fragment='')
    {
        $url = url::build($url);
        
        header("Location: ".$url);
        
        exit();
    }	
	
	public static function current($complete=true)
	{
		$current = $_SERVER['REQUEST_URI'];
		
		if($complete)
		{
		   $current = url::protocol().'://'.url::domain().$current;
		}
	    return $current;	    
	}

	public static function location()
	{
		$current = $_SERVER['REQUEST_URI'];
		$current = url::protocol().'://'.url::domain().$current;

	    return $current;	    
	}
	
	public static function referer()
	{

		$referer = $_SERVER['HTTP_REFERER'];

	    return $referer;	    
	}

	/**
	 * 返回 URL 中的基础部分， 如：/zotop/admin/index.php
	 *
	 * @return string
	 */
	public static function base()
	{
	    $scriptname = $_SERVER['SCRIPT_NAME'];
		return $scriptname;
	}

	/**
	 * 返回url中的页面名称， 如：index.php
	 *
	 * @return string
	 */
	public static function basename()
	{
	    $scriptname = $_SERVER['SCRIPT_NAME'];
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
	    $scriptname = $_SERVER['SCRIPT_NAME'];
        $pathinfo = pathinfo($scriptname);
        return $pathinfo['dirname'];
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

	/**
	 * 返回 url 中的uri部分，如 /system/index.php/zotop.index/id/1 返回 /zotop.index/id/1
	 *
	 * @return $string
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
            //URL兼容模式，通过一个GET变量传递PATHINFO，默认为zotop，index.php?zotop=/zotop.index.index/id/1/parentid/2
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

		$uri = trim($uri,'/');
        $uri = preg_replace('#//+#', '/', $uri);
        $uri = url::alias($uri);

		return $uri;
	}

	/**
	 * 返回 url 的别名
	 *
	 * @return $string
	 */	
	public static function alias($uri)
	{
		return $uri;
	}


	/**
	 * url 分析，返回url数组
	 *
	 * @return array
	 */		
	public static function parse($url='')
	{
		$url = empty($url) ? url::location() : $url;

		$url = parse_url($url);

		return $url;
	}


    /**
    * Resolves //, ../ and ./ from a path and returns
    * the result. Eg:
    *
    * /foo/bar/../boo.php    => /foo/boo.php
    * /foo/bar/../../boo.php => /boo.php
    * /foo/bar/.././/boo.php => /foo/boo.php
    *
    * This method can also be called statically.
    *
    * @param  string $path URL path to resolve
    * @return string      The result
    */
    public function resolve($path)
    {
        $path = explode('/', str_replace('//', '/', $path));

        for ($i=0; $i<count($path); $i++)
		{
            if ($path[$i] == '.')
			{
                unset($path[$i]);
                $path = array_values($path);
                $i--;

            }
			elseif ($path[$i] == '..' AND ($i > 1 OR ($i == 1 AND $path[0] != '') ) )
			{
                unset($path[$i]);
                unset($path[$i-1]);
                $path = array_values($path);
                $i -= 2;

            } 
			elseif ($path[$i] == '..' AND $i == 1 AND $path[0] == '')
			{
                unset($path[$i]);
                $path = array_values($path);
                $i--;

            }
			else
			{
                continue;
            }
        }

        return implode('/', $path);
    }

    /**
    * Returns the standard port number for a protocol
    *
    * @param  string  $scheme The protocol to lookup
    * @return integer         Port number or NULL if no scheme matches
    *
    * @author Philippe Jausions <Philippe.Jausions@11abacus.com>
    */
    public function getStandardPort($scheme)
    {
        switch (strtolower($scheme))
		{
            case 'http':    return 80;
            case 'https':   return 443;
            case 'ftp':     return 21;
            case 'imap':    return 143;
            case 'imaps':   return 993;
            case 'pop3':    return 110;
            case 'pop3s':   return 995;
            default:        return null;
       }
    }

	public function join($params, $url='')
	{
		if ( empty($url) ) $url = url::location();

		if ( is_string($params) )
		{
			parse_str($params,$params);
		}
		
		$u = parse_url($url);

        if(isset($u['query']))
		{
            parse_str($u['query'],$p);

            $params = array_merge($p,$params);
        }
		
		if ( is_array($params) )
		{
			$query = http_build_query($params);
		}

		$scheme = empty($u['scheme']) ? '' : $u['scheme'].'://';
		$user	= empty($u['user']) ? '' : $u['user'].':';
		$pass	= empty($u['pass']) ? '' : $u['pass'].'@';
		$host	= $u['host'];
		$port = empty($u['port']) ? '' : ':'.$u['port'];
		$path	= $u['path'];
		$query	= empty($query) ? '' : '?'.$query;
		$fragment = empty($u['fragment']) ? '' : '#'.$u['fragment'];

		return "$scheme$user$pass$host$port$path$query$fragment";
	}

}