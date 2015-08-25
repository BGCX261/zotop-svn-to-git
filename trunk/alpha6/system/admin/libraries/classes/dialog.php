<?php
class dialog extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'dialog'));
        $this->addScript('$common/js/zotop.dialog.js');
        parent::header();
    }

	public function top()
	{}

	public function bottom()
	{}

	public function footer()
	{
		 parent::footer();
	}
}
?>