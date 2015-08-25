<?php
class io_controller extends controller
{
   public function onDefault($status = -1)
   {
   	 $header['title'] = '测试文件系统';

     page::header($header);
	 page::top();

	 echo "<pre>";
	 //print_r(file::brower(ZOTOP_SYSTEM,'php'));
	 print_r(file::brower(ZOTOP_SYSTEM));
	 echo "</pre>";
	 page::footer();
   }
}