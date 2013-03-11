<?php

/**
 * Session : Singleton / classCache()
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Session
{

		/**
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 * session name 
		 * 
		 */

		const SESSION_NAME = 'CLASSAUTOID';
		/**
		 * session sonu yonlendirilecek web adresi 
		 * 
		 */
		const SESSION_END_URL = 'index.php';

		/**
		 * cookie icin timer 
		 * 
		 */
		private static $_cookieTimer;

		/**
		 * cookie icin path
		 * 
		 */
		private static $_cookiePath = '/';

		/**
		 * construct metodu kullanilamaz 
		 * 
		 * @return void 
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
						self::$_instance = new Session();
				}
				return self::$_instance;
		}
		/**
		 * session islemini a�ar 
		 * 
		 * @return void 
		 */
		public function start()
		{
				/**
				 * session acilmadan once zamani global'e not eder.
				 * 
				 */
				$this->set('timeStart', time());
				session_start();
		}
		/**
		 * yaratilan SESSION bu komut ile kapatiliyor
		 * browser SESSION_END_URL'e yonlendiriliyor
		 *
		 * @return void
		 */
		public function destroy()
		{
				session_unset();
				session_destroy();
				$scriptName = getScriptName() . '.php';
				if ($scriptName != self::SESSION_END_URL)
						header('Location: ' . self::SESSION_END_URL);
		}
		/**
		 * formdan veya baska bir sekilde $offsetValue degiskeni ile iletilen
		 * saat farki degiskeni ile kullanicinin bulundugu bolgenin 
		 * zaman farki bulunarak lokal saat bulunuyor
		 * 
		 * @return void
		 */
		public function setTimeZone($offsetValue)
		{
				$serverTimeZoneOffset = (date('O') / 100 * 60 * 60);
				$clientTimeZoneOffset = $offsetValue;
				$serverTime = time();
				$serverClientTimeDifference = $clientTimeZoneOffset - $serverTimeZoneOffset;
				$finalHourDifference = $serverClientTimeDifference / (60 * 60);
				$this->set('timeZone', $finalHourDifference);
		}
		/**
		 * TimeZone'u geri dondurur
		 *
		 * @return string
		 */
		public function getTimeZone()
		{
				return $this->get('timeZone');
		}
		/**
		 * yaratilan SESSION icin zamanlama metodu
		 *
		 * @return void
		 */
		public function setSessionTimer($limit)
		{
				$startTime = $this->get('timeStart');
				$endTime = time();

				$itemStartDate = $startTime . '<br>';
				$itemEndDate = $endTime . '<br>';

				$timeLeft = $itemEndDate - $itemStartDate;

				if ($timeLeft > 0) {
						$aDayInSecs = 24 * 60 * 60;
						$days = $timeLeft / $aDayInSecs;
						$days = intval($Days);

						$timeLeft = $timeLeft - ($days * $aDayInSecs);
						$hours = $timeLeft / (60 * 60);
						$hours = intval($hours);
						$timeLeft = $timeLeft - ($hours * 60 * 60);

						$minutes = $timeLeft / 60;
						$minutes = intval($minutes);
						$timeLeft = $timeLeft - ($minutes * 60);

						$seconds = $timeLeft;
						$seconds = intval($seconds);

						$timeLeft = $timeLeft - ($seconds / 60 * 60 );
						$milliSeconds = $timeLeft;
				}

				/**
				 * SESSION ZAMANLAMAYI BITIRIYORUM
				 * $Minute : Dakika cinsinden, $Second : Saniye cinsinden sonuc veriyor
				 * Sureyi $limit degiskeninden aliyor
				 *
				 */
				$timeOut = $limit;

				/**
				 * Istersek bu sekilde zamanlamayi saniye cinsinden ekrana yazdirabiliyoruz : echo $Seconds . ' Seconds ' ;
				 */
				//echo 'Gecen Saniye : ' . $seconds . ' Kalan Saniye : ' . ($timeOut - $seconds);
				/**
				 *
				 * Zamanin dolup dolmadigi kontrol ediliyor. Dolmussa destroy metodu cagiriliyor
				 *
				 */
				if ($seconds < $timeOut)
						$this->set('timeStart', $endTime);
				else
						$this->destroy();
		}
		/**
		 * cookie zamanlayicisi
		 *
		 * @param string saniye
		 */
		public function setCookieTimer($limit)
		{
				self::$_cookieTimer = $limit;
		}
		/**
		 * session degiskenleri ataniyor
		 *
		 * @param string $name
		 * @param string $value
		 */
		public function set($name, $value)
		{
				$_SESSION[$name] = $value;
		}
		/**
		 * session degiskenleri ataniyor
		 *
		 * @param string $name
		 */
		public function get($name)
		{
				return $_SESSION[$name];
		}
		/**
		 * cookie yarat
		 *
		 * @param string $name
		 * @param string $value
		 */
		public function setCookie($name, $value)
		{
				setcookie($name, $value, time() + self::$_cookieTimer, self::$_cookiePath);
		}
		/**
		 * cookie bilgilerini cagir
		 *
		 * @param string $name
		 * @return string
		 */
		public function getCookie($name)
		{
				return $_COOKIE[$name];
		}
}

?>