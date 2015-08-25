<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数据库操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
abstract class database_base
{
    protected $config = array(); //数据库配置
    protected $connect = null; //当期数据库链接
    protected $sql = array(); //查询语句容器
    protected $sqlBuilder = array(); //查询语句构建容器
    protected $query = null; //查询对象
    protected $numRows = 0; //影响的数据条数	    
    protected $insertID		= null;// 最后插入ID
    protected $selectSql	= 'SELECT%DISTINCT% %FIELDS% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT%';
	protected $datatypes	= array();

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

	public function datatypes()
	{
		return $this->datatypes;
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

	public function join($table, $key, $value, $type='')
	{
		
		if ( ! empty($type))
		{
			$type = strtoupper(trim($type));

			if ( ! in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE))
			{
				$type = '';
			}
			else
			{
				$type .= ' ';
			}
		}

		$join = array();

		$join['table'] = $table;
		$join['key'] = $key;
		$join['value'] = $value;
		$join['type'] = $type;

		$this->sqlBuilder['join'][] = $join;

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
		//清空全部的where条件
		if ( $key === null )
		{
			$this->sqlBuilder['where'] = array();
			return $this;
		}
		
		//添加条件
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
    public function parseJoin($joins)
    {
        $str = '';

		if ( is_array($joins) )
		{
			foreach( $joins as $join )
			{
				if ( is_array($join) )
				{
					$str .= ' '.$join['type'].'JOIN '. $this->escapeTable($join['table']) .' ON '. $this->escapeColumn($join['key']) .' = '. $this->escapeColumn($join['value']);
				}
			}
		}

		return $str;
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
				$where[1] = trim(strtoupper($where[1]));
				switch($where[1])
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
					case 'BETWEEN':
					case 'IN':
					case 'NOT IN':
						if ( is_array($where[2]) )
						{
							$escaped = array();	
							
							foreach( $where[2] as $v )
							{
								if (is_numeric($v))
								{
									$escaped[] = $v;
								}
								else
								{
									$escaped[] = $this->escapeValue($v);
								}
							}

							$where[2] = implode(",", $escaped);
						}
						return $this->escapeColumn($where[0]).' '.$where[1].' ('.$where[2].')';
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

        $this->execute($sql);

        //返回查询结果
        return $this->insertID();
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
		if( is_array($where) && !empty($where) )
		{
			$count = $this->select('count(*) as num')->where(null)->where($where)->orderby(null)->getOne();
		}
		else
		{
			$count = $this->select('count(*) as num')->orderby(null)->getOne();
		}

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