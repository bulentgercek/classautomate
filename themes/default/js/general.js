/**
 * classautomate - sayfa genel javascripleri
 *
 * @author : Bulent Gercek <bulentgercek@gmail.com>
 */
$(function() {
	/**
	 * link submit dugmelerinin 
	 * bagli oldugu formu 
	 * submit etmeye yarayan genel method
	 *
	 * form id formati : herhangibir isim olabilir
	 * form submit button id formati : form id + 'Submit'
	 *
	 * @event click
	 */
	$('[id*="Submit"]').click(function() {
		var buttonName = $(this).attr('id');
		var formName = buttonName.substring(0,buttonName.indexOf('Submit'));
		$('#'+formName).submit();
	});
	
	/** ajax gecici tasiyici degisken */
	var ajaxReturn;
});


$.messages = {
	/**
	 * form kayit formu kontrol mesaj fonksiyonlari
	 */
	correctMessage : function(formId,textSpanId) {
		$('#' + formId + " #" + textSpanId).hide().fadeIn('fast').text(' ');
	},
	
	notEmptyMessage : function(formId,textSpanId) {
		$('#' + formId + " #" + textSpanId).hide().fadeIn('fast').text(' ' + notEmpty);
	},

	notEmailMessage : function(formId,textSpanId) {
		$('#' + formId + " #" + textSpanId).hide().fadeIn('fast').text(' ' + notEmail);
	},	

	notNumbersMessage : function(formId,textSpanId) {
		$('#' + formId + " #" + textSpanId).hide().fadeIn('fast').text(' ' + notNumber);
	},

	notPhoneMessage : function(formId,textSpanId) {
		$('#' + formId + " #" + textSpanId).hide().fadeIn('fast').text(' ' + notPhone);
	}
	
}

/**
 * listeler icin splitter gizleme fonksiyonu
 */
$.disableSplitter = function(listId) {
	$('#' + listId + ' option[value="splitter"]').attr('disabled', true);
};

/**
 * zaman arasini hesaplayan fonksiyon
 */
$.getTimeDiff = function(earlierDate,laterDate) {
    var oDiff = new Object();

    //  Calculate Differences
    //  -------------------------------------------------------------------  //
    var nTotalDiff = laterDate.getTime() - earlierDate.getTime();

    oDiff.days = Math.floor(nTotalDiff / 1000 / 60 / 60 / 24);
    nTotalDiff -= oDiff.days * 1000 * 60 * 60 * 24;

    oDiff.hours = Math.floor(nTotalDiff / 1000 / 60 / 60);
    nTotalDiff -= oDiff.hours * 1000 * 60 * 60;

    oDiff.minutes = Math.floor(nTotalDiff / 1000 / 60);
    nTotalDiff -= oDiff.minutes * 1000 * 60;

    oDiff.seconds = Math.floor(nTotalDiff / 1000);
    //  -------------------------------------------------------------------  //

    //  Format Duration
    //  -------------------------------------------------------------------  //
    //  Format Hours
    var hourtext = '00';
    if (oDiff.days > 0){ hourtext = String(oDiff.days);}
    if (hourtext.length == 1){hourtext = '0' + hourtext};

    //  Format Minutes
    var mintext = '00';
    if (oDiff.minutes > 0){ mintext = String(oDiff.minutes);}
    if (mintext.length == 1) { mintext = '0' + mintext };

    //  Format Seconds
    var sectext = '00';
    if (oDiff.seconds > 0) { sectext = String(oDiff.seconds); }
    if (sectext.length == 1) { sectext = '0' + sectext };

    //  Set Duration
    var sDuration = hourtext + ':' + mintext + ':' + sectext;
    oDiff.duration = sDuration;
    //  -------------------------------------------------------------------  //

    return oDiff;
};

/** 
 * String'i buyuk karaterli harflerden kes ve diziye at 
 */
$.getUpperCaseArray = function(string) {
	
	return string.match(/[A-Z]?[a-z]+|[0-9]+/g);
	
}

/**
 * ajax temp deger tasima fonksiyonlari 
 */
$.ajaxValueSet = function(value) {
	ajaxReturn = $.trim(value);
},

$.ajaxValueGet = function(value) {
	return ajaxReturn;
}

/**
 * degisken varmi yok mu? 
 */
function isset(element) {
	if (element == undefined) return false; else return true;
}

/**
 * ozel karakterleri duzenler ve geri dondurur
 * 
 * @param str
 * @return string
 */
$.cleanEspaceChars = function(str) {
	return str.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
}
