<?php
class Database_Mysqli extends database implements DatabaseInterface
{
    
    private $contectObj;
    
    public function __construct($config=array())
    {
        
    }
    
    public function query($sql)
    {
        echo 'querys in Mysqli';
    }
    
    public function getAll($sql)
    {
        
    }
    
    public function getRow($sql)
    {
        
    }
    
    public function getInsertId()
    {
        
        
    }
    
    public function contect($config=array())
    {
        
    }
    
    public function setCharset($charset)
    {
    
    }
}