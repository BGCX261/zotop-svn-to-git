<?php
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
        if( !isset($instances[$id]) )
        {
            
            if( is_string($config) )
            {
                 $config = $this->parseDNS($config);
            }
            if( empty($config['driver']) )
            {
               zotop::error(-1,zotop::t('错误的数据库配置文件',$config));
            }
            //数据库驱动程序
            $driver = 'database_'.strtolower($config['driver']);
            
            //加载驱动程序
            if( !zotop::autoload($driver) )
            {
              zotop::error(-1,zotop::t('未能找到数据库驱动 "{$driver}"',$config));
            }
            
            //取得驱动实例
            $instance	= new $driver($config);
            
            $instances[$id] = &$instance;            
        }
        
        return $instances[$id];
    }
    
    public function connect()
    {
        die('ERROR : function (connect) need to be rewrited');
    }
    
    public function query()
    {
        die('ERROR : function (query) need to be rewrited');
    }
    
    public function insert($table='',$data='')
    {
        $sqlBuilder = (array)$this->sqlBuilder;          
       
        if( is_array($data) )
        {
             $sqlBuilder['set'] = array_merge($sqlBuilder['set'],$data);
        }
        if( !empty($table) && is_string($table))
        {
            $sqlBuilder['from'] = $table;
        }               
        
        $this->sqlBuilder = array();    
        
        $data = $sqlBuilder['set'];
        
        if ( !is_array($data) ) return false;
        foreach( $data as $field=>$value )
        {
            $fields[] = $this->escapeColumn($field);
            $values[] = $this->escapeValue($value);
        }
        
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
        
        return $this->execute($sql);
    }
    
    
    public function update($table='',$data='',$where='')
    {
        $sqlBuilder = (array)$this->sqlBuilder;          
        
        if( is_array($where) )
        {
            $sqlBuilder['where'] = array_merge($sqlBuilder['where'],$where);
        }
        if( is_array($data) )
        {
             $sqlBuilder['set'] = array_merge($sqlBuilder['set'],$data);
        }
        if( !empty($table) && is_string($table))
        {
            $sqlBuilder['from'] = $table;
        }
                
        if( empty($sqlBuilder['where']) )
        {
            return false;
        }
               
        $this->sqlBuilder = array();    
            
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
        
        return $this->execute($sql);
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

		$this->sqlBuilder['select'] = $fields;

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
		$this->sqlBuilder['from'] = $tables;
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
		
		if( !empty($where) && !empty($where[0]))
		{
    		if( count($this->sqlBuilder['where']) >0 )
    		{
    			$this->sqlBuilder['where'][] = 'AND';
    		}
    		$this->sqlBuilder['where'][] = $where;
		}
		return $this;
	}
	
	public function limit($limit, $offset = 0)
	{
		$this->sqlBuilder['limit']  = (int) $limit;

		if( $offset !== NULL || !is_int($this->sqlBuilder['offset']) )
		{
			$this->sqlBuilder['offset'] = (int) $offset;
		}

		return $this;
	}

	public function offset($value)
	{
		$this->sqlBuilder['offset'] = (int) $value;

		return $this;
	}

	public function orderby($orderby, $direction=null)
	{
		if( !is_array($orderby) )
		{
			$orderby = array($orderby => $direction);

		}
		$this->sqlBuilder['orderby'] = array_merge((array)$this->sqlBuilder['orderby'], $orderby);
		return $this;
	}
	
	public function set($name, $value='')
	{
	    if( is_string($name) )
	    {
	        $data = array($name=>$value);
	    }
	    $data = (array)$name;
		$this->sqlBuilder['set'] = array_merge((array)$this->sqlBuilder['set'], $data);
		return $this;	    
	}
	
    public function compileSelect($sql)
    {
        $str = '';
        if( empty($sql) )
		{
			$sql = $this->sqlBuilder;
		}
		elseif( is_array($sql) )
		{
			$sql = array_merge($this->sqlBuilder , $sql);
		}
		else
		{
			$str = $sql;
		}
		
        $this->sqlBuilder = array();
        
		if( is_array($sql) )
        {
 			$str = str_replace(
				array('%TABLE%','%DISTINCT%','%FIELDS%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%'),
				array(
					$this->parseFrom($sql['from']),
					$this->parseDistinct($sql['distinct']),
					$this->parseSelect($sql['select']),
					$this->parseJoin($sql['join']),
					$this->parseWhere($sql['where']),
					$this->parseGroupBy($sql['groupby']),
					$this->parseHaving($sql['having']),
					$this->parseOrderBy($sql['orderby']),
					$this->parseLimit($sql['limit'],$sql['offset']),
				),
				$this->selectSql
			);           
        }
        return $str;        
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
    
    public function parseSQL($sql)
    {
        $this->sql[] = $sql;
        return $sql;
    }
    
    /**
    * from 分析
    * @access protected
    * @param mixed $distinct
    * @return string
    */   
    protected function parseFrom($tables)
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
    
    /**
     * distinct分析
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct) {
        return empty($distinct) ? '' : ' DISTINCT ';
    }
    
    /**
     * select 分析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseSelect($fields)
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
    
    /**
     * join 分析
     * @access protected
     * @param mixed $join
     * @return string
     */
    protected function parseJoin($join) {
        return '';
    }
        
    /**
     * where 分析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseWhere($where)
    {
        $str = '';
        if( is_array($where) )
        {
            $str = $this->parseWhereArray($where);
        }
        else
        {
            $str = $where;
        }
        return empty($str) ? '' : ' WHERE '.$str;
    }

    protected function parseWhereArray($where)
    {
        if( !empty($where) )
        {
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
			for( $i=0,$j=count($where); $i<$j ; $i++ )
			{
				if( is_array($where[$i][0]) )
				{
					$str .= '('.$this->parseWhereArray($where[$i]).')';
				}
				elseif( is_array($where[$i]) )
				{
					$str .= $this->parseWhereArray($where[$i]);
				}
				elseif( is_string($where[$i]) )
				{
					$str .= ' '.strtoupper(trim($where[$i])).' ';
				}

			}
        }
        return $str;        
    }
    
    protected function parseGroupBy($group)
    {
        return empty($group) ? '' : ' GROUP BY '.$group;
    }
    
    protected function parseHaving($having)
    {
        return empty($having)? '' : ' HAVING '.$having;
    }
    
	public function parseOrderby($orderby)
	{
		$str = '';
		if( is_array($orderby) )
		{
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
		$str = trim($str,',');
		return empty($str) ? '' : ' ORDER BY '.$str;
	}    
 
    protected function parseLimit($limit, $offset=0)
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
    
    protected function parseSet($data)
    {
        $str = '';
        foreach($data as $key=>$val)
        {
                
            //解析值中的如：num = num + 1 或者 num = num - 1          
            if( preg_match('/^([a-zA-Z0-9_]+){1,32}(\s*)(\+|\-|\*|\%)(\s*)([0-9]){1,6}$/i',$val,$matches) )
            {
               $str .= ','.$this->escapeColumn($key).'='.$this->escapeColumn($matches[1]).$matches[3].(int)$matches[5]; 
            }
            else
            {
               $str .= ','.$this->escapeColumn($key).'='.$this->escapeValue($val);
            }
        }
        $str = trim($str,',');
        
        return empty($str) ? '' : ' SET '.$str;
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
        if( is_array($this->sql) )
        {
            return end($this->sql);
        }
        return '';
    }
    
    /**
    * sql语句构建
    *
    * @return array
    */      
    public function sqlBuilder($key='', $value=null)
    {
        if( empty($key) )
        {        
            return $this->sqlBuilder;
        }
        if( is_array($key) )
        {
            $this->sqlBuilder = array_merge($this->sqlBuilder , $key);
            return $this->sqlBuilder;       
        }
        if( isset($value) )
        {
            $this->sqlBuilder[$key] = $value;
            return $this->sqlBuilder;
        }
        $sqlBuilder = $this->sqlBuilder[$key];
        if( isset($sqlBuilder) )
        {
            return $sqlBuilder;
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

	public function size()
	{
		return 'Unknow!';
	}
	    
	public function version()
	{
		return 'Unknow!';
	}
	       
}
?>