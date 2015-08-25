<?php
$this->header();
$this->top();
$this->navbar();

			form::header();
			
			foreach($fields as $field)
			{
			    form::field($field);
			}
						
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

$this->bottom();
$this->footer();
?>