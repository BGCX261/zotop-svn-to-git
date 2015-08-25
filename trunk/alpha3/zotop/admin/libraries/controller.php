<?php
class controller extends zotop_controller
{

	public function __construct()
    {
        if( !zotop::user() )
        {
			zotop::redirect('zotop/login');
        }
        $this->__init();
    }
    
    public function __init()
    {
    }

    public function onDefault()
    {
        echo 'Hello Zotop Administrator!';
    }
}
?>