<?php
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


?>