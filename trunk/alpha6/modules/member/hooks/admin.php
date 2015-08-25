<?php
zotop::add('zotop.index.navbar','member_nav');

function member_nav()
{
	echo '<li><a href="'.zotop::url('member').'" target="mainIframe"><span>会员管理</span></a></li>';
}
?>