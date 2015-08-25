<?php
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


?>