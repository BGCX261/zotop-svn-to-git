<?php

zotop::add('zotop.main.side','zotop_notepad');
zotop::add('zotop.main.main','zotop_favorite_main');
zotop::add('zotop.index.quickbar','zotop_index_quickbar');
zotop::add('zotop.main.main','zotop_log');
zotop::add('system.shutdown','zotop_logsave');

function zotop_notepad()
{
	block::header(array(
		'title'=>'记事本',
		'action'=>'<a class="dialog" href="'.zotop::url('zotop/notepad/add').'">新建记事</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

function zotop_favorite_main()
{
	block::header(array(
		'title'=>'我的收藏夹',
		'action'=>'<a class="dialog" href="'.zotop::url('zotop/quick/add').'">管理</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

function zotop_index_quickbar()
{
	echo '<a href="'.zotop::url('zotop/setting').'" target="mainIframe">系统设置</a> <b>|</b> ';
}

function zotop_log()
{
	block::header(array(
		'title'=>'日志记录',
		'action'=>'<a class="more" href="'.zotop::url('zotop/log').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

function zotop_logsave()
{
	zotop::data('mylog',zotop::$logs);
}


zotop::add('zotop.system.side.tools','system_side_tool_database');

function system_side_tool_database()
{
	echo '<li><a href="'.zotop::url('database/manage/bakup').'" target="mainIframe">数据库备份及还原</a></li>';
}

zotop::add('zotop.main.action','content');
zotop::add('zotop.index.navbar','navbar_content');
function content()
{
	echo '<div>内容：<a href="#">待审核 3条</a> <a href="#">垃圾箱 5条</a></div>';
}
function navbar_content()
{
	echo '<li><a href="'.zotop::url('content').'" target="mainIframe"><span>内容管理</span></a></li>';
}

zotop::add('zotop.main.action','msg_unread');
zotop::add('zotop.index.useraction','msg_useraction');

function msg_unread()
{
	echo '<div>短消息：<a href="#">未读 3条</a> <a href="#">待处理 5条</a></div>';
}

function msg_useraction()
{
	?>
	<span id="msg">
		<a href="<?php echo zotop::url('msg/list') ?>" target="mainIframe">短消息</a>
		<span id="msg-unread">
			<a href="<?php echo zotop::url('msg/list/unread') ?>" target="mainIframe"><span id="msg-unread-num">0</span>条未读</a>
		</span>
		<b>|</b>
	</span>
	<script>
	//获取未读消息数目
	function getUnreadMsg(){
		var url = "<?php echo zotop::url('msg/list/unread') ?>";
		//获取
		$.get(url,'',function(msg){
			if(parseInt(msg.num) > 0)
			{
				$('#msg-unread').show();
				$('#msg-unread-num').html(msg.num);
			}else{
				$('#msg-unread').hide();
			}
		},'json');
	};
	//定时获取未读短消息数目
	(function(){
		setInterval(getUnreadMsg,10000);
	})();
	</script>
	<?php
}

zotop_field::set('editor','xheditor');

function xheditor($attrs)
{
		$attrs['class'] = isset($attrs['class']) ? 'editor '.$attrs['class'] : 'editor';		

		$tools = array(
			'image'=>'<a href="'.zotop::url('zotop/image/upload',array('globalid'=>form::globalid(),'field'=>$attrs['name'],'image'=>'__image__')).'" class="button editor-insert" name="'.$attrs['name'].'" type="image"><span class="zotop-icon zotop-icon-imageuploader button-icon"></span><span class="button-text">插入本地图片</span></a>',	
			'file'=>'<a href="'.zotop::url('zotop/file/upload',array('globalid'=>form::globalid(),'field'=>$attrs['name'],'image'=>'__file__')).'" class="button editor-insert" name="'.$attrs['name'].'" type="file"><span class="zotop-icon zotop-icon-fileuploader button-icon"></span><span class="button-text">插入本地文件</span></a>',
			'template'=>'<a href="'.zotop::url('zotop/file/upload',array('globalid'=>form::globalid(),'field'=>$attrs['name'],'image'=>'__file__')).'" class="button editor-insert" name="'.$attrs['name'].'" type="template"><span class="zotop-icon zotop-icon-template button-icon"></span><span class="button-text">插入模板</span></a>',
		);
		$tools = zotop::filter('editor.tools',$tools);
		$tools = arr::take('tools',$attrs) === false ? array() : $tools;

		$url = zotop::modules('xheditor','url');

		$html[] = html::script($url.'/editor/xheditor-zh-cn.min.js');
		$html[] = html::script($url.'/common/global.js');
		if ( is_array($tools) && !empty($tools) )
		{
			$html[] = '<div class="field-toolbar">';
			foreach($tools as $tool){
				$html[] = ' '.$tool;
			}
			$html[] = '</div>';
		}
		$html[] = '<div class="field-wrapper">';
		$html[] = '	'.field::textarea($attrs);
		$html[] = '</div>';
	 
		return implode("\n",$html);
}

?>