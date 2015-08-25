<?php
class index_controller extends controller
{
    public function indexAction()
    {
        $page = new page();
        $page->title = '系统管理中心';
        $page->body = array('class'=>'frame');
        $page->set('user',$this->user);
        $page->display();
    }
}
?>