<?php

/**
 * Login : Singleton / classCache()
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
interface LoginLayout
{
		public function check($loginUsername, $loginPassword);
		public function setLoginHistory();
		public function setSessionVars();
		public function setCookieVars();
}

class Login implements LoginLayout
{

		/**
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 * formdan gelen kullanici adi
		 *
		 * @var string
		 */
		private $_formUsername;

		/**
		 * login arama sonucu
		 *
		 * @var array
		 */
		private $_login;

		/**
		 * construct yapilamaz
		 * 
		 */
		private function __construct()
		{
				
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
						self::$_instance = new Login();
				}
				return self::$_instance;
		}
		/**
		 * login kontrol
		 * 
		 * @return array
		 */
		public function check($loginUsername, $loginPassword)
		{
				/**
				 * formdan kullanici adi dolu mu geldi?
				 * 
				 */
				$this->_formUsername = $loginUsername;

				if ($this->_formUsername != "") {
						/**
						 * classautomate'e baglan
						 * school_users'dan formdan girilen kullanici ismini bul
						 * 
						 */
						$Db = Db::classCache();
						$Db->connect('classautomate');
						$Db->selectSql(array('table' => 'school_users', 'where' => "username = '" . $loginUsername . "'"));
						$userResult = $Db->getRows();
						/**
						 * login isimli array'e sonuc donmus mu?
						 * donduyse sonuc arrayini duzenle
						 * 
						 */
						if ($userResult != NULL || count($userResult) > 0) {

								$fullLogResult = 'user_ok';

								($userResult[password] == $loginPassword ? $fullLogResult .= ',pass_ok' : $fullLogResult .= ',no_pass');
						} else {

								$fullLogResult = 'no_user';

								$userResult[username] = $loginUsername;
						}
						/**
						 * kullanici ismi ve sifre tamam ise true
						 * birisi bile yanlissa false dondur
						 * bunu da array'e isle
						 *
						 * @var array self::$_login;
						 */
						$this->_login[username] = $userResult[username];
						$this->_login[dbName] = $userResult[school];
						$this->_login[ip] = $this->_getIp();
						$this->_login[logDateTime] = $this->_getDateTime();
						$this->_login[fullUsername] = $userResult[fullName];
						$this->_login[refresh] = 'false';
						$this->_login[paneState] = '';

						if ($fullLogResult == 'user_ok,pass_ok') {

								if (debugger("Login"))
										echo 'DEBUG : ' . getCallingClass() . '->Login->check() kontrol sonucu ' . $fullLogResult . '<br>';
								$this->_login[logResult] = 'true';
						} else {

								$this->_login[logResult] = 'false';
						}
						/**
						 * login tam aciklama degiskenini array'e ekle
						 * 
						 */
						$this->_login[fullLogResult] = $fullLogResult;
						/**
						 * sonucu dondur
						 * 
						 */
						return $this->_login;
				}
		}
		/**
		 * login hareketi veritabanina kaydediliyor
		 * 
		 * @return void
		 */
		public function setLoginHistory()
		{
				if ($this->_formUsername != '') {
						$Db = Db::classCache();
						$Db->setSql("INSERT INTO `classautomate`.`login_history` (`dateTime`, `dbName`, `username`, `clientIp`, `result`)" .
								"VALUES ('" . $this->_login[logDateTime] . "', '" . $this->_login[dbName] .
								"', '" . $this->_login[username] . "', '" . $this->_login[ip] . "', '" . $this->_login[fullLogResult] . "')");
				}
		}
		/**
		 * login bilgileri session degiskenlerine gonderiliyor
		 * 
		 * @return array
		 */
		public function setSessionVars()
		{
				if ($this->_login[logResult] == 'true') {

						$Session = Session::classCache();

						$Session->set('username', $this->_login[username]);
						$Session->set('dbName', $this->_login[dbName]);
						$Session->set('ip', $this->_login[ip]);
						$Session->set('logDateTime', $this->_login[logDateTime]);
						$Session->set('fullUsername', $this->_login[fullUsername]);
						$Session->set('logResult', $this->_login[logResult]);
						$Session->set('fullLogResult', $this->_login[fullLogResult]);
						$Session->set('refresh', 'false');
						$Session->set('paneState', $this->_login[paneState]);
				}
		}
		/**
		 * login bilgileri cookie degiskenlerine gonderiliyor
		 * 
		 * @return array
		 */
		public function setCookieVars()
		{
				if ($this->_login[logResult] == 'true') {

						$Session = $Session->classCache();

						$Session->setCookieTimer(30);
						$Session->setCookie('username', $this->_login[username]);
						$Session->setCookie('dbName', $this->_login[dbName]);
						$Session->setCookie('ip', $this->_login[ip]);
						$Session->setCookie('logDateTime', $this->_login[logDateTime]);
						$Session->setCookie('fullUsername', $this->_login[fullUsername]);
						$Session->setCookie('logResult', $this->_login[logResult]);
						$Session->setCookie('fullLogResult', $this->_login[fullLogResult]);
						$Session->setCookie('refresh', 'false');
						$Session->setCookie('paneState', $this->_login[paneState]);
				}
		}
		/**
		 * kullanicinin ip adresi aliniyor
		 * 
		 * @return string
		 */
		private function _getIp()
		{
				return getClientIp();
		}
		/**
		 * kullanicinin lokal tarihi aliniyor
		 * 
		 * @return string
		 */
		private function _getDateTime()
		{
				return getClientDateTime('%Y-%m-%d %H:%M:%S');
		}
}

?>