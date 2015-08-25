<?php
class controller extends Zotop_Controller
{

	public function __construct()
    {
		if( zotop::user() == null )
        {
			zotop::redirect('zotop/login');
        }
    }

    public function onDefault()
    {
        echo 'Hello Zotop Administrator!';
    }
}
?>