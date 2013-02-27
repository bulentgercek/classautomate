/**
 * classautomate - form kontrol
 *
 * @author : Bulent Gercek <bulentgercek@gmail.com>
 */
/**
 * form nesnelerinin bilgilerini
 * kontrol eden ve duzeltme gerekenleri 
 * nesnenin labeli ile haber veren method
 *
 * @param object
 * @return void
 */
function formValidator(objName) {
	var validateText = '';
	var debug = document.getElementById('note');

	/**
	 * submit dugmesi mi tiklandi?
	 */
	if(objName == "submit") {
		var checkResult = true;
		/**
		 * sayfa icinde belirtilmis olan element listesi
		 * ve elementlerin nasil kontrol edilecegini gosteren 
		 * array'i oku (fElements, fElementsV)
		 */
		for (i=0; i<fElements.length; i++) {
			var label = document.getElementById(fElements[i]+'Label');
			var obj = document.getElementById(fElements[i]);
					
			validate = fElementsV[i].split(',');
			validateText = formValidatorChecker(obj,validate);

			if (validateText != '') { 
				formValidateWarner(obj,label,validateText,validate);
				checkResult = false;
			}
		}
		return(checkResult);
	
	/**
	 * form icerinde bulunan elementlerin yazim kontrolunu yap
	 */
	} else {
		var label = document.getElementById(objName+'Label');
		var obj = document.getElementById(objName);
		/**
		 * sayfa icinde belirtilmis olan element listesi
		 * ve elementlerin nasil kontrol edilecegini gosteren 
		 * array'i oku (fElements, fElementsV)
		 */
		for(var i=0; i<fElements.length; i++) {
			if (fElements[i] == objName) {
				validate = fElementsV[i].split(',');
			}
		}
		validateText = formValidatorChecker(obj,validate);
		formValidateWarner(obj,label,validateText,validate);
	}
}
/**
 * formValidator() kontrol sonucunu 
 * html uzerine donduren yardimci metot
 *
 * @param object obj
 * @param object label
 * @param string validateText
 * @return string
 */
function formValidateWarner(obj,label,validateText,validate) {
	if (validateText == '') {
		(validate == 'notNumber' ? label.innerHTML = '' : label.innerHTML = '&#10003');
		label.className = 'bold green';
		obj.className = 'gTextInput';
	
	} else {
		label.innerHTML = ' ' + validateText;
		obj.className = 'gTextInputNeedFix';
		/**
		 * eger notEmpty ozelligi yok ise * (asteriks) koyma
		 */
		if (validate[0] == 'notEmpty')
			label.className = 'requested pureRed';
		else
			label.className = 'pureRed';
	}
}
/**
 * form nesnelerinin bilgilerini
 * kontrol eden ve uyari yazisi donduren
 * formValidator() yardimci metodu
 *
 * @param object obj
 * @param array validate
 * @return string
 */
function formValidatorChecker(obj,validate) {
	var validateText = '';

	for (var i=0; i<validate.length; i++) {
		
		if (validate[i] == 'notEmpty') { 
			if (obj.value == '') {
				validateText += notEmpty;
				return validateText;
			}
		}
		
		else if (validate[i] == 'notEmail') { 
			checkEmail = obj.value;
			if ((checkEmail.indexOf('@') < 0) || ((checkEmail.charAt(checkEmail.length-4) != '.') && (checkEmail.charAt(checkEmail.length-3) != '.'))) {
				validateText += notEmail;
			} 
		}
		
		else if (validate[i] == 'notNumber') {
			if ( isNaN(obj.value) ) { 
				validateText += notNumber;
			}
		}
		
		else if (validate[i] == 'notPhone') {
			if ( isNaN(obj.value) ) {
				validateText += notPhone;
			}
		}
	}
	
	return validateText;
}
