<?php
$this->header();
$this->top();
$this->navbar();
?>
<?php
form::header();

	foreach($fields as $f)
	{
		form::field($f);
	}

	form::buttons(
		array('type'=>'submit'),
		array('type'=>'back')	
	);
	
	form::footer();

$this->bottom();
$this->footer();
?>