<?php
class side_controller extends controller
{
    public function onDefault()
    {

        $header['title'] = '侧边条';
		$header['body']['class'] = 'side';

        page::header($header);

		echo('<div style="height:600px;">dddd</div>');

        page::footer();
	}
}
?>