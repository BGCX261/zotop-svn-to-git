<?php
class Database_Sqlite extends database implements DatabaseInterface
{
    
    private $openObj;
    
    public function __construct($config=array())
    {
       if ($config)  $this->contect($config);
    }
    
    public function query($sql)
    {
        if ($sql)
        {
            return sqlite_query($this->openObj,$sql,'SQLITE_ASSOC');   
        }
        else
        {
            return false;
        }
    }
    
    public function getAll($sql)
    {
        if ($sql)
        {
            $query = $this->query($sql);
            if ($query)
            {
                return sqlite_fetch_all($query);    
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getRow($sql)
    {
        if ($sql)
        {
            $query = $this->query($sql);
            if ($query)
            {
                return sqlite_fetch_array($query);    
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getInsertId()
    {
        return  sqlite_last_insert_rowid($this->openObj);
    }
    
    public function contect($config=array())
    {
        if (empty($config['address']))
        {
            $this->openObj =sqlite_open($config['address'], 0666, $sqliteerror);
            if ($this->openObj)
            {
                return true;    
            }
            else
            {
                return false;       
            } 
        }
    }
    
    public function setCharset($charset)
    {
    
    }
    
    public function __destruct(){
        if ($this->openObj)
        {
            sqlite_close($this->openObj);    
        }   
    }
}