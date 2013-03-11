/**
 * classautomate - tablo kontrolu
 *
 * @author : Bulent Gercek <bulentgercek@gmail.com>
 */
$.tableControl = {

	addRow: function(itemId,newContent) {
		$("#"+itemId).append(newContent);
		$("#"+itemId+" tbody tr:last").hide().fadeTo('fast', 1);
	},
	
	removeRow: function(itemId,tableRowIndex) {
		$("#"+itemId+" tbody tr:eq(" + tableRowIndex + ")").fadeTo('fast', 0, function () { 
        		$(this).remove();
		});
	},
	
	getTableRowOfObj: function(docObject) {
		return $('#' + docObject).closest('tr').index();
	}

};
