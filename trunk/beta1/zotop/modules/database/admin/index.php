<?php
class database_controller_index extends controller
{
    public function actionIndex()
    {
		$db = zotop::db();
		if( $db->connect() )
		{
			zotop::redirect(zotop::url('database/table'));
		}
		msg::error('连接数据库失败，请检查数据库配置是否正确');        
    }
}
?>