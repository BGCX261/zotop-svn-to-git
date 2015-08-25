<?php
class main_controller extends controller
{
    public function onDefault()
    {
        $header['title'] = '控制中心';

        page::header($header);
		page::top();
		page::navbar(array(
			array('id'=>'main','title'=>'首页','href'=>url::build('zotop/index/main')),
			array('id'=>'info','title'=>'系统信息','href'=>url::build('zotop/index/info')),
		),'main');

		$db = zotop::db();
		$user = $db->select('*')->from('user')->orderby('id','asc')->limit(1)->getAll();

		zotop::dump($db->lastSql());
		zotop::dump($user);

		page::bottom('<span class="zotop-tip">最后一次登录时间：2009-8-9 14:17:54</span>');
        page::footer();
	}


	public function onInfo()
	{
        $header['title'] = '控制中心';

        page::header($header);
		page::top();
		page::navbar(array(
			array('id'=>'main','title'=>'首页','href'=>url::build('zotop/index/main')),
			array('id'=>'info','title'=>'系统信息','href'=>url::build('zotop/index/info')),
		));


		page::bottom();
        page::footer();
	}
}
?>