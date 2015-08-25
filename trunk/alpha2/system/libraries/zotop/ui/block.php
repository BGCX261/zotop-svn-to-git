<?php
class zotop_block
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
		$html[] = '	<div class="block-body">';
		$html[] = '';
		echo implode("\n",$html);

	}
	public static function footer($footer=null)
	{
		$html[] = '';
	    $html[] = '	</div>';
	    if( $footer !==null )
	    {
	        $html[] = '	<div class="block-footer">'.$footer.'</div>';
	    }
	    $html[] = '</div>';

		echo implode("\n",$html);
	}
}
?>