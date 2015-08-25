<?php
class site_controller_index extends controller
{
	public function actionIndex()
	{
		$page = new page();		
        $page->set('title','首页');
        $page->display('index');
	}
}
?>