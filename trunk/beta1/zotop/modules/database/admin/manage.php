<?php
class database_controller_manage extends controller
{
	public function navbar()
	{
		return array(
			array('id'=>'bakup','title'=>'数据库备份','href'=>zotop::url('database/manage/bakup')),
			array('id'=>'restore','title'=>'数据库还原','href'=>zotop::url('database/table/restore')),
			array('id'=>'op','title'=>'数据库优化','href'=>zotop::url('database/manage/op')),
			
		);
	}
	    
    public function actionIndex()
    {

    }

	public function actionBakup()
	{
		$db = zotop::model('database.database');

		if ( form::isPostBack() )
		{
			msg::error('功能开发中……');
		}
		
		$database = $db->db()->config();

		$tables = $db->tables();

		$page = new page();
		$page->title = '数据库备份';
		$page->set('position',$database['database'].' @ '.$database['hostname']);
		$page->set('navbar',$this->navbar());
		$page->set('database',$database);
		$page->set('tables',$tables);       
		$page->display();

	}
    
    
}
?>