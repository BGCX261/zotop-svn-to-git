<?php
class BaseController extends base
{
	public function onDefault()
	{
		echo 'Hello Zotop!';
	}

	public function _empty()
	{
	    zotop::run('system.status.404');
	}
}
?>