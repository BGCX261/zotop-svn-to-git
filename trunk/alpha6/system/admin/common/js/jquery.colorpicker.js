/**
 * Color Picker for jQuery
 *
 * @author: chenlei
 * @email: hankx_chen@qq.com
 * @site: http://www.zotop.com
 *
 * @version: 0.1
 */
(function($) {

	$.fn.colorPicker=function(options) {
		options = $.extend({},{setBackground:true,setColor:true,setValue:true,position:'right',zindex:10000},options);		
		$(this).each(function(){
			
			var $handle = $(this);
			
			var $picker = $('#zotop_colorpicker');
			var $mask = $('#zotop_colorpicker_mask');

			if( $picker.length == 0 )
			{
				$picker = $('<div id="zotop_colorpicker"></div>');
				$picker.css({'position':'absolute','z-index':options.zindex,'background-color':'#FFFFFF','border':'1px solid #CCCCCC','padding':'1px','cursor':'pointer','width':'270px'});


				
				var hc = ["FF","CC","99","66","33","00"];
				var i=0,j=0;
				var r,g,b,c;
				var s = new Array();
				s[0] = '<table cellpadding="1" cellspacing="1" style="table-layout:fixed;border-collapse:separate;border-spacing:1px 1px;"><tr>';
				for(r=0;r<6;r++) {
					for(g=0;g<6;g++) {
						for(b=0;b<6;b++) {
							c = hc[r] + hc[g] + hc[b];
							if (i%18==0 && i>0) {
								s[j+1] = "</tr><tr>";
								j++;
							}
							s[j+1] = '<td class="color" bgcolor="#'+c+'" width="14" height="14" title="#'+c+'" class="textflow"><b> </b></td>';
							i++;
							j++;
						}
					}
				}
				s[j+1] = '</tr><tr><td height="10" colspan="16" id="zotop_colorpicker_preview" align="center"></td><td class="color" bgcolor="" height="10" colspan="1" title="Empty" align="center">E</td><td class="color" bgcolor="transparent" height="10" colspan="1" title="Transparent" align="center">T</td></tr></table>';

				$picker.html(s.join(''));
				$picker.appendTo(document.body);
				$picker.bgiframe();


				var offset = $handle.offset();
				var top = offset.top + $handle.outerHeight();
				var left = offset.left;

				$picker.css({'top':top+'px','left':left+'px'});
				$picker.show();

				//отй╬узуж
				$mask=$('<div id="zotop-colorpicker-mask"></div>');
				$mask.css({
					position: 'absolute',
					top: '0px',
					left: '0px',
					zIndex : options.zindex-1,
					width : $(document).width(),
					height : $(document).height()
				});			
				$mask.appendTo(document.body).show();			
				$mask.click(function(){
					$picker.hide().remove();
					$mask.hide().remove();
				});
			}
			
			$('.color',$picker).unbind("mouseover").unbind("click").each(function() {
				var color = this.bgColor.toUpperCase();
				var color = color=='TRANSPARENT' ? 'transparent' : color;

				$(this).mouseover(function(){

					$("#zotop_colorpicker_preview").css('background-color',color).text(color);

				}).click(function(){
						
						$picker.hide().remove();
						$mask.hide().remove();

						if(options.setBackground != false) {
							$handle.css('background-color',color);
						}

						if(options.setColor != false) {
							$(options.setColor).css('color',color);
						}
						if(options.setValue != false) {
							$(options.setValue).val(color);
						}
						if(options.setText != false) {
							$(options.setText).text(color);
						}
						
						return true;
				});				

			});			

		});
	}

})(jQuery);
