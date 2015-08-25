<?php
class BaseBlock extends Base
{
	public static function header($header=array())
	{
	    if( !is_array($header) )
		{
		    $header = array('title'=>$header);
		}
	    $html[] = '';
		$html[] = '<div class="block '.$header['class'].'"'.(isset($header['id']) ? ' id="'.$header['id'].'"':'').'>';
		if(isset($header['title']))
		{
		    $html[] = '	<div class="block-header">';
		    $html[] = '		<h2>'.$header['title'].'</h2>';
		    if( isset($header['action']) )
		    {
		        $html[] = '		<h3>'.$header['action'].'</h3>';
		    }
		    $html[] = '	</div>';
		}
		$html[] = '<div class="block-body">';
		$html[] = '';
		echo implode("\n",$html);

	}
	public static function footer($footer=false , $extra='')
	{
		$html[] = '';
	    $html[] = '</div>';
	    if($footer)
	    {
	        $html[] = '	<div class="block-footer">'.$extra.'</div>';
	    }
	    $html[] = '</div>';

		echo implode("\n",$html);
	}
}
?>