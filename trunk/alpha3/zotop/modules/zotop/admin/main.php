<?php
class main_controller extends controller
{

    public function navbar()
	{
		$navbar = array(
			array('id'=>'main','title'=>'首页','href'=>url::build('zotop/main')),
			array('id'=>'system','title'=>'系统信息','href'=>url::build('zotop/main/system')),
			array('id'=>'phpinfo','title'=>'phpinfo','href'=>url::build('zotop/main/phpinfo')),
		);

		$navbar = zotop::filter('zotop.main.navbar',$navbar);

		return $navbar;
	}

	public function mylog()
	{
        block::header(array(
        	'title'=>'日志记录',
        	'action'=>'<a class="more" href="'.zotop::url('zotop/log').'">更多</a>',
        ));

        echo '<div style="height:200px;"></div>';

        block::footer();
	}

	public function notepad()
	{
        block::header(array(
        	'title'=>'记事本',
        	'action'=>'<a class="dialog" href="'.zotop::url('zotop/notepad/add').'">新建记事</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
        ));

        echo '<div style="height:200px;"></div>';

        block::footer();
	}

    public function onDefault()
    {
        $site = array();
        $site['name'] = zotop::config('zotop.site.name');
        $site['totalsize'] = zotop::config('zotop.ftp.size');
        $site['totalsize'] = ( (int)$site['totalsize'] == 0 ) ? '--' : format::byte($site['totalsize']);


        $database = array();
        $database['size'] = zotop::db()->size();
        $database['size'] = format::byte($database['size']);


        $page['title'] = '控制中心';
        $page['css'][]= url::module().'/admin/css/main.css';

        zotop::add('zotop.main.main',array(&$this,'notepad'));
        zotop::add('zotop.main.main',array(&$this,'mylog'));


        page::header($page);
		page::top();
		page::navbar($this->navbar(),'main');

        page::add('');

        page::add('<div id="user" class="clearfix">');
        page::add('	<div id="userface"><span class="image">'.html::image(zotop::user('image')).'</span></div>');
        page::add('	<div id="userinfo">');
        page::add('	<h2 id="welcome">欢迎您，'.zotop::user('name').' <span id="sign">'.zotop::user('sign').'</span></h2>');
        page::add('	<div id="login">登录时间：'.time::format(zotop::user('logintime')).' 登录次数：'.zotop::user('loginnum').' 登录IP：'.zotop::user('loginip').'</div>');
        //加载hook
        zotop::run('zotop.main.action');
        page::add('');
        page::add('</div>');

        page::add('<div class="grid-m-s">');

        page::add('<div class="col-main">');
        page::add('<div class="col-main-inner">');

        zotop::run('zotop.main.main');

        page::add('</div>');
        page::add('</div>');
        page::add('<div class="col-sub">');

        zotop::run('zotop.main.sub');

        block::header(array(
        	'title'=>'网站信息',
        	'action'=>'<a class="more" href="'.zotop::url('zotop/info/site').'">详细</a>',
        ));

			   echo '<table class="table">';
			   echo '<tr><td class="w80">网站名称：</td><td>'.$site['name'].'</td></tr>';
			   echo '<tr><td class="w80">空间占用：</td><td>'.$site['totalsize'].'</td></tr>';
			   echo '<tr><td class="w80">已上传文件：</td><td></td></tr>';
			   echo '<tr><td class="w80">数据库大小：</td><td>'.$database['size'].'</td></tr>';
			   echo '</table>';

        block::footer();

        block::header(array(
        	'title'=>'系统信息',
        	'action'=>'<a class="more" href="'.zotop::url('zotop/main/system').'">详细</a>',
        ));

			   echo '<table class="table">';
			   echo '<tr><td class="w80">程序版本：</td><td>'.zotop::config('zotop.version').'</td></tr>';
			   echo '<tr><td class="w80">程序设计：</td><td>'.zotop::config('zotop.author').'</td></tr>';
			   echo '<tr><td class="w80">程序开发：</td><td>'.zotop::config('zotop.authors').'</td></tr>';
			   echo '<tr><td class="w80">官方网站：</td><td><a href="'.zotop::config('zotop.homepage').'" target="_blank">'.zotop::config('zotop.homepage').'</a></td></tr>';
			   echo '<tr><td class="w80">安装时间：</td><td>'.zotop::config('zotop.install').'</td></tr>';
			   echo '</table>';

        block::footer();

        page::add('</div>');
        page::add('</div>');

		page::bottom('<span class="zotop-tip">上次登录时间：'.time::format(zotop::user('logintime')).'</span>');
        page::footer();
	}


	public function onSystem()
	{
        $header['title'] = '控制中心';
		$phpinfo = array();

		$server = $_SERVER['SERVER_ADDR'].' / '.PHP_OS;
		$php	= $_SERVER['SERVER_SOFTWARE'];
		$safemode = @ini_get('safe_mode') ? ' 开启' : '关闭';

		if(@ini_get('file_uploads')) {
			$upload_max_filesize = ini_get('upload_max_filesize');
		} else {
			$upload_max_filesize = '<b class="red">---</b>';
		}

		$upload_filesize = format::byte(dir::size(ZOTOP_UPLOAD));

		$database = zotop::db()->config();
		$database['size'] = zotop::db()->size();
		$database['version'] = zotop::db()->version();
		$database['db'] = $database['hostname'].':'.$database['hostport'].'/'.$database['database'];

        page::header($header);
		page::top();
		page::navbar($this->navbar());


		block::header('服务器信息');
			table::header();
			table::row(array('side 1 w60'=>'服务器','main  w300 1'=>$server,'side 2 w60 '=>'WEB服务器','main 2'=>$php));
			table::row(array('side 1 w60'=>'安全模式','main 1'=>$safemode,'side 2 w60 '=>'PHP版本','main 2'=>PHP_VERSION));
			table::row(array('side 1 w60'=>'程序版本','main 1'=>zotop::config('zotop.version'),'side 2 w60 '=>'程序根目录','main 2'=>ROOT));

			table::footer();
		block::footer();



		block::header('文件夹权限<span>如果某个文件或目录被检查到“无法写入”（以红色列出），请即刻通过 FTP 或其他工具修改其属性（例如设置为 777），以确保程序功能的正常使用</span>');
			table::header();
			table::row(array('side 1 w60'=>'配置目录','main w300 1'=>'','side 2 w60 '=>'备份目录','main 2'=>''));
			table::row(array('side 1 w60'=>'运行目录','main w300 1'=>'','side 2 w60 '=>'模块目录','main 2'=>''));
			table::footer();
		block::footer();

		block::header('数据库信息');
			table::header();
			table::row(array('side 1 w60'=>'驱动名称','main w300 1'=>$database['driver'],'side 2 w60 '=>'数据库','main 2'=>$database['db']));
			table::row(array('side 1 w60'=>'数据库版本','main 1'=>$database['version'],'side 2 w60 '=>'占用空间','main 2'=>$database['size']));
			table::footer();
		block::footer();

		block::header('文件上传');
			table::header();
			table::row(array('side 1 w60'=>'上传许可','main w300 1'=>$upload_max_filesize,'side 2 w60 '=>'已上传文件','main 2'=>'<span class="loading">'.$upload_filesize.'</span>'));
			table::footer();
		block::footer();



		page::bottom();
        page::footer();
	}

	public function onPhpInfo()
	{


	    ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_clean();

		if( preg_match('/<body><div class="center">([\s\S]*?)<\/div><\/body>/',$phpinfo,$match) )
		{
		    $phpinfo =$match[1];
		}

	    $phpinfo = str_replace('class="e"','style="color:#ff6600;"',$phpinfo);
		$phpinfo = str_replace('class="v"','',$phpinfo);
		$phpinfo = str_replace('<table','<table class="table list" style="table-layout:fixed;"',$phpinfo);
		$phpinfo = str_replace('<tr class="h">','<tr class="title">',$phpinfo);
	    $phpinfo = preg_replace('/<a href="http:\/\/www.php.net\/"><img(.*)alt="PHP Logo" \/><\/a><h1 class="p">(.*)<\/h1>/',"<h1>\\2</h1>",$phpinfo);

	    $page['title'] = 'PHP探针';

        page::header($page);
		page::top();
		page::navbar($this->navbar());

	    echo $phpinfo;

	    page::bottom();
        page::footer();
	}
}
?>