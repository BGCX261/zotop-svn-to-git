<?php
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