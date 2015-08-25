<?php
//ÉèÖÃÄ¬ÈÏµÄuri
zotop::add('zotop.uri','site_default_uri');

function site_default_uri($uri)
{
	if ( empty($uri) )
	{
		return 'site';
	}
}
?>