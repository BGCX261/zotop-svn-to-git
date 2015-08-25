<?php
abstract class BaseDatabase extends base
{
    protected $config		= array(); //数据库配置
    protected $link			= null;	//当期数据库链接
    protected $connected	= false;//连接标志位
	protected $sql			= '';	//查询语句
	protected $query		= null;	//查询对象
    protected $numRows		= 0;	// 返回或者影响记录数
    protected $resultSet	= null;	//当前查询的结果数据集
    protected $options		= array(); // 查询表达式参数

    /**
     * 获取数据库的唯一实例
     *
     * database::instance($config);
     *
     * $config 数据库相关配置
     *
     * @return object
     */
    public static function &instance()
    {
        $config = func_get_args();
        return zotop::instance('database','factory',$config);
    }

    /**
     * 生成数据库唯一实例
     *
     * @param $config
     * @return unknown_type
     */
    public function &factory($config='')
    {
       if( is_string($config) )
       {
           $config = $this->parseDNS($config);
       }
       if( empty($config['driver']) )
       {
           zotop::(-1,'There is some error in database config');
       }
       $driver = 'DataBase_'.ucfirst(strtolower($config['driver']));
       if(!zotop::autoload($driver))
       {
          zotop::error(-1,'The database driver ('.$driver.') does not support');
       }
       $db = new $driver($config);
       return $db;
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

    public function connect($test = false)
    {
        zotop::error(1,'function (connect) need to be rewrited');
    }

    //魔术方法，实现一些函数
    public function __call($method,$args)
    {
        if(in_array(strtolower($method),array('from','field','where','order','limit')))
        {
            $this -> options[strtolower($method)] = $args[0];
            return $this;
        }
    }

    public function select($options = array())
    {
        $options = $this->options($options);

    }

    public function options($options=array())
    {
        if(is_array($options))
        {
            $options =  array_merge($this->options,$options);
        }
        else
        {
            $options = $this->options;
        }
        $this->options=array();
        return $options;
    }


    /**
     * 对表名称，字段名称进行安全处理,需要
     *
     * @return string
     */
    public function escape($str)
    {
        return $str;
    }


    public function parseTable($tables)
    {
        $array = array();
        if(is_string($tables))
        {
            $tables = explode(',',$tables);
        }
        foreach($tables as $key=>$table)
        {
            if(is_numeric($key))
            {
                $array[]=$this->escape($table);
            }
            else
            {
                $array[]=$this->escape($key).' AS '.$this->escape($table);
            }
        }
        return implode(',',$array);
    }

    public function parseField($fields)
    {
          if(empty($fields))
          {
              return '*';
          }
          if(is_string($fields))
          {
              return $this->escape($fields);
          }
          if(is_array($fields))
          {
              $array = array();
              foreach($fields as $key=>$filed)
              {
                  if(is_numeric($key))
                  {
                      $array[]=$this->escape($filed);
                  }
                  else
                  {
                      $array[]=$this->escape($key).' AS '.$this->escape($filed);
                  }
              }
              return implode(',',$array);
          }
          return '*';//这儿可能有些问题，待修正
    }

}
?>