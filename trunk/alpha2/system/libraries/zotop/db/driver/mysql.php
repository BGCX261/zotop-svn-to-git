<?php
class zotop_dataBase_mysql extends zotop_database implements zotop_database_interface
{
    public function __construct($config = array())
    {
         $default = array(
			 'driver'=>'mysql',
			 'username'=>'root',
			 'password'=>'',
			 'hostname'=>'localhost',
			 'hostport'=>'3306',
			 'database'=>'zotop',
			 'charset'=>'utf8',
			 'pconnect'=>false,
			 'autocreate'=>false
		 );
		 $this->config = array_merge( $default , $this->config , $config);
    }

	public function __destruct()
	{
		is_resource($this->link) and mysql_close($this->link);
	}

    public function connect()
    {
        if(is_resource($this->link))
        {
            return $this->link;
        }
        $connect = ( $this->config['pconnect'] == TRUE ) ? 'mysql_pconnect' : 'mysql_connect';

        $host = $this->config['hostname'];
        $host = empty($this->config['hostport']) ? $host : $host.':'.$this->config['hostport'];
        $user = $this->config['username'];
        $pass = $this->config['password'];
        $database = $this->config['database'];


        if($this->link = @$connect($host,$user,$pass,true))
        {

			if(@mysql_select_db($database , $this->link))
            {

				$version = $this->version();
				if ($version >= '4.1' && $charset = $this->config['charset'] )
    			{
					@mysql_query("SET NAMES '".$charset."'" , $this->link);//使用UTF8存取数据库 需要mysql 4.1.0以上支持
    			}
				if($version >'5.0.1'){
					@mysql_query("SET sql_mode=''",$this->link);//设置 sql_model
				}
                return true;
            }
            else
            {
				if( $this->config('autocreate') )
				{
					return $this->create();
				}
				zotop::error(-2,zotop::t('Unknown database `{$database}` {$autocreate}',$this->config));
            }
        }
        else
        {
			zotop::error(-1,'Could not connect to database server ，'.mysql_error());
        }
        return false;
    }

	public function free()
	{
		@mysql_free_result($this->query);
        $this->query = 0;
	}

	/**
	 * 执行一个sql语句 query，相当于包装后的mysql_query
	 *
	 * @param $sql
	 * @param $silent
	 * @return unknown_type
	 */
	public function query($sql,$silent=false)
    {

        if( !is_resource($this->link) )
        {
			$this->connect();
        }
		if( $sql = $this->parseSql($sql) )
		{
			//echo $this->sql;
			if ( $this->query ) {$this->free();} //释放前次的查询结果
			$this->query = @mysql_query($sql , $this->link );//查询数据
			if($this->query === false)
			{
				if($silent) return false;
				zotop::error( -3 , '查询语句错误' ,zotop::t('<h2>SQL: {$sql}</h2>{$mysqlerror}',array('sql'=>$sql,'mysqlerror'=>mysql_error())));
			}
			$this->numRows = mysql_num_rows($this->query);
			return $this->query;
		}
		return false;
    }

	/**
	 * 执行一个sql语句，并返回影响的数据行数
	 *
	 * @param $sql
	 * @param $silent
	 * @return bool||number
	 */
	public function execute($sql,$silent=false)
	{
		if($result = $this->query($sql,$silent))
		{
			if($result === false)
			{
				return false;
			}
			$this->numRows = mysql_affected_rows($this->link);
			return $this->numRows;
		}
		return false;
	}

    public function parseLimit($limit, $offset=0)
	{
		$str = '';

		if( is_int($offset) )
		{
			$str .= (int)$offset.',';
		}
		$str .= $limit;
		return $str;
    }





	/**
	 * 执行一个sql语句并返回结果数组
	 *
	 * @param $sql
	 * @return array
	 */
	public function getAll($sql)
	{
		$sql = $this->compileSelect($sql);

		if($query = $this->query($sql))
		{
			$result = array();
			if($this->numRows >0) {
				while($row = mysql_fetch_assoc($query)){
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
	public function getRow($sql)
	{
		$sql = $this->compileSelect($sql);
		if($query = $this->query($sql))
		{
			$row = mysql_fetch_assoc($query);
			if($row) {
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
	public function getOne($sql)
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
	 * 返回limit限制的数据,用于带分页的查询数据
	 *
	 * @param $sql
	 * @return mixed
	 */
	public function getRange($sql,$from=0,$count=10)
	{

	}

	public function create()
	{
		$database = $this->config('database');
		$charset = $this->config('charset');
		if($create = $this->query("CREATE DATABASE IF NOT EXISTS ".$database." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
		"))
		{
			if(!$create)
			{
				zotop::error(-20,'Could not connect to database server ('.$this->config('hostname').')');
			}
		}
		return true;
	}

	public function table($tablename='')
	{
		$table = new zotop_dataBase_mysql_table(&$this , $tablename );
		$table -> prefix = $this->config('prefix');
		return $table;
	}

	public function tables($status=false)
	{
		static $tables = array();
		if( !empty($tables) && $status==false )
		{
			return $tables;
		};
		$results = $this->getAll('SHOW TABLE STATUS');
		foreach($results as $table)
		{
			$tables[$table['Name']] = array(
				'name' => $table['Name'],
				'size' => $table['Data_length'] + $table['Index_length'],
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

	public function escape($str)
	{
		is_resource($this->link) or $this->connect();
		return mysql_real_escape_string($str, $this->link);
	}

	public function escapeColumn($field)
	{
		if( $field=='*' )
		{
			return $field;
		}

		if( strpos($field,'.') !==false )
		{
			$field = $this->config('prefix').$field;

			$field = str_replace('.', '`.`', $field);
		}
		if( stripos($field,' as ') !==false )
		{
			$field = str_replace(' as ', '` AS `', $field);
		}

		return '`'.$field.'`';
	}

	public function escapeTable($table)
	{
		$table = $this->config('prefix').$table;
		if (stripos($table, ' AS ') !== FALSE)
		{
			$table = str_ireplace(' as ', ' AS ', $table);
			$table = array_map(array($this, __FUNCTION__), explode(' AS ', $table));
			return implode(' AS ', $table);
		}
		return '`'.str_replace('.', '`.`', $table).'`';
	}


    public function insertId()
    {
        return mysql_insert_id($this->link);
    }

    /**
	 *
	 *
     * @return number || string 返回当前数据库的版本号
     */
    public function version($complete = false)
    {

        if( !is_resource($this->link) )
        {
			$this->connect();
        }
		$version = mysql_get_server_info($this->link);
		if(!$complete)
		{
			$v = explode('.' , $version);
			$version = $v[0].'.'.$v[1].'.'.(int)$v[2];
		}
		return $version;
    }

	public function size()
	{
		$tables = $this->tables();
		foreach($tables as $table)
		{
			$size  +=  $table['size'];
		}
		return format::byte($size);
	}
}

//mysql对应的表操作
class zotop_database_mysql_table
{
	public $db = null; //表隶属于的db
	public $name = ''; //表的名称
	public $prefix = ''; //表的前缀名称prefix

	public function __construct(&$db , $name='' , $prefix='')
	{
		$this->db = $db;
		$this->name = empty($name) ? $this->name : $name ;
		$this->prefix = empty($prefix) ? $this->prefix : $prefix ;
	}

	//表名称以#tablename开始将被自动加上前缀
	public function name($tablename='')
	{
		if(empty($tablename)){$tablename = $this->name;}
		if($tablename[0] == '#')
		{
			$tablename = $this->prefix . substr($tablename,1);
		}
		return $tablename;
	}

	public function exist()
	{
		if($tablename = $this->name())
		{
			$tables = $this->db->tables();
			if(in_array($tablename , array_keys($tables)))
			{
				return true;
			}
		}
		return false;
	}

    public function isValidName($tablename)
    {
        if ($tablename !== trim($tablename))
		{
            return false;
        }
        if (! strlen($tablename))
		{
            return false;// zero length

        }
        if (preg_match('/[.\/\\\\]+/i', $tablename))
		{
            return false;  // illegal char . / \
        }
        return true;
    }

	//创建表功能，应该使用field的创建功能自动创建表
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

	public function drop()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('DROP TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

	public function rename($newname)
	{
		$newname = ($newname[0]==='#') ? $this->prefix . substr($newname,1) : $newname;

		if( $this->isValidName($newname) && $tablename = $this->name())
		{
			$tables = $this->db->tables();
			if( !in_array($newname , $tables) )
			{

				if( false !== $this->db->execute('RENAME TABLE `'.$tablename.'` TO `'.$newname.'`;') )
				{
					return true;
				}
			}
		}
		return false;
	}

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

	public function check()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('CHECK TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

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

	public function comment($comment)
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('ALTER TABLE `'.$tablename.'` COMMENT=\''.$comment.'\'') )
			{
				return true;
			}
		}
		return false;
	}

	public function primary($key='')
	{
		static $fields = array();
		if( empty($fields) )
		{
			$fields = $this->fields(true);
		}
		if(empty($key))
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
			if( $this->primary() )
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
					if ($index['Index_type']=='FULLTEXT')
					{
						$type = 'FULLTEXT';
					}
					elseif ($index['Key_name'] == 'PRIMARY')
					{
						$type = 'PRIMARY';
					}
					elseif ($index['Non_unique'] == '0')
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

	public function fields($status = false)
	{
		static $fields = array();
		if( !empty($fields) && $status== false )
		{
			return $fields;
		}
		$result = $this->db->getAll('SHOW FULL FIELDS FROM `'.$this->name().'`');
		//zotop::dump($result);
		foreach($result as $field)
		{
			$fields[$field['Field']] = array(
				'name' => $field['Field'],
				'type' => $field['Type'],
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

	public function add($field)
	{
		$tablename = $this->name();

		$sql = 'ALTER TABLE `'.$tablename.'` ADD '.$this->specification($field);

		if( false !== $this->db->execute($sql) )
		{
			return true;
		}
		return false;
	}

	public function modify($field)
	{
		//ALTER TABLE `zotop_msg` MODIFY `title` INT( 10 ) AFTER `id`
		$tablename = $this->name();
		$fields = $this->fields(true);
		$data = $fields[$field['name']];
		if( !isset($data) ) return false;

		$field = array_merge($data,$field);

		$sql = 'ALTER TABLE `'.$tablename.'` MODIFY '.$this->specification($field);

		if( false !== $this->db->execute($sql) )
		{
			return true;
		}
		return false;
	}

	public function specification($field)
	{
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
		if($length !='' && !preg_match('@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)$@i', $type))
		{
			$sql .= '('.$length.')';
		}
		//VARCHAR(32) UNSIGNED
        if ($attribute != '') {
            $sql .= ' ' . $attribute;
        }
		//VARCHAR(32) UNSIGNED NOT NULL
		if ($null !== false) {
            if (!empty($null)) {
                $sql .= ' NOT NULL';
            } else {
                $sql .= ' NULL';
            }
        }
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value'
		if(strtoupper($type) == 'TIMESTAMP' && strtoupper($default) == 'NOW')
		{
			$sql .= ' DEFAULT CURRENT_TIMESTAMP';
		}
		elseif( $extra !== 'AUTO_INCREMENT' && strlen($default)>0 )
		{
            if (strtoupper($default) == 'NULL') {
                $sql .= ' DEFAULT NULL';
            } else {
                if (strlen($default)) {
                    $sql .= ' DEFAULT \'' .$default . '\'';
                }
            }
		}
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value' COMMENT 'ddddd'
        if (!empty($comment)) {
            $sql .= " COMMENT '" . $comment . "'";
        }
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value' COMMENT 'ddddd' AFTER `id`
		if(!empty($position)){
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

	public function field($fieldname)
	{
		$field = new zotop_database_mysql_table_field(&$this->db , $this->name(), $fieldname );
		return $field;
	}
}
//mysql对应的字段操作
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

	public function rename($newname)
	{
		$tablename = $this->tablename;
		$fieldname = $this->fieldname;
		$fields = $this->db->table($tablename)->fields();
		$field = $fields[$fieldname];
		if($newname !== $fieldname)
		{
			$sql = 'ALTER TABLE `'.$tablename.'` CHANGE `'.$fieldname.'` `'.$newname.'` '.$field['type'];
			if( false !== $this->db->execute($sql) )
			{
				return true;
			}
			return false;
		}
		return true;
	}

	public function drop()
	{
		$tablename = $this->tablename;
		$fieldname = $this->fieldname;
		if( false !== $this->db->execute('ALTER TABLE `'.$tablename.'` DROP `'.$fieldname.'`') )
		{
			return true;
		}
		return false;
	}
}
?>