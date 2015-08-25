<?php
class dialog extends page
{
	public static function header($header=array())
	{
		$header['js']['dialog'] = url::common().'/js/zotop.dialog.js';
		$header['body']['class'] = 'dialog';
		parent::header($header);
	}

	public static function top()
	{
	    $html[] = '';
	    $html[] = '<div id="header">';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public static function bottom()
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		$html[] = '</div>';

		echo implode("\n",$html);
	}
}
?>