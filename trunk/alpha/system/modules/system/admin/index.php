<?php
class IndexController extends controller
{
    public function onDefault()
    {
        $header['title'] = '控制中心';
        $header['css'] = url::module().'/admin/css/index.css';
        $header['js'][] = url::module().'/admin/js/index.js';
        $header['body']['class'] = 'frame';

        page::header($header);

        $html[] = '';
		$html[] = '<script type="text/javascript">';
		$html[] = '	zotop.url.main="'.url::build('system/index/main').'"';
		$html[] = '	zotop.url.msgUnread="'.url::build('system/msg/unread').'"';
        $html[] = '</script>';
		$html[] = '<table id="frames" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">';
        $html[] = '	<tr id="header">';
        $html[] = '		<td colspan="2">';
        $html[] = '		<div id="top">';
        $html[] = '			<div id="logo"><a href="'.url::build('system').'" target="mainIframe" onfocus="this.blur();" title="首页"></a></div>';
        $html[] = '			<div id="action">';
        $html[] = '				<div id="topbar">';
		$html[] = '					<a href="javascript:notepad()">记事本</a><b>|</b>';
		$html[] = '					<a href="'.url::build('system/setting').'" target="mainIframe">系统设置</a><b>|</b>';
		$html[] = '					<a href="'.url::build('system/about').'" target="mainIframe" class="dialog {width:450,height:160}">关于</a>';
		$html[] = '				</div>';
        $html[] = '				<div id="user">';
		$html[] = '					<span id="user-info"><b>zotop</b>(administrator)</span>';
		$html[] = '					<span id="user-action">';
		$html[] = '						<span id="msg"><a href="'.url::build('system/msg').'" target="mainIframe">短消息</a><span id="msg-unread"><a href="'.url::build('system/msg/default/0').'" target="mainIframe"><span id="msg-unread-num">0</span>条未读</a></span><b>|</b></span>';
		$html[] = '						<a href="'.url::build('system/user/changepassword').'" target="mainIframe">修改我的密码</a><b>|</b>';
		$html[] = '						<a href="'.url::build('system/login/logout').'" id="logout" class="confirm {content:\'<h1>您确定要退出登录？</h1>退出登陆后将默认将返回系统登录页面\',yes:\'安全退出\'}">安全退出</a>';
		$html[] = '					</span>';
		$html[] = '				</div>';
        $html[] = '			</div>';
        $html[] = '		</div>';
        $html[] = '		</td>';
        $html[] = '	</tr>';
        $html[] = '	<tr id="body">';
        $html[] = '		<td id="side">';
        $html[] = '		<div class="sideinner">';
		$html[] = '		<div class="header">';
		$html[] = '			<div class="top">';
        $html[] = '				<a href="'.url::build('system/index/main').'" target="mainIframe">控制中心</a>';
        $html[] = '				<a href="'.url::build('system/login').'" target="mainIframe">登陆测试</a>';
		$html[] = '				<a href="'.url::build('system/test').'" target="mainIframe">表单测试</a>';
		$html[] = '				<a href="'.url::build('system/database').'" target="mainIframe">数据库管理</a>';
		$html[] = '			</div>';
		$html[] = '			<div class="body-header">';
		$html[] = '				<div class="body-title">栏目列表</div>';
		$html[] = '				<div class="body-action">刷新</div>';
		$html[] = '			</div>';
		$html[] = '		</div>';
		$html[] = '		<div class="body">';
		$html[] = '		<div class="inner">';
		$html[] = '		<div style="height:600px;">fffffff</div>';
		$html[] = '		</div>';
		$html[] = '		</div>';
		$html[] = '		<div class="footer">';
		$html[] = '		</div>';
		$html[] = '		</div>';
        $html[] = '		</td>';
        $html[] = '		<td id="main">';
        $html[] = '			'.html::iframe('mainIframe','about:blank',array('frameborder'=>'no','scrolling'=>'auto','width'=>'100%','height'=>'100%'));
        $html[] = '		</td>';
        $html[] = '	</tr>';
        $html[] = '	<tr id="footer">';
        $html[] = '		<td colspan="2"></td>';
        $html[] = '	</tr>';
        $html[] = '</table>';

        echo implode("\n",$html);

        page::footer();
    }

    public function onMain()
    {
        $header['title'] = '控制中心';

        page::header($header);
		page::top();
		page::navbar(array(
			array('id'=>'main','title'=>'首页','href'=>url::build('system/index/main')),
			array('id'=>'info','title'=>'系统信息','href'=>url::build('system/index/info')),
		));

		//zotop::config('zotop.url.model',0);

		echo url::build('system/database/create',array('name'=>'zotop'));

		page::bottom('<span class="zotop-tip"> </span>最后一次登录时间：2009-8-9 14:17:54');
        page::footer();
	}


	public function onInfo()
	{
        $header['title'] = '控制中心';

        page::header($header);
		page::top();
		page::navbar(array(
			array('id'=>'main','title'=>'首页','href'=>url::build('system/index/main')),
			array('id'=>'info','title'=>'系统信息','href'=>url::build('system/index/info')),
		));


		page::bottom();
        page::footer();
	}
}
?>