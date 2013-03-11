/**
 * classautomate - dayTimeTable
 *
 * @author : Bulent Gercek <bulentgercek@gmail.com>
 */

$.dayTimeTableControl = {
	
	addDayTime: function(day, time, endTime) {
		var classroom = $.url().param('code');
		$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

		$.ajax({
			type : 'GET',
			url : 'WEB-INF/classes/DayTimeTableControl.class.php',
			data: 'process=add&classroom=' + classroom + '&day=' + day + '&time=' + time + '&endTime=' + endTime,
			async: false,
			success: function(data) {
				//$.dayTimeTableControl.htmlOutput(data);
				$.ajaxValueSet(data);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert('There was an error while accesing DayTimeTableControl Ajax.');
			}
		});
		
		return $.ajaxValueGet();
	},
	
	removeDayTime: function(object) {
		var classroom = $.url().param('code');
		var code = $(object).attr('id').split('_')[4];

		$.ajax({
			type : 'GET',
			url : 'WEB-INF/classes/DayTimeTableControl.class.php',
			data: 'process=delete&classroom=' + classroom + '&code=' + code,
			async: false,
			success : function(data){
				//$.dayTimeTableControl.htmlOutput(data);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert('There was an error while accesing DayTimeTableControl Ajax.');
			}
		});

		return $.ajaxValueGet();
	},
	
	htmlOutput: function(data) {
		window.document.write( $.trim(data) );
	},
};
