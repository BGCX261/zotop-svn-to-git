<?php
abstract class zotop_database
{
    protected $config		= array(); //数据库配置
    protected $link			= null;	//当期数据库链接
    protected $connected	= false;//连接标志位
	protected $sql			= array();	//查询语句
	protected $lastSql		= '';	//最后的sql语句
	protected $query		= null;	//查询对象
    protected $numRows		= 0;	// 返回或者影响记录数
    protected $resultSet	= null;	//当前查询的结果数据集
	protected $selectSql	= 'SELECT%DISTINCT% %FIELDS% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT%';

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
        $args = func_get_args();
        return zotop::instance('database','factory',$args);
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
           zotop::error(-1,'there is some error in database config');
       }
       $driver = 'Zotop_DataBase_'.ucfirst(strtolower($config['driver']));
       if(!zotop::autoload($driver))
       {
          zotop::error(-1,'the database driver ('.$driver.') does not support');
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

    public function connect()
    {
        die('ERROR : function (connect) need to be rewrited');
    }

    public function config($key='' , $value=null)
    {
        if( empty($key) )
        {
            return $this->config;
        }
        if( is_array($key) )
        {
            $this->config = array_merge($this->config , $key);
            return $this->config;
        }
        if( isset($value) )
        {
            $this->config[$key] = $value;
            return $this->config;
        }
        $config = $this->config[$key];
        if( isset($config) )
        {
            return $config;
        }
        return false;
    }

    public function parseSql($sql)
    {
        $this->lastSql = $sql;
		return $sql;
    }

    public function lastSql()
    {
        return $this->lastSql;
    }


	public function select($fields = '*')
	{
		if( func_num_args()>1 )
		{
			$fields = func_get_args(); //select('id','username','password')
		}
		elseif( is_string($fields) )
		{
			$fields = explode(',', $fields); //select('id,username,password')
		}else
		{
			$fields = (array)$fields; //select(array('id','title'))
		}

		$this->sql['select'] = $fields;

		return $this;
	}

	public function from($tables)
	{
		if(  func_num_args()>1 )
		{
			$tables = func_get_args(); //from('user','config')
		}
		elseif( is_string($tables) )
		{
			$tables = explode(',', $tables); //from('user,config')
		}else
		{
			$tables = (array)$tables; //from(array('user','config'))
		}


		$this->sql['from'] = $tables;

		return $this;
	}

	/* 设置where
	 * where('id',1)，where('id','<',1)，where(array('id','<',1),'and',array('id','>',1))，where(array('id','like',1),'and',array(array('id','>',1),'or',array('id','>',1)))
	 *
	 */

	public function where($key,$value=null)
	{
		if( !is_array($key) )
		{
			$where = func_get_args();
			switch( count($where) )
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
			if( func_num_args()==1 )
			{
				$where = $key;
			}
			else
			{
				$where = func_get_args(); //where(array('id','=','1'),'and',array('status','>','0'))
			}

		}
		if( count($this->sql['where']) >0 )
		{
			$this->sql['where'][] = 'AND';
		}
		$this->sql['where'][] = $where;

		return $this;
	}

	public function limit($limit, $offset = 0)
	{
		$this->sql['limit']  = (int) $limit;

		if( $offset !== NULL || !is_int($this->sql['offset']) )
		{
			$this->sql['offset'] = (int) $offset;
		}

		return $this;
	}

	public function offset($value)
	{
		$this->sql['offset'] = (int) $value;

		return $this;
	}

	public function orderby($orderby, $direction=null)
	{
		if( !is_array($orderby) )
		{
			$orderby = array($orderby => $direction);

		}
		$this->sql['orderby'] = array_merge((array)$this->sql['orderby'], $orderby);
		return $this;
	}

	public function compileSelect($sql)
	{
	    $str = '';
		if( empty($sql) )
		{
			$sql = $this->sql;
		}
		elseif( is_array($sql) )
		{
			$sql = array_merge($this->sql , $sql);
		}
		else
		{
			$str = $sql;
		}

		if( is_array($sql) )
        {
			$distinct = ($sql['distinct'] == TRUE) ? 'DISTINCT' : '';
			$field = $this->parseSelect($sql['select']);
			$table = $this->parseFrom($sql['from']);
			$where = $this->parseWhere($sql['where']);
			$where = empty($where) ? '': ' WHERE '.$where;
			$order = $this->parseOrderby($sql['orderby']);
			$order = empty($order) ? '': ' ORDER BY '.$order;
			$limit = ' LIMIT '.$this->parseLimit($sql['limit'],$sql['offset']);





			$str   = str_replace(
					array('%TABLE%','%DISTINCT%','%FIELDS%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%'),
					array(
						$table,
						$distinct,
						$field,
						$join,
						$where,
						$group,
						$having,
						$order,
						$limit
					),
					$this->selectSql
				);

        }
        return $str;
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
				$value = (int) $value;
			break;
			case 'double':
				$value = sprintf('%F', $value);
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
	 * 解析字段
	 *
	 *
	*/

    public function parseSelect($fields)
    {
          if( !empty($fields) )
          {
              if(is_string($fields))
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

    public function parseFrom($tables)
    {
        $array = array();
        if(is_string($tables))
        {
            $tables = explode(',',$tables);
        }
        foreach($tables as $key=>$table)
        {
			$array[] = $this->escapeTable($table);
        }
        return implode(',',$array);
    }


	/* 解析where
	 * array('id','<',1) 或者array(array('id','<',1),'and',array('id','>',1))，或者 array(array('id','like',1),'and',array(array('id','>',1),'or',array('id','>',1)))
	 *
	 */
    public function parseWhere($where)
    {

        if( !empty($where) )
        {
			//zotop::dump($where);
            if( is_string($where[0]) && count($where)==3 )
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
			for( $i=0,$j=count($where); $i<$j ; $i++ )
			{
				if( is_array($where[$i][0]) )
				{
					$str .= '('.$this->parseWhere($where[$i]).')';
				}
				elseif( is_array($where[$i]) )
				{
					$str .= $this->parseWhere($where[$i]);
				}
				elseif( is_string($where[$i]) )
				{
					$str .= ' '.strtoupper(trim($where[$i])).' ';
				}

			}
        }
        return $str;
    }

	public function parseOrderby($orderby)
	{
		$str = '';
		if( is_array($orderby) )
		{
			zotop::dump($orderby);

			foreach( $orderby as $key=>$direction )
			{
				$direction = strtoupper(trim($direction));
				if ( !in_array($direction, array('ASC', 'DESC', 'RAND()', 'RANDOM()', 'NULL')) )
				{
					$direction = 'ASC';
				}
				$str .= ','.$this->escapeColumn($key).' '.$direction;
			}
		}
		return trim($str,',');
	}

    public function parseLimit($limit, $offset=null)
	{
		$str = '';

		if( is_int($offset) )
		{
			$str .= $offset;
		}
		if( is_int($limit) )
		{
			$str .= ','.$limit;
		}
		return $str;
    }

}
?>