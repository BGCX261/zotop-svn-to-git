<?php
zotop::add('zotop.main.action','msgtip');
zotop::add('zotop.main.action','weather');
zotop::add('zotop.main.action','msgtip2');
function msgtip()
{
    echo '<div>未读短消息：<a href="#">5条</a> This\'s a hook</div>';
}
function msgtip2()
{
    echo '<div>待处理文档：<a href="#">5条</a> HOOK，暂时写在~hook文件中</div>';
}

function weather()
{
	echo '<div style="position:absolute;top:10px;right:0px;">';
	echo '<iframe src="http://m.weather.com.cn/m/pn6/weather.htm " width="160" height="20" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no" allowtransparency="true" ></iframe>';
	echo '</div>';
}

?>