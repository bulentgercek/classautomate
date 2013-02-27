<?php
/**
 * Tab Control
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class Tab
{
	/**
	 * Bu class'in yedegi
	 *
	 * @access private
	 * @var object
	 */
	private static $_instance;
	/**
	 * aktif tab
	 *
	 * @var string
	 */
	public $activeTab = NULL;
	/**
	 * url kontrolu / json karsilastirmasi sonucu
	 *
	 * @var string
	 */
	public $checkVarsResult = true;
	/**
	 * tab listesi
	 *
	 * @var array
	 */
	private $_tabs;
	/**
	 * secilen tab
	 *
	 * @var array
	 */
	private $_currentTab;
	/**
	 * secilen tab
	 *
	 * @var array
	 */
	private $_actionList;
	/**
	 * singleton nesneleri tanimla
	 *
	 * @var object
	 */
	public $Session;
	public $Setting;
	public $Execute;
	/**
	 * dil json dosyasi
	 * url json dosyasi
	 *
	 * @vars arrays
	 */
	public $languageJSON;
	public $urlVarsJSON;
	
	/**
	 * classın construct methodu tek seferlik icindir
	 *
	 * @return void
	 */
	public function __construct()
	{
		/**
		 * gerekli class objeleri
		 */
		$this->Session = Session::classCache();
		$this->Setting = Setting::classCache();
		$this->Execute = Execute::classCache();
		/**
		 * database'e baglanip dil dosyasini degiskene esitle
		 */
		$this->Setting->setInterfaceLang('db');
		$this->languageJSON = $this->Setting->getInterfaceLang();
		/**
		 * class ilk ve tek kez cagirildiginda
		 * tab listesini session'dan al ve array'e yerlestir
		 */
		$this->setTabsDoCommands();
	}

	/**
	 * Singleton fonksiyonu
	 *
	 * @access public
	 * @return object
	 */
	public static function classCache()
	{
		if (!self::$_instance) {
			self::$_instance = new Tab();
		}
		return self::$_instance;
	}

	/**
	 * tab bilgisi
	 *
	 * @return array
	 */
	public function getTabs()
	{
		return $this->_tabs;
	}

	/**
	 * tablari session bilgisinden al
	 *
	 * @return void
	 */
	public function setTabsDoCommands()
	{
		/**
		 * kullanici bilgisinde yer alan ',' ile ayrilmis tablari parcala
		 */
		$dbTabs = explode(',', $this->Setting->getTabs());
		/**
		 * tab degiskenini veritabani ve dil dosyasinda ki verilerle birlestirerek esitle
		 */
		foreach ($dbTabs as $value) {
			$expTabValue = explode("&", $value);
			$expPositionValue = explode("position=", $value);
			$expMainSelectValue = explode("mainSelect=", $value);

			if ($expTabValue[0] == "app_person_add" || $expTabValue[0] == "app_person_update")
				$titleHeader = "title_" . $expPositionValue[1];
			else if ($expTabValue[0] == "app_accountant")
				$titleHeader = "title_" . $expMainSelectValue[1];
			else 
				$titleHeader = "title";
			
			$this->_tabs[$value] = $this->languageJSON->classautomate->$expTabValue[0]->$titleHeader;
		}

		/**
		 * url ve json dosyasini karsilastir currentTab'i belirle
		 */
		if ($this->checkVars()) {
			$this->_currentTab = $_GET['tab'];
		} else{
			$this->_currentTab = $this->urlVarsJSON->classautomate->urlTabError;
		}
		/**
		 * secilen tab user tablosunda yok ise
		 * aktif tab uygulamasi yap
		 */
		$this->checkSetActiveTab();
	}

	/**
	 * url'i ayristir
	 *
	 * @return boolean
	 */
	public function checkVars()
	{
		/**
		 * [url_variables].json dosyasini oku.
		 * url degiskenlerini array'e gonder
		 */
		$this->urlVarsJSON = $this->Setting->getUrlVariables();
		/**
		 * adres cubugunda tab verisi var mi?
		 */
		if (strlen($_GET['tab']) > 0) {
			/**
			 * istenen tab degiskeni var mi?
			 */
			if ($this->urlVarsJSON->classautomate->tab->$_GET['tab']) {
				$getVars = $this->urlVarsJSON->classautomate->tab->$_GET['tab']->get;
				$postVars = $this->urlVarsJSON->classautomate->tab->$_GET['tab']->post;
				$actionVars = $this->urlVarsJSON->classautomate->tab->$_GET['tab']->action;

				/**
				 * Json dosyasinda 'get' verisi en az 1 tane olmali. Var mi?
				 */
				if ($getVars != NULL) {
					if (debugger("Tab"))
						echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : \$getVars (Olmasi gereken Json tab 'get' verileri)";
					if (debugger("Tab"))
						var_dump($getVars);

					if (debugger("Tab"))
						echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : \$_GET (GET ile gelen veriler)";
					if (debugger("Tab"))
						var_dump($_GET);

					foreach ($getVars as $var) {
						if (!array_key_exists($var, $_GET)) {
							if (debugger("Tab"))
								echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'daki <b>$var</b> degiskeni URL icinde bulunamadi. Adres ihlali!<br>";
							$this->checkVarsResult = false;
						} else {
							if ($_GET["tab"] == "app_person_add" || $_GET["tab"] == "app_person_update") {
								if ($_GET["position"] == "student" || $_GET["position"] == "instructor" || 
									$_GET["position"] == "asistant" || $_GET["position"] == "secretary" || $_GET["position"] == "cleaner") {
									if (debugger("Tab"))
										echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'daki <b>$var</b> degiskeninin degeri person islemlerindendir.<br><br>";
									$this->checkVarsResult = true;
								} else {
									if (debugger("Tab"))
										echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'daki <b>$var</b> degiskeninin degeri person islemlerinden birisi değildir. 
											(orn: student, instructor, vs.)<br><br>";
									$this->checkVarsResult = false;
								}
							}
							
							if ($_GET["tab"] == "app_accountant") {
								if ($_GET["mainSelect"] == "income" || $_GET["mainSelect"] == "expense" || 
									$_GET["mainSelect"] == "profit") {
									if (debugger("Tab"))
										echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'daki <b>$var</b> degiskeninin degeri accountant islemlerindendir.<br><br>";
									$this->checkVarsResult = true;
								} else {
									if (debugger("Tab"))
										echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'daki <b>$var</b> degiskeninin degeri accountant islemlerinden birisi değildir. 
											(orn: income, expense, profit)<br><br>";
									$this->checkVarsResult = false;
								}
							}
						}
					}
				}
				/**
				 * Json dosyasinda 'post' verisi en az 1 tane olmali. Var mi?
				 */
				if ($postVars != NULL) {
					if (debugger("Tab"))
						echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : \$postVars (Olmasi gereken Json tab 'post' verileri)";
					if (debugger("Tab"))
						var_dump($postVars);

					if (debugger("Tab"))
						echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : \$_POST (POST ile gelen veriler)";
					if (debugger("Tab"))
						var_dump($_POST);

					foreach ($postVars as $key => $value) {
						/**
						 * Not : post verisi altinda istenilen post geldigi takdirde
						 * URL_VARIABLES ile dizi gonderebilme secenegi ekledim
						 * lazim olabilir! (2012-06-07)
						 */
						if (gettype($value) == "object") {
							$checkedValue = stdToArray($value);
						} else {
							$checkedValue = $value;
						}

						if (array_key_exists($key, $_POST)) {
							if (debugger("Tab"))
								echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'daki <b>$key</b> degiskeni POST icinde yer alıyor. Islem uygulanıyor.<br>";
							
							$this->Execute->setCommand($checkedValue);
						}
					}
				}
				/**
				 * Json dosyasinda ilgili tab sayfasindaki form sayisi kadar 'action' verisi olmali. Var mi?
				 */
				if ($actionVars != NULL) {
					if (debugger("Tab"))
						echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : \$actionVars (Json tab 'action' verileri)";
					if (debugger("Tab"))
						var_dump($actionVars);

					foreach ($actionVars as $key => $value) {
							if (debugger("Tab"))
								echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : Json'da <b>$key</b> formu icin <b>$value</b> action bilgisi var. Smarty'e ekleniyor.<br>";
							$this->setActionList($key, $value);
					}
				}
			} else {
				if (debugger("Tab"))
					echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : URL icinde ki tab tanimina uyan bir tab JSON'da yok. Adres ihlali!<br>";
				$this->checkVarsResult = false;
			}

		} else {
			if (debugger("Tab"))
				echo "DEBUG : " . getCallingClass() . "->Tab->checkVars() : URL icinde herhangibir tab tanimi bulunamadi. Adres ihlali!<br>";
			$this->checkVarsResult = false;
		}

		return $this->checkVarsResult;
	}

	/**
	 * active tab mi?
	 *
	 * @return void
	 */
	public function checkSetActiveTab()
	{
		/** active tab kontrol degiskeni yarat */
		$activeTabStatus = false;
		
		/**
		 * url kontrolu sonucu olumlu mu?
		 * olumlu ise aktif tab array'ini olustur
		 */
		if ($this->checkVarsResult) {
			/**
			 * student, instructor vs. durumları için genel 'person' kavramı kullanılıyor
			 * ayirt edici degisken 'position'.
			 * dolayisiyla tab dizisindeki 'key' degerlendirmesini yaparken '&' isaretinden itibaren
			 * kesilerek degiskene aktarilmasi gerekiyor
			 */
			foreach ($this->_tabs as $key => $value) {
				$expKey = explode("&", $key);
				$cleanTabs[$expKey[0]]= $value;
				
				if ($expKey[0] == "app_person_add" || $expKey[0] == "app_person_update") {
					$expPersonPosition = explode("=", $expKey[1]);
					$cleanTabs[$expKey[0]] = array ($expPersonPosition[0] => $expPersonPosition[1]);
				}
				if ($expKey[0] == "app_accountant") {
					$expMainSelect = explode("=", $expKey[1]);
					$cleanTabs[$expKey[0]] = array ($expMainSelect[0] => $expMainSelect[1]);
				}
			}

			if (!array_key_exists($_GET["tab"], $cleanTabs)) {
				$activeTabStatus = true;
				$this->activeTab['url'] = 'main.php?tab=' . $_GET["tab"];

				/**
				 * eger adres cubugunda code degiskeni var ise
				 * kod numarasinin geldigi nesnenin NAME bilgisi tab'a yazdiriliyor
				 */
				if ($_GET['code'] != "") {
					$expTab = explode("_", $_GET['tab']);
					$object = $expTab['1'];

					switch ($object) {
						case 'classroom':
						case 'content':
						case 'program':
						case 'saloon':
						case 'holiday':
							$titleKey = "title";
							break;
						case 'person':
							$titleKey = "title_" . $_GET["position"];
							break;
						case 'accountant':
							$titleKey = "title_" . $_GET['mainSelect'];
					}
					
				} else
					$titleKey = "title";
			
			} else if (array_key_exists($_GET["tab"], $cleanTabs)) {
			
				if ($_GET["tab"] == "app_person_add" || $_GET["tab"] == "app_person_update") {
					
					foreach($cleanTabs as $key => $value) {

							if ($key == "app_person_add" || $key == "app_person_update") {
								
								if ($value["position"] != $_GET["position"]) {
									$activeTabStatus = true;
									$this->activeTab['url'] = 'main.php?tab=' . $_GET["tab"] . "&position=" . $_GET["position"];
									$titleKey = "title_" . $_GET["position"];
								}
							}
					
					}
				}
			}

			/** active tab onaylandi ise */
			if ($activeTabStatus) {
				/**
				 * tab'a metin bilgisi aktarimi
				 */
				$this->activeTab['value'] = $this->languageJSON->classautomate->$_GET["tab"]->$titleKey;
			}
		}
	}

	/**
	 * secilen tabi dondur
	 *
	 * @return string
	 */
	public function getCurrentTab()
	{
		return $this->_currentTab;
	}

	/**
	 * action listesine gonderilen veriyi isle
	 *
	 * @return array
	 */
	public function setActionList($actionKey, $actionValue)
	{
		$this->_actionList[$actionKey] = $actionValue;
	}

	/**
	 * sayfa icindeki formlarin action listesini dondur
	 *
	 * @return array
	 */
	public function getActionList()
	{
		return $this->_actionList;
	}

	/**
	 * gonderilen formun gidecegi tab'i dondurur
	 *
	 * @return string
	 */
	public function getFormAction()
	{
		return $this->_actionList[ getTransferInfo('tf', $_POST) ];
	}

}
