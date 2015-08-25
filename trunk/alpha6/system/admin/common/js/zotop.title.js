//title
$(function(){
	$('a.setcolor').click(function(event){

		event.preventDefault();
		
		$(this).colorPicker({
   			setBackground: false,
   			setColor: 'input[name='+$(this).attr("colorto")+']',
   			setValue: 'input[name='+$(this).attr("valueto")+']',
   			align: "left" 
		});
		
	});

	$('a.setweight').click(function(event){

		event.preventDefault();
		
		var	weightto = 'input[name='+$(this).attr("weightto")+']';
		var valueto = 'input[name='+$(this).attr("valueto")+']';

		var isWeight = $(weightto).css('fontWeight') == 'bold' ? true : false;
		
		if( isWeight ){
			$(weightto).css('fontWeight','');
			$(valueto).val('');
			$(this).find('.zotop-icon').removeClass('bold');
		}else{
			$(weightto).css('fontWeight','bold');
			$(valueto).val('bold');
			$(this).find('.zotop-icon').addClass('bold');
		}
		
	});
})