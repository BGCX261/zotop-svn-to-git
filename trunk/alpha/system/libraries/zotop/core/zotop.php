<?php
class zotop
{
    public static $marks=array();
	public static $events=array();
    /**
     * 系统启动，并注册一系列的系统启动事件
     *
     */
    public static function boot()
    {
        static $boot=false;
        if($boot)
        {
            return true;//boot函数只能运行一次
        }
		$boot=true;
        self::mark('system.begin');
		//获取当前时间
		define('TIME',time());

		//错误及异常处理
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		//时区设置
		if (function_exists('date_default_timezone_set'))
		{
			$timezone = self::config('zotop.locale.timezone');
			$timezone = empty($timezone) ? date_default_timezone_get() : $timezone;
			if($timezone)
			{
				date_default_timezone_set($timezone);
			}
		}

		//缓存开始
		ob_start();
		//注册加载函数
		spl_autoload_register(array(__CLASS__, 'autoload'));
		//输出头
		header("Content-Type: text/html;charset=utf-8");
		//注册系统事件
		self::add('system.routing',array('router','finduri'));
		self::add('system.routing',array('router','execute'));
		self::add('system.operation', array('module', 'operation'));
		self::add('system.shutdown', array(__CLASS__, 'shutdown'));
    }

	//系统关闭
	public static function shutdown()
	{
		$content = ob_get_contents();
		ob_end_clean();
		self::render($content);
	}

	//渲染
	public static function render($output)
	{
		$mark = self::mark('system.begin','system.end');
		$output=str_replace
		(
			array('{$runtime}','{$memory}','{$include}'),
			array($mark['time'].' S',$mark['memory'].' MB',count(get_included_files())),
			$output
		);
		echo $output;
	}

	public static function config($key='',$value=null)
	{
		static $configs=array();
		//value 值不为空，则为赋值
		if(!is_null($value))
		{
			$configs[strtolower($key)]=$value;
			return ;
		}
		//$name 为空，则获取整个config数组
		if(empty($key))
		{
			return $configs;
		}
		//$name为数组，则追加整个数组
		if(is_array($key))
		{
			$configs=array_merge($configs,array_change_key_case($key));
			return $configs;
		}
		$key=strtolower($key);
		if( ! array_key_exists($key,$configs))
		{
             return NULL;
		}
		return $configs[$key];
	}

	//存储简单类型数据，字符串，数组等,$file=完整的名称
	public static function file($file,$value='',$expire=0)
	{
		static $files=array();
		$file=path::clean($file);
		//echo $file.'<br>';
		if(''!== $value)
		{
			if(is_null($value))
			{
				$result=unlink($file);
				if($result)
				{
					unset($files[$file]);
				}
				return $result;
			}
			else
			{
				$content = "<?php\nif (!defined('ZOTOP')) exit();\n//".sprintf('%012d',$expire)."\nreturn ".var_export($value,true).";\n?>";
				$result	= file_put_contents($file,$content);
				$files[$file] = $value;
			}
			return true;
		}
		if(isset($files[$file]))
		{
			return $files[$file];
		}
		if(file_exists($file) && false !== $content = file_get_contents($file))
		{
			//注意：Dreamweaver会在文件最前面插入两个未知字符,39
			$expire=(int)substr($content,strpos($content,'//')+2,12);
			if($expire!= 0 && time() > filemtime($file) + $expire)
			{
				//过期删除
				unlink($file);
				return false;
			}
			$value = eval(substr($content,strpos($content,'//')+14,-2));
			$files[$file] = $value;
		}
		else
		{
			$value = false;
		}
		return $value;
	}

	public static function instance($classname,$method='',$args=array())
	{
	    static $instances = array();
	    $id = empty($args) ? strtolower($classname.$method) : strtolower($classname.$method.zotop::guid($args));
	    if(!isset($instances[$id]))
	    {
	        if(class_exists($classname))
	        {
	            $instance = new $classname();
	            if(method_exists($instance,$method))
	            {
                    $instances[$id] = call_user_func_array(array(&$instance, $method), $args);
	            }
	            else
	            {
	                $instances[$id] = $instance;
	            }
	        }
	        else
	        {
	             msg::error($classname.' not found!');
	        }
	    }
	    return $instances[$id];
	}

	public static function guid($mix)
	{
        if(is_object($mix) && function_exists('spl_object_hash'))
        {
            return spl_object_hash($mix);
        }
        elseif(is_resource($mix))
        {
            $mix = get_resource_type($mix).strval($mix);
        }else
        {
            $mix = serialize($mix);
        }
        return md5($mix);
	}

    /**
     * 导入文件，支持别名和名称空间
     * @param string $name 别名或者名称空间，如：page或者system.ui.page,其中点号标识目录分割，最后一个单元（page）指的是文件名称
     * @param string $path 文件位置，系统默认的为系统的库文件夹
     * @param string $base 名称空间的前缀
     * @return bool;
     */
    public static function import($name,$path=LIBROOT,$base='')
	{
 		static $imports=array();
		if(self::register($name) != false)
		{
			$file=self::register($name);
		}
		else
		{
			if(!empty($base))
			{
				$name=trim($base , '.').'.'.$name;
			}
		    $file = $path.DS.str_replace( '.', DS, $name).'.php';
		}
		//这儿应该加入一个对class是否存在的判断？
		//echo $file.'<br>';
		if(isset($imports[$file]))
		{
			return true;
		}
		$imports[$file] = true;
		return self::load($file);
	}

	/**
	 * load 用于加载文件,相当于include_once，不返回任何错误
	 * @param string $file 要加载的文件
	 */
	public static function load($file){
		static $loads=array();
		if(isset($loads[$file]))
		{
			return false;
		}
		if(is_file($file))
		{
			require $file;
			$loads[$file]=true;
			return true;
		}
		return false;
	}

	/**
	 * 类注册，用于自动加载文件
	 *
	 * @param string $name 类的名称
	 * @param string $file 类对应的文件
	 * @return array|string
	 */
	public static function register($name='',$file='')
	{
		static $register=array();

		//无参数的时候返回整个别名数据
		if(empty($name))
		{
			return $register;
		}
		//第一个参数为数组时，将整个别名加入数组中
		if(is_array($name))
		{
			$register = array_merge($register,$name);
			return $register;
		}
		//第二个参数即路径为空时候根据名称返回路径
		if(empty($file))
		{
			$name=strtolower($name);
			$register=array_change_key_case($register);
			$key=$register[$name];
			return isset($key)?$key:false;
		}
		//加入别名
		$register[$name] = $file;
		return true;
	}

	public static function autoload($class)
	{
		//echo $class.'<br>';
		//如果类已经存在则不自动加载
		if(class_exists($class,false))
		{
		    return true;
		}

		// 上面代码里面没有返回false? 是不是每次执行到这儿都会执行 return?
		//如果存在该类的注册，则加载该类
		if(self::register($class))
		{
		    return self::import($class);
		}

		$baseclass='Base'.ucfirst($class);

		if(!class_exists($baseclass,false))
		{
			if(self::register($baseclass)==false)
			{
				return false;
			}

			self::import($baseclass);
		}
		$newclass = 'class '.$class.' extends '.$baseclass.'{ }';
		//echo $newclass.'<br>';
		eval($newclass);
		return true;
	}

    /**
     * 输出变量的内容
     * @param mixed $var 要输出的变量
	 * @param string $label 多个变量输出问题可以加上标签
     * @param boolean $return 是否返回输出内容
	 */
    //TODO 多个变量支持
	public static function dump($var, $label=null, $return=false)
	{
		$content = "\n<pre>\n".'('.gettype($var).') '.htmlspecialchars(print_r($var, TRUE))."\n</pre>\n";
		if($return)
		{
			return $content;
		}else{
	        echo $content;
		    return false;
		}

	}

	public static function error($code='',$description='',$file='',$line=0)
	{
		static $error = array('code'=>100,'description'=>'');
		if(is_int($code)){
			$error['code']	=	$code;
			$error['description'] = $description;
		}
		if(is_array($code))
		{
			$error = array_merge($error,array_change_key_case($code));
		}
		if(is_string($code) && !empty($code))
		{
			$error['code']	=	1;
			$error['description'] = $code;
		}
		if($error['code']===0)
		{
			return false;//没有任何错误
		}
		return $error;//有错误
	}

	public function halt()
	{

		$args = func_get_args();
		if($error = call_user_func_array(array('zotop','error') , $args) )
		{
			//zotop::dump($error);

			exit($error['description']);
		}
		exit;
	}

	public static function url($url , $params=array() , $fragment='')
	{
		return url::build($url,$params,$fragment);
	}

	/**
	 * 性能测试，通过标记记录特定区域之间的性能
	 *
	 * @param string $start 标记点名称
	 * @param string $end 结束标记点名称
	 * @param int $decimals 返回的数据有效位数
	 * @return array|bool
	 */
	public static function mark($start='', $end='', $decimals = 4)
	{
	    static $marks=array();
	    //zotop::mark(),返回全部标记
	    if(empty($start))
	    {
	        return $marks;
	    }
	    //zotop::mark($name),做一个标记
	    $start = strtolower($start);
	    if(empty($end))
	    {
	        $marks[$start]['time']=microtime(TRUE);
	        $marks[$start]['memory']=function_exists('memory_get_usage') ? memory_get_usage() : 0 ;
	        return true;
	    }
	    //zotop::mark($start,$end,6);返回两个标记之间的数据
	    $end = strtolower($end);
	    if( !isset($marks[$end]) )
	    {
	        self::mark($end);
	    }
		return array(
			'time'=>number_format($marks[$end]['time']-$marks[$start]['time'],$decimals),//返回单位为秒
			'memory'=>number_format(($marks[$end]['memory']-$marks[$start]['memory'])/1024/1024,$decimals) //返回单位为Mb
		);
	}

	/**
	 *   获取特定的事件的函数集,相关函数参考Kohana的事件
	 *
	 * @param string $name 事件的名称，如果为空则返回全部事件
	 * @return array 事件函数集
	 */
	public static function event($name='')
	{
		if(empty($name))
		{
			return self::$events;
		}
		return empty(self::$events[$name]) ? array() : self::$events[$name];
	}

	/**
	 * 运行一个事件，并传入相关参数，结果是每次运行函数的和
	 *
	 * @param string $name Event Name
	 * @param array $args 相关参数
	 * @return bool 如果运行了事件就返回真，否则返回假
	 */
	public static function run($name,$args='')
	{

	    if(!empty(self::$events[$name]))
		{
			$callbacks = self::event($name);
			$args = func_get_args();
			$str = '';
			//zotop::dump($callbacks);
			foreach($callbacks as $callback)
			{
			    $str .=(string)call_user_func_array($callback,array_slice($args,1));
			}
			return $str;
		}
		return false;
	}

	/**
	 * 运行一系列的对$var的处理函数，最终返回的还是$var,此函数与run相似，只是功能不同，返回的结果不同
	 *
	 * @param string $name 事件 的 名称
	 * @param mix $value 待处理的数据，可以使string，也可以是array或者其它数据
	 * @return mix 处理后的$value
	 */
	public static function filter($name,$value)
	{
		if(!empty(self::$events[$name]))
		{
		    //处理可能的传入的多个参数,其他参数为辅助参数
		    $args=func_get_args();
			$callbacks=self::event($name);
			foreach($callbacks as $callback)
			{
				$args[1] = $value;
			    $value = call_user_func_array($callback , array_slice($args,1));
			}
		}
		return $value;
	}


	/**
	 * 在末尾添加一个新的回调函数
	 *
	 * @param $name
	 * @param $callback
	 * @return boolean
	 */
	public static function add($name,$callback)
	{
		if ( ! isset(self::$events[$name]))
		{
			self::$events[$name] = array();
		}
		elseif (in_array($callback, self::$events[$name], TRUE))
		{
			return FALSE;
		}
		self::$events[$name][] = $callback;
		return TRUE;
	}

	public static function set($name,$callback)
	{
	    //清理全部的数据
	    self::remove($name);
	    self::add($name,$callback);
	}

	/**
	 * 从事件集合中删除特定的事件或者事件组
	 *
	 * @param string $name 事件名称
	 * @param string $callback
	 * @return null
	 */
	public static function remove($name , $callback = false)
	{
		if ($callback === FALSE)
		{
			self::$events[$name] = array();
		}
		elseif (isset(self::$events[$name]))
		{
			foreach (self::$events[$name] as $i => $event_callback)
			{
				if ($callback === $event_callback)
				{
					unset(self::$events[$name][$i]);
				}
			}
		}
	}

	/**
	 * 在特定的事件组前插入特定的事件
	 *
	 * @param string $name 事件名称
	 * @param array $existing 函数
	 * @param array $callback 插入的函数
	 * @return boolean 返回插入是否成功
	 */
	public static function before($name,$existing,$callback)
	{
		if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE)
		{
			return self::add($name, $callback);
		}
		else
		{
			return self::insert($name, $key, $callback);
		}
	}

	/**
	 * 在特定的事件组后插入特定的事件
	 *
	 * @param string $name 事件名称
	 * @param array $existing 函数
	 * @param array $callback 插入的函数
	 * @return boolean 返回插入是否成功
	 */
	public static function after($name,$existing,$callback)
	{
		if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE)
		{
			return self::add($name, $callback);
		}
		else
		{
			return self::insert($name, $key+1, $callback);
		}
	}


	/**
	 * 在特定的事件组的特定位置插入事件
	 *
	 * @param string $name 事件组名称
	 * @param int $key		插入事件的位置
	 * @param array $callback 被插入的事件
	 * @return boolean 插入是否成功
	 */
	public static function insert($name,$key,$callback)
	{
		if(in_array($callback,self::$events[$name],TRUE))
		{
			return false;
		}
		self::$events[$name]=array_merge
		(
			array_slice(self::$events[$name],0,$key),
			array($callback),
			array_slice(self::$events[$name],$key)
		);
		return true;
	}

	/**
	 * 替换事件
	 *
	 * @param   string  $name  事件组名称
	 * @param   array   $existing  被替换的事件
	 * @param   array   $callback 新的事件
	 * @return  boolean 替换是否成功
	 */
	public static function replace($name, $existing, $callback)
	{
		if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name], TRUE)) === FALSE)
			return FALSE;

		if ( ! in_array($callback, self::$events[$name], TRUE))
		{
			// Replace the exisiting event with the new event
			self::$events[$name][$key] = $callback;
		}
		else
		{
			// Remove the existing event from the queue
			unset(self::$events[$name][$key]);

			// Reset the array so the keys are ordered properly
			self::$events[$name] = array_values(self::$events[$name]);
		}

		return TRUE;
	}

	/**
	 * 判断事件是否存在
	 *
	 * @param $name 事件组名称
	 * @return boolean
	 */
	public static function exist($name)
	{
		return empty(self::$events[$name]) ? false : true;
	}

	/**
	 * 存取系统的用户信息，此代码目前需带完善，尚有错误
	 *
	 * @param $key 用户属性名称或者用户数据数组或者空
	 * @return array或者string
	 */
	public static function user($key = '')
	{
		static $user = array();
		if(empty($user))
		{
			$user = cookie::get('zotop.user');
		}
		if(empty($key))
		{
			return $user;
		}
		if(is_string($key))
		{
			return $user[strtolower($key)];
		}
		if(is_array($key))
		{
			$user = array_merge($user,$key);
			$user = cookie::set('zotop.user',$user);
			return $user;
		}
		return $user;
	}

    /**
     * 连接系统默认的数据库
     *
     * @param $config array  数据库参数
     * @return object 数据库连接
     */
    public static function db($settings = array())
    {
        $config = array(
           'driver' => zotop::config('zotop.db.driver'),
           'username' => zotop::config('zotop.db.username'),
           'password' => zotop::config('zotop.db.password'),
           'hostname' => zotop::config('zotop.db.hostname'),
           'hostport' => zotop::config('zotop.db.hostport'),
           'database' => zotop::config('zotop.db.database'),
           'prefix' => zotop::config('zotop.db.prefix'),
           'charset' => zotop::config('zotop.db.charset'),
        );
        $config = array_merge($config,$settings);

        return database::instance($config);
    }

}
?>