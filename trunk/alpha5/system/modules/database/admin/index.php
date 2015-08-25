<?php
class index_controller extends controller
{
    public function indexAction()
    {
		$db = zotop::db();
		if( $db->connect() )
		{
			zotop::redirect('database/table');
		}
		msg::error('连接数据库失败，请检查数据库配置是否正确');        
    }
}
?>