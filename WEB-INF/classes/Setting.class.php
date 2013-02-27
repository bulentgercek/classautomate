<?php

/**
 * Setting : Singleton / classCache()
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Setting
{

		/**
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 * okul setting tablosu
		 *  
		 * @var array
		 */
		private $settingSchool;

		/**
		 * genel kullanici tablosu
		 *  
		 * @var array
		 */
		private $settingUser;

		/**
		 * aktif dil
		 *
		 * @var string
		 */
		private $language;

		/**
		 * arayuz dili
		 *
		 * @var array 
		 */
		private $interfaceLang;

		/**
		 * url degiskenleri
		 *
		 * @var array 
		 */
		private $urlVariables;

		/**
		 * degisiklik listesi
		 *
		 * @var array 
		 */
		private $_determinantsList;

		/**
		 * construct metodu new yapilamaz
		 */
		private function __construct()
		{
				/**
				 * db setting okunmus mu?
				 */
				if ($this->settingSchool == NULL) {
						/**
						 * okul db setting tablosunu okunmamis ise;
						 * okul bilgisini oku ve static array icerisine yerlestir
						 */
						$this->settingSchool = $this->readSchool();

						/** okul setting bilgileri debug ediliyor */
						if (debugger("Setting")) {
								echo 'DEBUG : ' . getCallingClass() . '->Setting->readSchool() : ';
								var_dump($this->settingSchool);
						}
						/**
						 * okul db setting tablosunu okunmamis ise;
						 * kullanici bilgisini oku ve static array icerisine yerlestir
						 */
						$this->settingUser = $this->readUser();

						/** kullanıcı bilgileri debug ediliyor */
						if (debugger("Setting")) {
								echo 'DEBUG : ' . getCallingClass() . '->Setting->readUser() : ';
								var_dump($this->settingUser);
						}
				}
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
						self::$_instance = new Setting();
				}
				return self::$_instance;
		}
		/**
		 * Okulun setting tablosundan veriler okunuyor
		 *
		 * @return array
		 */
		public function readSchool()
		{
				/**
				 * session bilgisi acilmis mi?
				 * acilmadi ise okul settings bilgisi okunmayacak
				 *
				 */
				if (Session::classCache()->get('dbName') != '') {

						Db::classCache()->connect(Session::classCache()->get('dbName'));
						/**
						 * veritabanindan setting_code'a gore setting tablosu okunuyor
						 */
						Db::classCache()->selectSql(array('table' => 'setting', 'orderby' => "'code' ASC"));
						/**
						 * bilgiler okunuyor
						 */
						return Db::classCache()->getRows('rowBase');
				}
		}
		/**
		 * classautomate / user tablosundan veriler okunuyor
		 *
		 * @return array
		 */
		public function readUser()
		{
				/**
				 * user bilgileri ancak login gerceklestiginde okunacak
				 *
				 */
				if (Session::classCache()->get('username') != '') {

						Db::classCache()->connect('classautomate');
						/**
						 * classautomate veritabanindan user'a gore setting tablosu okunuyor
						 */
						Db::classCache()->selectSql(array('table' => 'school_users', 'where' => "username = '" . Session::classCache()->get('username') . "'"));
						/**
						 * bilgiler okunuyor
						 */
						return Db::classCache()->getRows('fieldBase');
				}
		}
		/**
		 * istenilen dil kodu
		 *  
		 * @return String
		 */
		public function getLanguage($langStorage)
		{
				/** dil bilgisinin alinmasi debug ediliyor */
				if (debugger("Setting")) {
						echo 'DEBUG : ' . getCallingClass() . '->Setting->getLanguage() : ';
						var_dump($langStorage);
				}
				if ($langStorage == 'db') {

						/** db dili debug ediliyor */
						if (debugger("Setting"))
								var_dump($this->settingUser['language']);

						return $this->settingUser['language'];
				} else if ($langStorage == 'browser') {

						/** browser dili debug ediliyor */
						if (debugger("Setting"))
								var_dump(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));

						return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				}
		}
		/**
		 * arayuz dili
		 *
		 * @return array
		 */
		public function getInterfaceLang()
		{
				return $this->interfaceLang;
		}
		/**
		 * arayuz dilini xml'den oku
		 * 
		 * @return void
		 */
		public function setInterfaceLang($langStorage)
		{
				$langFileStr = 'languages/language_' . $this->getLanguage($langStorage) . '.json';
				$languageJson = file_get_contents($langFileStr);
				$this->interfaceLang = json_decode($languageJson);
		}
		/**
		 * url degiskenleri arrayini dondur
		 *
		 * @return array
		 */
		public function getUrlVariables()
		{
				if ($this->urlVariables == NULL) {

						$this->setUrlVariables();
						return $this->urlVariables;
				} else {

						return $this->urlVariables;
				}
		}
		/**
		 * url degiskenlerini oku
		 *
		 * @return array
		 */
		public function setUrlVariables()
		{
				$urlVarsFileStr = '[urlVariables].json';
				$urlVarsJson = file_get_contents($urlVarsFileStr);
				$this->urlVariables = json_decode($urlVarsJson);
		}
		/**
		 * takip edilecek degisiklikleri xml'den oku
		 * 
		 * @return void
		 */
		public function setDeterminantsList()
		{
				$determinantsListfileStr = '[determinantsList].json';
				$determinantsListJson = file_get_contents($determinantsListfileStr);
				$this->_determinantsList = json_decode($determinantsListJson);
		}
		/**
		 * takip edilecek degisiklikler arrayini dondur
		 *
		 * @return array
		 */
		public function getDeterminantsList()
		{
				if ($this->_determinantsList == NULL) {

						$this->setDeterminantsList();
						return $this->_determinantsList;
				} else {

						return $this->_determinantsList;
				}
		}
		/**
		 * timer
		 * 
		 * @return string
		 */
		public function getTimer()
		{
				return $this->settingSchool[timer];
		}
		/**
		 * timer
		 * 
		 * @return string
		 */
		public function getCompanyName()
		{
				return $this->settingSchool[companyNameL];
		}
		/**
		 * siniflar listesi liste formati
		 * 
		 * @return array
		 */
		public function getFormat($code)
		{
				$codeName = $code . 'ListFormat';
				/**
				 * tablo ve degisken isimleri format icinden ayristiriliyor
				 */
				$listFormat = $this->settingSchool[$codeName];
				return $listFormat;
		}
		/**
		 * tema ismi
		 * 
		 * @return string
		 */
		public function getTabs()
		{
				return $this->settingUser['tabs'];
		}
		/**
		 * tema ismi
		 * 
		 * @return string
		 */
		public function getTheme()
		{
				if (getScriptName() == 'index')
						return 'index';
				else
						return $this->settingUser['theme'];
		}
		/**
		 * varsayilan odeme metodu
		 * 
		 * @return string
		 */
		public function getDefPayPeriod()
		{
				return $this->settingSchool['defaultPayPeriod'];
		}
		/**
		 * haftanin ilk gununu dondur
		 * 
		 * @return integer
		 */
		public function getFirstDay()
		{
				return $this->settingSchool['firstDay'];
		}
		/**
		 * para birimini dondur
		 * 
		 * @return string
		 */
		public function getCurrency()
		{
				return $this->settingSchool['currency'];
		}
}

?>