<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * memcache缓存操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.cache
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class cache_memcache_base
{
	protected $memcache;
	protected $connected;

	public function __construct($config=array())
	{
		if ( !$this->test() )
		{
			zotop::error(zotop::t('The memcache extension is not available'));
		}

		
		$host = $config['host'];
		$host = empty($host) ? zotop::config('system.cache.memcache.host') : $host;
		$host = empty($host) ? '127.0.0.1' : $host;

		$post = $config['post'];
		$port = empty($port) ? zotop::config('system.cache.memcache.port') : $port;
		$port = empty($port) ? '11211' : $port;

		$timeout = isset($config['timeout']) ? (bool)$config['timeout'] : false;

		$persistent = isset($config['persistent']) ? (bool)$config['persistent'] : false;
		
		unset($config);
		
		//是否持久链接
		$connect = $persistent ? 'pconnect' : 'connect';

		$this->memcache = &new Memcache;

		if ( $timeout === false )
		{
			$this->connected = @$this->memcache->$connect($host, $port);
		}
		else
		{
			$this->connected = @$this->memcache->$connect($host, $port, $timeout);
		}

		if ( !$this->connected )
		{
			zotop::error(zotop::t('无法连接memcache服务器 “{$host}:{$port}”，请检查参数配置是否正确',array('host'=>$host,'port'=>$port)));
		}

	}

	/**
	 * 测试是否支持memcache
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function test()
	{
		return (extension_loaded('memcache') && class_exists('Memcache'));
	}



    /**
     * 读取缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function get($key)
	{
		return $this->memcache->get($key);
	}

    /**
     * 设置缓存
     * 
     * @param string $key 缓存变量名
	 * @param mix $value 缓存数据
	 * @param int $expire 缓存时间,单位秒
     * @return mixed
     */
	public function set($key, $value ,$expire=0)
	{
		return $this->memcache->set($key, $value, 0,  $expire);
	}

    /**
     * 删除缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function delete($key)
	{
		return $this->memcache->delete($key);
	}

    /**
     * 清理缓存缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function clear()
	{
		return $this->memcache->flush();
	}
}