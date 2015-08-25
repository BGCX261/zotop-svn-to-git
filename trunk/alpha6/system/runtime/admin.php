<?php

/**
 * 系统的核心文件，包含系统底层的常用功能
 *
 * @package    zotop
 * @author     zotop team
 * @copyright  (c)2009 zotop team 
 * @license    http://zotop.com/license.html
 */
class zotop
{
    public static $events = array();
    public static $marks = array();
	public static $logs = array();

    public static function boot()
    {
        static $boot = false;
        
        //boot函数只能运行一次
        if($boot) return true;        
         
        //启动缓存区
        ob_start();
        
        //注册自动加载函数
        spl_autoload_register(array('zotop','autoload'));
        
        //设置系统事件
        zotop::add('system.init',array('application','init'));
        zotop::add('system.route',array('router','init'));
        zotop::add('system.route',array('router','execute'));
        zotop::add('system.run',array('application','boot'));
        zotop::add('system.run',array('application','execute'));                
        zotop::add('system.render',array('application','render'));
        zotop::add('system.shutdown',array('zotop','shutdown'));
        zotop::add('system.404',array('application','show404'));		
		zotop::add('system.reboot',array('runtime','reboot'));
		zotop::add('system.reboot',array('application','reboot'));

		// Sanitize all request variables
		$_GET    = zotop::sanitize($_GET);
		$_POST   = zotop::sanitize($_POST);
		$_COOKIE = zotop::sanitize($_COOKIE);

		//boot true
        $boot = true;
        
    }
    
    /**
     * 系统关闭，并输出渲染内容
     *
     * @return string
     */
    public static function shutdown()
    {
        //获取页面内容
        $contents = ob_get_contents();
        
        //清理输出数据
        ob_end_clean();
        
        //渲染页面内容
        $contents = zotop::filter('system.render',$contents);
        
        //输入页面内容
        echo $contents;
    }    

    /**
     * 系统重新启动
     *
     * @return string
     */
    public static function reboot()
    {
        zotop::run('system.reboot');
    }
    
    /**
     * 系统重建
     *
     * @return string
     */
    public static function build()
    {
        zotop::boot();
        zotop::reboot();
    }
    
	public static function sanitize($value)
	{
		if ( is_array($value) OR is_object($value) )
		{
			foreach ( $value as $key => $val )
			{
				// Recursively clean each value
				$value[$key] = zotop::sanitize($val);
			}
		}
		elseif ( is_string($value) )
		{
			if ( MAGIC_QUOTES_GPC === TRUE )
			{
				// Remove slashes added by magic quotes
				$value = stripslashes($value);
			}

			if ( strpos($value, "\r") !== FALSE )
			{
				// Standardize newlines
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}

		return $value;
	}    
    
    /**
     * 类注册，用于自动加载文件
     *
     * @param string $name 类的名称
     * @param string $file 类对应的文件
     * @return array|string
     */
    public static function register($name='', $file='')
    {
        static $register = array();

        //无参数的时候返回整个别名数据
        if ( empty($name) )
        {
            return $register;
        }
        
        //第一个参数为数组时，将整个别名加入数组中
        if ( is_array($name) )
        {      
            $register = array_merge($register,$name);
                        
            return $register;
        }
        
        //第二个参数即路径为空时候根据名称返回路径
        if( empty($file) )
        {
            $name = strtolower($name);
            $register = array_change_key_case($register);
            $file = isset($register[$name]) ? $register[$name] : false;
            return $file;
        }
        
        //加入已注册类
        $register[$name] = $file;
        return true;
    }
    
    /**
     * 自动加载，用于自动加载系统的类
     *
     * @param string $class 类名
     * @return unknown_type
     */
    public static function autoload($class)
    {
        if ( class_exists($class,false) )
        {
            return true;
        }
              
        //如果存在该类的注册，则加载该类
        if ( self::register($class) )
        {
            return self::import($class);
        }
            
        //如果类尚未注册，尝试自动加载基类并自动冲基类继承
        $baseclass='zotop_'.$class;
        
        if ( !class_exists($baseclass,false) )
        {            
            if ( self::register($baseclass) == false )
            {
                return false;
            }
            self::import($baseclass);
        }
        
        
        if ( class_exists($baseclass,false) )
        {
            $newclass = 'class '.$class.' extends '.$baseclass.'{}';
            eval($newclass);
            return true;
        }
        
        return false;
    }

    /**
     * 导入文件，支持别名和名称空间
     * @param string $name 别名或者名称空间，如：page或者system.ui.page,其中点号标识目录分割，最后一个单元（page）指的是文件名称
     * @param string $path 文件位置，系统默认的为系统的库文件夹
     * @param string $ext 类的后缀名称
     * @return bool;
     */
    public static function import($name, $path=ZOTOP_PATH_LIBRARIES, $ext='.php')
    {
        static $imports=array();
        
        if ( self::register($name) != false )
        {
            $file = self::register($name);
        }
        else
        {
            $file = $path.DS.str_replace( '.', DS, $name).$ext;
        }
        
        if ( isset($imports[$file]) )
        {
            return true;
        }
        
        $imports[$file] = true;
        
        $load = self::load($file); 
               
        return $load;
    }

    /**
     * load 用于加载文件,相当于include_once，不返回任何错误
     * 
     * @param string $file 要加载的文件
     */
    public static function load($file)
    {
        static $loads = array();
        
        if ( isset($loads[$file]) )
        {
            return true;
        }
        
        if ( file_exists($file) )
        {
            require $file;            
            $loads[$file]=true;
            return true;
        }
        
        return false;
    }
    
    /**
     *   获取特定的事件的函数集,相关函数参考Kohana的事件
     *
     * @param string $name 事件的名称，如果为空则返回全部事件
     * @return array 事件函数集
     */
    public static function event($name='')
    {
        if ( empty($name) )
        {
            return self::$events;
        }
        return empty(self::$events[$name]) ? array() : self::$events[$name];
    }

    /**
     * 在末尾添加一个新的回调函数
     *
     * @param $name
     * @param $callback
     * @return boolean
     */
    public static function add($name, $callback)
    {
        if ( ! isset(self::$events[$name]) )
        {
            self::$events[$name] = array();
        }
        elseif ( in_array($callback, self::$events[$name], TRUE) )
        {
            return FALSE;
        }
        
        self::$events[$name][] = $callback;
        
        return TRUE;
    }

    /**
     * 从事件集合中删除特定的事件或者事件组
     *
     * @param string $name 事件名称
     * @param string $callback
     * @return null
     */
    public static function remove($name, $callback=false)
    {
        if ( $callback === FALSE )
        {
            self::$events[$name] = array();
        }
        elseif ( isset(self::$events[$name]) )
        {
            foreach ( self::$events[$name] as $i => $event_callback )
            {
                if ( $callback === $event_callback )
                {
                    unset(self::$events[$name][$i]);
                }
            }
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
    public static function insert($name, $key, $callback)
    {
        if ( in_array($callback, self::$events[$name],TRUE) )
        {
            return false;
        }
        
        self::$events[$name]=array_merge
        (
            array_slice(self::$events[$name], 0, $key),
            array($callback),
            array_slice(self::$events[$name], $key)
        );
        
        return true;
    }
    
    /**
     * 在特定的事件组前插入特定的事件
     *
     * zotop::before('system.routing', array('router', 'boot'), array('myrouter', 'setup'));
     *
     * @param string $name 事件名称
     * @param array $existing 函数
     * @param array $callback 插入的函数
     * @return boolean 返回插入是否成功
     */
    public static function before($name, $existing, $callback)
    {
        if ( empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE )
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
    public static function after($name, $existing, $callback)
    {
        if ( empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE )
        {
            return self::add($name, $callback);
        }
        else
        {
            return self::insert($name, $key+1, $callback);
        }
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
        if ( empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name], TRUE)) === FALSE )
        {
            return FALSE;
        }
        
        if ( ! in_array($callback, self::$events[$name], TRUE) )
        {
            self::$events[$name][$key] = $callback;
        }
        else
        {
            unset(self::$events[$name][$key]);
            self::$events[$name] = array_values(self::$events[$name]);
        }
        
        return TRUE;
    }

    /**
     * 运行一个事件，并传入相关参数，结果是每次运行函数的和
     *
     * @param string $name Event Name
     * @param array $args 相关参数
     * @return bool 如果运行了事件就返回真，否则返回假
     */
    public static function run($name, $args='')
    {

        if ( $callbacks = self::event($name) )
        {
            $str = '';
            
            $args = func_get_args();
            
            foreach($callbacks as $callback)
            {
                $str .= (string) call_user_func_array($callback, array_slice($args,1));
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
    public static function filter($name, $value)
    {
        if ( $callbacks = self::event($name) )
        {
            //处理可能的传入的多个参数,其他参数为辅助参数
            $args = func_get_args();
            
            foreach( $callbacks as $callback )
            {
                $args[1] = $value;
                $value = call_user_func_array($callback, array_slice($args,1));
            }
        }
        return $value;
    }
    
    /**
     * 系统config设置函数
     *
     * @param string|array $key string:设置的键名获取或者设置该键值，array：配置数组赋值数组，空返回整个设置数组
     * @param string $value 键值，用于赋值
     * @return mix
     */
    public static function config($key='', $value=null)
    {
        static $configs=array();

        //$key 为空，则获取整个config数组
        if ( empty($key) )
        {
            return $configs;
        }
        
        //$key为数组，则追加整个数组
        if ( is_array($key) )
        {
            $configs=array_merge($configs, array_change_key_case($key));
            
            return $configs;
        }
        
        //$key为字符串，根据$key取值或者赋值
        if ( is_string($key) )
        {
            $key=strtolower($key);
            
            if ( is_null($value) )
            {
                if ( isset($configs[$key]) )
                {
                    return $configs[$key];
                }
                return null;              
            }
            
            $configs[$key] = $value;
            return $value;            
        }
        
        //返回整个配置
        return $configs;
    }

    /**
     * 语言设置函数
     *
     * @param string|array $key string:设置的键名获取或者设置该键值，array：配置数组赋值数组，空返回整个设置数组
     * @param string $value 键值，用于赋值
     * @return mix
     */
    public static function lang($key=null, $value=null)
    {
        static $langs = array();
        
        //返回语言集
        if ( empty($key) )
        {
            return $langs;
        }
        
        //批量设定语言
        if ( is_array($key) )
        {
            $langs=array_merge($langs, array_change_key_case($key));
            return $langs;            
        }
        
        //赋值或者取值
        if( is_string($key) )
        {            
            if ( is_null($value) )
            {
                if ( isset($langs[$key]) )
                {
                    return $langs[$key];
                }
                
                //未能找到对应的翻译时候，直接返回原语句
                return $key;
            }
            
            $langs[$key] = $value;
            
            return $value;
        }
        
        //返回全部
        return $langs;
    }    

    /**
     * 
     * 翻译并替换字符串中的变量
     * 
     * @param string $string 待转换字符串
     * @param array $params 参数 
     * @return string
     */
    public static function t($string, $params=array())
    {
        
        
        //翻译
        $string = zotop::lang($string);

        //替换掉变量      
        if ( is_array($params) )
        {
            foreach( $params as $key=>$value )
            {
                $string = str_replace('{$'.$key.'}', $value, $string);
            }
        }
        
        return $string;
    }

	/**
	 * 标记一个监测开始点，并记录时间和内存消耗
	 * 
	 * @param string $tag 标记名称
	 * @return bool；
	 */
	public static function start($tag)
	{
		$tag=strtolower($tag);
		
		if ( !isset(self::$marks[$tag]['start']) )
		{
			self::$marks[$tag]['start']=array(
				'time' => microtime(TRUE),
				'memory' => function_exists('memory_get_usage') ? memory_get_usage() : 0
			);
		}
		
		return true;
	}

	/**
	 * 标记一个监测结束点，并记录用时和内存消耗
	 * 
	 * @param string $tag 标记名称
	 * @return true;
	 */
	public static function stop($tag)
	{
		$tag=strtolower($tag);
		
		if ( !isset(self::$marks[$tag]['stop']) )
		{
			self::$marks[$tag]['stop']=array(
				'time' => microtime(TRUE),
				'memory' => function_exists('memory_get_usage') ? memory_get_usage(): 0
			);
		}
		return true;
	}
	
	/**
	 * 获取特定的监测数据
	 * 
	 * @param string $tag 如果标记点为空则返回全部的监测点数据
	 * @return array；
	 */
	public static function mark($tag='')
	{
		if ( empty($tag) )
		{
			return self::$marks;
		}
		
		$tag=strtolower($tag);

		if ( !isset(self::$marks[$tag]['start']) )
		{
			return false;
		}
		
		if ( !isset($marks[$tag]['stop']) )
		{
			self::stop($tag);
		}
		
		return array(
			'time' => number_format(self::$marks[$tag]['stop']['time'] - self::$marks[$tag]['start']['time'], 4),//返回单位为秒
			'memory' => number_format((self::$marks[$tag]['stop']['memory'] - self::$marks[$tag]['start']['memory'])/1024/1024, 4) //返回单位为Mb
		);
	}

     /**
     * 输出变量的内容
     * @param mixed $var 要输出的变量
     * @param string $label 多个变量输出问题可以加上标签
     * @param boolean $return 是否返回输出内容
     */
    public static function dump($var, $return=false)
    {
        if ( $var === NULL )
		{
			$content = '<small>(NULL)</small>';
		}
		elseif ( is_bool($var) )
		{
			$content = '<small>(bool)</small> '.($var ? 'TRUE' : 'FALSE');
		}
		elseif ( is_float($var) )
		{
			$content = '<small>(float)</small> '.$var;
		}
		else
		{
            $content = "\n<pre>\n".'('.gettype($var).') '.htmlspecialchars(print_r($var, TRUE))."\n</pre>\n";
		}
		
        if ( $return )
        {
            return $content;
        }
        else
        {
            echo $content;
        }
    }

    /**
     * 错误输出信息
     * 
     */
    public static function error($message='')
    {
        $error = array(
        	'code' => 0,
        	'title' => 'ZOTOP ERROR',
        	'content' => 'Unknown System Error!',
            'detail' => ''
        ); 
              
        //数组设置
        if ( is_array($message) )
        {
            $error = array_merge($error, array_change_key_case($message));
        }
        if ( is_string($message) )
        {
            $error['content'] = $message;
        }
        
        msg::error($error);        
    }
    
    public static function applicationes($id='', $key='')
    {
        return '';
    }

    /**
     * 模块的配置获取
     *
     * @param string|array $id 应用的ID，如：admin
     * @param string $key 键名称，如：name
     * @return mix
     */    
    public function modules($id='', $key='')
    {
        static $modules = array();

        if ( empty($modules) )
        {
            $modules = (array)zotop::config('zotop.module');
        }
               
        if ( is_array($modules) )
        {
            if ( !isset($modules['zotop']) )
            {
                $modules['zotop'] = array('id' => 'zotop','title' => 'zotop','path' => '$modules/zotop','url' => '$modules/zotop','status'=>1);
            }
            
            foreach( $modules as $k=>$m )
            {
                $modules[$k]['path'] = path::decode($modules[$k]['path']);
                $modules[$k]['url'] = url::decode($modules[$k]['url']);
            }
        }
        
        if ( empty($id) )
        {
            return $modules;
        }        

        if ( is_array($id) )
        {
            $modules = array_merge($modules,$id);
            
            zotop::config('zotop.module',$modules);
            
            return $modules;
        }
        
        //return module
        if ( is_string($id) )
        {
            $module = array();
            
            if ( isset($modules[strtolower($id)]) )
            {
                $module = $modules[strtolower($id)];
            }
			else
			{
				return null;
			}
           
            if ( empty($key) )
            {
                return $module;
            }
                        
            return $module[strtolower($key)];            
        }
    }

    public static function url($uri , $params=array() , $fragment='')
    {
        return url::build($uri, $params, $fragment);
    }
    
    public static function redirect($uri , $params=array() , $fragment='')
    {
        $url = zotop::url($uri, $params, $fragment);
        
        header("Location: ".$url);
        
        exit();
    }

    /**
     * 存储简单类型数据，字符串，数组
     * @param string $file 完整的file名称或者cache.setting,config.system
     * @param mix $value 值
     * @param int $expire 缓存时间
     * 
     * @return mix
     */
    public static function data($file, $value='', $expire=0)
    {
        static $files = array();
        
		if ( strtolower(substr($file , -4)) != '.php' && strpos($file, DS) == false )
		{
		    $file = ZOTOP_PATH_DATA.DS.str_replace( '.', DS, $file).'.php';		    
		}
        
        //删除缓存
        if ( $value === null )
        {
            $result = @unlink($file);
            
            if ( $result )
            {
                unset($files[$file]);
            }
            
            return $result;
        }
        
        //设置缓存
        if( $value!=='' ) 
        {
            $content = "<?php\nif (!defined('ZOTOP')) exit();\n//".sprintf('%012d',$expire)."\nreturn ".var_export($value,true).";\n?>";
            $result	= file_put_contents($file,$content);
            $files[$file] = $value;
            return true;
        }

        //直接读取已读缓存
        if ( isset($files[$file]) )
        {
            return $files[$file];
        }
        
        //读取缓存
        if ( file_exists($file) && false !== $content = file_get_contents($file) )
        {
            $expire = (int) substr($content, strpos($content,'//')+2,12);
            
            if ( $expire != 0 && time() > filemtime($file) + $expire )
            {
                //过期删除
                @unlink($file);
                return null;
            }
            
            $value = eval(substr($content, strpos($content,'//') + 14, -2));
            
            $files[$file] = $value;
        }
        else
        {
            $value = null;
        }
        
        return $value;
    }
    
    /**
     * 缓存
     *
     * @param $config array  数据库参数
     * @return object 数据库连接
     */
	public static function cache($name, $value='', $expire=0)
	{

	}

    public static function cookie($name='', $value='', $expire='', $path='', $domain='')
    {
        $prefix = zotop::config('system.cookie.prefix');
        $prefix = empty($prefix) ? 'zotop_' : $prefix;

        $name = isset($name) ? str_replace('.', '_', $prefix.$name) : null ;

        $expire = empty($expire) ? zotop::config('system.cookie.expire') : $expire;
        $expire = empty($expire) ? 0 : (int)$expire + time();

        $path = empty($path) ? zotop::config('system.cookie.path') : $path;
        $path = empty($path) ? '/' : $path;

        $domain = empty($domain) ? zotop::config('system.cookie.domain') : $domain;
        $domain = empty($domain) ? '' : $domain;

        if ( $name === null)
        {
            unset($_COOKIE); 
            return true;
        }

        if ( $name === '' )
        {
            return $_COOKIE;
        }

        if ( $value === null )
        {
            unset($_COOKIE[$name]);
            return setcookie($name,'', time()-3600 , $path, $domain);
        }
        if ( $value === '' )
        {
            if ( isset($_COOKIE[$name]) )
            {
                $value   = $_COOKIE[$name];
                $value   =  unserialize(base64_decode($value));
                return $value;
            }
            return false;
        }
        //set cookie
        $value   =  base64_encode(serialize($value));
        
        return setcookie($name, $value, $expire, $path, $domain);
    }

    public static function session()
    {
        
    }   

    
    /**
     * 连接系统默认的数据库
     *
     * @param $config array  数据库参数
     * @return object 数据库连接
     */
    public static function db($config=array())
    {
		if ( empty($config) )
		{
			$config = zotop::config('zotop.database');
		}
		
		$db = database::instance($config);

		return $db;
    }

    /**
     * 用于实例化一个模型{module}.{model}，如 zotop.user,实例化系统模块的user模型
     *
     *
     * @param $name 模型名称空间
     * @return object(model)
     */
    public static function model($name='')
    {
        static $models = array();
        
        if ( empty($name) )
        {
            return new model();
        }
        
        if ( isset($models[$name]) )
        {
            return $models[$name];
        }
        
        list($module, $model) = explode('.', $name);
        
        $modelName = $module.'_model_'.$model;
        
        if ( !class_exists($modelName) )
        {
            $modelPath = zotop::modules($module,'path').DS.'models'.DS.$model.'.php';
                      
            if ( zotop::load($modelPath)== false )
            {
                zotop::error(array(
                    'content' => zotop::t('请检查相应的模型文件是否存在'),
                    'detail' => zotop::t('文件地址：{$modelPath}', array('modelPath'=>$modelPath))
                )); 
            }
        }
        
        if ( class_exists($modelName) )
        {
            $m = new $modelName();
            
            $models[$name] = $m;
            
            return $m;
        }
        
        zotop::error(array(
            'content' => zotop::t('请检查相应的模型文件中是否存在模型类 ：{$modelName}', array('modelPath'=>$modelPath,'modelName'=>$modelName)),
            'detail' => zotop::t('文件地址：{$modelPath}', array('modelPath'=>$modelPath,'modelName'=>$modelName))
        ));
    } 

   /**
     * 读取存储的用户信息
     *
     */
    public static function user($key='',$application='')
    {
        $user = array();
        
        $application = empty($application) ? ZOTOP_APP_NAME : $application;
        
        $cookieName = 'zotop.user.'.$application;
        
        if ( empty($user) )
        {
            $user = zotop::cookie($cookieName);
            $user = is_array($user) ? array_change_key_case($user) : array();
        }

        if ( $key === null )
        {
            return zotop::cookie($cookieName,null);
        }

        if ( empty($key) )
        {
            return empty($user) ? false : $user;
        }

        if ( is_array($key) )
        {
            $user = array_merge($user , array_change_key_case($key));
            return zotop::cookie($cookieName,$user);
        }

        $value = $user[strtolower($key)];

        if( isset($value) )
        {
            return $value;
        }
        return null;
    }
	
	public static function log($type='', $data=array())
	{
		if ( empty($type) )
		{
			return zotop::$logs;
		}

		if ( is_array($type) )
		{
			$log = $type;
		}

		if ( is_string($data) )
		{
			$log = array('type'=>$type,'content'=>$data);
		}

		if ( is_array($data) )
		{
			$log = array('type'=>$type,'content'=>$data);
		}

		if( !empty($log) && is_array($log) )
		{
			$log = array(
				'type' => $log['type'],
				'title' => $log['title'],
				'content' => $log['content'],
				'description' => $log['description'],
				'userid' => zotop::user('id'),
				'url' => url::location(),
				'createip' => ip::location(),
				'createtime' => TIME,				
			);
			zotop::$logs[] = $log;
		}

		return zotop::$logs;
	}
    
}
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的运行时类 Application
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_runtime
{
	public static $hooks = array();

    public static function reboot()
    {
        runtime::clear();
        runtime::config();
        runtime::library();                
        runtime::hooks();
		runtime::run();
    }

    /**
     * 清理全部运行时文件
     *
     */
    public static function clear()
    {
        dir::clear(ZOTOP_PATH_RUNTIME);      
    }    
    
    /**
     * 打包全部类库文件
     *
     */
    public static function library()
    {
        zotop::register(include(ZOTOP_PATH_LIBRARIES.DS.'zotop'.DS.'classes.php'));
        zotop::register(include(ZOTOP_APP_ROOT.DS.'libraries'.DS.'classes.php'));        
    }
    
    /**
     * 打包全部的配置文件
     */
    public static function config()
    {
        //加载全部配置
        zotop::config(include(ZOTOP_PATH_DATA.DS.'config.php'));
        zotop::config('zotop.database',@include(ZOTOP_PATH_DATA.DS.'database.php'));
        zotop::config('zotop.application',@include(ZOTOP_PATH_DATA.DS.'application.php'));
        zotop::config('zotop.module',@include(ZOTOP_PATH_DATA.DS.'module.php'));
        zotop::config('zotop.router',@include(ZOTOP_PATH_DATA.DS.'router.php'));    	
    }
    
    /**
     * 打包全部的hook文件
     *
     */
    public static function hooks()
    {
        $modules = zotop::data('module');        
        foreach($modules as $module)
        {
            if( (int)$module['status'] >= 0 && dir::exists($module['path']) )
            {
				//只加载相应的hook文件
				runtime::$hooks[] = $module['path'].DS.'hooks'.DS.ZOTOP_APP_NAME.'.php';
            }
        }
    }


    /**
     * 运行时执行，并加载相关文件
     */
	public static function run()
	{
		
		//打包配置文件
		zotop::data(ZOTOP_PATH_RUNTIME.DS.'config.php',zotop::config());

		//打包全部hook文件
        $hooks = runtime::compile(runtime::$hooks);
        if( !empty($hooks) )
        {
            file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'_hooks.php', $hooks,true);
        }
		
		//加载hooks以便核心文件使用
        zotop::load(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'_hooks.php');
	
        //打包核心文件
		$libraries = zotop::register();        
        $libraries = runtime::compile($libraries);        
        if( !empty($libraries) )
        {
            file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'.php', $libraries, true);
        }
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
}
defined('ZOTOP') OR die('No direct access allowed.');

/**
 * 系统的应用类 Application
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_application
{
	public static $group = 'admin';
    public static $module = 'zotop';
    public static $controller = 'index';
    public static $action = 'index';
    public static $arguments = array();
    /**
     * 应用程序初始化
     *
     */
    public static function init()
    {
        //错误及异常处理
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        set_error_handler(array('application', 'error'));
        set_exception_handler(array('application', 'exception'));
        
        //时区设置
        if ( function_exists('date_default_timezone_set') )
        {
            $timezone = zotop::config('system.locale.timezone');
            $timezone = empty($timezone) ? date_default_timezone_get() : $timezone;
            if( $timezone )
            {
                date_default_timezone_set($timezone);
            }
        }
        
        //输出头
        header("Content-Type: text/html;charset=utf-8");
        
    }
    
    /**
     * 应用程序启动
     *
     */
    public static function boot()
    {
        //app
        define('ZOTOP_APP_URL_THEME', ZOTOP_APP_URL.'/themes/'.application::theme());
        define('ZOTOP_APP_URL_COMMON', ZOTOP_APP_URL.'/common');                
        define('ZOTOP_APP_URL_CSS', ZOTOP_APP_URL_THEME.'/css');
        define('ZOTOP_APP_URL_IMAGE', ZOTOP_APP_URL_THEME.'/image');
        define('ZOTOP_APP_URL_JS', ZOTOP_APP_URL_COMMON.'/js');        
        //group
        define('ZOTOP_GROUP', application::group());        
        //module
        define('ZOTOP_MODULE', application::module());               
        define('ZOTOP_MODULE_URL', zotop::modules(ZOTOP_MODULE, 'url'));
        define('ZOTOP_MODULE_URL_GROUP', ZOTOP_MODULE_URL.'/'.ZOTOP_GROUP);
        define('ZOTOP_MODULE_PATH', zotop::modules(ZOTOP_MODULE,'path'));
        define('ZOTOP_MODULE_PATH_GROUP', ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP);
        //controller
        define('ZOTOP_CONTROLLER', application::controller());
        //action
        define('ZOTOP_ACTION', application::action());
        
    }    
    
    
    


    /**
     * 应用程序执行
     *
     *
     * @return null
     */
    public static function execute()
    {
		if( zotop::modules(ZOTOP_MODULE) === null ||  (int)zotop::modules(ZOTOP_MODULE,'status') < 0 )
		{
             msg::error(array(
            	'title' => '404 error',
                'content' => zotop::t('<h2>未能找到模块，模块可能尚未安装或者已经被禁用？</h2>'),
                'detail' => zotop::t('模块名称：{$module}', array('module' => ZOTOP_MODULE))
            ));   			
		}

        //controller classname
        $controllerName = ZOTOP_MODULE.'_controller_'.ZOTOP_CONTROLLER;
        $controllerPath = ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP.DS.ZOTOP_CONTROLLER.'.php';
                
        if ( !class_exists($controllerName, false) )
        {
            if ( !zotop::load($controllerPath) )
            {
                msg::error(array(
                	'title' => '404 error',
                    'content' => zotop::t('<h2>未能找到控制器，请检查控制器文件是否存在？</h2>'),
                    'detail' => zotop::t('文件名称：{$file}',array('file'=>$controllerPath))
                ));            
            }    
        }

        if ( class_exists($controllerName,false) )
        {
            //实例化控制器
            $controller = new $controllerName();
            
            //调用__execute方法
            $controller->execute(ZOTOP_ACTION, application::arguments());            
        }
        else
        {
             msg::error(array(
            	'title' => '404 error',
                'content' => zotop::t('<h2>未能找到控制器类，请检查控制器文件中是否存在控制器类？</h2>'),
                'detail' => zotop::t('类名称：{$className}', array('className' => $controllerName))
            ));            
        }
    }

    /**
     * 渲染输出内容
     *
     * @param string $output 待渲染输出的内容
     * @return string
     */
    public static function render($output)
    {
        $time = number_format(microtime(TRUE) - ZOTOP_START_TIME, 4);
        $memory = number_format((memory_get_usage() - ZOTOP_START_MEMORY)/1024/1024, 4);
        $output = str_ireplace
        (
            array('{#runtime}', '{#memory}', '{#include}', '{#queries}'),
            array($time.' S', $memory.' MB', count(get_included_files()), database::Q()),
            $output
        );
        return $output;
    }
    
    
    public static function reboot()
    {
        
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
     * 返回当前的名称空间
     *
     */
	public static function namespace()
	{
		return ZOTOP_GROUP.'://'.ZOTOP_MODULE.'.'.ZOTOP_CONTROLLER.'.'.ZOTOP_ACTION;
	}

    /**
     * 返回当前的组名称
     *
     */    
    public static function group()
    {
        $group = router::group();
        
        if ( empty($group) )
        {
            $group = application::$group;
        }
        
        return $group;
    }

    /**
     * 返回当前的模块名称
     *
     * @return string
     */
    public static function module()
    {
        $module = router::module();
                
        if ( empty($module) )
        {
            $module = application::$module;
             
        }          
        return $module;
    }
    
    /**
     * 返回当前URL路由的控制器名称，如果未能获取路由分发的控制器，则获取当前应用的默认路由
     *
     * @return string
     */
    public static function controller()
    {
        $controller = router::controller();
                      
        if ( empty($controller) )
        {
            $controller = application::$controller;
             
        }
        return $controller;
    }
    
    /**
     * 返回当前URL路由的动作名称，未能获取则返回当前应用的默认动作
     *
     * @return string
     */
    public static function action()
    {
        $action = router::action();
        
        if ( empty($action) )
        {
            $action = application::$action;
             
        }
        
        return $action;
    }
    
    
    public static function arguments()
    {
        return router::arguments();
    }
    
    public static function template($action='')
    {
        if ( empty($action) )
        {
            $action = ZOTOP_ACTION;
        }
        
        $template = ZOTOP_MODULE_PATH.DS.ZOTOP_GROUP.DS.'template'.DS.ZOTOP_CONTROLLER.DS.$action.'.php';
        
        return $template;
    }

    public static function theme()
    {
        $theme = zotop::config('system.admin.theme');
        $theme = empty($theme) ? 'default' : $theme;
        
        return $theme;
    }
}
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的路由类，完成对url的解析
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_router
{
    public static $uri = '';
    public static $group = '';
    public static $module = '';
    public static $controller = '';
    public static $action = '';
    public static $arguments = array();
    
    
    /**
     * 路由初始化
     * 
     *
     */
    public static function init()
    {
        $uri = '';
        
        if ( PHP_SAPI === 'cli' )
        {
            //cli
        }
        elseif ( isset($_GET['zotop']) )
        {
            //URL兼容模式，通过一个GET变量传递PATHINFO，默认为zotop，index.php?zotop=/zotop,index,index/id,1/parentid,2
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

    /**
     * 解析URI
     * 
     * URI 由模块名/控制器/动作/参数组成，采用如下的格式：
     *
     * @code php
     * module/controller/action/param1/vlaue1/param2/value2
     * @endcode
     *
     */
    public static function execute()
    {
          
        if ( $uri = trim(router::$uri , '/') )
        {
            router::$arguments = explode('/',$uri);
            
            //分配module、controller、action
            router::$module = array_shift(router::$arguments);            
            router::$controller = array_shift(router::$arguments);
            router::$action = array_shift(router::$arguments);
            
            //处理参数
			
            $arguments = array();
            
            for ( $i = 0, $cnt = count(router::$arguments); $i <$cnt; $i++ )
            {

				$arguments[$i] =  rawurldecode(router::$arguments[$i]);
            }
            
            router::$arguments = $arguments;
           


            //unset($_GET['zotop']);            
            //$_GET = array_merge($_GET, array('module'=>router::$module,'controller'=>router::$controller,'action'=>router::$action), $arguments);
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
     * 获取当前的模块名称
     *
     * @return string;
     */
    public static function group()
    {
        return router::$group;
    }    
    
    /**
     * 获取当前的模块名称
     *
     * @return string;
     */
    public static function module()
    {
        return router::$module;
    }

    /**
     * 获取控制器的名称
     * 
     * @return string;
     */
    public static function controller()
    {
        return router::$controller;
    }

    /**
     * 获取动作名称
     *
     * @return string;
     */
    public static function action()
    {
        return router::$action;
    }

    /**
     * 获取参数
     * 
     * @return array;
     */
    public static function arguments()
    {
        return router::$arguments;
    }     
}
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 控制器基类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_controller
{
	public $_defaultAction = 'index';
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        $this->__init();
        $this->__check();
    }
    /**
     * 初始化动作，当控制器被初始化的时候调用
     *
     */
    public function __init()
    {
        
    }
    /**
     * 初始化权限检查，当控制器被初始化的时候调用
     *
     */
    public function __check()
    {
        
    }        
    /**
     * 动作触发之前调用
     *
     */
    public function __before($arguments='')
    {
        
    }
    
    /**
     * 当动作被触发之后调用
     *
     */
    public function __after($arguments='')
    {
        
    }
        
    /**
     * 执行动作，当找不到对应动作时候触发，可以被重载 
     *
     */
    public function execute($action='',$arguments='')
    {
        //设置默认的动作
		$action = empty($action) ? $this->_defaultAction : $action;
		
		//取得方法名称
		$method = 'action'.ucfirst($action);
        
        if( method_exists($this, $method) )
        {
            call_user_func_array(array($this, '__before'), $arguments);
            call_user_func_array(array($this, $method), $arguments);
            call_user_func_array(array($this, '__after'), $arguments);
        }
        else
        {
             call_user_func_array(array($this, '__empty'), array($action, $arguments));
        }
    }
    
    public function __empty($action='', $arguments='')
    {
        msg::error(array(
        	'title' => '404 error',
            'content' => zotop::t('<h2>未能找到相应的动作，请检查控制器中动作是否存在？</h2>'),
            'detail'=> zotop::t('动作名称：{$action}',array('action'=>$action))
        ));
    }
    
	/*
    public function redirect($uri , $params=array() , $fragment='')
    {
        $url = zotop::url($uri,$params,$fragment);
        header("Location: ".$url);
        exit();
    }
    
    public function error($content='', $life=5)
    {
        msg::error($content, $life=5);
    }
    
    public function success($content='', $url='', $life=3, $extra='')
    {
        msg::success($content, $url, $life, $extra);
    }
	*/

}
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统模型类，所有的模型都继承自此类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_model
{
    protected $_db = null; // 当前数据库操作对象
    protected $_name = ''; //模型名称
    protected $_table = ''; //数据表名称
    protected $_prefix = ''; //数据表的前缀
    protected $_key = ''; //主键名称
    protected $_bind = array(); //属性设置
	protected $_user = array();
	protected $_error = 0;
	protected $_msg = array();
    
    
	public function __construct()
	{
	    if ( ! is_object($this->_db) )
		{
	       $this->_db  = zotop::db();
		}
		
		$this->_user = zotop::user();
	}
	
    /**
     * 返回错误
     * 
     */
	public function error($err=0, $msg=array())
	{
		if ( $err )
		{
			$this->_error = $err;
			$this->msg($msg);
		}
		return $this->_error;
	}

    /**
     * 返回消息
     * 
     */
	public function msg($msg='')
	{
		if ( !empty($msg) )
		{
			if( is_string($msg) )
			{
				$this->_msg['content'] = $msg;
			}
			else
			{
				$this->_msg = array('title'=>$msg['title'],'content'=>$msg['content'],'description'=>$msg['description']);
			}
		}
		return $this->_msg;
	}
	
    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->_bind[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_bind[$name]) ? $this->_bind[$name] : null;
    }


    /**
     * 获取或者绑定数据
     * 
     * @param mixed $name 名称/数据数组
	 * @param mixed $value 值
     * @return mixed
     */

	public function bind($name='', $value='')
	{
		if ( empty($name) )
		{
			return $this->_bind;
		}

		if ( is_array($name) )
		{
			$this->_bind = array_merge($this->_bind, $name);
		}

		if ( is_string($name) )
		{
			$this->_bind[$name]  =   $value;
		}

		return $this->_bind;
	}

    /**
     * 获取当前的db对象
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function db()
    {
        return $this->_db->from($this->table());
    }

	public function user($key='')
	{
		if( empty($key) )
		{
			return $this->_user;
		}
		return $this->_user[$key];
	}
    
    /**
     * 得到当前的模块名称
     * 
     * @access public
     * @return string
     */
    public function module()
    {
        if( empty($this->_module) )
        {
            $class = explode('_model_', get_class($this));
            $this->_module =  $class[0]; 
        }
        return $this->_module;
    }    

    /**
     * 得到当前的模型名称
     * 
     * @access public
     * @return string
     */
    public function name()
    {
        if( empty($this->_name) )
        {
            $class = explode('_model_', get_class($this));
            $this->_name =  $class[1]; 
        }
        return $this->_name;
    }
    
    /**
     * 得到当前的数据表的前缀名称
     * @access public
     * @return string
     */
    public function prefix()
    {
        if( empty($this->_prefix) )
        {
            $this->_prefix =  $this->db()->config('prefix');
        }
        return $this->_prefix;
    }
    
    
    /**
     * 得到当前的数据表名称
     * 
     * @access public
     * @return string
     */
    public function table($fullName = false)
    {
        if( empty($this->_table) )
        {
            $this->_table =  $this->name();
        }
        if( $fullName )
        {
            return $this->prefix().$this->_table;
        }
        return $this->_table;
    }

    /**
     * 得到当前的数据表的主键名称
     * 
     * @access public
     * @return string
     */
    public function key()
    {
        if( empty($this->_key) )
        {
            $table = $this->table(true);
            $structure = $this->structure();
            
            if( $structure )
            {
                $this->_key = $structure['primarykey'];
            }
            if( empty($this->_key) )
            {
               $this->_key = $this->db()->primaryKey();
            }
        }
        return $this->_key;
    }

    public function getAll($sql='')
    {
        return $this->db()->getAll($sql);
    }
	

	/**
	 * 返回limit限制的数据,用于带分页的查询数据
	 *
	 * @param $page int 页码
	 * @param $pagesize int 每页显示条数
	 * @param $num int|bool 总条数|缓存查询条数，$toal = (false||0) 不缓存查询
	 * @return mixed
	 */
	public function getPage($page=0, $pagesize=30, $num = false)
	{
        
		$page = $page <=0 ? (int)$_GET['page'] :$page;
		$page = $page <=0 ? 1 :$page;

		//获取查询参数
		$sqlBuilder = $this->db()->sqlBuilder($sql);

		if ( is_numeric($num) && $num > 0 )
		{
			$total = $num;
		}
		else
		{
			$hash = md5(serialize($sqlBuilder['where']));

			if ( $page == 1 || $num == true || !is_numeric(zotop::cookie($hash)))
			{
				//获取符合条件数据条数
				$total = $this->count($sqlBuilder['where']);				

				zotop::cookie($hash,$total);				
			}
			else
			{
				$total = zotop::cookie($hash);
			}

		}

		//zotop::dump($this->db()->lastsql());

		//计算$offset
		$offset = intval($page) > 0 ? (intval($page)-1)*intval($pagesize) : 0;
		
		//设置limit
		$this->db()->sqlBuilder($sqlBuilder);
		$this->db()->limit($pagesize, $offset);

		//获取指定条件的数据
		$data = $this->db()->getAll();

		return array(
			'data' => (array) $data,
			'page' => intval($page),
			'pagesize' => intval($pagesize),
			'total' => intval($total),
		);
	}
    
    /**
     * 读取具体的某条数据
     * 
  	 * 空条件：$model->read();前面必须定义过主键值： $model->id = 1; 
     * 默认条件：$model->read(1) 相当于 $model->read(array('id','=',1))
     * 自定义条件：$model->read(array('id','=',1))
     * 
     * @param mix $where 键值
     * 
     * @return array
     */
    public function read($where='')
    {
        if ( !is_array($where) )
        {
            $key = $this->key();
            $value = empty($where) ? $this->$key : $where;
            $where = array($key, '=', $value);
        }
               
        //读取并设置属性
        $this->_bind = $this->db()->select('*')->where($where)->getRow();

		$this->_bind = zotop::filter($this->table().'.read', $this->_bind, $where);

        if ( empty($this->_bind) )
        {
			$this->error(1, array(
                'title' => zotop::t('读取数据失败'),
                'content' => zotop::t('未能找到 <b>{$0}</b> <b>{$1}</b> <b>{$2}</b> 的数据', $where),
                'detail' => $this->db()-> lastSql()
            ));
        }
        return $this->_bind;
    }
    
    /**
     * 创建数据
     * 
     * @param mix $data 待创建的数据
     * 
     * @return mix
     */     
    public function insert($data)
    {
        
		$data = zotop::filter($this->table().'.insert',$data);

		$insert = $this->db()->set($data)->insert();

        if ( $insert )
        {
            //返回插入数据的主键？
            return true;
        }
        return false;             
    }    
    
    /**
     * 更新数据
     * 
     * @param mix $data 待更新的数据
     * @param mix $where 条件
     * 
     * @return array
     */    
    public function update($data=array(), $where = '')
    {

		
		if ( is_array($data) )
        {
            $this->db()->set($data);
        }
        
        if ( !is_array($where) )
        {
            $key = $this->key();
            
            if ( empty($where) )
            {
                $set = $this->db()->sqlBuilder('set');
                
                if( isset($set[$key]) )
                {
                    $where = array($key,'=',$set[$key]);
                }
                else
                {
                    $where = array($key,'=',$this->$key);
                }
            }
            
            if( is_numeric($where) || is_string($where) )
            {
        
                $where = array($key,'=',$where);
            }
        }
        
		$data = zotop::filter($this->table().'.update', $data, $where);

        $update = $this->db()->where($where)->update();
        
        return $update;
    }
      
    /**
     * 判断是否存在
     * 
     * 
     * @param mix $key 键
     * @param mix $value 键值     * 
     * 
     * @return array
     */    
    public function isExist($key='', $value='')
    {
        if ( !is_array($key)  )
        {
            $key = empty($key) ? $this->key() : $key;
            $value = empty($value) ? $this->$key : $value;
            $where = array($key,'=',$value);
        }
        else
        {
            $where = $key;
        }
        
        $count = $this->db()->select('count('.$key.') as num')->where($where)->getOne();
        
        if( is_numeric($count) && $count > 0 )
        {
            return true;
        }
        return false;
    }

    /**
     * 删除数据
     * 
     * @param mix $where 删除条件
     * 
     * @return mix
     */     
    public function delete($where)
    {

        
        if( !is_array($where) )
        {
            $key = $this->key();
            
            if( empty($where) )
            {
               $where = array($key,'=',$this->$key); 
            }
            
            if( is_numeric($where) || is_string($where) )
            {
               $where = array($key,'=',$where);
            }
            
        }

		$data = zotop::filter($this->table().'.delete', $data);
            
        return $this->db()->where($where)->delete();     
    }

	public function count($key='',$value='')
	{
	    
		if ( is_string($key) )
        {
            $key = empty($key) ? $this->key() : $key;

            $value = empty($value) ? $this->$key : $value;
			
			if( !empty($value) )
			{
				$where = array($key,'=',$value);
			}
        }
        elseif ( is_array($key) )
        {
            $where = $key;
        }
		
		$count = $this->db()->select('count('.$key.') as num')->where($where)->getOne();
		
		if( !is_numeric($count) )
		{
			$count = 0;
		}
		return $count;
	}

    public function max($key='', $where='', $default=0)
    {
        $key = empty($key) ? $this->key() : $key;
        
        $max = $this->db()->select('max('.$key.') as max')->where($where)->getOne();
        
        if ( is_numeric($max) )
        {
            return $max;
        }
        return $default;
    }

	public function cache($reload=false)
	{
		$name = $this->table();
		
		$data = zotop::data($name);
		
		//设置缓存数据
		if ( $reload || $data===null )
		{
			$data = $this->getAll($sql);
			
    		if( is_array($data) )
    		{
    		    zotop::data($name, $data);
    		}
		}
		
		return $data;	
	}    
}
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 页面组件
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_page
{
    public $uid ='';
	public $template = '';
	public $data = array();
    
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        
    }    
    
	
	public function getTemplatePath($action='')
	{
	    if( empty($this->template) )
	    {
            $path = application::template($action);
            
            return $path;   	        
	    }
	    return $this->template;   
	}
	
    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->data[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name])?$this->data[$name]:null;
    }	

	public function set($name, $value=null)
	{
        if( is_array($name) )
        {
            $this->data = array_merge($this->data,$name);
        }
        
        if( is_string($name) )
        {
            $this->data[$name] = $value;
        }
	}
	
	public function data()
	{
	    return $this->data;
	}

	
	public function header()
	{
		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$this->title.' '. zotop::config("zotop.title").'</title>';
        $html[] = '	'.html::meta('keywords',$this->keywords);
        $html[] = '	'.html::meta('description',$this->description);
        $html[] = '	'.html::meta('Content-Type','text/html;charset=utf-8');
        $html[] = '	'.html::meta('X-UA-Compatible','IE=EmulateIE7');
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/global.css',array('id'=>'global'));		
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));        
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';

		$str =  implode("\n",$html);

		echo $str;	    
	}
	
	public function footer()
	{
	    $html[] = '';
		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}	
	
	public function add($str)
	{
		echo $str."\n";
	}

	/**
	 * 给页面附加一个js文件，必须在header之前声明
	 *
	 */
	public function addScript($file)
	{
	    $this->js = array_merge((array)$this->js,(array)$file);
	}
	public function addJS($file)
	{
	    $this->addScript($file);
	}	
	
	/**
	 * 给页面附加一个css文件，必须在header之前声明
	 * 
	 */
	public function addStyleSheet($file)
	{    
	    $this->css = array_merge((array)$this->css,(array)$file);
	}
	public function addCSS($file)
	{
	    $this->addStyleSheet($file);
	}

	public function render($action='')
	{
	    $this->template = $this->getTemplatePath($action);

	    if( file_exists($this->template) )
        {
            ob_start();            
            extract($this->data(), EXTR_SKIP);
            include $this->template;
    		$content = ob_get_contents();
    		ob_clean();
    		return $content;
        }
        msg::error(array(
            'title' => '404 error',
            'content' => zotop::t('未能找到页面模板，请检查确认模板文件是否存在'),
            'detail' => zotop::t('模板文件：{$file}',array('file'=>$this->template)) 
        ));	    
	}
	
	public function display($action='')
	{
        static $display = false;
        
        if($display) return true;
        
	    echo $this->render($action);
	    
	    $display = true;
	}
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 表单辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_form
{
	public static $globalid = '';
	public static $template = '';
	public static $buttons = '';


	public static function isPostBack()
	{

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
		{
    		if ( (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['_FORMHASH'] == form::hash() )
			{
				return true;
			}
			else
			{
				zotop::error('invalid submit!');
			}
		}
		return false;
	}

	/**
	 * 为表单生成一个全局编号，用于media等数据定位
	 *
	 */
	public static function globalid()
	{
		$globalid = form::$globalid;

		if( empty($globalid) )
		{
			$globalid = ZOTOP_MODULE.'.'.ZOTOP_CONTROLLER.'.'.ZOTOP_ACTION;
		}

		$globalid = md5($globalid);

		return $globalid;
	}

	public static function hash()
	{
		$hash = zotop::config('system.safety.authkey');
		$hash = empty($hash) ? 'zotop form hash!' : $hash;
		$hash = substr(time(), 0, -7).$hash;
		$hash = strtoupper(md5($hash));
		return $hash;
	}
	
	public static function post()
	{
	    $post = array();
	    foreach($_POST as $key=>$val)
	    {
	        if($key[0] != '_' )
	        {
	            $post[$key] = $val;
	        }   
	    }	    
	    return $post;
	}

    public static function header($form=array())
	{
		
		if( is_string($form) )
		{
			$form['description'] = $form;
		}

		form::$template = isset($form['template']) ? $form['template'] : form::$template;
		form::$globalid = isset($form['globalid']) ? $form['globalid'] : form::$globalid;

		//form 标签
		$attrs['class'] = isset($form['class']) ? $form['class'] : 'form';
		$attrs['method'] = isset($form['method']) ? $form['method'] : 'post';
		$attrs['target'] = isset($form['target']) ? $form['target'] : '';
		$attrs['action'] = isset($form['action']) ? $form['action'] : url::location();

		if( isset($form['enctype']) || isset($form['upload']) )
		{
			$attrs['enctype'] = 'multipart/form-data';
		}

        //加载表头
		$html[] = '';
		$html[] = '<form'.html::attributes($attrs).'>';
		$html[] = field::hidden(array('name' => '_REFERER','value' => request::referer()));
		$html[] = field::hidden(array('name' => '_FORMHASH','value' => form::hash()));
		$html[] = field::hidden(array('name' => '_GLOBALID','value' => form::globalid()));

        //加载常用js
		if ( $form['valid'] !== false )
		{
			$html[] = html::script(ZOTOP_APP_URL_JS.'/jquery.validate.js');
		}
		
		if ( $form['ajax'] !== false )
		{
			$html[] = html::script(ZOTOP_APP_URL_JS.'/jquery.form.js');
		}

		//表单头部		
		if( isset($form['title']) || isset($form['description']) )
		{
		    $html[] = '<div class="form-header clearfix">';
		    $html[] = isset($form['title']) ? '		<div class="form-title">'.$form['title'].'</div>' : '';
            $html[] = isset($form['description']) ? '		<div class="form-description">'.$form['description'].'</div>' : '';
            $html[] = '</div>';
		}

	    //表单body部分开始
        $html[] = '<div class="form-body">'; 
        
        echo implode("\n",$html);
	}

	public static function footer($str ='', $buttons = array())
	{
		$html[] = '';
		$html[] = '</div>';
	    $html[] = '<div class="form-footer"><div class="form-footer-main">'.form::$buttons.'</div><div class="form-footer-sub">'.$str.'</div></div>';
	    $html[] = html::script(ZOTOP_APP_URL_JS.'/zotop.form.js');
		$html[] = '</form>';

		echo implode("\n",$html);

		form::$template = '';
		form::$buttons = '';
	}

	public static function top()
	{
		echo '<div class="form-top clearfix"><div class="form-title">'.$title.'</div><div class="form-description">'.$description.'</div></div>';
	}

	public static function bottom($main='',$extra='')
	{
		echo '<div class="form-bottom clearfix"><div class="form-bottom-main">'.$main.'</div><div class="form-bottom-sub">'.$extra.'</div></div>';
	}

	//创建buttons
	public static function buttons()
	{
	    $buttons = func_get_args();

	    if( !empty($buttons) && !empty($buttons[0]))
	    {
    	    if( is_array($buttons) )
    	    {
        	    $html[] = '<div class="buttons">';

        		foreach($buttons as $button)
        		{
					$html[] = form::control($button);
				}

        		$html[] = '</div>';     
    	    }
    	    else
    	    {
    	        $html[] = $buttons;
    	    }

			form::$buttons = implode("\n",$html);
	    }	    
	}

	public static function field($attrs)
	{
		if ( $attrs['type'] == 'hidden' )
		{
			$str = form::control($attrs);
		}
		else
		{
			$label = arr::take('label',$attrs);
			$description = arr::take('description',$attrs);

			$str =  form::template(form::$template);
			$str = str_replace('{$field:label}',form::label($label,$attrs),$str);
			$str = str_replace('{$field:description}', form::description($description),$str);
			$str = str_replace('{$field:controller}', form::control($attrs), $str);
			$str = str_replace('{$field:display}', arr::take('display',$attrs) == 'none' ? ' style="display:none;"' : '', $str);
		}
		echo $str;
	}

	public static function template($template='div')
	{
		$template = empty($template) ? 'table' : $template;
		$html = array();
		switch($template)
		{
			case 'div':
				$html[] = '';
				$html[] = '<div class="field"{$field:display}>';
				$html[] = '	<div class="field-side">';
				$html[] = '		{$field:label}<span class="field-valid inline-block"></span>';
				$html[] = '		{$field:description}';
				$html[] = '	</div>';
				$html[] = '	<div class="field-main">';
				$html[] = '	{$field:controller}';
				$html[] = '	</div>';
				$html[] = '</div>';
				break;
			case 'table':
				$html[] = '';
				$html[] = '<table class="field"{$field:display}><tr>';
				$html[] = '	<td class="field-side">';
				$html[] = '		{$field:label}';
				$html[] = '	</td>';
				$html[] = '	<td class="field-main">';
				$html[] = '	{$field:controller}<span class="field-valid inline-block"></span>';
				$html[] = '	{$field:description}';
				$html[] = '	</td>';
				$html[] = '</tr></table>';
				break;
			default:
				$html[] = '';
				$html[] = $template;
				break;
		}
	    return implode("\n",$html);
	}

	public static function label($label,$attrs,$required='*')
	{
		if ( strpos($attrs['valid'],'required') !== false )
		{
			$label .= '<span class="field-required">'.$required.'</span>';
		}
		
		$label = html::label($label,$attrs['name']);

		return $label;
	}

	public static function description($str)
	{
		if($str)
		{
			return '<span class="field-description">'.$str.'</span>';
		}
		return '';
	}

	public static function control($attrs)
	{
	    $html[] = '';
		if( is_array($attrs) )
		{
			$type = arr::take('type',$attrs);
			$type = isset($type) ? $type : 'text';
			$html[] = field::get($type,$attrs);
		}
		else
		{
			 $html[] = $attrs;
		}
		return implode("\n",$html);
	}

	public static function referer($url='')
	{
		static $referer;
		if(empty($url))
		{
			$url = request::post('_REFERER');
			return $url;
		}
		$referer = $url;
		return $referer;
	}

	

	public function add()
	{
	
	}

}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 表单辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_field
{

    /**
     * 设置一个新的表单控件，用于覆盖系统默认的表单控件
     *
     * function mytextarea($attrs)
     * {
     *   return field::textarea($attrs).'<div>new textarea</div>';
     * }
     * field::set('textarea','mytextarea');
     *
     *
     * @param $name  string  控件名称
     * @param $callback function 控件函数
     * @return bool
     */
    public static function set($name,$callback='')
	{
	    static $fields = array();
	    $name = strtolower($name);
	    if(!empty($callback))
	    {
	        $fields[$name] = $callback;
	    }
	    if(isset($fields[$name]))
	    {
	        return $fields[$name];
	    }
	    return false;
	}

	/**
	 * 生成一个控件的Html数据
	 *
	 *
	 * @param $name string 控件名称
	 * @param $attrs array  控件参数
	 * @return string 返回控件的代码
	 */
	public static function get($name,$attrs=array())
	{
	    $callback = field::set($name);
	    if($callback)
	    {
	        return call_user_func_array($callback,array($attrs));
	    }
		if( method_exists('field',$name) )
		{
		    return field::$name($attrs);
		}

		return 'Unkown FieldController : <b>'.$name.'</b>';
	}

    /**
     * label控件，显示一个文本
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function label($attrs)
	{
		$value = arr::take('value',$attrs);
		
	    return '<span class="label" '.html::attributes($attrs).'>'.$value.'</span>';
	}
	
    /**
     * html控件，显示Html
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function html($attrs)
	{
		$value = arr::take('value',$attrs);
		$html = arr::take('html',$attrs);
		$html = empty($html) ? $value : $html;
		
	    return '<div class="field-wrapper inline-block"><div '.html::attributes($attrs).'>'.$html.'</div></div>';
	}

	/**
     * text文本控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function text($attrs)
	{
		$attrs['type'] = 'text';
		$attrs['class'] = isset($attrs['class']) ? 'text '.$attrs['class'] : 'text';
		return html::input($attrs);
	}
    /**
     * 隐藏类型控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function hidden($attrs)
	{
		$attrs['type'] = 'hidden';
		return html::input($attrs);
	}
    /**
     * 密码输入框控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function password($attrs)
	{
		$attrs['type'] = 'password';
		$attrs['class'] = isset($attrs['class']) ? 'password '.$attrs['class'] : 'password';
		return html::input($attrs);
	}
    /**
     * 按钮控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function button($attrs)
	{
		$attrs['type'] = 'button';
		return html::button($attrs);
	}
    /**
     * 表单提交按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function submit($attrs)
	{

	    $attrs['type'] = 'submit';
		$attrs['class'] = isset($attrs['class']) ? 'submit '.$attrs['class'] : 'submit';
		$attrs += array
		(
			'id'=>'submitform',
			'value'=>zotop::t('提交')
		);
		return html::button($attrs);
	}
    /**
     * 表单重置按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function reset($attrs)
	{
		$attrs['type'] = 'reset';
		$attrs['class'] = isset($attrs['class']) ? 'reset '.$attrs['class'] : 'reset';
		$attrs += array
		(
			'id'=>'resetform',
			'value'=>zotop::t('重置')
		);
		return html::input($attrs);
	}

    /**
     * 返回前页按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function back($attrs)
	{
		$attrs += array
		(
			'class'=>'zotop-back',
			'onclick'=>'window.history.go(-1);',
			'value'=>zotop::t('返回前页')
		);
		return field::button($attrs);
	}

    /**
     * 返回前页按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function close($attrs)
	{
		$attrs += array
		(
			'class'=>'zotop-close',
			'value'=>zotop::t('关闭')
		);
		return field::button($attrs);
	}

    /**
     * 文本段输入控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function textarea($attrs)
	{
	    $attrs['class'] = isset($attrs['class']) ? 'textarea '.$attrs['class'] : 'textarea';
		$attrs += array
		(
			'rows'=>'8',
			'cols'=>'5'
		);
		$value = arr::take('value',$attrs);
		return '<textarea'.html::attributes($attrs).'>'.html::encode($value).'</textarea>';
	}

	/**
	 * 生成一个标准的select控件
	 *
	 * @param $attrs
	 * @return string
	 */
	public static function select($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = arr::take('options',$attrs);
		$options = field::_option($options);
	    $value = arr::take('value',$attrs);//当value为数组时，则为多选
		if (is_array($value))
		{
			$attrs['multiple'] = 'multiple';
		}
		else
		{
			$value = array($value);
		}
		//为所有的select都加上select样式，便于全局统一控制select的样式，同input
	    if(isset($attrs['multiple']))
	    {
	        $defaultClass='select multiple';
	    }
	    else
	    {
	        $defaultClass='select';
	    }

		$attrs['class'] = isset($attrs['class']) ? $defaultClass.' '.$attrs['class'] : $defaultClass;
	    $html[] = '';
	    $html[] = '<select'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        foreach($options as $val=>$text)
	        {
	            $selected = in_array($val,$value) ? ' selected="selected"' : '';
	            $html[] = '	<option value="'.$val.'"'.$selected.'>'.html::encode($text).'</option>';
	        }
	    }
	    $html[] = '</select>';
	    $html[] = '';
	    return implode("\n",$html);
	}

	public static function dropdown($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr:take
		$html[] = '';
		$html[] = '<div class="inline-block dropdown">';
		$html[] = '<input type="text" value="'.$options[$attrs['value']].'" class="text '.$attrs['class'].'" style="'.$attrs['style'].'" readonly="readonly">';
		$html[] = '<input type="hidden" value="'.$attrs['value'].'" valid="'.$attrs['valid'].'" title="'.$attrs['title'].'" class="value">';
		$html[] = '<div class="dropdownBox">';
		$html[] = '<ul class="dropdownOptions">';
		foreach($options as $val=>$text)
		{
			$html[] = '<li rel="'.$val.'">'.$text.'</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';
		$html[] = '</div>';
	    $html[] = '';
	    return implode("\n",$html);
	}

    /**
     * 多选输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function checkbox($attrs)
	{
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr::take
	    $options = field::_option($options);
	   
		$value = arr::take('value',$attrs);//即取出了value和options，又把他们从$attrs中去掉了		
		$value = json_decode($value);

		if ( !is_array($value) && isset($value) && $value !== '' )
		{
			$value = array($value);
		}
		//zotop::dump($value);
	    $attrs['class'] = isset($attrs['class']) ? 'checkbox '.$attrs['class'] : 'checkbox';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $valid = arr::take('valid',$attrs);
		$html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {

				$checked = is_array($value) && in_array($val,$value) ? ' checked="checked"' : '';

	            $html[] = '<li><input type="checkbox" name="'.$attrs['name'].'[]" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.''.((isset($valid) && $i==1) ? ' valid = "'.$valid.'"':'').'/>';
				$html[] = '<label for="'.$attrs['name'].'-item'.$i.'">'.html::encode($text).'</label></li>';//这儿代码不完美
				$i++;
	        }
	    }
	    $html[] = '</ul>';

	    return implode("\n",$html);
	}

    /**
     * 单选输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function radio($attrs)
	{
	    $options = arr::take('options',$attrs);
		$options = field::_option($options);
	    $value = arr::take('value',$attrs);	    
	    $attrs['class'] = isset($attrs['class']) ? 'radio '.$attrs['class'] : 'radio';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $valid = arr::take('valid',$attrs);
		$html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {
	            $checked = ($val==$value) ? ' checked="checked"' : ''; //这儿代码可能有问题，请检查
	            $html[] = '	<li>';
	            $html[] = '		<input type="radio" name="'.$attrs['name'].'" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.''.((isset($valid) && $i==1) ? ' valid = "'.$valid.'"':'').'/>';
				$html[] = '		<label for="'.$attrs['name'].'-item'.$i.'">'.html::encode($text).'</label>';
				$html[] = '	</li>';//这儿代码不完美
				$i++;
	        }
	    }
	    $html[] = '</ul>';

	    return implode("\n",$html);
	}
    /**
     * 图片上传输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function image($attrs)
	{
	   //上传handle
	   $handle = arr::take('handle',$attrs);
	   $handle = empty($handle) ? zotop::url('zotop/image/index',array('globalid'=>form::globalid(),'field'=>$attrs['name'],'image'=>'__image__')) : $handle;
	   
	   
	   $html[] = html::script('$common/js/zotop.upload.js');
	   $html[] = '<div class="field-wrapper clearfix">';
	   $html[] = '	'.field::text($attrs);
       $html[] = '	<span class="field-handle">';
	   $html[] = '		&nbsp;<a href="'.$handle.'" class="imageuploader" title="'.zotop::t('选择或者上传图片').'"><span class="zotop-icon zotop-icon-imageuploader"></span></a>';
	   $html[] = '	</span>';
	   $html[] = '</div>';

	   return implode("\n",$html);
	}

    /**
     * 标题输入框，含有标题样式
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function title($attrs)
	{
		$style = arr::decode($attrs['style'], ';', ':');

		$html[] = html::script('$common/js/jquery.colorpicker.js');
		$html[] = html::script('$common/js/zotop.title.js');
		$html[] = '<div class="field-wrapper clearfix">';
		$html[] = '	'.field::text($attrs);
		$html[] = '	'.field::hidden(array('name'=>$attrs['name'].'_color','id'=>$attrs['name'].'_style','class'=>'short','value'=>$style['color']));
		$html[] = '	'.field::hidden(array('name'=>$attrs['name'].'_weight','id'=>$attrs['name'].'_weight','class'=>'short','value'=>$style['font-weight']));
		$html[] = '	<span class="field-handle">';
		$html[] = '		<a class="setweight" style="display:inline-block;" valueto="'.$attrs['name'].'_weight" weightto="'.$attrs['name'].'" title="'.zotop::t('加粗').'"><span class="zotop-icon zotop-icon-b"></span></a>';
		$html[] = '		<a class="setcolor" style="display:inline-block;" valueto="'.$attrs['name'].'_color" colorto="'.$attrs['name'].'" title="'.zotop::t('色彩').'"><span class="zotop-icon zotop-icon-setcolor '.$style['font-weight'].'"></span></a>';
		$html[] = '	</span>';
		$html[] = '</div>';

		return implode("\n",$html);
	}

    /**
     * 关键词输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function keywords($attrs)
	{
   
		$html[] = html::script('$common/js/zotop.keywords.js');
		$html[] = '<div class="field-wrapper clearfix">';
		$html[] = '	'.field::text($attrs);
		$html[] = '	<span class="field-handle">';
		$html[] = '		&nbsp;<a class="setkeywords" style="display:inline-block;" valueto="'.$attrs['name'].'" title="'.zotop::t('常用关键词').'"><span class="zotop-icon zotop-icon-keywords"></span></a>';
		$html[] = '	</span>';
		$html[] = '</div>';

		return implode("\n",$html);
	}


	/**
     * 文件上传框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function file($attrs)
	{
		$attrs['type'] = 'file';
		$attrs['class'] = isset($attrs['class']) ? 'file '.$attrs['class'] : 'file';
		return html::input($attrs);
	}



    /**
     * 富文本编辑器
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function editor($attrs)
	{
		$attrs['class'] = isset($attrs['class']) ? 'editor '.$attrs['class'] : 'editor';

		return field::textarea($attrs);
	}

	
    public static function source($attrs)
    {
        $attrs['style'] = 'width:600px;height:460px;';

        return field::textarea($attrs);
    }


    
    /**
     * 控件组
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function group($attrs)
	{
		$html[] = '<div class="field-group">';

		$fields = arr::take('field',$attrs);
		
		if ( is_array($fields) )
		{
			foreach($fields as $field)
			{
				if ( is_array($field) )
				{
					$type = arr::take('type',$field);
					$type = isset($type) ? $type : 'text';
					$field['class'] =  isset($field['class']) ? 'short '.$field['class'] : 'short';
					$html[] = '	<div class="field-group-item">';
					$html[] = '		<label for="'.$field['name'].'">'.arr::take('label',$field).'</label>';
					$html[] = '		'.field::get($type,$field);
					$html[] = '	</div>';
				}
				else
				{
					$html[] = $field;
				}
			}
		}
		else
		{
			$html[] = $fields;
		}
		
		$html[] = '</div>';

		return implode("\n",$html);
	}

	/**
	 * 将字符串转化成标准的选项数组
	 *
	 * @param  string $options 选项字符串
	 * @param  string $s1  第一分割符号
	 * @param  string $s2  第二分割符号
	 * @return array
	 */
	public function _option($options, $s1 = "\n", $s2 = '|')
	{
		if( is_array($options) )
		{
			return $options;
		}

		$options = explode($s1, $options);
		
		foreach($options as $option)
		{
			if(strpos($option, $s2))
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			$os[$value] = $name;
		}

		return $os;
	}

}

defined('ZOTOP') OR die('No direct access allowed.');

/**
 * 页面辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_block
{
	/**
	 * 区块的头部输出
	 * 
	 *	
	 * @param array|string $header  头部参数
	 * @return null 输出头部的代码
	 */
	public static function header($block=array())
	{
		$block = is_array($block) ? $block : array('title'=>$block);

		$icon = isset($block['icon']) ? '<span class="zotop-icon zotop-icon-'.(empty($block['icon']) ? 'empty' : $block['icon']).'"></span>' : '';

		$action = isset($block['action']) ? '<div class="block-action">'.$block['action'].'</div>' : '';

		$class = isset($block['class']) ? ' '.$block['class'] : '';

		$id = isset($block['id']) ? ' id="'.$block['id'].'"' : '';

	    $html[] = '';
		$html[] = '<div'.$id.' class="block clearfix'.$class.'">';
		if(isset($block['title']))
		{
		    $html[] = '	<div class="block-header clearfix">';
			$html[] = '		'.$action.'<div class="block-title">'.$icon.$block['title'].'</div>';
		    $html[] = '	</div>';
		}
		$html[] = '	<div class="block-body clearfix">';
		$html[] = '';
		echo implode("\n",$html);

	}
	
	
	/**
	 * 区块的尾部输出，闭合区块代码
	 * 
	 * @return null 
	 */
	public static function footer($footer=null)
	{
		$html[] = '';
	    $html[] = '	</div>';
	    $html[] = '	<div class="block-footer">'.$footer.'</div>';
	    $html[] = '</div>';

		echo implode("\n",$html);
	}
	
	public static function add($str)
	{
	    echo $str;    
	}
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 简化表格输出
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_table
{
	public static $attrs = array();
	public static $datas = array();


	public static function set($name, $value='')
	{
		if ( is_string($name) )
		{
			table::$attrs[$name] = $value;
		}

		if ( is_array($name) )
		{
			table::$attrs = array_merge(table::$attrs, $name);
		}
	}
	
	public static function header($classname='',$titles='')
	{
		if ( is_string($classname) )
		{
			table::$attrs['class'] = $classname;
		}

		if ( is_array($classname) )
		{
			table::$attrs = array_merge(table::$attrs, $classname);
		}

		if ( is_array($titles)  )
		{
			table::$attrs['titles'] = $titles;
		}
	}

	public static function footer()
	{
		echo table::render();

		table::$attrs = array();
		table::$datas = array();	
	}

	public static function row($data,$class='')
	{
		if( is_array($data) )
		{
			table::$datas[] = array('data' => $data,'class' => $class);
		}
	}

	public static function render($datas=array(), $attrs=array())
	{
		$datas = empty($datas) ? table::$datas : $datas;
		$attrs = empty($attrs) ? table::$attrs : $attrs;

		
		if ( isset($attrs['titles']) )
		{
			$titles = $attrs['titles'];
			unset($attrs['titles']);
		}
		
		$attrs['class'] = empty($attrs['class']) ? 'table' : 'table '.$attrs['class'];
		
		//渲染表格
			
		$html[] = '';
		$html[] = '<table'.html::attributes($attrs).'>';

		if(is_array($titles))
		{
			$html[] = '	<thead>';
			$html[] = '		<tr class="title">';

			foreach($titles as $name=>$title)
			{
				$html[] = '			<th class="'.$name.'"><b>'.$title.'</b></th>';
			}
			
			$html[] = '		</tr>';
			$html[] = '	</thead>';
		}
		$html[] = '	<tbody>';
		
		if ( is_array($datas) && !empty($datas) )
		{
			$i = 0;

			foreach($datas as $row)
			{
				$html[] = '';
				$html[] = '		<tr class="item '.($i%2==0?'odd':'even').' '.$row['class'].'">';
				foreach($row['data'] as $key=>$value)
				{
					if( is_string($value) )
					{
						$html[] = '			<td class="'.$key.'">'.$value.'</td>';
					}
					else
					{
						$data = arr::take('value',$value);

						$html[] ='			<td'.html::attributes($value).'>'.$data.'</td>';
					}
				}
				$html[] = '		</tr>';
				$i++;
			}
		}
		else
		{
			$html[] = '		<tr><td colspan="'.count($titles).'"><div class="zotop-empty">'.zotop::t('未能找到符合要求的数据').'</div></td></tr>';
		}

		$html[] = '	</tbody>';
		$html[] = '</table>';
			
		return implode("\n",$html);	
	}



}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 消息提示
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_msg
{
    public static function show(array $msg)
    {

		$page = new page();
		$page->title = $msg['type'];
		$page->body = array('class'=>'msg');
		$page->header();
		
		    $page->add('');
		    $page->add('<div id="icon"><div class="zotop-icon zotop-icon-'.$msg['type'].'"></div></div>');
			$page->add('<div id="msg" class="'.$msg['type'].' clearfix">');
			$page->add('	<div id="msg-type">'.$msg['type'].'</div>');
			$page->add('	<div id="msg-life">'.(int)$msg['life'].'</div>');
		    $page->add('	<div id="msg-title">'.$msg['title'].'</div>');
			$page->add('	<div id="msg-content">'.$msg['content'].'</div>');
			$page->add('	<div id="msg-detail">'.$msg['detail'].'</div>');
  			$page->add('	<div id="msg-action">'.$msg['action'].'</div>');
  			$page->add('	<div id="msg-file">'.$msg['file'].'</div>');
  			$page->add('	<div id="msg-line">'.$msg['line'].'</div>');

  			if( !empty($msg['url']) )
  			{
    			$page->add('	<div>');
    			$page->add('		<div><b>如果页面没有自动跳转，请点击以下链接</b></div>');
    			$page->add('		<a href="'.$msg['url'].'" id="msg-url">'.$msg['url'].'</a>');
    			$page->add('	</div>');
  			}
			$page->add('</div>');
            $page->add('<div id="powered">'.zotop::config('zotop.name').' '.zotop::config('zotop.version').'</div>');
			
		$page->footer();
		exit;
    }
    
    /**
     * 显示错误消息
     *
     */
    public static function error($content='', $life=3)
    {
        $msg = array('type'=>'error','title'=>zotop::t('error'),'content'=>'','life'=>$life,'file'=>'','line'=>'');
        
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content,array('type'=>'error'));
        }
        else
        {
           $msg['content'] =  $content;
        }
		
		//action提示	
		$html[] = '<div class="msg-title"><b>如果问题未能解决，请尝试以下操作：</b></div>';
		$html[] = '<ul class="list">';
		if( is_string($msg['action']) )
		{
			$html[] = '<li>'.$msg['action'].'</li>';	
		}
		if( is_array($msg['action']) )
		{
			foreach($msg['action'] as $action)
			{
				$html[] = '<li>'.$action.'</li>';
			}
		}
		$html[] = '<li>点击<a href="javascript:document.location.reload();"> 刷新 </a>重试，或者以后再试</li>';
		$html[] = '<li>或者尝试点击<a href="javascript:window.history.go(-1);"> 返回前页 </a>后再试</li>';
		$html[] = '</ul>';

		$msg['action'] = implode("\n",$html);

        msg::show($msg);
    }

    /**
     * 显示成功消息
     *
     */
    public static function success($content='', $url='', $life=2)
    {
        $msg = array('type'=>'success','title'=>zotop::t('success'),'content'=>'','detail'=>'','url'=>$url,'life'=>$life,'action'=>'');
		
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content,array('type'=>'success'));
        }
        else
        {
           $msg['content'] =  $content;
        }
		       
        msg::show($msg);
    }    
}
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 页面组件
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_pagination
{
	protected $config = array();
    
    /**
     * 初始化控制器
     * 
     */
    public function __construct($config=array())
    {
        $this->config = array_merge(array(
			'first'=>zotop::t('首页'),
			'prev'=>zotop::t('上页'),
			'next'=>zotop::t('下页'),
			'end'=>zotop::t('末页'),
			'page'=>isset($_GET['page']) ? $_GET['page'] : 1,
			'total'=>0,
			'pagesize'=>30,
			'param'=>'page',
			'template'=>zotop::t('<div class="pagination"><ul><li class="total">共 $total 条记录</li> <li class="page">$page页/$totalpages页</li> $first $prev $pages $next $end</ul></div>')
		),$config);
    }   
	
    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->config[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->config[$name])?$this->config[$name]:null;
    }	

	public function config($name=null, $value=null)
	{
        
		if( !isset($name) )
		{
			return $this->config;
		}

		if( !isset($value) )
		{
			return $this->config[$name];
		}
		
		if( is_array($name) )
        {
            $this->config = array_merge($this->config,$name);

			 return $this->config;
        }
        
        if( is_string($name) )
        {
            $this->config[$name] = $value;

			return $this->config;
        }	
	}

	public function url($page=1)
	{
		$param = empty($this->param) ? 'page' : $this->param;

		return url::join(array($param=>$page));
	}

	public function render()
	{
		$total = (int)$this->total; //总条数
		$pagesize = (int)$this->pagesize; //每页显示条数
		$page = (int)$this->page; //当前页码
		$showpage = (int)$this->showpage; //显示页码数,如值为10的时候，一共显示10个页码
		$showpage = 10;
		$maxpages = (int)$this->maxpages; //最多显示页数
		$offset = 2;
		
		if (  $total == 0 ||  $pagesize == 0 ) return '';

		//计算全部页数
		$totalpages = @ceil($total / $pagesize);
		$totalpages = $maxpages && $maxpages < $totalpages ? $maxpages : $totalpages; //最多显示页数计算

		//if ( $totalpages == 1 ) return '';

		//当前页码
		$page = $page <=0 ? 1 : $page;
		$page = $page > $totalpages ? $totalpages : $page;

		
		
		
		if ( $showpage > $totalpages )
		{
			$from = 1;
			$to = $totalpages;
		}
		else
		{
			$from = $page - $offset;
			$to = $from + $showpage -1;

			if ( $from < 1 )
			{
				$from = 1;
				$to = $page + 1 - $from;
				if ( $to - $from < $showpage )
				{
					$to = $showpage;
				}
			}
			elseif ( $to > $totalpages )
			{
				$from = $totalpages - $showpage + 1;
                $to = $totalpages;
			}

		}

		for($i = $from; $i <= $to; $i++) {
			if ( $i == $page )
			{
				$pages .= ' <li class="active">'.$i.'</li> ';
			}
			else
			{
				$pages .= ' <li><a href="'.$this->url($i).'">'.$i.'</a></li> ';
			}
        }



		
		//上下翻页
		$prev = $page - 1;
		$next = $page + 1;

		$prevPage = $prev > 0 ? '<li class="previous"><a href="'.$this->url($prev).'">'.$this->prev.'</a></li>' : '<li class="previous-off">'.$this->prev.'</li>';

		$nextPage = $next <= $totalpages ? '<li class="next"><a href="'.$this->url($next).'">'.$this->next.'</a></li>' : '<li class="next-off">'.$this->next.'</li>';

		$firstPage = $page == 1 ? '<li class="first-off">'.$this->first.'</li>' : '<li class="first"><a href="'.$this->url(1).'">'.$this->first.'</a></li>';

		$endPage = $page == $totalpages ? '<li class="end-off">'.$this->end.'</li>' : '<li class="end"><a href="'.$this->url($totalpages).'">'.$this->end.'</a></li>';


		$str = $this->template;
		$str = str_ireplace(
			array('$totalpages','$total','$pages','$page','$first','$prev','$next','$end'),
			array($totalpages,$total,$pages,$page,$firstPage,$prevPage,$nextPage,$endPage),
			$str
			);

		return $str;
	}
	

}

class zotop_path
{
	/* path解析
	 *
	 * @parem string $path 路径名称
	 * @return string
	 */
	public static function decode($path)
	{
        
	    $p = array(
	        '$system' => ZOTOP_PATH_SYSTEM,
	    	'$modules' => ZOTOP_PATH_MODULES,
	    );
	    $path = strtr($path,$p);
	    $path = path::clean($path);
	    return $path;
	}

	/**
	 * 将真实的path转化为系统的path表示方法
	 * 
	 *
	 */
	public static function encode($path)
	{
        return $path;
	}
    
	
	/**
	 * 清理路径中多余的斜线
	 * 
	 *
	 */
	public static function clean($path,$ds='')
	{
		$ds = empty($ds) ? DIRECTORY_SEPARATOR : $ds;
		
	    $path = trim($path);
		$path = empty($path) ? ZPATH_ROOT : $path;
		return preg_replace('#[/\\\\]+#', $ds, $path); //清理并转化
	}
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * dir类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_dir
{

	/**
	 * 判断目录是否 存在
	 *
	 */
	public static function exists($dir)
	{
		return is_dir( path::decode($dir) );
	}

	
	/**
	 * 返回文件夹的大小
	 */
	public static function size($dir)
	{
		$dir = path::decode($dir);
		
	    $handle = @opendir($dir);
        $size = 0;
        
		while (false!==($f = readdir($handle)))
        {

			if($f != "." && $f != "..")
            {
                if(is_dir("$dir/$f"))
                {
                    $size += dir::size("$dir/$f");
                }
                else
                {
                    $size += filesize("$dir/$f");
                }
            }
        }
        
        @closedir($handle);
        return $size;
	}

	/**
	 * 创建文件夹，返回true或者false
	 */
	public static function create($dir, $mode = 0755)
	{
	    
	  $dir = path::decode($dir);
	  
	  if ( is_dir($dir) || @mkdir($dir,$mode) )
	  {
		  return true;
	  }
	  
	  if( !dir::create(dirname($dir),$mode) )
	  {
		  return false;
	  }
	  
	  return @mkdir($dir,$mode);
	}
	
	/**
	 * 清理文件夹中全部文件
	 */
	public static function clear($dir, $subfolder= true)
	{
	    $dir = path::decode($dir);

	    $files = (array)dir::files($dir);
        
        foreach($files as $file)
        {
           @unlink($dir.DS.$file);
        }   	  
	}	

	/**
	 * 删除文件夹
	 */
	public static function delete($dir, $subfolder= true)
	{
	    $dir = path::decode($dir);
        
	}
    
	/**
	 * 返回目录下的全部文件的数组
	 * @param string $path 路径
	 * @param array $ext 特定的文件格式,如只获取jpg,png格式
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */
	public static function files($path, $ext='', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{

    	$files =array();
    	
	    $path = path::clean($path);
	    
	    if( !is_dir($path) )
	    {
	        return false;
	    }
	    
	    $handle = opendir($path);
	    
	    while (($file = readdir($handle)) !== false)
	    {
	        if( $file != '.' && $file != '..' && !in_array($file,$ignore) )
	        {
	            $f = $path .DS. $file;
	            if( is_dir($f) )
	            {
	                if ( $recurse )
	                {
    	                if( is_bool($recurse) )
    	                {
    	                    $subfiles = dir::files($f,$ext,$recurse,$fullpath);
    	                }
    	                else
    	                {
    	                    $subfiles = dir::files($f,$ext,$recurse-1,$fullpath);
    	                }
                    	if( is_array($subfiles) )
	                    {
	                        $files = array_merge($files,$subfiles);
	                    }    	                
	                }	                
	            }
	            else
	            {   
	                if( !empty($ext) )
	                {
	                    if( is_array($ext) && in_array(file::ext($file),$ext) )
	                    {
	                        $files[] = $fullpath ? $f :  $file;
	                    }
	                }
	                else
	                {
	                    $files[] = $fullpath ? $f :  $file;
	                }
	                
	            }
	        }
	    }
	    closedir($handle);
	    return $files;
	}
	/**
	 * 返回目录下的全部文件夹的数组
	 * @param string $path 路径
	 * @param array $filter 
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */	
	public static function folders($path, $filter='.', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{
	    $folders = array();
	    
	    $path = path::clean($path);
	    
	    if( !is_dir($path) )
	    {
	       return false;
	    }
	    
	    $handle = opendir($path);
	    
	    while (($file = readdir($handle)) !== false)
	    {
	        $f = $path .DS. $file;
	        if( $file != '.' && $file != '..' && !in_array($file,$ignore) && is_dir($f) )
	        {
                if (preg_match("/$filter/", $file)) {
                	if ($fullpath) {
                		$folders[] = $f;
                	} else {
                		$folders[] = $file;
                	}
                }
	            if ($recurse) {
					if (is_integer($recurse)) {
						$recurse--;
					}
					$subfolders = dir::folders($f, $filter, $recurse, $fullpath, $ignore);
					$folders = array_merge($folders, $subfolders);
				}       
	        }	        
	    }
	    
	    closedir($handle);
	    
	    return $folders;	    
	}
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * file操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_file
{
	
	/**
	 * 获取文件的扩展名
	 * @param string $file 文件名称
	 * @return string
	 */
	public static function ext($file)
	{
		$dot = strrpos($file, '.') + 1;
		
		return substr($file, $dot);
	}

	/**
	 * 获取文件名称 , 第二个参数控制是否返回文件扩展名，默认为返回带扩展名的文件名称
	 * @param string $file
	 * @param boolean $stripext
	 * @return string
	 */
	public static function name($file,$stripext=false)
	{
		$name=basename($file);
		if ( $stripext )
		{
			$ext = file::ext($file);
			$name=basename($file,'.'.$ext);
		}
		return $name;
	}

	/**
	 * 判断文件是否存在
	 * @param string $file
	 * @return boolean
	 */
	public static function exists($file)
	{
	    if ( empty($file) ) return false;
	    
	    $file = path::decode($file);
	    
	    return @is_file($file);
	}

	/**
	 * 获取文件的类型
	 * @param string $file
	 * @return string
	 */
	public static function type($file)
	{
		$ext = file::ext($file);
		
		if ( preg_match('/^(jpe?g|png|[gt]if|bmp|ico|tif|tiff|psd|xbm|xcf)$/', $ext) )
		{
			$type = 'image';
		}
		elseif ( preg_match('/^(doc|docx|xlt|xls|xlt|xltx|mdb|chm)$/', $ext) )
		{
			$type = 'document';
		}
		elseif ( preg_match('/^(html|htm|txt|php|asp|js|css|htc|tml|config|module|data|sql)$/', $ext) )
		{
			$type = 'text';
		}
		elseif ( preg_match('/^(rar|zip|7z|tar)$/', $ext) )
		{
			$type = 'zip';
		}
		elseif ( preg_match('/^(swf|flv)$/', $ext) )
		{
			$type = 'flash';
		}
		elseif ( preg_match('/^(mp3|mp4|wav|wmv|midi|ra|ram)$/', $ext) )
		{
			$type = 'audio';
		}
		elseif ( preg_match('/^(mpg|mpeg|avi|rm|rmvb|mov)$/', $ext) )
		{
			$type = 'video';
		}
		else
		{
			$type = 'unknown';
		}
	}


	/**
	 * 读取文件内容
	 *
	 * @param string $file
	 * @return string
	 */
	public static function read($file)
	{
       $file = path::decode($file);
       
       return @file_get_contents($file);
	}


	/**
	 * 写入文件
	 *
	 * @param string $file
	 * @param string $content
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public static function write($file, $content='', $overwrite=TRUE)
	{
	    $file = path::decode($file);
	    
	    //当目录不存在的情况下先创建目录
	    if ( !dir::exists(dirname($file)) )
		{
			dir::create(dirname($file));
		}

		if ( !file::exists($file) || $overwrite )
		{
		    return @file_put_contents($file, $content);
		}
		
        return false;
	}


	/**
	 * 删除文件
	 * @param string $file
	 * @return boolean
	 */
	public static function delete($file)
	{
        $file = path::decode($file);
        return @unlink($file);
	}

	/**
	 * 返回目录下的全部文件的数组,当level为0时候返回全部子文件夹目录
	 * @param string $path 路径
	 * @param array $ext 特定的文件格式,如只获取jpg,png格式
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */
	public static function brower($path, $ext='', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{
        return dir::files($path,$ext,$recurse,$fullpath,$ignore);
	}

	public static function copy()
	{
	    return false;
	}

	public static function move()
	{
        return false;
	}

    /**
     * 上传文件
     *
     * @param string $field  FILE字段名称
     * @param array|string $params 上传的参数|上传文件名称
     * @return array
     */
    public static function upload($field,$params)
    {
		if( is_array($params) )
		{
			
		}
	}

    /**
     * 远程下载文件
     *
     * @param string $url  远程文件地址
     * @param array $params 下载的参数
     * @return array
     */
	public static function download($url,$params)
	{
	
	
	}


	public static function find()
	{

	}
	
	public static function compile($file)
	{
	    $content = file::read($file);
	    $content = trim($content);
	    //strip <?php	    

	    $content = substr($content,5); 
	    //strip <?php
		if( strtolower(substr($content,-2)) == '?>' )
	    {
	       $content = substr($content,0,-2); 
	    }
	    return $content;	    
	}
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * image操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_image
{
	
	/**
	 * 获取获取指定大小的图片url
	 * @param string $image 图片文件名称
	 * @return string
	 */
	public static function url($image, $width=0, $height=0, $newext='')
	{
		if ( $width === 0 )
		{
			return $image;
		}
		
		//计算相应宽度高度的图片地址
		$ext = file::ext($image);
		$img = substr($image, 0, strlen($image)-strlen($ext)-1);
		$img = $img.'_'.$width;

		if ( $height !== 0 )
		{
			$img = $img.'_'.$height;
		}
		
		//新的扩展名
		if( !empty($newext) )
		{
			$ext = $newext;
		}
		
		//合成完整的图片url
		$img = $img.'.'.$ext;

		return $img;
	}

    public static function info($image) 
	{
        $image = ZOTOP_PATH_ROOT.DS.$image;

		$i = @getimagesize($image);

        if($i === false)
		{
			return false;
		}

		$size = @filesize($image);

		$info = array(
				'width'=>$i[0],
				'height'=>$i[1],
				'type' => $i[2],
				'size'=>$size,
				'mime'=>$i['mime']
				);

		return $info;
    }

	public static function resize($image, $width=0, $height=0)
	{
	
	}


}

class zotop_upload
{
	protected $files = array();
	protected $uploads = 0;
	protected $error = 0;
	protected $data = array(
		'field' => 'file',
		'maxsize' => 0,
		'savepath' => '',
		'overwrite' => true,
		'alowexts' => 'jpg|jpeg|gif|bmp|png|doc|docx|xls|ppt|pdf|txt|rar|zip',
		'filename' => '',
	);

    /**
     * 初始化控制器
     * 
     */
    public function __construct($config=array())
    {
        //设置上传参数
		$this->data = array_merge($this->data, (array)$config);		
    } 


    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->data[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 上传保存过程
     * 
     * @param string $field 上传文件字段名称
     * @return mixed
     */
	public function save($field='', $file='')
	{
		$uploadfiles = array();
		
		//字段名称
		$field = empty($field) ? $this->field : $field;



		//初始化上传数据
		$this->uploads = count($_FILES[$field]['name']);

		if ( 0 == $this->uploads )
		{
			$this->error = 5;//没有文件被上传，上传文件字段错误
			return false;
		}

		if ( 1 == $this->uploads )
		{
			if ( $_FILES[$field]['error'] === 0 )
			{
				$uploadfiles[0] = array(
					'id' => md5_file($_FILES[$field]['tmp_name']),
					'name' => $_FILES[$field]['name'],
					'tmp_name' => $_FILES[$field]['tmp_name'],
					'type' => $_FILES[$field]['type'],
					'size' => $_FILES[$field]['size'],
					'ext' => $this->getFileExt($_FILES[$field]['name']),
					'description' => $this->getFileDescription($_FILES[$field]['name']),
					'error' => $_FILES[$field]['error']				
				);
			}
		}
		else
		{
			foreach( $_FILES[$field]['name'] as $key => $value )
			{
				if ( $_FILES[$field]['error'][$key] === 0 )
				{
					$uploadfiles[$key] = array(
						'id' => md5_file($_FILES[$field]['tmp_name'][$key]),
						'name' => $_FILES[$field]['name'][$key],
						'tmp_name' => $_FILES[$field]['tmp_name'][$key],
						'type' => $_FILES[$field]['type'][$key],
						'size' => $_FILES[$field]['size'][$key],
						'ext' => $this->getFileExt($_FILES[$field]['name'][$key]),
						'description' => $this->getFileDescription($_FILES[$field]['name'][$key]),
						'error' => $_FILES[$field]['error'][$key]				
					);
				}
			}
		}		
		
		if ( empty($uploadfiles) )
		{
			$this->error = 4;//没有选择上传文件
			return false;
		}

		//上传
		foreach($uploadfiles as $key=>$file)
		{
	
			//格式检查
			if ( !$this->isAlowedFile($file) )
			{
				$this->error = 10; //上传格式错误
				return false;
			}
			
			//文件大小检查
			if($this->maxsize && $file['size'] > $this->maxsize)
			{
				$this->error = 11; //不被允许的格式
				return false;
			}
			
			//文件检查
			if(!$this->isuploadedfile($file['tmp_name']))
			{
				$this->error = 12;
				return false;
			}
			
			$savepath = $this->getSavePath($file);
			$filename = $this->getFileName($file);
			
			$filepath = $savepath.$filename; // uploads/2010/0307/dsfsdfasdfsdfsd.jpg
			
			//获取存储的实际文件
			$savefile = $this->getSaveFile($savepath,$filename); // E://wwwroot/zotopcms/uploads/dsfsdfasdfsdfsd.jpg
			//$savefile = preg_replace("/(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2", $savefile);

			//不允许覆写则调过该过程
			if ( !$this->overwrite && file_exists($savefile) )
			{
				$this->error = 13;
				return false;
			};

			//移动上传文件
			if( move_uploaded_file($file['tmp_name'], $savefile) || @copy($file['tmp_name'], $savefile) )
			{
				@chmod($savefile, 0644);
				@unlink($file['tmp_name']);
				$this->files[] = array(
					'id'=>md5($filepath),
					'name'=>$file['name'],
					'path'=>$filepath,
					'type'=>$file['type'],
					'size'=>$file['size'],
					'ext'=>$file['ext'],
					'description'=>$file['description']
				);
			}
			
		} // foreach
		
		return (array)$this->files;
	}



    /**
     * 上传保存位置
     * 
     * @return mixed
     */
	public function getSavePath($file='')
	{	
		$savepath = $this->savepath;
		
		if ( empty($savepath) )
		{
			$savepath = trim(zotop::config('upload.dir'),'/').'/'.trim(zotop::config('upload.filepath'),'/'); //  upload/2010/0312/
		}

		$savepath = $this->parsePath($savepath); //替换特殊变量

		
		return $savepath;
	}

	public function getSaveFile($savepath, $filename)
	{
		//返回实际目录
		$dir = ZOTOP_PATH_ROOT.DS.$savepath;

		//目录检测
		if( !is_dir($dir) && !dir::create($dir, 0777) )
		{
			$this->error = 8; //目录不存在且无法自动创建
			return false;
		}

		@chmod($dir, 0777);
		
		if(!is_writeable($dir) && ($dir != '/'))
		{
			$this->error = 9; //不可写
			return false;
		}
		
		$savefile = $dir.$filename;

		return $savefile;
	}

	public function getFileName($file)
	{
		$ext =  //获取原格式名称
		
		$filename = $this->filename; //获取文件命名方式		

		if ( empty($filename) )
		{
			$filename = zotop::config('upload.filename');
		}
		
		if ( $filename == 'time' )
		{
			$newfilename = date('Ymdhis').rand(1000, 9999).'.'.$file['ext'];
		}
		elseif ( $filename == 'md5' ||  $filename == 'id' )
		{
			$newfilename = $file['id'].'.'.$file['ext'];
		}
		else
		{
			$newfilename = $this->cleanFileName($file['name']);
		}
		
		return $newfilename;
	}


    /**
     * 获取文件扩展名
     * 
     * @return mixed
     */
	public function getFileExt($filename)
	{
		$x = explode('.', $filename);

		return strtolower(end($x));
	}

	public function getFileDescription($filename)
	{
		$x = explode('.', $filename);

		return current($x);
	}
	
	public function cleanFileName($filename)
	{
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						"%20",
						"%22",
						"%3c",		// <
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (
						"%29", 		// )
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);
					
		$filename = str_replace($bad, '', $filename);

		return stripslashes($filename);		
	}

	/*
	 * 接续文件路径，支持变量，如：$upload = 上传目录 , $type = 文件类型 ，$year = 年 ，$month = 月 ，$day = 日
	 * 
	 * @param string $filename，
	 */
	public function parsePath($savepath)
	{
		$p = array(
	        '$upload' => zotop::config('upload.dir'),
			'$year' => date("Y"),
			'$month' => date("m"),
			'$day' => date("d"),
			'$Y' => date("Y"),
			'$m' => date("m"),
			'$d' => date("d"),
	    );

	    $path = strtr($savepath, $p);

		$path = str_replace("\\", "/", $path);
		$path = str_replace("///", "/", $path);
		$path = str_replace("//", "/", $path);
	    $path = rtrim($path, '/').'/';

		return $path;
	}



	public function isuploadedfile($file)
	{
		return is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file));
	}

	public function isAlowedFile($file)
	{
		$alowexts = $this->alowexts;

		if ( is_array($alowexts) )
		{
			$alowexts = implode('|', $alowexts);
		}
		else
		{
			$alowexts = str_replace(array(',','/','\\'), '|', $alowexts);
		}

		$fileext = $file['ext'];
		
		return preg_match("/^(".strtolower($alowexts).")$/", $fileext);
	}

	public function error()
	{
		return $this->error;
	}

	function msg()
	{
		$messages = array(
			0 => zotop::t('文件上传成功'),
			1 => zotop::t('上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值'),
			2 => zotop::t('上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值'),
			3 => zotop::t('文件只有部分被上传'),
			4 => zotop::t('请选择要上传文件'),
			5 => zotop::t('没有文件被上传'),
			6 => zotop::t('找不到临时文件夹'),
			7 => zotop::t('文件写入临时文件夹失败'),
			8 => zotop::t('目录不存在且无法自动创建'),
			9 => zotop::t('目录没有写入权限'),
			10 => zotop::t('不允许上传该类型文件'),
			11 => zotop::t('文件超过了管理员限定的大小'),
			12 => zotop::t('非法上传文件'),
			13 => zotop::t('文件已经存在，且系统不允许覆盖已有文件'),
		);
		return $messages[$this->error];
	}


	

}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_arr
{
	/**
	 * 从数组中弹出键，类似于array_pop,但是是根据键名弹出
	 *
	 * @param string $key 弹出的键名
	 * @param array $array 目标数组
	 * @param boolean $bool 是否区分大小写
	 * @return $mix	被弹出 的数据
	 */
	public static function take($key, &$array, $bool=TRUE)
	{
		$array = (array)$array;
		if ( $bool )
		{
			$key = strtolower($key);
			$array = array_change_key_case($array);
		}

		if ( array_key_exists($key, $array) )
		{
			$str=$array[$key];
			unset($array[$key]);
			return $str;
		}
		
		return NULL;
	}

	/**
	 * 将字符串转化为数组,如：color:#0066cc;font-weight:bold;
	 *
	 * @param string $key 弹出的键名
	 * @param array $array 目标数组
	 * @param boolean $bool 是否区分大小写
	 * @return $mix	被弹出 的数据
	 */
	public static function decode($array,$s1 = "\n", $s2 = '|')
	{
		$os = array();
		$options = explode($s1, $array);
		
		foreach( $options as $option )
		{
			if ( strpos($option, $s2) )
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			
			$os[$name] = $value;
		}
		
		return $os;
	}

	public static function trim($input)
	{
		if ( !is_array($input) )
		{
			return trim($input);
		} 
		return array_map(array('arr','trim'), $input);
	}
	
    /**
     * 从数组中删除空白的元素（包括只有空白字符的元素）
     *
     * 用法：
     * @code php
     * $arr = array('', 'test', '   ');
     * arr::clear($arr);
     *
     * dump($arr);
     *   // 输出结果中将只有 'test'
     * @endcode
     *
     * @param array $arr 要处理的数组
     * @param boolean $trim 是否对数组元素调用 trim 函数
     */	
    public static function clear(&$arr, $trim=true)
    {
        foreach ($arr as $key => $value) 
        {
            if ( is_array($value) ) 
            {
                arr::clear($arr[$key]);
            } 
            else 
            {
                $value = trim($value);
                
                if ( $value == '' ) 
                {
                    unset($arr[$key]);
                } 
                elseif ( $trim ) 
                {
                    $arr[$key] = $value;
                }
            }
        }
        return $arr;
    }
    
    /**
     * 从一个二维数组中返回指定键的所有值
     *
     * 用法：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $values = arr::column($rows, 'value');
     *
     * dump($values);
     *   // 输出结果为
     *   // array(
     *   //   '1-1',
     *   //   '2-1',
     *   // )
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $col 要查询的键
     *
     * @return array 包含指定键所有值的数组
     */
    public static function column($arr, $col)
    {
        $ret = array();
        
        foreach ($arr as $row) 
        {
            if (isset($row[$col])) { $ret[] = $row[$col]; }
        }
        return $ret;
    }

    /**
     * 将一个二维数组转换为 HashMap，并返回结果
     *
     * 用法1：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = arr::hashmap($rows, 'id', 'value');
     *
     * dump($hashmap);
     *   // 输出结果为
     *   // array(
     *   //   1 => '1-1',
     *   //   2 => '2-1',
     *   // )
     * @endcode
     *
     * 如果省略 $value_field 参数，则转换结果每一项为包含该项所有数据的数组。
     *
     * 用法2：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = arr::hashmap($rows, 'id');
     *
     * dump($hashmap);
     *   // 输出结果为
     *   // array(
     *   //   1 => array('id' => 1, 'value' => '1-1'),
     *   //   2 => array('id' => 2, 'value' => '2-1'),
     *   // )
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_field 按照什么键的值进行转换
     * @param string $value_field 对应的键值
     *
     * @return array 转换后的 HashMap 样式数组
     */
    public static function hashmap($arr, $key_field, $value_field = null)
    {
        $ret = array();
        if ($value_field) 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row[$value_field];
            }
        } 
        else 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row;
            }
        }
        return $ret;
    }
    
    /**
     * 将一个二维数组按照指定字段的值分组
     *
     * 用法：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     * $values = arr::group($rows, 'parent');
     *
     * dump($values);
     *   // 按照 parent 分组的输出结果为
     *   // array(
     *   //   1 => array(
     *   //        array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *   //        array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *   //        array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *   //   ),
     *   //   2 => array(
     *   //        array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *   //        array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *   //   ),
     *   //   3 => array(
     *   //        array('id' => 6, 'value' => '6-1', 'parent' => 3),
     *   //   ),
     *   // )
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_field 作为分组依据的键名
     *
     * @return array 分组后的结果
     */
    public static function group($arr, $key_field)
    {
        $ret = array();
        foreach ($arr as $row) 
        {
            $key = $row[$key_field];
            $ret[$key][] = $row;
        }
        return $ret;
    }

    /**
     * 将一个路径转化成路径数组
     *
     * 用法：
     * @code php
     * $dir = 'system/admin/common'
	 *
     * $arr = arr::dirpath($dir, '/');
     *
     * dump($arr);
     *   // 输出结果为
     *   // array(
     *   //   array('system','system'),
     *   //   array('admin','system/admin'),
	 *   //   array('common','system/admin/common'),
     *   // )
     * @endcode
     *
     * @param array $dir 路径
     * @param string $d 分隔符
     *
     * @return array 包含全部路径的数组
     */
    public static function dirpath($dir, $d='/')
    {
		$array = explode($d, trim($dir, $d));

		$path = '';
		$dirs = array();

		foreach($array as $a)
		{
			$path .= $a.$d;
			$dirs[] = array($a, $path);
		}

		return $dirs;
    }    

    
}

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
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 产生随机字符
 * Thanks for Thinkphp！
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_rand
{
    public static  function string($len,$add='')
    {
		// 默认去掉了容易混淆的字符oOLl和数字01
		$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$add;
		if( $len > 10 ){
			$chars= str_repeat($chars,5);
		}
        $chars   =   str_shuffle($chars);
		//返回指定长度的字字符
		return substr($chars,0,$len);
    }

    public static function number($len=4)
    {
		$chars= str_repeat('0123456789',5);
		if( $len > 10 ){
			$chars= str_repeat($chars,$len);
		}
		//返回指定长度的数字
		return substr($chars,0,$len);
    }

	public static function letter($len=4,$type='',$add='')
	{
		switch(strtoupper($type))
		{
			case 'UPPER':
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$add;
				break;
			case 'LOWER':
				$chars='abcdefghijklmnopqrstuvwxyz'.$add;
				break;
			default:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$add;
				break;
		}
		if( $len > 10 ){
			$chars= str_repeat($chars,5);
		}
        $chars   =   str_shuffle($chars);
		//返回指定长度的字母
		return substr($chars,0,$len);
	}

	public static function guid($mix='',$len=32)
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

	public static function chinese($len=4,$add='')
	{
		$chars = "的一是在不了有和人这中大为上个国我以要他时来用们生到作地于出就分对成会可主发年动同工也能下过子说产种面而方后多定行学法所民得经十三之进着等部度家电力里如水化高自二理起小物现实加量都两体制机当使点从业本去把性好应开它合还因由其些然前外天政四日那社义事平形相全表间样与关各重新线内数正心反你明看原又么利比或但质气第向道命此变条只没结解问意建月公无系军很情者最立代想已通并提直题党程展五果料象员革位入常文总次品式活设及管特件长求老头基资边流路级少图山统接知较将组见计别她手角期根论运农指几九区强放决西被干做必战先回则任取据处队南给色光门即保治北造百规热领七海口东导器压志世金增争济阶油思术极交受联什认六共权收证改清己美再采转更单风切打白教速花带安场身车例真务具万每目至达走积示议声报斗完类八离华名确才科张信马节话米整空元况今集温传土许步群广石记需段研界拉林律叫且究观越织装影算低持音众书布复容儿须际商非验连断深难近矿千周委素技备半办青省列习响约支般史感劳便团往酸历市克何除消构府称太准精值号率族维划选标写存候毛亲快效斯院查江型眼王按格养易置派层片始却专状育厂京识适属圆包火住调满县局照参红细引听该铁价严".$add;

        for($i=0;$i<$len;$i++){
          $str.= string::substr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }

		return $str;

	}


}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_format
{
	public static function byte($size,$len=2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$pos=0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return number_format($size,$len).' '.$units[$pos];
	}
	
	/**
	 * 表情转换，可以通过hook(zotop.smiles)扩展
	 * 
	 *
	 */
	public static function smiles($str)
	{
	    $smiles = array(
	        ':)'=>url::theme().'/image/smiles/smile.gif',
	    	':-)'=>url::theme().'/image/smiles/cool.gif',
	    );
	    
	    $smiles = zotop::filter('zotop.smiles',$smiles);
	    
	    foreach($smiles as $key=>$val)
	    {
	        $str = str_replace($key,'<img src='.$val.' class="zotop-smile"/>',$str);
	    }
	    
	    return $str;
	}
	
	public static function email()
	{
	    
	}
	
	public static function link()
	{
	    
	}


}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * REQUEST
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_request
{
	public static function get($name='', $valid = NULL, $default = NULL)
	{
		if(empty($name))
		{
			return $_GET;
		}
		$get = $_GET[$name];
		return trim($get);
	}

	public static function post($name ='', $valid = NULL, $default = NULL)
	{
		if(empty($name))
		{
			return $_POST;
		}
		$post = $_POST[$name];
		$post = is_string($post) ? trim($post) : $post;
		return $post;
	}

	public static function referer()
	{
		return $_SERVER['HTTP_REFERER'];
	}



}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_html
{
	
	//encode的别名
	public static function specialchars($str)
	{
		return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
	}
	//编码字符串
	public static function encode($str)
	{
		return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
	}
	//解码字符串
	public static function decode($str)
	{
		return htmlspecialchars_decode($str, ENT_QUOTES);
	}
	//创建标签
	public static function attributes($attrs,$value=NULL)
	{
		if ( empty($attrs) )
		{
			return '';
		}
		
		if ( is_string($attrs) )
		{
			if ( !isset($value) )
			{
				return ' '.$attrs;
			}
			if ( empty($value) )
			{
			    return '';
			}
			return ' '.$attrs.'="'.$value.'"';
		}
		
		$str = '';
		
		if ( is_array($attrs) )
		{
			foreach ( $attrs as $key=>$val )
			{
			    if(!is_null($val))
			    {
				    $str .= ' '.$key.'="'.$val.'"';
			    }
			}
		}
		
		return $str;
	}
	/**
	 * 创建一个超链接传入uri或者完整的url
	 *
	 * @param string URL或者URI字符串
	 * @param string 链接显示文字
	 * @param array 链接的其它属性
	 * @param string 协议，如：http,https,ftp
	 * @return string 链接字符串
	 *
	 */
	public static function a($url,$title=NULL,$attrs=NULL,$protocol=NULL)
	{
		$title = isset($title) ? $title : $url;
	    return '<a href="'.$url.'" '.html::attributes($attrs).'>'.$title.'</a>';
	}

	public static function b($text,$attrs)
	{
		return '<b'.html::attributes($attrs).'>'.$text.'</b>';
	}

	/**
	 * 创建一个超链接传入uri或者完整的url
	 *
	 * @param string URL或者URI字符串
	 * @param array 图片的其它属性
	 * @return string 链接字符串
	 *
	 */
	public static function image($url, $attrs=array())
	{
		//如果不是完整的链接，如：http://www.zotop.com/a/b/1.gif ，则将相对连接处理成绝对链接
	    if( strpos($url, '://') === false && $url[0]!='/' && $url[0]!='$' )
		{
		    $url = '$root/'. $url;
		}
		
		//解析url中的特殊字符串
		$url = url::decode($url);
		
	    return '<img src="'.$url.'" '.html::attributes($attrs).'/>';
	}

	public static function flash($url,$width,$height,$attrs=array())
	{

	}

	public static function link($href,$attrs=array())
	{
		$links = array();
           
		$str='';
		if(is_array($href))
		{
			foreach($href as $h)
			{
				$str .= html::link($h,$attrs);
			}
		}
		else
		{
            $href = url::clean($href);
            $href = url::decode($href);
            //只加载一次
    	    $links[strtolower($href)] = true;
		    $attrs['href'] = $href;
			$str = '<link'.html::attributes($attrs).' />';
		}
		return $str;
	}


	public static function stylesheet($href,$attrs='')
	{
	    $attrs['rel']= 'stylesheet';
	    $attrs['type']= 'text/css';
	    return html::link($href,$attrs);
	}

	public static function style($style)
	{
	    if($style)
	    {
	        return '<style type="text/css">'.$style.'</style>';
	    }
	    return '';
	}


	public static function script($href,$attrs=array())
	{
		static $scripts = array();

	    $str = '';
		if(is_array($href))
		{
			foreach($href as $src)
			{
				$str .= html::script($src,$attrs);
			}
		}
		else
		{
		    //如果不是是直接输出的话
		    if(strpos($href , ';')==0)
		    {
				$href = url::clean($href);
				$href = url::decode($href);
			    //一个页面只允许加载一次
			    if( isset($scripts[strtolower($href)]) )
			    {
			       return '';
			    }
			    $scripts[strtolower($href)] = true;

			    $attrs['type'] = 'text/javascript';
			    $attrs['src'] = $href.'?v'.zotop::config('zotop.version');
			    $str = '<script'.html::attributes($attrs).'></script>';
		    }
		    else
		    {
		        $attrs['type'] = 'text/javascript';
		        $str = '<script'.html::attributes($attrs).'>'.$href.'</script>';
		    }

		}
		return $str;
	}

	public static function meta($tag,$value='')
	{
		$str= '';
		if(is_array($tag))
		{
			foreach($tag as $attr=>$value)
			{
				$str .= html::meta($attr,$value)."\n";
			}
		}
		else
		{
			$attr = in_array(strtolower($tag), array('content-type','content-language',strtolower('X-UA-Compatible'))) ? 'http-equiv' : 'name';
			$str = '<meta '.$attr.'="'.$tag.'" content="'.$value.'" />';
		}
		return $str;
	}
	//input标签
	public static function label($text,$for='',$attrs=array())
	{
		$attrs['for']=$for;
		return '<label'.html::attributes($attrs).'>'.$text.'</label>';
	}

	public static function input(array $attrs)
	{
		$attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'text';

		//if( isset($attrs['name']) )
		//{
		//    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
		//}

		$attrs['value'] = isset($attrs['value']) ? $attrs['value'] : '';

		return '<input'.html::attributes($attrs).'/>';
	}

	public static function button(array $attrs)
	{
		$attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'button';
		$attrs['class'] = isset($attrs['class']) ? 'button '.$attrs['class'] : 'button';

		$value = arr::take('value',$attrs);
		$icon = arr::take('icon',$attrs);
		$icon = empty($icon) ? 'empty' : $icon;
		
		return '<button'.html::attributes($attrs).'><p><span class="button-icon zotop-icon zotop-icon-'.$icon.'"></span><span class="button-text">'.html::encode($value).'</span></p></button>';		
	}

    public static function iframe($name,$src,$extra=array(),$noframe='')
    {
        $attrs = array(
            'id'=>$name,
        	'name'=>$name,
            'src'=>$src,
        );
        $attrs += (array)$extra;
        return '<iframe'.html::attributes($attrs).'>'.$noframe.'</iframe>';
    }

	public static function media()
	{

	}

	public static function checkbox($attrs, $value='', $checked=false , $label='')
	{
		if(!is_array($attrs))
		{
			$attrs = array(
        		'name'=>$attrs,
				'value'=>$value,
				'checked'=>$checked,
				'label'=>$label
			);
		}

		$attrs['type'] = 'checkbox';
		$attrs['id'] = empty($attrs['id']) ? $attrs['name'] : $attrs['id'];

		if ($checked == TRUE OR (isset($attrs['checked']) AND $attrs['checked'] == TRUE))
		{
			$attrs['checked'] = 'checked';
		}
		else
		{
			unset($attrs['checked']);
		}
		
		$label = arr::take('label',$attrs);
		$label = empty($label) ? '' : ' <label for="'.$attrs['id'].'">'.$label.'</label>';

		return html::input($attrs).$label;
	}

	public static function radio($attrs, $value='', $checked=false , $extra=array())
	{
		if(!is_array($attrs))
		{
			$attrs = array(
        		'name'=>$attrs,
				'value'=>$value,
			);
		}

		$attrs['type'] = 'radio';

		if ($checked == TRUE OR (isset($attrs['checked']) AND $attrs['checked'] == TRUE))
		{
			$attrs['checked'] = 'checked';
		}
		else
		{
			unset($attrs['checked']);
		}
		return html::input($attrs);
	}


	public static function nbs($num = 1)
	{
		return str_repeat("&nbsp;", $num);
	}

	public static function h($text = '', $h = '1')
	{
		return "<h".$h.">".$text."</h".$h.">";
	}

	public static function br($num = 1)
	{
		return str_repeat("<br />", $num);
	}
	
	public static function ul($data,$attrs=array('class'=>'list'))
	{
	    $html[] = '<ul'.html::attributes($attrs).'>';
	    foreach($data as $item)
	    {
	        $html[] = '<li>'.$item.'</li>';
	    }
	    $html[] = '</ul>';
	    return implode("\n",$html);
	}
	
	public static function msg($messages,$type='notice')
	{
        $html[] = '<div class="zotop-msg clearfix '.$type.'">';
        $html[] = '	<div class="zotop-msg-icon"><div class="zotop-icon zotop-icon-'.$type.'"></div></div>';
        $html[] = '	<div class="zotop-msg-content">';
        $html[] = is_array($messages) ? html::ul($messages) : $messages;
        $html[] = '	</div>';
        $html[] = '</div>';
        return implode("\n",$html);	   
	}
	
	public static function icon($name='')
	{
	    if ( empty($name) )
	    {
	       return '<div class="zotop-icon"></div>'; 
	    }
	    return '<div class="zotop-icon zotop-icon-'.$name.'"></div>';
	}

}

class zotop_ip
{
    /**
     * 获取当前的ip地址     * 
     *
     */
    public static function current()
    {
        return ip::get();
    }

    /**
     * 获取当前的ip地址     * 
     *
     */
    public static function location($ip='')
    {
        return ip::get();
    }

	public static function get()
	{
		$ip = '';
		
		if ( !empty($_SERVER["HTTP_CLIENT_IP"]) )
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif ( !empty($_SERVER["HTTP_X_FORWARDED_FOR"]) )
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif ( !empty($_SERVER["REMOTE_ADDR"]) )
		{
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		preg_match("/[\d\.]{7,15}/", $ip, $matches);

		$ip = isset($matches[0]) ? $matches[0] : 'unknown';

		unset($matches);
		
		return $ip;
	}
    

}

class zotop_time
{
    /**
     * 返回当前时间或者当前时间戳
     * 
     * @param bool $format 是否格式化时间
     *
     * @return string
     */
    public static function current($format=false)
    {
        static $time ='';
        if( empty($time) )
        {
            $time = time();
        }
        $time = $format ? time::format($time) : $time;
        return $time;
    }
    
    /**
     * 等同于 time::current() 函数
     *
     */
    public static function now($format=false)
    {
        return time::current($format);
    }
    
    /**
     * 对时间进行格式化，支持多种格式化方式
     * 
     * @param string 待格式化的时间戳或者时间标准串
     * @param string 时间格式
     * 
     * @return string 格式化后的时间
     */
    public static function format($time,$format='{YYYY}-{MM}-{DD} [HH]:[MM]:[SS]')
    {
		if( is_null($time) )
		{
			return null;
		}
        $format = strtoupper($format);
        $formatTime = strtr($format,array(
            '{YYYY}' => date('Y',$time),//2009
            '{YY}' => date('y',$time),//09
        	'{MM}' => date('m',$time),//01
            '{M}' => date('n',$time),//1
            '{DD}' => date('d',$time),//03
            '{D}' => date('j',$time),//3
            '[HH]' => date('H',$time),//12
            '[H]' => date('G',$time),//5
            '[MM]' => date('i',$time),//00
            '[M]' => date('i',$time),//00
            '[SS]' => date('s',$time),//00
            '[S]' => date('s',$time), //00               
        ));
        return $formatTime;
    }


    /**
     * 两个时间的间隔，如 1天3小时56分钟25秒
     * 
     * @param string 时间1
     * @param string 时间2
     * 
     * @return string 格式化后的时间
     */
	public static function span($t1, $t2='')
	{
	
	}

	public static function zone()
	{
	
	}
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 树形操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_tree
{
    public $nodes = array(); //树形的元数据2维数组
    public $root = 'root'; //根元素
    
	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      array('id'=>'1','parentid'=>'0','name'=>'一级栏目一'),
	*      array('id'=>'2','parentid'=>'0','name'=>'一级栏目二'),
	*      array('id'=>'3','parentid'=>'1','name'=>'二级栏目一'),
	*      array('id'=>'4','parentid'=>'1','name'=>'二级栏目二'),
	*      array('id'=>'5','parentid'=>'2','name'=>'二级栏目三'),
	*      array('id'=>'6','parentid'=>'3','name'=>'三级栏目一'),
	*      array('id'=>'7','parentid'=>'3','name'=>'三级栏目二')
	* )
	*/    
    public function __construct($trees=array(), $root='root')
    {
        $nodes =array();
        if( is_array($trees) )
        {
            foreach($trees as $tree)
            {
                $nodes[$tree['id']] = $tree;
            }
        }
        $this->nodes = $nodes;
        $this->root = $root;
    }
    
    /**
	* 获取父级节点数组
	* @param int|string
	* @return array
	*/
    public function getParent($id)
    {
        $parent = array();
        if( isset($this->nodes[$id]) )
        {
            $parentid = $this->nodes[$id]['parentid'];
            $parentid = $this->nodes[$parentid]['parentid'];
            foreach($this->nodes as $key=>$node)
            {
                if( $node['parentid'] == $parentid )
                {
                    $parent[$key] = $node; 
                }
            }
            return $parent;
        }
        return false;
    }
    
    /**
	* 获取子级节点数组
	* @param int|string
	* @return array
	*/    
    public function getChild($parentid)
    {
        $child = array();
        foreach($this->nodes as $key=>$node)
        {
            if( $node['parentid'] == $parentid )
            {
                $child[$key] = $node; 
            }
        }
        return $child;
    }

    /**
	* 得到当前位置的节点数组
	* @param int|string
	* @return array
	*/    
    public function getPosition($id,&$pos=array())
    {
        $position = array();
        if( isset($this->nodes[$id]) )
        {
            $pos[] = $this->nodes[$id];            
            $parentid = $this->nodes[$id]['parentid'];
            if( isset($this->nodes[$parentid]) )
            {
                $this->getPosition($parentid,$pos);
            }
            
            if( is_array($pos) )
            {
                krsort($pos);//逆向排序
                foreach($pos as $node)
                {
                    $position[$node['id']] = $node;
                }
            }
            return $position;
        }
        return false;        
    }
    
    
    
}

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数据库操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
abstract class zotop_database
{
    protected $config = array(); //数据库配置
    protected $connect = null; //当期数据库链接
    protected $sql = array(); //查询语句容器
    protected $sqlBuilder = array(); //查询语句构建容器
    protected $query = null; //查询对象
    protected $numRows = 0; //影响的数据条数
    protected $selectSql	= 'SELECT%DISTINCT% %FIELDS% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT%';

     /**
     * 生成数据库唯一实例
     *
     * @param $config
     * @return object
     */   
    public function &instance($config = array())
    {
        static $instances = array();
        
        //实例唯一的编号        
        $id = serialize($config);
        
        if ( !isset($instances[$id]) )
        {
            
            if ( is_string($config) )
            {
                 $config = $this->parseDNS($config);
            }
            
            if ( empty($config['driver']) )
            {
               zotop::error(zotop::t('错误的数据库配置文件',$config));
            }
            
            //数据库驱动程序
            $driver = 'database_'.strtolower($config['driver']);
            
            //加载驱动程序
            if ( !zotop::autoload($driver) )
            {
              zotop::error(zotop::t('未能找到数据库驱动 "{$driver}"',$config));
            }
            
            //取得驱动实例
            $instance	= new $driver($config);
            
            //存储实例
            $instances[$id] = &$instance;            
        }
        
        return $instances[$id];
    }
    
    /**
    * 将数据库连接的DNS字符串转化为数组
    *
    * @param string $str 数据库连接DNS，如：mysql://root:123456@localhost:80/test
    * @return array|bool 数据库连接信息
    */
    public function parseDNS($str)
    {
       $str = str_replace('@/', '@localhost/', trim($str));

       $info = parse_url($str);

       if( !empty($info['scheme']) )
       {
           $dns = array();
           $dns['driver'] = $info['scheme'];
           $dns['username'] = isset($info['user']) ? $info['user'] : '';
           $dns['password'] = isset($info['pass']) ? $info['pass'] : '';
           $dns['hostname'] = isset($info['host']) ? $info['host'] : '';
           $dns['hostport'] = isset($info['port']) ? $info['port'] : '';
           $dns['database'] = isset($info['path']) ? $info['path'] : '';

           return $dns;
       }
       return false;
    }
    
   /**
    * CONFIG 设置
    *
    * @return array
    */      
    public function config($key='' , $value=null)
    {
        if ( empty($key) )
        {
            return $this->config;
        }
        
        if ( is_array($key) )
        {
            $this->config = array_merge($this->config , $key);
            return $this->config;
        }
        
        if ( isset($value) )
        {
            $this->config[$key] = $value;
            return $this->config;
        }
        
        $config = $this->config[$key];
        
        if ( isset($config) )
        {
            return $config;
        }
        
        return false;
    }
    
   /**
    * 连接数据库，该方法必须被重载，实现数据库的连接
    *
    * @return object
    */  
    public function connect()
    {
        zotop::error(zotop::t('函数必须被重载'));
    }
    
    /**
     * 对字符串进行安全处理
     *
     * @return string
     */
    public function escape($str)
    {
        return addslashes($str);
    }
    
	/**
	 * 对输入的值进行处理
	 *
	 * @param   mixed   value to escape
	 * @return  string
	 */
	public function escapeValue($value)
	{
	    switch (gettype($value))
		{
			case 'string':
				$value = '\''.$this->escape($value).'\'';
			    break;
			case 'boolean':
				//$value = (int) $value;
				$value = ($value === FALSE) ? 0 : 1;
			    break;
			case 'double':
				$value = sprintf('%F', $value);
			    break;
			case 'array':
				$value = $this->escapeValue(json_encode($value));
				break;
			default:
				$value = ($value === NULL) ? 'NULL' : $value;
			    break;
		}

		return (string) $value;
	}

    /**
     * 对字段名称进行安全处理
     *
     * @return string
     */
    public function escapeColumn($str)
    {
        return $str;
    }
    
    /**
     * 对表名称进行安全处理
     *
     * @return string
     */
    public function escapeTable($str)
    {
        return $str;
    }

    /**
    * sql语句构建
    *
    * @return array
    */      
    public function sqlBuilder($key='', $value=null)
    {
        if ( empty($key) )
        {        
            return $this->sqlBuilder;
        }
        
        if ( is_array($key) )
        {
            $this->sqlBuilder = array_merge($this->sqlBuilder , $key);
            
            return $this->sqlBuilder;       
        }
        
        if ( isset($value) )
        {
            $this->sqlBuilder[$key] = $value;
            
            return $this->sqlBuilder;
        }
        
        $sqlBuilder = $this->sqlBuilder[$key];
        
        if ( isset($sqlBuilder) )
        {
            return $sqlBuilder;
        }
        
        return false;        
    }
    
    
    /**
     * 重置查询
     */
    public function reset()
    {
        $this->sqlBuilder = array();
    }
    
	/**
	 * 链式查询：设置读取的字段
	 *   
	 */
	public function select($fields = '*')
	{
	    if ( func_num_args()>1 )
		{
			//select('id','username','password')
		    $fields = func_get_args(); 
		}
		elseif ( is_string($fields) )
		{
			//select('id,username,password')
		    $fields = explode(',', $fields); 
		}

		$this->sqlBuilder['select'] = (array) $fields;

		return $this;
	}

	/**
	 * 链式查询，设置查询数据表 "FROM ..."
	 *
	 * @param   mixed  table name or array($table, $alias) or object
	 * @param   ...
	 * @return  $this
	 */
	public function from($tables='')
	{
	    if ( !empty($tables) )
	    {
    	    if (  func_num_args()>1 )
    		{
    			//from('user','config')
    		    $tables = func_get_args(); 
    		}
    		elseif ( is_string($tables) )
    		{
    			//from('user,config')
    		    $tables = explode(',', $tables); 
    		}
    		
    		$this->sqlBuilder['from'] = $tables;
	    }		
		return $this;
	}

	/* 链式查询：设置查询条件
	 * 
	 * where('id',1)
	 * where('id','<',1)
	 * where(array('id','<',1),'and',array('id','>',1))
	 * where(array('id','like',1),'and',array(array('id','>',1),'or',array('id','>',1)))
	 *
	 */

	public function where($key,$value=null)
	{
		if ( !is_array($key) )
		{
		    $where = func_get_args();
			
			switch ( count($where) )
			{
				case 2:
					$where = array($where[0],'=',$where[1]); //where('id',1)  => array('id','=',1)
					break;
				case 3:
					$where = array($where[0],$where[1],$where[2]); //where('id','<',1)  => array('id','<',1)
					break;
			}
		}
		else
		{
			if ( func_num_args()==1 )
			{
				$where = $key;
			}
			else
			{
				$where = func_get_args(); //where(array('id','=','1'),'and',array('status','>','0'))
			}

		}
		
		if ( !empty($where) && !empty($where[0]))
		{
    		if ( count($this->sqlBuilder['where']) >0 )
    		{
    			$this->sqlBuilder['where'][] = 'AND';
    		}
    		$this->sqlBuilder['where'][] = $where;
		}
		return $this;
	}

	/**
	 * 链式查询，设置查询范围
	 */
	public function limit($limit, $offset = 0)
	{
		$this->sqlBuilder['limit']  = (int) $limit;

		if ( $offset !== NULL || !is_int($this->sqlBuilder['offset']) )
		{
			$this->sqlBuilder['offset'] = (int) $offset;
		}

		return $this;
	}
	
	/**
	 * 链式查询，设置查询范围
	 */
	public function offset($value)
	{
		$this->sqlBuilder['offset'] = (int) $value;

		return $this;
	}
		
	/**
	 * 链式查询，设置查询排序
	 */
	public function orderby($orderby, $direction=null)
	{
		if ( $orderby === null )
		{
			$this->sqlBuilder['orderby'] = '';
		}
		else
		{
			if ( is_string($orderby) )
			{
				$orderby = array($orderby => $direction);
			}
			
			$this->sqlBuilder['orderby'] = array_merge((array)$this->sqlBuilder['orderby'], $orderby);
		}
		
		return $this;
	}
	
	/**
	 * 链式查询，数据设置
	 *
	 */
	public function set($name, $value='')
	{
	    if ( is_string($name) )
	    {
	        $data = array($name=>$value);
	    }
	    elseif ( is_array($name) )
	    {
	        $data = $name;
	    }
		
	    $this->sqlBuilder['set'] = array_merge((array)$this->sqlBuilder['set'], $data);
		
	    return $this;	    
	}

    /**
    * 查询构建器  FROM 解析
    * @access protected
    * @param mixed $distinct
    * @return string
    */   
    public function parseFrom($tables)
    {
        $array = array();
        
        if ( is_string($tables) )
        {
            $tables = explode(',',$tables);
        }
        
        foreach ($tables as $key=>$table)
        {
			$array[] = $this->escapeTable($table);
        }
        
        return implode(',',$array);
    }

    /**
     * 查询语句构建器 DISTINCT 解析
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    public function parseDistinct($distinct)
    {
        return empty($distinct) ? '' : ' DISTINCT ';
    }
    
    /**
     * 查询语句构建器 SELECT字段 解析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    public function parseSelect($fields)
    {
          if ( !empty($fields) )
          {
              if ( is_string($fields) )
              {
                  $fields = explode('.',$fields);
              }
              
			  $array = array();
			  
			  foreach($fields as $key=>$filed)
			  {
				$array[] = $this->escapeColumn($filed);
			  }
			  
			  return implode(',',$array);
          }
          return '*';
    } 

    /**
     * 查询语句构建器 JOIN 解析
     * @access protected
     * @param mixed $join
     * @return string
     */
    public function parseJoin($join)
    {
        return '';
    }

    /**
     * 查询语句构建器 WHERE 解析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    public function parseWhere($where)
    {
        $str = '';
        if ( is_array($where) )
        {
            $str = $this->parseWhereArray($where);
        }
        else
        {
            $str = $where;
        }
        
        return empty($str) ? '' : ' WHERE '.$str;
    }

    /**
     * 查询语句构建器 WHERE 解析
     *
     */
    public function parseWhereArray($where)
    {
        if ( !empty($where) )
        {
            if ( is_string($where[0]) && count($where)==3 )
            {
				$operator = trim(strtoupper($where[1]));
				switch($operator)
				{
					case '=':
					case '!=':
					case '>':
					case '<':
					case '>=':
					case '<=':
						return $this->escapeColumn($where[0]).' '.$where[1].' '.$this->escapeValue($where[2]);
						break;
					case 'IS':
					case 'IS NOT':
					case 'IN':
					case 'NOT IN':
					case 'BETWEEN':
						return $this->escapeColumn($where[0]).' '.$where[1].' '.$this->escapeValue($where[2]);
						break;
					case 'LIKE':
					case '%LIKE%':
						return $this->escapeColumn($where[0]).' '.trim($where[1],'%').' '.$this->escapeValue('%'.trim($where[2],'%').'%');
						break;
					case 'LIKE%':
						return $this->escapeColumn($where[0]).' '.trim($where[1],'%').' '.$this->escapeValue(''.trim($where[2],'%').'%');
						break;
					case '%LIKE':
						return $this->escapeColumn($where[0]).' '.trim($where[1],'%').' '.$this->escapeValue('%'.trim($where[2],'%').'');
						break;
					default :
						//die("错误的SQL参数");
						return '';
				}
            }
            
			$str = '';

			for ( $i=0,$j=count($where); $i<$j ; $i++ )
			{
				if ( is_array($where[$i][0]) )
				{
					$str .= '('.$this->parseWhereArray($where[$i]).')';
				}
				elseif ( is_array($where[$i]) )
				{
					$str .= $this->parseWhereArray($where[$i]);
				}
				elseif ( is_string($where[$i]) )
				{
					$str .= ' '.strtoupper(trim($where[$i])).' ';
				}

			}
        }
        
        return $str;        
    }

    /**
     * 查询语句构建器  GROUP BY 解析
     */
    public function parseGroupBy($group)
    {
        return empty($group) ? '' : ' GROUP BY '.$group;
    }
    
    /**
     * 查询语句构建器  HAVING 解析
     */    
    public function parseHaving($having)
    {
        return empty($having)? '' : ' HAVING '.$having;
    }    

	/**
     * 查询语句构建器  ORDER BY 解析
	 */
	public function parseOrderBy($orderby)
	{
		$str = '';
		
		if ( is_array($orderby) )
		{
			foreach ( $orderby as $key=>$direction )
			{
				$direction = strtoupper(trim($direction));
				if ( !in_array($direction, array('ASC', 'DESC', 'RAND()', 'RANDOM()', 'NULL')) )
				{
					$direction = 'ASC';
				}
				$str .= ','.$this->escapeColumn($key).' '.$direction;
			}
		}
		else
		{
			$str = $orderby;
		}
		
		$str = trim($str,',');
		
		return empty($str) ? '' : ' ORDER BY '.$str;
	}     
	
    /**
     * 查询语句构建器  LIMIT 解析
	 * 
	 * @param $offset int 起始位置
	 * @param  $limit  int 条数限制
	 * @return string
	 *
     */
    public function parseLimit($limit, $offset=0)
	{
		$str = '';

		if( is_int($offset) )
		{
			$str .= (int)$offset.',';
		}
		if( is_int($limit) )
		{
		    $str .= $limit;
		}
		return empty($str) ? '' : ' LIMIT '.$str;
    }
    

    
    /**
     * 查询语句构建器 SET 解析
     */
    public function parseSet($data)
    {
        $str = '';

        foreach($data as $key=>$val)
        {
                
            //解析值中的如：num = array('num','+',1) 或者array('num','-',1) 
			if ( is_array($val) && count($val)==3 && in_array($val[1],array('+','-','*','%')) && is_numeric($val[2]) )
			{
				 $str .= ','.$this->escapeColumn($key).' = '.$this->escapeColumn($val[0]).$val[1].(int)$val[2];
			}
			else
			{
				$str .= ','.$this->escapeColumn($key).' = '.$this->escapeValue($val);
			}

        }
        
        $str = trim($str,',');
        
        return empty($str) ? '' : ' SET '.$str;
    }    
    
     /**
     * 数据查询，该方法必须被重载，实现数据库的查询，并返回查询
     *
     * @return object
     */  
    public function query()
    {
        zotop::error(zotop::t('函数必须被重载'));
    }

     /**
     * 数据查询，该方法必须被重载，实现数据库的查询，无返回
     *
     * @return null
     */  
    public function execute()
    {
        zotop::error(zotop::t('函数必须被重载'));
    }

     /**
     * 执行多个sql，自动分割sql语句
     *
     * @return null
     */  
	public function run($sqldump, $silent=true)
	{
		$sqls = $this->splitSql($sqldump);

		if(is_array($sqls))
		{
			foreach($sqls as $sql)
			{
				if(trim($sql) != '')
				{
					$this->execute($sql, $silent);
				}
			}
		}
		else
		{
			$this->execute($sql, $silent);
		}
		return true;			
	}

	public function splitSql($sqldump)
	{
		$sql = str_replace("\r", "\n", $sqldump);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach ($queriesarray as $query)
		{
			$queries = explode("\n", trim($query));
			foreach($queries as $query)
			{
				if ( !empty($query[0]) && $query[0] != '#' && $query[0].$query[1] != '--' )
				{
					$ret[$num] .= $query;
				}
			}
			$num++;
		}
		return $ret;	
	}

    /**
     * 解析sql语句
     */
    public function parseSql($sql)
    {
        if( is_string($sql) )
        {
            $this->sql[] = $sql;
        }
                    
        return $sql;        
    }

	function replacePrefix($sql, $newprefix='', $prefix='#')
	{
		if ( empty($newprefix) )
		{
			$newprefix = $this->config('prefix');
		}

		if ( $newprefix == $prefix )
		{
			return $sql;
		}


		$sql = trim( $sql );

		$escaped = false;
		$quoteChar = '';

		$n = strlen( $sql );

		$startPos = 0;
		$literal = '';
		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos( $sql, "'", $startPos );
			$k = strpos( $sql, '"', $startPos );
			if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
				$quoteChar	= '"';
				$j			= $k;
			} else {
				$quoteChar	= "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace( $prefix, $newprefix, substr( $sql, $startPos, $j - $startPos ) );
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (TRUE) {
				$k = strpos( $sql, $quoteChar, $j );
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j	= $k+1;
					continue;
				}
				break;
			}
			if ($k === FALSE) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr( $sql, $startPos, $k - $startPos + 1 );
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr( $sql, $startPos, $n - $startPos );
		}
		return $literal;
	}

    /**
     * 数据插入
     *
     */
    public function insert($table='', $data=array())
    {
        //设置查询
        $this->from($table)->set($data);
        
        //获取sqlBuilder
        $sqlBuilder = $this->sqlBuilder();
        
        $data = $sqlBuilder['set'];
        
        if ( !is_array($data) )
        {
            return false;
        }
        
        //处理插入数据
        foreach( $data as $field=>$value )
        {
            $fields[] = $this->escapeColumn($field);
            $values[] = $this->escapeValue($value);
        }        
        
        //sql
        $sql = 'INSERT INTO %TABLE% (%FIELDS%) VALUES (%VALUES%)';
        $sql = str_replace(
            array('%TABLE%','%FIELDS%','%VALUES%'),
            array(
				$this->parseFrom($sqlBuilder['from']),
				implode(',', $fields),
				implode(',', $values)
            ),
            $sql
        );
        
        //返回查询结果
        return $this->execute($sql);
    }
    
    /**
     * 数据更新
     *
     */
    public function update($table='', $data=array(), $where=array())
    {
        //设置查询
        $this->from($table)->set($data)->where($where);

        //获取sqlBuilder
        $sqlBuilder = $this->sqlBuilder();
        
        //必须设置更新条件
        if( empty($sqlBuilder['where']) )
        {
            return false;
        }
        
        //sql            
        $sql = 'UPDATE %TABLE%%SET%%WHERE%';
        $sql = str_replace(
            array('%TABLE%','%SET%','%WHERE%'),
            array(
				$this->parseFrom($sqlBuilder['from']),
				$this->parseSet($sqlBuilder['set']),
				$this->parseWhere($sqlBuilder['where']),
            ),
            $sql
        );

        //返回查询结果
        return $this->execute($sql);         
    }
    
    public function delete($table='', $where=array())
    {
        //设置查询
        $this->from($table)->where($where);
        
        //获取sqlBuilder
        $sqlBuilder = $this->sqlBuilder();
        
        //必须设置删除条件
        if( empty($sqlBuilder['where']) )
        {
            return false;
        }
        
        $sql = 'DELETE FROM %TABLE%%WHERE%';
        $sql = str_replace(
            array('%TABLE%','%WHERE%'),
            array(
				$this->parseFrom($sqlBuilder['from']),
				$this->parseWhere($sqlBuilder['where']),
            ),
            $sql
        );

        //返回查询结果
        return $this->execute($sql);         
    }

	public function count($where=array())
	{
		$count = $this->select('count(*) as num')->where($where)->orderby(null)->getOne();

		if( !is_numeric($count) )
		{
			$count = 0;
		}
		return $count;
	}
    
    public function compileSelect($sql)
    {
        if ( is_array($sql) || empty($sql) )
        {
            $sqlBuilder = $this->sqlBuilder($sql);
                        
 			$sql = str_replace(
				array('%TABLE%','%DISTINCT%','%FIELDS%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%'),
				array(
					$this->parseFrom($sqlBuilder['from']),
					$this->parseDistinct($sqlBuilder['distinct']),
					$this->parseSelect($sqlBuilder['select']),
					$this->parseJoin($sqlBuilder['join']),
					$this->parseWhere($sqlBuilder['where']),
					$this->parseGroupBy($sqlBuilder['groupby']),
					$this->parseHaving($sqlBuilder['having']),
					$this->parseOrderBy($sqlBuilder['orderby']),
					$this->parseLimit($sqlBuilder['limit'],$sqlBuilder['offset']),
				),
				$this->selectSql
			);              
        }
        
        return $sql;
    }
    

   /**
    * 获取指定数据
    *
    * @return array
    */ 
    public function get()
    {}
    

   /**
    * 获取全部数据
    *
    * @return array
    */ 
    public function getAll()
    {}
    
   /**
    * 获取一行数据
    *
    * @return array
    */ 
    public function getRow()
    {}    
    
   /**
    * 获取单个字段数据
    *
    * @return mix
    */   
    public function getOne()
    {}
      
	/**
	 * 返回limit限制的数据,用于带分页的查询数据
	 *
	 * @param $page int 页码
	 * @param $pagesize int 每页显示条数
	 * @param $num int|bool 总条数|缓存查询条数，$toal = (false||0) 不缓存查询
	 * @return mixed
	 */
	public function getPage($page=0, $pagesize=15, $num = false)
	{
        
		$page = $page <=0 ? (int)$_GET['page'] :$page;
		$page = $page <=0 ? 1 :$page;

		//获取查询参数
		$sqlBuilder = $this->sqlBuilder($sql);

		if ( is_numeric($num) && $num > 0 )
		{
			$total = $num;
		}
		else
		{
			$hash = md5(serialize($sqlBuilder['where']));

			if ( $page == 1 || $num == true || !is_numeric(zotop::cookie($hash)))
			{
				//获取符合条件数据条数
				$total = $this->count($sqlBuilder['where']);				

				zotop::cookie($hash,$total);				
			}
			else
			{
				$total = zotop::cookie($hash);
			}

		}

		//zotop::dump($this->lastsql());

		//计算$offset
		$offset = intval($page) > 0 ? (intval($page)-1)*intval($pagesize) : 0;
		
		//设置limit
		$this->sqlBuilder($sqlBuilder);
		$this->limit($pagesize, $offset);

		//获取指定条件的数据
		$data = $this->getAll();

		return array(
			'data' => (array) $data,
			'page' => intval($page),
			'pagesize' => intval($pagesize),
			'total' => intval($total),
		);
	}
    
   /**
    * 返回查询语句的数组
    *
    * @return array
    */   
    public function sql()
    {
        return $this->sql;
    }

    
    /**
     * 返回最后一条查询语句
     *
     * @return array
     */      
    public function lastSql()
    {
        if ( is_array($this->sql) )
        {
            return end($this->sql);
        }
        return '';
    }
    
	/**
	 * 返回数据库的大小
	 */
	public function size()
	{
		return 'Unknow!';
	}
	
	/**
	 * 返回数据库的版本
	 */	    
	public function version()
	{
		return 'Unknow!';
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
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * MYSQL 数据库操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_database_mysql extends zotop_database
{
    /**
     * 数据库类初始化
     * 
     * @param array|string $config
     */
    public function __construct($config = array())
    {
        $default = array(
         'driver' => 'mysql',
         'username' => 'root',
         'password' => '',
         'hostname' => 'localhost',
         'hostport' => '3306',
         'database' => 'zotop',
         'charset' => 'utf8',         
		 'prefix'=>'zotop_',
		 'pconnect' => false
        );
        
		$this->config = array_merge($default, $this->config, $config);
    }

	/**
	 * 数据库类对象销毁
	 */
	public function __destruct()
	{
		is_resource($this->connect) and mysql_close($this->connect);
	}

	/**
	 * 数据库连接
	 */
	public function connect($test = false)
	{
	    if ( is_resource($this->connect) )
	    {
	        return $this->connect;
	    }
	    
	    $connect = ( $this->config('pconnect') == TRUE ) ? 'mysql_pconnect' : 'mysql_connect';
	    
        $host = $this->config('hostname');
        $port = $this->config('hostport');
        $host = empty($port) ? $host : $host.':'.$port;
        $user = $this->config('username');
        $pass = $this->config('password');
        

        if ( $this->connect = @$connect($host, $user, $pass, true) )
        {
            $database = $this->config('database');
            
            if ( @mysql_select_db($database, $this->connect) )
            {
                $version = $this->version();
                
				if ( $version > '4.1' && $charset = $this->config('charset') )
    			{
					@mysql_query("SET NAMES '".$charset."'" , $this->connect);//使用UTF8存取数据库 需要mysql 4.1.0以上支持
    			}
    			
				if ($version > '5.0.1'){
					@mysql_query("SET sql_mode=''",$this->connect);//设置 sql_model
				}
                
				return true;
            }
            if ( $test ) return false;//测试连接是否有效
            zotop::error(zotop::t('Cannot use database `{$database}`',$this->config()));
        }
        zotop::error(array('content'=>'Cannot connect to database server','detail'=>mysql_error()));
	}

	/**
	 * 测试数据库连接
	 *
	 */
	public function test()
	{
	    return true;
	}
	
	/**
	 * 执行一个sql语句 ，等价于 mysql_query
	 *
	 * @param $sql
	 * @param $silent
	 * @return unknown_type
	 */
	public function query($sql, $silent=false)
    {
        $this->reset();
        
        if ( !is_resource($this->connect) )
        {
			$this->connect();
        }
                
		if ( $sql = $this->parseSql($sql) )
		{
			//zotop::dump($this->sql);
			if ( $this->query )
			{
			    $this->free(); //释放前次的查询结果
			}
			
			$this->query = @mysql_query($sql, $this->connect);//查询数据
			
			if ( $this->query === false )
			{
				if ( $silent ) return false;
				
				zotop::error(array(
					'content'=>@mysql_error(),
					'detail'=>zotop::t('SQL : {$sql}',array('sql'=>$sql))
				));
			}

			database::Q(true);
			
			$this->numRows = @mysql_num_rows($this->query);
			
			return $this->query;
		}
		return false;
    }
    
	/**
	 * 释放一个Query
	 */
	public function free()
	{
		@mysql_free_result($this->query);
		
        $this->query = 0;
	}

	/**
	 * 执行一个sql语句，并返回影响的数据行数
	 *
	 * @param $sql
	 * @param $silent
	 * @return bool||number
	 */
	public function execute($sql, $silent=false)
	{
        $result = $this->query($sql,$silent);
        
		if( $result === false )
		{
			return false;
		}
		
		//当使用 UPDATE 查询，MySQL 不会将原值与新值一样的列更新。这样使得 mysql_affected_rows() 函数返回值不一定就是查询条件所符合的记录数，只有真正被修改的记录数才会被返回
		$this->numRows = @mysql_affected_rows($this->connect);						
					
		return true;
	}
	
    /**
     * 解析sql语句
     */
    public function parseSql($sql)
    {
        $prefix = $this->config('prefix');
		
		//$sql = 
		
		if( is_string($sql) )
        {
            $this->sql[] = $sql;
        }
                    
        return $sql;        
    }

    
	/**
	 * 执行一个sql语句并返回结果数组
	 *
	 * @param $sql
	 * @return array
	 */
	public function getAll($sql='')
	{
		
		$sql = $this->compileSelect($sql);

		if ( $query = $this->query($sql) )
		{
			$result = array();
			
			if ( $this->numRows >0 ) {
				
			    while( $row = mysql_fetch_assoc($query) )
			    {
					$result[]   =   $row;
				}
				
				mysql_data_seek($this->query,0);
			}
			return $result;
		}
		return false;
	}

	/**
	 * 从查询句柄提取一条记录
	 *
	 * @param $sql
	 * @return array
	 */
	public function getRow($sql='')
	{
	    
		$sql = $this->compileSelect($sql);

		
		
		if ( $query = $this->query($sql) )
		{
			
			$row = mysql_fetch_assoc($query);
			
			if($row)
			{
                return $row;
			}
			
			return null;
		}
		return false;
	}	
	
	/**
	 * 从查询句柄提取一条记录，并返回该记录的第一个字段
	 *
	 * @param $sql
	 * @return mixed
	 */
	public function getOne($sql='')
	{
	    $sql = $this->compileSelect($sql);

		$row = $this->getRow($sql);

		if( $row && $this->numRows > 0 )
	    {
	       return reset($row);
	    }
	    return false;
	}



    /**
     * SQL指令安全过滤
	 *
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */	
	public function escape($str)
	{
		if ( is_array($str) )
		{
			foreach ($str as $key => $val)
	   		{
				$str[$key] = $this->escape($val);
	   		}
	   		return $str;
		}

		if ( function_exists('mysql_real_escape_string') AND is_resource($this->connect) )
		{
			$str = mysql_real_escape_string($str, $this->connect);
		}
		elseif ( function_exists('mysql_escape_string') )
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}

		return $str;
	}

	/**
	 * escape 字段名称
	 */
	public function escapeColumn($field)
	{
		if ( $field=='*' )
		{
			return $field;
		}
		
	    if ( preg_match('/(avg|count|sum|max|min)\(\s*(.*)\s*\)(\s*as\s*(.+)?)?/i', $field, $matches))
		{
		    if ( count($matches) == 3)
			{
				return $matches[1].'('.$this->escapeColumn($matches[2]).')';
			}
			else if ( count($matches) == 5)
			{
				return $matches[1].'('.$this->escapeColumn($matches[2]).') AS '.$this->escapeColumn($matches[4]);
			}
		}		

		if ( strpos($field,'.') !==false )
		{
			$field = $this->config('prefix').$field;

			$field = str_replace('.', '`.`', $field);
		}
		
		if ( stripos($field,' as ') !==false )
		{
			$field = str_replace(' as ', '` AS `', $field);
		}

		return '`'.$field.'`';
	}

	/**
	 * escape 数据表名称
	 */
	public function escapeTable($table)
	{
		$table = $this->config('prefix').$table;
		
		if ( stripos($table, ' AS ') !== FALSE )
		{
			$table = str_ireplace(' as ', ' AS ', $table);
			$table = array_map(array($this, __FUNCTION__), explode(' AS ', $table));
			return implode(' AS ', $table);
		}
		return '`'.str_replace('.', '`.`', $table).'`';
	}
		
	/**
	 * 返回数据库的版本
	 */
	public function version()
	{
	    if ( !is_resource($this->connect) )
        {
			$this->connect();
        }		
	    return mysql_get_server_info($this->connect);
	}

	/**
	 * 返回数据库的size
	 */
	public function size()
	{
		$tables = $this->tables();
		
		foreach($tables as $table)
		{
			$size  +=  $table['size'];
		}
		return format::byte($size);
	}

	/**
	 * 返回全部的数据表信息
	 */
	public function tables($status=false)
	{
		static $tables = array();
		
		if ( !empty($tables) && $status==false )
		{
			return $tables;
		};
		
		$results = $this->getAll('SHOW TABLE STATUS');
		
		foreach($results as $table)
		{
			$tables[$table['Name']] = array(
				'name' => $table['Name'],
				'size' => $table['Data_length'] + $table['Index_length'],
			    'datalength' => $table['Data_length'],
				'indexlength' => $table['Index_length'],
				'rows' => $table['Rows'],
				'engine' => $table['Engine'],
				'collation' => $table['Collation'],
				'createtime' => $table['Create_time'],
				'updatetime' => $table['Update_time'],
				'comment' => $table['Comment'],
			);
		}
		return $tables;
	}

	/**
	 * 实例化数据表
	 */
	public function table($tablename='')
	{
		$table = new database_mysql_table(&$this, $tablename);
		
		return $table;
	}	
	
}

class zotop_database_mysql_table
{
	protected $db = null; //表隶属于的db
	protected $name = ''; //表的名称
	protected $prefix = ''; //表的前缀名称prefix

	public function __construct(&$db , $name='' , $prefix='')
	{
		$this->db = $db;
		$this->name = empty($name) ? $this->name : $name ;
		$this->prefix = empty($prefix) ? $this->db->config('prefix') : $prefix ;
	}

	/**
	 * 表名称以#tablename开始将被自动加上前缀
	 * 
	 */	
	public function name($tableName='')
	{
		if(empty($tableName)){$tableName = $this->name;}
		if($tableName[0] == '#')
		{
			$tableName = $this->prefix . substr($tableName,1);
		}
		return $tableName;
	}
	
	/**
	 * 表是否存在
	 * 
	 */	
	public function exist()
	{
		if ( $tableName = $this->name() )
		{
			$tables = $this->db->tables();
			
			if ( in_array($tableName , array_keys($tables)) )
			{
				return true;
			}
		}
		return false;
	}
    
	/**
	 * 表名称是否有效
	 * 
	 */		
    public function isValidName($tableName='')
    {
        if(empty($tableName)){$tableName = $this->name;}
        
        if ($tableName !== trim($tableName))
		{
            return false;
        }
        
        if (! strlen($tableName))
		{
            return false;// zero length

        }
        
        if (preg_match('/[.\/\\\\]+/i', $tableName))
		{
            return false;  // illegal char . / \
        }
        return true;
    }
    
	/**
	 * 创建数据表,该函数会默认创建一个名为id的主键
	 * 
	 */	
	public function create($overwirte = false)
	{
		//如果已经存在，是否覆写
		if( $tablename = $this->name())
		{

			if($this->exist())
			{
				if($overwirte)
				{
					$this->drop();
				}
			}
			
			if( false !== $this->db->execute('CREATE TABLE `'.$tablename.'`( id int( 10 ) NOT NULL,PRIMARY KEY  (`id`)) ENGINE = MYISAM ;') )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 删除数据表
	 */	
	public function drop()
	{
		if ( $tablename = $this->name())
		{
			if( false !== $this->db->execute('DROP TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}	

	/**
	 * 重命名数据表
	 */	
	public function rename($newname)
	{
		$newname = ($newname[0]==='#') ? $this->prefix . substr($newname,1) : $newname;

		if ( $this->isValidName($newname) && $tablename = $this->name())
		{
			$tables = $this->db->tables();
			if ( !in_array($newname , $tables) )
			{

				if ( false !== $this->db->execute('RENAME TABLE `'.$tablename.'` TO `'.$newname.'`;') )
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 优化数据表
	 */
	public function optimize()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('OPTIMIZE TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 检查数据表
	 */
	public function check()
	{
		if ( $tablename = $this->name())
		{
			if( false !== $this->db->execute('CHECK TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 修复数据表
	 */
	public function repair()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('REPAIR TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 设置表的comment属性
	 *
	 */
	public function comment($comment)
	{
		if( $tablename = $this->name())
		{
			if ( false !== $this->db->execute('ALTER TABLE `'.$tablename.'` COMMENT=\''.$comment.'\'') )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 设置或者获取主键
	 * 
	 * @param $key 字段名称
	 */
	public function primaryKey($key='')
	{
		static $fields = array();
		
		if( empty($fields) )
		{
			$fields = $this->fields(true);
		}
		
		if ( empty($key) )
		{

			$indexes = $this->index();
			if ( isset($indexes['PRIMARY']) )
			{
				return $indexes['PRIMARY']['field'];
			}
			return false;
		}
		
		if( array_key_exists(strtolower($key) , array_change_key_case($fields)) )
		{
			if( $this->primaryKey() )
			{
				$sql = 'ALTER TABLE `'.$this->name().'` DROP PRIMARY KEY, ADD PRIMARY KEY ( `'.$key.'` )';
			}
			else
			{
				$sql = 'ALTER TABLE `'.$this->name().'` ADD PRIMARY KEY ( `'.$key.'` )';
			}
			if( false !== $this->db->execute($sql) )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 获取或者设置字段索性属性
	 * 
	 * @param $key 字段名称
	 * @param $action 索引类型
	 */
	public function index($key='', $action='INDEX')
	{
		if( empty($key) )
		{
			static $indexs = array();
			
			if(!empty($indexs)) return $indexs;
			
			$result = $this->db->getAll('SHOW INDEX FROM `'.$this->name().'`');
			
			if( $result )
			{
				foreach( $result as $index )
				{
					if ( $index['Index_type']=='FULLTEXT' )
					{
						$type = 'FULLTEXT';
					}
					elseif ( $index['Key_name'] == 'PRIMARY' )
					{
						$type = 'PRIMARY';
					}
					elseif ( $index['Non_unique'] == '0' )
					{
						$type = 'UNIQUE';
					}
					else
					{
						$type = 'INDEX';
					}

					$indexs[$index['Key_name']] = array(
						'name' => $index['Key_name'],
						'field' => $index['Column_name'],
						'unique' => ($index['Non_unique']==true)? 0 : 1,
						'index' => $index['Seq_in_index'],
						'type' => $type,
						'cardinality ' => $index['Cardinality '],
						'comment' => $index['Comment'],
					);
				}
			}
			return $indexs;
		}
		
		switch(strtoupper($action))
		{
			case 'INDEX':
				$sql = 'ALTER TABLE `'.$this->name().'` ADD INDEX `'.$key.'` ( `'.$key.'` )';
				break;
			case 'UNIQUE':
				$sql = 'ALTER TABLE `'.$this->name().'` ADD UNIQUE `'.$key.'` ( `'.$key.'` )';
				break;
			case 'FULLTEXT':
				$sql = 'ALTER TABLE `'.$this->name().'` ADD FULLTEXT `'.$key.'` ( `'.$key.'` )';
				break;
			case 'DROP':
				$sql = 'ALTER TABLE `'.$this->name().'` DROP INDEX `'.$key.'`';
				break;
		}
		
		if( !empty($sql) )
		{
			if( false !== $this->db->execute($sql) )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 获取字段信息
	 */
	public function fields($status = false)
	{
		static $fields = array();
		
		if ( !empty($fields) && $status== false )
		{
			return $fields;
		}
		
		$result = $this->db->getAll('SHOW FULL FIELDS FROM `'.$this->name().'`');
		
		//zotop::dump($result);
		foreach($result as $field)
		{
			$fields[$field['Field']] = array(
				'name' => $field['Field'],
				'type' => strpos($field['Type'], '(') ? chop(substr($field['Type'], 0, strpos($field['Type'], '('))) : $field['Type'],
				'length' => strpos($field['Type'], '(') ? chop(substr($field['Type'], (strpos($field['Type'], '(') + 1), (strpos($field['Type'], ')') - strpos($field['Type'], '(') - 1))) : '',
				'null' => $field['Null'],
				'key' => $field['Key'],
				'default' => $field['Default'],
				'collation' => $field['Collation'],
				'extra' => $field['Extra'],
				'comment' => $field['Comment'],
			);
		}
		
		return $fields;
	}

	/**
	 * 添加字段
	 */
	public function add($field)
	{
		$tablename = $this->name();

		$sql = 'ALTER TABLE `'.$tablename.'` ADD '.$this->specification($field);

		if ( false !== $this->db->execute($sql) )
		{
			return true;
		}
		return false;
	}

	/**
	 * 修改字段
	 */
	public function modify($field)
	{
		//ALTER TABLE `zotop_msg` MODIFY `title` INT( 10 ) AFTER `id`
		
		$tablename = $this->name();
		$fields = $this->fields(true);
		$data = $fields[$field['name']];
		
		if( !isset($data) ) return false;

		$field = array_merge($data,$field);

		$sql = 'ALTER TABLE `'.$tablename.'` MODIFY '.$this->specification($field);

		if ( false !== $this->db->execute($sql) )
		{
			return true;
		}
		return false;
	}

	/**
	 * 字段创建或者修改语句
	 */
	public function specification($field)
	{
	    if ( !is_array($field) ) return false;
	    
		$name = $field['name'];
		$type = $field['type'];
		$length = isset($field['length']) ? $field['length'] :'';
		$null = isset($field['null']) ? $field['null'] :'';
		$attribute = isset($field['attribute']) ? $field['attribute'] :'';
		$comment = isset($field['comment']) ? $field['comment'] :'';
		$position = (isset($field['position']) || !empty($field['position'])) ? $field['position'] : '0';
		$default = isset($field['default']) ? $field['default'] :'';
		$extra = isset($field['extra']) ? $field['extra'] :'';
		$collation = isset($field['collation']) ? $field['collation'] :'';

		//VARCHAR
		$sql = '`'.$name.'` '.$type;
		
		//VARCHAR(32)
		if( $length !='' && !preg_match('@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)$@i', $type) )
		{
			$sql .= '('.$length.')';
		}
		
		//VARCHAR(32) UNSIGNED
        if ( $attribute != '' )
        {
            $sql .= ' ' . $attribute;
        }
        
		//VARCHAR(32) UNSIGNED NOT NULL
		if ( $null !== false )
		{
            if (!empty($null))
            {
                $sql .= ' NOT NULL';
            }
            else
            {
                $sql .= ' NULL';
            }
        }
        
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value'
		if ( strtoupper($type) == 'TIMESTAMP' && strtoupper($default) == 'NOW' )
		{
			$sql .= ' DEFAULT CURRENT_TIMESTAMP';
		}
		elseif( $extra !== 'AUTO_INCREMENT' && strlen($default)>0 )
		{
            if (strtoupper($default) == 'NULL') 
            {
                $sql .= ' DEFAULT NULL';
            } 
            else
            {
                if (strlen($default))
                {
                    $sql .= ' DEFAULT \'' .$default . '\'';
                }
            }
		}
		
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value' COMMENT 'ddddd'
        if (!empty($comment)) 
        {
            $sql .= " COMMENT '" . $comment . "'";
        }
        
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value' COMMENT 'ddddd' AFTER `id`
		if ( !empty($position) )
		{
			if($position == '-1')
			{
				$sql .=' FIRST';
			}
			else
			{
				$sql .= ' AFTER `'.$position.'`';
			}
		}
		
		return $sql;
	}

	/**
	 * 实例化字段
	 * 
	 * @param $fieldname 字段名称
	 */
	public function field($fieldname)
	{
		$field = new database_mysql_table_field(&$this->db , $this->name(), $fieldname );
		return $field;
	}
}

class zotop_database_mysql_table_field
{
	public $db = null; //表隶属于的db
	public $tablename = ''; //表的名称
	public $fieldname =''; //字段名称

	public function __construct(&$db , $tablename , $fieldname)
	{
		$this->db = $db;
		$this->tablename = $tablename ;
		$this->fieldname = $fieldname ;
	}

	/**
	 * 字段重命名
	 */
	public function rename($newname)
	{
		$tablename = $this->tablename;
		$fieldname = $this->fieldname;
		$fields = $this->db->table($tablename)->fields();
		
		$field = $fields[$fieldname];
		$field['name'] =  $newname;

		$s = $this->db->table($tablename)->specification($field);

		if ( $newname !== $fieldname )
		{
			$sql = 'ALTER TABLE `'.$tablename.'` CHANGE `'.$fieldname.'` '.$s;
			
			if ( false !== $this->db->execute($sql) )
			{
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * 删除字段
	 */
	public function drop()
	{
		$tablename = $this->tablename;
		$fieldname = $this->fieldname;
		if ( false !== $this->db->execute('ALTER TABLE `'.$tablename.'` DROP `'.$fieldname.'`') )
		{
			return true;
		}
		return false;
	}
}
class router extends zotop_router
{
    
}

class controller extends zotop_controller
{
    public $page;
    public $user;
    
    public function __init()
    {
        $this->user = zotop::user();
    }
    
    public function __check()
    {                
        if( empty($this->user) )
        {
            zotop::redirect('zotop/login');
        }        
    }

    public function __before()
    {

    }
    
    public function __after()
    {
       
    }
    

    public function navbar()
    {
        
    }
    

}

class dialog extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'dialog'));
        $this->addScript('$common/js/zotop.dialog.js');
        parent::header();
    }

	public function top()
	{}

	public function bottom()
	{}

	public function footer()
	{
		 parent::footer();
	}
}

class page extends zotop_page
{
	public function header()
	{
        $javascript = (array)$this->js;
        $css = (array)$this->css;
        $metas = (array)$this->meta;
        
		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$this->title.' '. zotop::config("zotop.title").'</title>';
        $html[] = '	'.html::meta('keywords',$this->keywords.' '.zotop::config("zotop.keywords"));
        $html[] = '	'.html::meta('description',$this->description.' '.zotop::config("zotop.description"));
        $html[] = '	'.html::meta('Content-Type','text/html;charset=utf-8');
        $html[] = '	'.html::meta('X-UA-Compatible','IE=EmulateIE7');
	    foreach($metas as $meta)
		{
		    $html[] = '	'.html::meta($meta);
		}        
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/global.css',array('id'=>'global'));
	    foreach($css as $stylesheet)
		{
		    $html[] = '	'.html::stylesheet($stylesheet);
		}			
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/jquery.js',array('id'=>'jquery'));
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/jquery.plugins.js',array('id'=>'plugins'));
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/zotop.js',array('id'=>'zotop'));
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/global.js',array('id'=>'global'));
		foreach($javascript as $js)
		{
		    $html[] = '	'.html::script($js);
		}	
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';
        $html[] = '<div id="wrapper">';
        $html[] = '<div id="page">';
        $html[] = '';
        
		echo implode("\n",$html);	    
	}
	
	public function footer()
	{
	    $html[] = '';
        $html[]	= '</div>';
	    $html[]	= '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop" class="clearfix">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>';
		$html[] = '	<span id="page-title">'.$this->title.'</span>';
		
		
		if( !empty($this->data['position']) )
		{
		$html[] = '	<span id="page-position"><span>当前位置：</span>'.$this->data['position'].'</span>';
		}
		$html[] = '</h2>';
		$html[] = '<h3><a id="favorate" href="'.zotop::url('zotop/favorate/add').'" class="button dialog" title="将该页面加入我的收藏夹"><span class="button-icon zotop-icon zotop-icon-favorate"></span><span class="button-text">加入收藏</span></a></h3>';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public function bottom($str='')
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		if(!empty($str))
		{
			$html[] = '<div id="bottom" class="clearfix">'.$str.'</div>';
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{#runtime}</b>,memory:<b>{#memory}</b>,includefiles:<b>{#include}</b>,queries:<b>{#queries}</b></div>';
		$html[] = '</div>';

		

		echo implode("\n",$html);
	}

	public function navbar($data='',$current='')
	{
		$html = array();
		
        if( !is_array($data) )
        {
            $data = $this->data['navbar'];
        }
        
		if(is_array($data))
		{
            $current=empty($current) ? $this->data['navbar.current'] : $current;
			$current=empty($current) ? application::action() : $current;
			$current=empty($current) ? $data[0]['id'] : $current;
            $html[] = '';
			$html[] = '<div class="navbar">';
			$html[] = '	<ul>';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id']) ? 'current' : (empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$item['class'].' '.$class.'"><a href="'.$href.'"  id="'.$item['id'].'"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[] = '		<li class="'.$item['class'].' '.$class.'">'.$item.'</li>';
				}
			}
			$html[] = '	</ul>';
			$html[] = '</div>';
		}
		echo implode("\n",$html);
	}
	
	public function mainHeader()
	{
		echo '<div id="main">';
		echo '<div id="main-inner">';
	}

	public function mainFooter()
	{
		echo '</div>';
		echo '</div>';
	}

	public function sideHeader()
	{
		echo '<div id="side">';
		echo '<div id="side-inner">';
	}

	public function sideFooter()
	{
		echo '</div>';
		echo '</div>';	
	}
}

class form extends zotop_form
{

}

class side extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'side'));
        $this->addScript('$common/js/zotop.side.js');
        parent::header();
    }
    
	public function navlist($data,$current='')
	{
        $html = array();        
		if(is_array($data))
		{
            $html[] = '';
			$html[] = '	<ul class="list">';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$item['class'].' '.$class.'"><a href="'.$href.'"  id="'.$item['id'].'" target="mainIframe"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[] = '		<li class="'.$item['class'].' '.$class.'">'.$item.'</li>';
				}
			}
			$html[] = '	</ul>';
		}
		return implode("\n",$html);        
	}
	
}

class url extends zotop_url
{

}

?>