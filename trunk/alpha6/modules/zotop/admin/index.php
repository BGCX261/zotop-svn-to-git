<?php
class zotop_controller_index extends controller
{
    public function actionIndex()
    {
        $page = new page();
        $page->title = '系统管理中心';
        $page->body = array('class'=>'frame');
        $page->set('user',$this->user);
        $page->display();
    }
}
?>