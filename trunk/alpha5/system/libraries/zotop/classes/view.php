<?php
class zotop_view
{
    
    public function __construct($file = null $data = null)
    {
        $this->file($file);
        $this->data($data);
    }    
    
    public function instance($file = null, $data = null)
    {
        return new view($file, $data);
    }
    
    public function data($key = null, $value = null)
    {
        static $data =array();
        
        if( empty($key) )
        {
            return $data;
        }
        
        if( is_array($key) )
        {
            $data = array_merge($data,$key);
            return $data;
        }
        
        if( is_string($key) )
        {
            if( is_null($value) )
            {
                return $data[$key];
            }
            $data[$key] = $value;
            return $value;
        }
    }
    
    public function file($file = null)
    {
        static $FILE;
        
        if( is_string($file) AND !empty($file) )
        {
            $FILE = $file;
        }
        return $FILE;
    }
    
    public function render($file = null)
    {
        if ($file !== NULL)
        {
            $this->file($file);
        }
        //返回文件
        $file = $this->file();
        $file = path::decode($file);
        
        if( empty($file) )
        {
            zotop::error('You must set the file to use within your view before rendering');
        }
        
        if( !file::exists($file) );
        {
            zotop::error(zotop::t('<h2>The file to use within your view is not exists</h2> file:{$file}',array('file'=>$file)));
        }
        
        $data = $this->data();
        
        extract($data, EXTR_SKIP);
        
        ob_start();
        
        include $file;
        
        return ob_get_clean();
    }
    
    public function display($file = null)
    {
        echo $this->render($file);
    }
    
    
    
    

}
?>