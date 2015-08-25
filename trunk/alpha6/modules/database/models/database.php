<?php
class database_model_database
{
    protected $_db ='';
    
    public function __construct()
    {
    	if ( ! is_object($this->_db) )
		{
	       $this->_db  = zotop::db();
		}        
    }
    
    public function db()
    {
        return $this->_db;
    }
    
    public function tables()
    {
		$tables = $this->db()->tables();

		
        
        return $tables;
    }


    
	/**
	 * 倒出
	 */    
    public function export($tables, $option=array())
    {
        
    }

	/**
	 * 分割sql语句
	 */
	public function splitSql($sqldump) {
		$sql = str_replace("\r", "\n", $sqldump);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$queries = explode("\n", trim($query));
			foreach($queries as $subquery) {
				if(!empty($subquery[0])){
					$ret[$num] .= $subquery[0] == '#' ? NULL : $subquery;
				}
			}
			$num++;
		}
		return $ret;
	}

	public function bakupTable($table)
	{
		$sql = '';
		$sql .= "DROP TABLE IF EXISTS ".$table."\n";

		$create = $this->db()->getAll('SHOW CREATE TABLE '.$table);

		$sql .= $create['Create Table'].";\n\n";

		return $sql;

	}

	
	public function bakupData($table)
	{
	
	}
}