<?php
$this->header();
$this->top();
$this->navbar();
?>
<script tyle="text/javascript">
$(function(){
	//默认加载高级设置
	toggle();
	//change
	$('select[name=system.cache.driver]').change(function(){
		toggle();
	});
})

function toggle(driver){
	driver = driver || $('select[name=system.cache.driver]').val();

	if ( driver == 'memcache' ){
		$('#memcache-settings').show();
	}else{
		$('#memcache-settings').hide();
	}
}
</script>
<?php

			form::header();	
			
			foreach($cache as $field)
			{
			    form::field($field);
			}
			
			form::field('<div id="memcache-settings" style="display:none;">');

			foreach($memcache as $field)
			{
			    form::field($field);
			}
			
			form::field('</div>');

			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);

			form::footer();
?>

<?php
$this->bottom();
$this->footer();
?>