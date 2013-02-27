<?php

/**
 * classautomate - index
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package classautomate
 */
////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// GIRIS BOLUMU //////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * index sabitleri
 * temel baslangic fonksiyonlari ve
 * smarty class dosyasi
 *
 * @var const START_FUNCTION_FILE
 * @var const SMARTY_CLASS_FILE
 */
define('START_FUNCTION_FILE', 'WEB-INF/classes/Start.function.php');
define('SMARTY_CLASS_FILE', 'WEB-INF/lib/smarty/libs/Smarty.class.php');
/**
 * baslangic icin temel/kritik fonksiyonlar START_FUNCTION_FILE ile cagiriliyor
 */
require START_FUNCTION_FILE;
/**
 * session yaratiliyor
 */
$Session = Session::classCache();
$Session->start();
/**
 * session baslangic zamani set ediliyor
 */
$Session->set('timeStart', time());
/**
 * Db objesi yaratiliyor
 */
$Db = Db::classCache();
/**
 * dil icin browser ayari cache'lenerek okunuyor
 * gerekli XML verisi global degiskene cache'lenerek ataniyor
 *
 * @var string $language_code
 * @var array $languageJSON
 */
$Setting = Setting::classCache();
$Setting->setInterfaceLang('browser');
$languageJSON = $Setting->getInterfaceLang();
/**
 * kullanicinin bulundugu yere gore
 * saat farki aliniyor
 */
if (isset($_POST['timeZoneOffset'])) {
		$Session->set('timeZone', $_POST['timeZoneOffset']);
}
/**
 * array'e gore tab disindaki sayfalar include ediliyor
 *
 * @var array @contentOther
 */
$pageAreas = array('login');
foreach ($pageAreas as $value) {
		include $value . '.php';
}
/**
 * tema atamasi yapiliyor
 */
$theme = $Setting->getTheme();
$themePath = 'themes/' . $theme . '/';
GlobalVar::set("themePath", $themePath);
/**
 * giris bolumunun ve sayfanin sonu
 * acik database varsa kapatiliyor.
 */
$Db->close();
////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// SMARTY BOLUMU /////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Smarty.class.php cagiriliyor
 */
require SMARTY_CLASS_FILE;
/**
 * smarty objesi yaratiliyor
 */
$Smarty = new Smarty;
/**
 * smarty temel klasorleri belirleniyor
 */
setSmartyFolders($Smarty);
/**
 * aktif sayfanin template'i cagiriliyor
 */
setSmartyVars($Smarty, $languageJSON, getScriptName());
/**
 * sayfadaki tablolarin(pageAreas) template'leri cagiriliyor
 */
for ($i = 0; $i < count($pageAreas); $i++) {
		/**
		 * sayfa tablolarinin smarty degiskenleri 
		 * $pageAreas array'ine gore olusturuluyor
		 */
		setSmartyVars($Smarty, $languageJSON, $pageAreas[$i]);
		/**
		 * sayfa tablolarinin templateleri gosteriliyor
		 */
		$Smarty->assign($pageAreas[$i] . '_area', $pageAreas[$i] . '.tpl');
}
/**
 * diger tablolardan (pageAreas) gonderilen
 * degiskenler ve degerleri smarty'e aktariliyor
 */
$extSmartyVars = GlobalVar::get(extSmartyVars);

if (count($extSmartyVars) > 0) {
		foreach ($extSmartyVars as $var => $value) {
				$Smarty->assign($var, $value);
		}
}
/**
 * tema degiskenleri smarty'e gonderiliyor
 */
$Smarty->assign('theme', $theme);
$Smarty->assign('themePath', $themePath);
/**
 * yuklenen tum template'ler gosteriliyor
 */
$Smarty->display(getScriptName() . '.tpl');
?>
