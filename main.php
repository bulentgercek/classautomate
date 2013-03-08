<?php
/**
 * classautomate - index
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// GIRIS BOLUMU ///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
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
define('KINT_THE_DEBUGGER', 'WEB-INF/lib/kint/Kint.class.php');
/**
 * baslangic icin temel/kritik fonksiyonlar
 *
 */
require KINT_THE_DEBUGGER;
require START_FUNCTION_FILE;
/**
 * sayfanin tamamlanma suresini ogrenmek icin sayaci baslatalim
 */
PageGenerateTimer::startTime();
/**
 * session yaratiliyor
 *
 */
$Session = Session::classCache();
$Session->start();
/**
 * Db objesi yaratiliyor
 */
$Db = Db::classCache();

/**
 * Setting objesi yaratiliyor
 * dil icin browser ayari okunuyor
 * gerekli XML verisi global degiskene cache'lenerek ataniyor
 *
 * @var string $language_code
 * @var array $languageJSON
 */
$Setting = Setting::classCache();
$Setting->setInterfaceLang('db');
$languageJSON = $Setting->getInterfaceLang();
/**
 * [url_variables].json dosyasi okunuyor
 * url degiskenleri array'e gonderiliyor
 */
$urlVarsJSON = $Setting->getUrlVariables();
/**
 * session zamanlayicisi calistiriliyor
 *
 */
$Session->setSessionTimer($Setting->getTimer());
/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * tablar olusturuluyor
 * GET ve POST verileri Json veritabani ile karsilastiriliyor
 * uyumsuzluk varsa Json'daki urlTabError sayfasi goruntuleniyor
 * yoksa JSON'daki komutlara gore islem baslatiliyor ve
 * form sonrasi action olarak JSON'da ki tab ataniyor ve
 * guncel tab olarak URL'den aliniyor
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @var string currentTab
 */
$Tab = Tab::classCache();
$tabs = $Tab->getTabs();
$currentTab = $Tab->getCurrentTab();
$formAction = $Tab->getFormAction();
/**
 * eger form islemi olursa refresh edilmesi gerektigi gonderilecektir
 * iste burada da refresh komutu olup olmadigina bakiliyor
 * ve degerlendirme sonucuna gore REFRESH metasi ile sayfa URL JSON dosyasindaki
 * action tab adresine gonderiliyor
 */
if (getRefresh() == 'true') {
		setRefresh('false');
		$refreshUrl = getScriptName() . ".php?tab=" . $formAction;
		/**
		 * PERSON sistemi için pozisyon eklemesi zorunludur, yoksa URL bulunamadı mesajı gider
		 */
		if ($formAction == "app_person_add")
				$refreshUrl .= "&position=" . $_POST["position"];
		if ($formAction == "app_person_update")
				$refreshUrl .= "&code=" . $_POST["code"] . "&position=" . $_POST["position"];
		/**
		 * adres alanına veri eklemesi zorunlu tablar burada hazirlaniyor, yoksa URL bulunamadı mesajı gider
		 */
		if ($formAction == "app_accountant")
				$refreshUrl .= "&mainSelect=" . $_POST["mainSelect"];
		if ($formAction == "app_rollcall")
				$refreshUrl .= "&classroom=all&dayTime=&date=" . $_POST["date"];
		if ($formAction == "app_content")
				$refreshUrl .= '&code=' . $_GET['code'] . '&orderby=' . $_GET['orderby'];


		echo "<html><meta http-equiv=\"Refresh\" content=\"0;url=" . $refreshUrl . "\">";
} else {
		/**
		 * siniflarin donem sureleri kontrol ediliyor
		 * zamani gecen sinif bosaltilip kapatiliyor
		 */
		School::classCache()->checkClassroomsLimits();
		/**
		 * History objesi yaratiliyor
		 */
		$History = History::classCache();
		/**
		 * tablar icin gereken smarty degiskenleri tanimlaniyor
		 */
		setExtSmartyVars('tabs', $tabs);
		setExtSmartyVars('currentTab', $currentTab);
		setExtSmartyVars('activeTab', $Tab->activeTab);
		if ($currentTab == "app_person_add" || $currentTab == "app_person_update") {
				setExtSmartyVars('personPosition', $_GET["position"]);
		}
		setExtSmartyVars('paneState', $Session->get('paneState'));
		/**
		 * tab disindaki smarty template'leri icin array hazirlaniyor
		 * sayfalar include ediliyor
		 *
		 * @var array @pageAreas
		 */
		$contentOther = array('main_header', 'main_dailypane', 'main_footer', $currentTab);
		foreach ($contentOther as $value) {
				if (substr($value, 0, 3) == 'app') {
						$value = 'apps/' . $value;
				}
				include $value . '.php';
		}
		/**
		 * sayfa tema yolu belirleniyor
		 */
		$theme = $Setting->getTheme();
		$themePath = 'themes/' . $theme . '/';
		GlobalVar::set("themePath", $themePath);
		/**
		 * giris bolumunun ve sayfanin sonu
		 * acik database varsa kapatiliyor.
		 *
		 */
		$Db->close();
		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////// SMARTY BOLUMU //////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		/**
		 * Smarty.class.php cagiriliyor
		 *
		 */
		require SMARTY_CLASS_FILE;
		/**
		 * smarty objesi yaratiliyor
		 *
		 */
		$Smarty = new Smarty;
		/**
		 * smarty temel klasorleri belirleniyor
		 *
		 */
		setSmartyFolders($Smarty);
		/**
		 * aktif sayfanin template'i cagiriliyor
		 *
		 */
		setSmartyVars($Smarty, $languageJSON, getScriptName());
		/**
		 * sayfadaki tablolarin($contentOther) template'leri cagiriliyor
		 *
		 */
		for ($i = 0; $i < count($contentOther); $i++) {
				/**
				 * sayfa tablolarinin smarty degiskenleri
				 * $pageAreas array'ine gore olusturuluyor
				 *
				 */
				setSmartyVars($Smarty, $languageJSON, $contentOther[$i]);
				/**
				 * sayfa tablolarinin templateleri gosteriliyor
				 *
				 * dikkat : alt sayfalar icin smarty include'da degisken adi
				 * 'page_area' adinin yanina '_area' konularak yazilmali
				 *
				 */
				$Smarty->assign($contentOther[$i] . '_area', $contentOther[$i] . '.tpl');
		}
		/**
		 * tab alani smarty'e gonderiliyor
		 */
		$Smarty->assign('main_center_area', $currentTab . '.tpl');
		/**
		 * diger tablolardan (pageAreas) gonderilen
		 * degiskenler ve degerleri smarty'e aktariliyor
		 *
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
		 *
		 */
		$Smarty->display(getScriptName() . '.tpl');

		/**
		 * sayfanin tamamlanma suresini goster
		 */
		PageGenerateTimer::endTime();
}
?>
