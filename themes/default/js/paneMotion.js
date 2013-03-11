/**
 * classautomate - pane motion
 *
 * @author : Bulent Gercek <bulentgercek@gmail.com>
 */
var slideDuration = 250;

/**
 * panelin durumunu degistir
 */
$(document).ready(function() {
	var paneWidth = $('#paneDiv').width() + 'px';
	var paneState = sessionPaneState;
	
	if (paneState == 'up') {
		//alert('Up Controls');
		$('#paneDiv').hide();
		$('#paneDivTd').width('0px');
	} else if (paneState = 'down') {
		//alert('Down Controls');
		$('#paneDiv').show();
		$('#paneDivTd').width(paneWidth);
	}

	$('#paneButton').click(function() {
		if (paneState == 'down') {
			$('#paneDiv')
				.slideUp({queue: false, duration: slideDuration})
				.fadeOut(slideDuration+10);
			$('#paneDivTd').delay(slideDuration+10)
				.animate({width: '0px'});
			paneState = 'up';
			
		} else {
			$('#paneDivTd').animate({width: paneWidth});
			$('#paneDiv')
				.delay(slideDuration+100)
				.slideDown(slideDuration);
			paneState = 'down';
		}
		
		$.ajax({
			type : 'GET',
			url : 'WEB-INF/classes/PaneControl.class.php',
			data: 'process=set&state=' + paneState,
			success : function(data){
				//alert(data);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert('There was an error while accesing paneMotion Ajax.');
			}
		});
	});
});
