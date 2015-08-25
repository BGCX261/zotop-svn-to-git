<?php

//设置默认的uri
zotop::add('zotop.uri','system_default_uri');

function system_default_uri($uri)
{
	if ( empty($uri) ) router::$uri = 'system';
}

//为控制中心增加记事本功能
zotop::add('system.main.side','notepad_box');
function notepad_box()
{
	box::header(array(
		'title'=>'记事本',
		'action'=>'<a class="dialog" href="'.zotop::url('system/notepad/add').'">新建记事</a>|<a class="more" href="'.zotop::url('system/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	box::footer();
}

//为控制中心增加个人收藏夹功能
zotop::add('system.main.main','favorite_box');

function favorite_box()
{
	box::header(array(
		'title'=>'我的收藏夹',
		'action'=>'<a class="dialog" href="'.zotop::url('system/quick/add').'">管理</a>|<a class="more" href="'.zotop::url('system/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	box::footer();
}

//为主框架增加系统设置快捷功能
zotop::add('system.quickbar','system_quickbar_settings');

function system_quickbar_settings()
{
	echo '<a href="'.zotop::url('system/setting').'" target="mainIframe">系统设置</a> <b>|</b> ';
}




//增加template字段，用于选择模板
zotop::add('system.ready','system_field_template');

function system_field_template()
{
	field::set('template','field_template');

	function field_template($attrs)
	{
		$html[] = field::text($attrs);
		$html[] = '<a class="dialog" href="'.zotop::url('system/template/select/'.$attrs['name']).'" title="选择模板"><span class="zotop-icon zotop-icon-template"></span></a>';
		return implode("\n",$html);
	}
}

zotop::add('system.ready','field_xheditor');

function field_xheditor()
{
	field::set('editor','xheditor_rc1');

	function xheditor_rc1($attrs)
	{
			$attrs['class'] = isset($attrs['class']) ? 'editor '.$attrs['class'] : 'editor';

			$tools = array(
				'image'=>'<a href="'.zotop::url('system/image/upload').'" class="button editor-insert" name="'.$attrs['name'].'"><span class="zotop-icon zotop-icon-imageuploader button-icon"></span><span class="button-text">插入图片</span></a>',	
				'file'=>'<a href="'.zotop::url('system/file/upload').'" class="button editor-insert" name="'.$attrs['name'].'"><span class="zotop-icon zotop-icon-fileuploader button-icon"></span><span class="button-text">插入文件</span></a>',
				'template'=>'<a href="'.zotop::url('system/file/upload').'" class="button editor-insert" name="'.$attrs['name'].'"><span class="zotop-icon zotop-icon-template button-icon"></span><span class="button-text">插入模板</span></a>',
			);
			$tools = zotop::filter('editor.tools',$tools);
			$tools = arr::take('tools',$attrs) === false ? array() : $tools;

			$url = zotop::module('xheditor','url');

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
			$html[] = '	'.field::textarea($attrs);
		 
			return implode("\n",$html);
	}
}



zotop::add('zotop.system.side.tools','system_side_tool_database');

function system_side_tool_database()
{
	echo '<li><a href="'.zotop::url('database/manage/bakup').'" target="mainIframe">数据库备份及还原</a></li>';
}

zotop::add('system.ready','field_keywords');

function field_keywords()
{
	field::set('keyword','site_keywords');

	function site_keywords($attrs)
	{
		//$html[] = html::script('$common/js/zotop.keywords.js');
		$html[] = '<div class="field-wrapper clearfix">';
		$html[] = '	'.field::text($attrs);
		$html[] = '	<span class="field-handle">';
		$html[] = '		&nbsp;<a class="setkeywords" style="display:inline-block;" valueto="'.$attrs['name'].'" title="'.zotop::t('常用关键词').'"><span class="zotop-icon zotop-icon-keywords"></span></a>';
		$html[] = '	</span>';
		$html[] = '</div>';

		return implode("\n",$html);
	}
}



//为主导航增加内容管理功能
zotop::add('system.navbar','system_navabr_content');

function system_navabr_content()
{
	?>
	<li><a href="javascript:void(0);" onclick="top.go('<?php echo zotop::url('content/index/side') ?>','<?php echo zotop::url('content/index/index') ?>')"><span>内容管理</span></a></li>
	<?php
}

//link 字段
/*
zotop::add('system.ready','field_linkurl');

function field_linkurl()
{
	field::set('link','linkurl');

	function linkurl($attrs)
	{
		return field::text($attrs).' <span style="white-space:nowrap;"><input type="checkbox" name="link" id="linkurl" value=""/> <label for="linkurl">'.zotop::t('使用转向链接').'</label></span>';
	}
}
*/



zotop::add('system.ready','field_codemirror');

function field_codemirror()
{
	field::set('code','codemirror');

	function codemirror($attrs)
	{		
			$url = zotop::module('codemirror','url');
		
			$options = new stdClass();
			$options->path = $url.'/codemirror/js/';
			$options->parserfile = array('parsexml.js');
			$options->stylesheet = array($url.'/codemirror/css/xmlcolors.css');
			$options->height = is_numeric($attrs['height']) ? $attrs['height'].'px' : $attrs['height'];
			$options->width = is_numeric($attrs['width']) ? $width.'px' : $attrs['width'];
			$options->continuousScanning = 500;
			$options->autoMatchParens = true;
			if ( $attrs['linenumbers'] !== false ) {
				$options->lineNumbers = true;
				$options->textWrapping = false;
			}
			if ( $attrs['tabmode'] == '' )
			{
				$options->tabMode = 'shift';
			}
			
			$html = array();
			$html[] = html::script($url.'/codemirror/js/codemirror.js');
			$html[] = html::stylesheet($url.'/codemirror/css/codemirror.css');
			$html[] = '	'.field::textarea($attrs);
			$html[] = '<script type="text/javascript">';
			$html[] = '	var editor = CodeMirror.fromTextArea("'.$attrs['name'].'", '.json_encode($options).');';
			$html[] = '$(function(){';
			$html[] = '	$("form").submit(function(){';
			$html[] = '		$("textarea[name=+'.$attrs['name'].'+]").val(editor.getCode());';
			$html[] = '	});';
			$html[] = '})';
			$html[] = '</script>';
		 
			return implode("\n",$html);
	}

	field::set('templateeditor',templateeditor);

	function templateeditor($attrs)
	{
		return codemirror($attrs);
	}
}



zotop::add('system.main.action','msg_unread');
zotop::add('system.useraction','msg_useraction');

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
		$.get(url,'',function(msg){
			msg.num = parseInt(msg.num);			
			$('#msg-unread-num').html(msg.num);
		},'json');
	};
	//定时获取未读短消息数目
	(function(){		
		setInterval(getUnreadMsg,10000);
	})();
	</script>
	<?php
}

?>