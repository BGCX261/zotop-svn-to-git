<?php
class content_controller_detail extends controller
{
	public $action = 'detail';

	public function actionDetail($id)
	{
		$content = zotop::model('content.content');
		$content->read($id);

		$page = new page();
		$page->set('content',$content);
		$page->set('body',array('class'=>'detail'));
		$page->display($content->template);
	}
}
?>