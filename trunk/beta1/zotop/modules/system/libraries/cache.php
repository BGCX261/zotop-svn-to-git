<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * cache操作类，完成对cache的操作
 *
 * @package		zotop
 * @class		cache_base
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class cache_base
{
	protected $driver = null; //缓存缓存驱动
	protected $config = array(); //缓存配置

	/**
	 * 类初始化
	 *
	 * @param   string|array  config 配置
	 * @return  object
	 */
	public function __construct($config = array())
	{
		//支持json格式的缓存配置
		if ( is_string($config) )
		{
			 $config = json_decode($config,true);
		}

		if ( is_array($config) )
		{
			$config += array(
				'driver'=> zotop::config('system.cache.driver'),
				'expire'=> (int)zotop::config('system.cache.expire'),
			);		
		}
		
		if ( empty($config['driver']) )
		{
			$config['driver'] = 'file';
		}
		
		//缓存驱动程序
		$driver = 'cache_'.strtolower($config['driver']);
		
		//加载驱动程序
		if ( !zotop::autoload($driver) )
		{
			zotop::error(zotop::t('未能找到缓存驱动 "{$driver}"',$config));
		}

		$this->driver = new $driver($config);

		return $this->driver;		
	}

	/**
	 * 获取相同配置的唯一实例
	 *
	 * @param   string|array  config 配置
	 * @return  object
	 */

	public static function &instance($config = array())
	{
        static $instances = array();
		
        //实例唯一的编号        
        $id = serialize($config);
        
        if ( !isset($instances[$id]) )
        {
            //取得驱动实例
            $instance	= new cache($config);
            
            //存储实例
            $instances[$id] = &$instance;            
        }
        
        return $instances[$id];		
	}

	/**
	 * 测试缓存是否被支持
	 *
	 * @return  bool
	 */
	protected function test()
	{
		return $this->driver->test();	
	}

	/**
	 * 格式化缓存的key
	 *
	 * @param   string   cache key
	 * @return  string
	 */
	protected function escape($key)
	{
		return str_replace(array('/', '\\', ' '), '_', $key);
	}

	/**
	 * 判断缓存是否存在
	 *
	 * @param   string   cache key
	 * @return  string
	 */
	public function exists($key)
	{
		$key = $this->escape($key);

		return $this->driver->exists($key);		
	}

    /**
     * 读取缓存
     * 
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function get($key)
	{
		$this->Q(true);
		
		$key = $this->escape($key);

		return $this->driver->get($key);
	}

    /**
     * 设置缓存
     * 
     * @param string $key 缓存变量名
	 * @param mix $value 缓存数据
	 * @param int $expire 缓存时间,单位秒
     * @return mixed
     */
	public function set($key,$value,$expire=null)
	{
		$this->W(true);

		$key = $this->escape($key);

		if ( $expire === null )
		{
			//获取默认的缓存时间
			$expire = $this->config['expire'];
		}

		return $this->driver->set($key, $value, $expire);
	}

    /**
     * 删除缓存
	 *
     * @param string $key 缓存变量名
     * @return mixed
     */
	public function delete($key)
	{
		$key = $this->escape($key);

		return $this->driver->delete($key);		
	}

    /**
     * 清除全部缓存
	 *
     * @return mixed
     */
	public function clear()
	{
		return $this->driver->clear();	
	}

   /**
    * 查询次数
    *
    * @return int
    */ 
    public static function Q($n=false)
    {
        static $times = 0;

        if ( empty($n) )
        {
            return $times;
        }

        $times++;
    }
    
   /**
    * 写入次数
    *
    * @return int
    */    
    public static function W($n=false)
    {
        static $times = 0;

        if ( empty($n) )
        {
            return $times;
        }
        $times++;    
    }


}
?>