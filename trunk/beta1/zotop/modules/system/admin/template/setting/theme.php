<?php
$this->header();
$this->top();
$this->navbar();

			form::header();
			
			box::header('站点主题');
			box::footer();
						
			box::header('系统主题');
			box::footer();

			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

$this->bottom();
$this->footer();
?>