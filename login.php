<?php
/**
 * classautomate - login tablosu
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
/**
 * sorgu alanini goster
 */
setExtSmartyVars('login_ask','inline');
/**
 * giris yazisi
 */
$resultText = $languageJSON->classautomate->login->makeLogin;
/**
 * daha once giris yapilmaya calisilmis mi?
 */
if ($Session->get('logResult') != '' || $_POST['login_username'] != '') {
	/**
	 * girilen kullanici adi form icine tekrar gonderiliyor
	 */
	setExtSmartyVars('login_formUsername', $_POST['login_username']);
	/**
	 * hali hazirda giris yapilmis mi?
	 */
	if ($Session->get('logResult') != 'true') {
		/**
		 * sessionlarda giris yapildigi tespit edilmedi
		 * girilen form bilgileri kullanici adi ve sifresi kontrol ediliyor
		 *
		 * @return array $login_info
		 */
		$Login = Login::classCache();
		$loginInfo = $Login->check($_POST['login_username'], md5Converter($_POST['login_password']) );
		/** 
		 * classautomate / login_history tablosuna kaydet
		 */
		$Login->setLoginHistory();
		/** 
		 * login bilgilerini session degiskenlerine gonder
		 */	
		$Login->setSessionVars();
	}
	/**
	 * giris basarili ise,
	 */
	if ($loginInfo[logResult] != 'false') {
		/**
		 * hosgeldiniz metnini kullanici bilgilendirmeye ekle
		 *
		 * @var string $resultText
		 */
		$resultText = $languageJSON->classautomate->login->welcome . ', ' . $Session->get('fullUsername');
		/**
		 * sorgu alanlarini kaldir
	 	 */		
		setExtSmartyVars('login_ask','none');
		/**
		 * ENTER form dugmesine mi tiklandi?
	 	 */	
		if ($_POST['login_formButton'] == 'enter') {
			/**
			 * anasayfaya yonlendir
			 */	
			setExtSmartyVars('index_loginMeta' , "<meta http-equiv=\"Refresh\" content=\"0;url=main.php?tab=app_welcome\">");
		/**
		 * LOGOUT form dugmesine mi tiklandi?
		 */	
		} else if ($_POST['login_formButton'] == 'logout') {
			/**
			 * session'i yoket
			 */	
			$Session->destroy();
			/**
			 * index.php sayfasina yonlendir
			 */	
			setExtSmartyVars('index_loginMeta' , "<meta http-equiv=\"Refresh\" content=\"0;url=index.php\">");
		}
		/**
		 * giris dugmesini kaldir
	 	 */
		setExtSmartyVars('login_askSubmit','none');
		/**
		 * login sonrasi butonlari goster
	 	 */
		setExtSmartyVars('login_askEnter','show');
		
	} else {
		/**
		 * giris basarisiz ise,
		 *
		 * @var $resultText;
		 */		
		$resultText = $languageJSON->classautomate->login->wrong;
	}
}
/**
 * kullanici bilgilendirme degiskeni
 */	
setExtSmartyVars('login_info', $resultText);
?>