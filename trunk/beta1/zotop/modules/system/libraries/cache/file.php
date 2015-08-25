<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * MYSQL 数据库操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.cache
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class cache_file_base
{
	protected $config = array();
	protected $root = '';

	public function __construct($config=array())
	{
		$this->config = array_merge($this->config, $config);
		
		if ( !empty($this->config['dir']) )
		{
			$this->root = ZOTOP_PATH_ROOT.DS.$this->config['dir'];
		}
		else
		{
			$this->root = ZOTOP_PATH_CACHE;
		}

		$this->root = trim($this->root,DS);
		
		if ( !$this->test() )
		{
			throw new exception('cache.unwritable', $this->root);
		}
	}

	public function test()
	{
		if ( ! is_dir($this->root) OR ! is_writable($this->root) )
		{
			return false;
		}

		return true;
	}

	public function filepath($key)
	{
		$filename =	md5($key);
		
		$filename = $filename.'.php';

		$filename = $this->root.DS.$filename;

		return $filename;
	}

    /**
     * 读取缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function get($key)
	{
		$filename = $this->filepath($key);

		return zotop::data($filename);
	}

    /**
     * 读取缓存
     * 
     * @param string $key 缓存变量名
	 * @param mix $value 缓存数据
	 * @param int $expire 缓存时间
     * @return mixed
     */
	public function set($key, $value ,$expire=0)
	{
		$filename = $this->filepath($key);

		return zotop::data($filename,$value,$expire);	
	}

    /**
     * 删除缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function delete($key)
	{
		$filename = $this->filepath($key);

		return zotop::data($filename,null,-1000);		
	}

    /**
     * 清理缓存缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function clear()
	{
		folder::clean($this->root);
	}
}