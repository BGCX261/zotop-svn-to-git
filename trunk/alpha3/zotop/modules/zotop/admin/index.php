<?php
class index_controller extends controller
{
    public function onDefault()
    {
        $header['title'] = '控制中心';
        $header['js'] = url::module().'/admin/js/index.js';
        $header['body']['class'] = 'frame';

		page::header($header);

        $html[] = '';
		$html[] = '<script type="text/javascript">';
		$html[] = '	zotop.url.frame.side="'.zotop::url('zotop/side').'"';
		$html[] = '	zotop.url.frame.main="'.zotop::url('zotop/main').'"';
		$html[] = '	zotop.url.msg.unread="'.zotop::url('zotop/msg/unread').'"';
        $html[] = '</script>';
        $html[] = '	<div id="header">';

		$html[] = '		<div id="top">';
        $html[] = '			<div id="logo"><a href="'.zotop::url('zotop/main').'" target="mainIframe" onfocus="this.blur();" title="返回控制中心首页"></a></div>';

        $html[] = '			<div id="action">';
        $html[] = '				<div id="topbar">';
		$html[] = '					<a href="javascript:notepad()">记事本</a><b>|</b>';
		$html[] = '					<a href="'.zotop::url('zotop/setting').'" target="mainIframe">系统设置</a><b>|</b>';
		$html[] = '					<a href="'.zotop::url('zotop/about').'" target="mainIframe" class="dialog {width:450,height:160}">关于</a>';
		$html[] = '				</div>';
        $html[] = '				<div id="user">';
		$html[] = '					<span id="user-info"><b>'.zotop::user('username').'</b>(administrator)</span>';
		$html[] = '					<span id="user-action">';
		$html[] = '						<a href="'.zotop::url('zotop/main').'" target="mainIframe">控制中心</a><b>|</b>';
		$html[] = '						<span id="msg"><a href="'.zotop::url('zotop/msg').'" target="mainIframe">短消息</a><span id="msg-unread"><a href="'.zotop::url('zotop/msg/default/0').'" target="mainIframe"><span id="msg-unread-num">0</span>条未读</a></span><b>|</b></span>';
		$html[] = '						<a href="'.zotop::url('zotop/mine/changepassword').'" target="mainIframe">修改我的密码</a><b>|</b>';
		$html[] = '						<a href="'.zotop::url('zotop/login/logout').'" id="logout" class="confirm {content:\'<h1>您确定要退出登录？</h1>退出登陆后将默认将返回系统登录页面\',yes:\'安全退出\'}">安全退出</a>';
		$html[] = '					</span>';
		$html[] = '				</div>';
		$html[] = '				<div id="navbar">';
		$html[] = '					<ul>';
		$html[] = '						<li><a href="'.zotop::url('zotop/side').'" target="sideIframe"><span>控制面板</span></a></li>';
		$html[] = '						<li><a href="'.zotop::url('content').'" target="mainIframe"><span>内容管理</span></a></li>';
		$html[] = '						<li><a href="'.zotop::url('member').'" target="mainIframe"><span>会员管理</span></a></li>';
		$html[] = '						<li><a href="'.zotop::url('zotop/system/side').'" target="sideIframe"><span>系统管理</span></a></li>';
		$html[] = '					</ul>';
		$html[] = '				</div>';
		$html[] = '				<div id="favorate"><a href="'.zotop::url('zotop/favorate/side').'" target="sideIframe" title="打开收藏夹">收藏夹</a></div>';
        $html[] = '			</div>';

        $html[] = '		</div>';
		$html[] = '		<div id="position">';
		$html[] = '		<div id="position-side">';
		$html[] = '		</div>';
		$html[] = '		<div id="position-main">';
		$html[] = '		</div>';
		$html[] = '		</div>';
		$html[] = '	</div>';
        $html[] = '	<div id="body" class="clearfix">';



        $html[] = '		<div id="main">';
        $html[] = '		<div id="main-inner">';
        $html[] = '			'.html::iframe('mainIframe','about:blank',array('frameborder'=>'no','scrolling'=>'auto','width'=>'100%','height'=>'100%'));
        $html[] = '		</div>';
        $html[] = '		</div>';

		$html[] = '		<div id="side">';
		$html[] = '		<div id="side-inner">';
		$html[] = '			<div id="side-header">';
		$html[] = '			</div>';
		$html[] = '			<div id="side-body">';
		$html[] = '				<div id="side-body-inner"></div>';
        $html[] = '				'.html::iframe('sideIframe','about:blank',array('frameborder'=>'no','scrolling'=>'auto','allowtransparency'=>'true','width'=>'100%','height'=>'100%'));
		$html[] = '			</div>';
		$html[] = '			<div id="side-footer">';
		$html[] = '				<div>Powered by <a href="'.zotop::config('zotop.homepage').'" target="_blank">'.zotop::config('zotop.name').' '.zotop::config('zotop.version').'</a></div>';
		$html[] = '			</div>';
		$html[] = '		</div>';
        $html[] = '		</div>';

        $html[] = '	</div>';
        $html[] = '	<div id="footer">';

        $html[] = '	</div>';
        echo implode("\n",$html);

        page::footer();
    }
}
?>