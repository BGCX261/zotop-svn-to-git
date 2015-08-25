<?php
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
?>