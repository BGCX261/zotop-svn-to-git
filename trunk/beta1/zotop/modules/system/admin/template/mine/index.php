<?php
$this->header();
$this->top();
$this->navbar();
       
zotop::dump(zotop::user());

$this->bottom();
$this->footer();
?>