/**
 * classautomate - liste icerik duzenleyicisi
 *
 * @author : Bulent Gercek <bulentgercek@gmail.com>
 */
$(function() {
	/**
	 * listArray array'i genel kullanim icin yaratiliyor
	 */
	listArray = new Array();
});

$.listControl = {
	/**
	 * listenin tanimlandigi method
	 */
	init: function(itemId) {
		listArray[itemId] = new Array();

		var listCount = $("#"+itemId+" option").length;

		$("#"+itemId+" option").each(function(index) {
			value = $(this).val();
			text = $(this).text();
			listArray[itemId][index] = [value,text];
		});
	},
	
	getCount: function(itemId) {
		return listArray[itemId].length;
	},
	
	addItem: function(itemId,newContent) {
		$.listControl.baseControl(itemId,newContent,'add');
	},
	
	restoreItem: function(itemId,itemValue) {
		$.listControl.baseControl(itemId,itemValue,'restore');
	},
	
	restoreAll: function(itemId) {
		$("#"+itemId).empty();
		$.each(listArray[itemId], function(index,value) {
			$("#"+itemId).append('<option value="' + value[0] + '">' + value[1] + '</option>');
		});
	},
	
	removeItem: function(itemId,itemIndex) {
		$.listControl.baseControl(itemId,itemIndex,'remove');

	},
	
	baseControl: function(itemId,itemInfo,command) {
		if (command == 'add') {
			$("#"+itemId).append('<option value="' + itemInfo[0] + '">' + itemInfo[1] + '</option>');
		}
		if (command == 'restore') {
			tempArray = new Array();
			$("#"+itemId+" option").each(function() {
				val = $(this).val();
				text = $(this).text();
				tempArray.push([val,text]);
			});

			$("#"+itemId).empty();
			
			$.each(listArray[itemId], function(index,value) {
			
				if (tempArray.length != 0) {
					$.each(tempArray, function(indexTemp) {
						if (tempArray[indexTemp][0] == value[0]) {
							$("#"+itemId).append('<option value="' + value[0] + '">' + value[1] + '</option>');
							return false;
							
						} else if (value[0] == itemInfo) {
							$("#"+itemId).append('<option value="' + itemInfo + '">' + value[1] + '</option>');
							return false;
						}
					});
				} else {
					if (value[0] == itemInfo) {
							$("#"+itemId).append('<option value="' + itemInfo + '">' + value[1] + '</option>');
					}
				}

			});
		}
		if (command == 'remove') {
			$("#"+itemId+" option").eq(itemInfo).remove();
		}
				
	}
};
