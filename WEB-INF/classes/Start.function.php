<?php
/**
 * Start Metodlari
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
function __autoload($className)
{
		/**
		 * otomatik olarak gelen stringi class olarak yukle
		 */
		if (file_exists("WEB-INF/classes/{$className}.class.php"))
				require ("WEB-INF/classes/{$className}.class.php");
		else
				require ("{$className}.class.php");
}
/**
 * javascript ALERT fonksiyonu
 * $value degeri herhangibir string olabiliyor
 *
 * @return echo javascript alert
 */
function alert($value = "classautomate.com ©2011")
{
		echo "<script> alert(\"" . $value . "\") </script>";
}

/**
 * sayfanin sonunda kodun ne kadar zamanda calistigini donduren class
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class PageGenerateTimer
{

		/**
		 * static degiskenler
		 */
		public static $start, $finish, $totalTime;

		/**
		 * zamanlamayi baslat
		 *
		 * @return void
		 */
		public static function startTime()
		{
				$time = microtime();
				$time = explode(' ', $time);
				$time = $time[1] + $time[0];
				self::$start = $time;
		}
		/**
		 * zamanlamayi dondur
		 *
		 * @return void
		 */
		public static function endTime()
		{
				/**
				 * dil icin browser ayari uygulaniyor
				 * gerekli JSON verisi okunuyor
				 *
				 * @var array $languageJSON
				 */
				$Setting = Setting::classCache();
				$languageJSON = $Setting->getInterfaceLang();

				$time = microtime();
				$time = explode(' ', $time);
				$time = $time[1] + $time[0];
				self::$finish = $time;
				self::$totalTime = round((self::$finish - self::$start), 4);
				if (debugger("PageGenerateTimer")) {
						echo '<br><p align="center">' . $languageJSON->classautomate->main->pageGeneratedSeconds . ' : ';
						echo self::$totalTime . ' (' . $languageJSON->classautomate->main->seconds . ")";
						echo "</p><br><br>";
				}
		}
}

/**
 * stdClass obje arrayini normal diziye ceviren metot
 */
function stdToArray($stdArray)
{
		/** gelen diziyi donguye al */
		foreach ($stdArray as $key => $value) {
				$convArray[$key] = $value;
		}
		return $convArray;
}
/**
 * debugger metodu
 */
function debugger($className)
{
		if (GlobalVar::get("debuggerArray") == NULL) {
				$debuggerFileStr = '[debugger].json';
				$debuggerFileJson = file_get_contents($debuggerFileStr);
				$debuggerFile = json_decode($debuggerFileJson);

				foreach ($debuggerFile->classautomate->classes as $key => $value) {
						$debuggerArray[$key] = $value;
				}

				GlobalVar::set("debuggerArray", $debuggerArray);
		}

		foreach (GlobalVar::get("debuggerArray") as $key => $value) {
				if ($key == $className)
						return $value;
		}
}
/**
 * [log].txt dosyasini okuyarak parse eden fonksiyon
 *
 * @return string sistem versiyon numarasi
 */
function systemVersion()
{
		$gotSystemVersion = false;
		$versions = array();
		/*
		 * sistem versiyonu icin [LOGS].TXT dosyasi taramasi yapiliyor
		 * "V." veya "V" ile baslayan bilgiden sonraki rakamlar VERSIONS array'i icine atiliyor
		 * ilk bulunan VERSIONS array'i bilgisi en son versiyon kabul ediliyor ve $SYSTEM_VERSION degiskenine atiliyor.
		 *
		 */
		$logFileStr = '[logs].json';
		$logJson = file_get_contents($logFileStr);
		$logFile = json_decode($logJson);

		foreach ($logFile->classautomate->versions as $key => $value) {
				$versions[] = $key;
		}

		$systemVersion = $versions[0];

		return $systemVersion;
}
/**
 * text dosyasi okuyan fonksiyon
 *
 * @param string text dosyasi ismi
 * @return array dosya icerigi
 */
function readTxtFile($fileName)
{
		$lines = array();

		$fileHandle = fopen($fileName, 'rb');

		while (!feof($fileHandle)) {
				$lineOfText = fgets($fileHandle);
				$parts = explode('=', $lineOfText);
				$partsCombined = $parts[0] . $parts[1];
				$lines = array_merge($lines, (array) $partsCombined);
		}
		fclose($fileHandle);

		return $lines;
}
/**
 * tarih fonksiyonu
 *
 * @return array
 */
function mainDate()
{
		/**
		 * dil icin browser ayari uygulaniyor
		 * gerekli JSON verisi okunuyor
		 *
		 * @var array $languageJSON
		 */
		$Setting = Setting::classCache();
		$languageJSON = $Setting->getInterfaceLang();

		$mainDate = array();
		/**
		 * ay tespiti ve dil XML'den karsilastirma yapiliyor
		 *
		 * @var array $mainDate[month]
		 */
		for ($i = 1; $i <= 12; $i++) {
				$xmlMount = $languageJSON->classautomate->month[$i - 1];
				settype($xmlMount, 'string');

				if (getClientDateTime('%m') == $i) {
						$mainDate[month] = $xmlMount;
				}
		}

		/**
		 * haftanin gunu tespiti ve dil XML'den karsilastirma yapiliyor
		 *
		 * @var array $mainDate[dayOfWeek]
		 */
		for ($i = 0; $i <= 6; $i++) {
				$xmlDow = $languageJSON->classautomate->dayOfWeek[$i];
				settype($xmlDow, 'string');

				if (getClientDateTime('%w') == $i) {
						$mainDate[dayOfWeek] = $xmlDow;
				}
		}
		/**
		 * gun . ay . yil formatinda bir string olusturuluyor
		 *
		 * @var array $mainDate[full]
		 */
		$mainDate[full] = getClientDateTime('%d ') . $mainDate[month] . getClientDateTime(' %Y');

		return $mainDate;
}
/**
 * haftanın günleri için array
 *
 * @return array
 */
function daysOfWeek()
{
		$Setting = Setting::classCache();
		$languageJSON = $Setting->getInterfaceLang();
		$firstDay = $Setting->getFirstDay();

		$daysArray = $languageJSON->classautomate->dayOfWeek;

		foreach ($daysArray as $key => $value) {
				if ($key <= count($daysArray)) {
						if ($key == 0)
								$daysOfWeek[] = array('no' => $firstDay, 'day' => $daysArray[$firstDay]);
						if ($key >= 1 && $key < count($daysArray) - $firstDay)
								$daysOfWeek[] = array('no' => strval($firstDay + $key), 'day' => $daysArray[strval($firstDay + $key)]);
						if ($key >= count($daysArray) - $firstDay)
								$daysOfWeek[] = array('no' => strval($key - count($daysArray) + $firstDay), 'day' => $daysArray[strval($key - count($daysArray) + $firstDay)]);
				}
		}

		return $daysOfWeek;
}
/**
 * verilen numaraya gore haftanın gününü
 * seçilen dile göre döndüren metot
 */
function getWeekDayAsText($value)
{
		$Setting = Setting::classCache();
		return $Setting->getInterfaceLang()->classautomate->dayOfWeek[$value];
}
/**
 * gunun tarihini yıl-ay-gun olarak dondurur
 */
function getDateAsFormatted()
{
		return getClientDateTime('%Y-%m-%d');
}
/**
 * cagirildigi anin zaman bilgisini saat:dakika:saniye formatinda dondurur
 */
function getTimeAsFormatted()
{
		return getClientDateTime('%H:%M:%S');
}
/**
 * gunun tarihini yıl-ay-gun saat:dakika:saniye olarak dondurur
 */
function getDateTimeAsFormatted()
{
		return getClientDateTime('%Y-%m-%d %H:%M:%S');
}
/**
 * tarihe gore yılın kaçıncı haftası oldugunu donduren metot
 */
function getWeekNumberOfDate($date = 'now')
{
		if ($date == 'now') {
				$day = date('d');
				$month = date('m');
				$year = date('Y');
		} else {
				$expFormattedDate = explode('-', $date);
				$day = $expFormattedDate[2];
				$month = $expFormattedDate[1];
				$year = $expFormattedDate[0];
		}

		$date = mktime(0, 0, 0, $month, $day, $year);
		return $weekNumberOfDate = (int) date('W', $date);
}
/**
 * istenilen array'i baska bir array'e gore sirala
 *
 * ornek :
 * $orderArray = array(3,4,5,6);
 * $array = array('pazar','pazartesi','sali','carsamba','persembe','cuma','cumartesi');
 * Output :
 * array { [3]=>"carsamba" [4]=>"persembe" [5]=>"cuma" [6]=>"cumartesi" [0]=>"pazar" [1]=>"pazartesi" [2]=>"sali" }
 *
 * @return array
 */
function sortArrayByArray($array, $orderArray, $orderBy = null)
{
		$ordered = array();

		if ($orderBy == null || $orderBy == 'value') {
				foreach ($orderArray as $key) {
						if (array_key_exists($key, $array)) {
								$ordered[$key] = $array[$key];
								unset($array[$key]);
						}
				}
		}

		if ($orderBy == 'key') {
				foreach ($orderArray as $key => $value) {
						if (array_key_exists($key, $array)) {
								$ordered[$key] = $array[$key];
								unset($array[$key]);
						}
				}
		}
		return array_values($ordered + $array);
}
/**
 * yeni smarty degiskenleri atamasi icin fonksiyon
 *
 * @return void
 */
function setSmartyVars($smartyObject, $xmlObject, $xmlHeader)
{
		/**
		 * gonderilen xml basligindaki alt basliklarin isimleri
		 * $header_children icerisine yerlestiriliyor
		 *
		 */
		$headerChildren = array();

		foreach ($xmlObject->classautomate->$xmlHeader as $key => $value) {
				array_push($headerChildren, $key);
		}

		/**
		 * xml'den alt baslik isimlerinin degerleri
		 * smarty nesnesine gonderiliyor
		 *
		 */
		for ($i = 0; $i < count($headerChildren); $i++) {
				$smartyObject->assign($xmlHeader . "_" . $headerChildren[$i], $xmlObject->classautomate->$xmlHeader->$headerChildren[$i]);
		}
}
/**
 * extra smarty degiskenleri atama fonksiyonu
 *
 * @return string
 */
function setExtSmartyVars($var, $value)
{
		GlobalVar::set("extSmartyVars[$var]", $value);
}
/**
 * smarty'nin temel klasorleri belirleniyor
 *
 * @return void
 */
function setSmartyFolders($smartyObject)
{
		$smartyObject->template_dir = GlobalVar::get(themePath) . 'templates/';
		$smartyObject->compile_dir = 'WEB-INF/lib/smarty/templates_c/';
		$smartyObject->config_dir = 'WEB-INF/lib/smarty/config/';
		$smartyObject->cache_dir = 'WEB-INF/lib/smarty/cache/';
}
/**
 * uygulamalarin listesini dondur
 *
 * @return array
 */
function getAppList()
{
		/**
		 * dil icin browser ayari uygulaniyor
		 * gerekli JSON verisi okunuyor
		 *
		 * @var array $languageJSON
		 */
		$Setting = Setting::classCache();
		$languageJSON = $Setting->getInterfaceLang();
		$appListJSON = $languageJSON->classautomate->applications;

		foreach ($appListJSON as $key => $value) {
				$expKey = explode("&", $key);
				if (isset($expKey[1])) {
						if ($expKey[0] == "app_person_add" || $expKey[0] == 'app_accountant') {
								$expExpKey1 = explode("=", $expKey[1]);
								$image = $expKey[0] . "_" . $expExpKey1[1];
						}
						if ($expKey[0] == "app_rollcall") {
								$image = $expKey[0];
						}
						$appList[$key] = array("image" => $image, "text" => $value);
				} else {
						$image = $expKey[0];
						$appList[$key] = array("image" => $image, "text" => $value);
				}
		}
		return $appList;
}
/**
 * fonksiyonun cagirildigi andaki dosyanin adini verir
 *
 * @return string
 */
function getScriptName()
{
		$file = $_SERVER['SCRIPT_NAME'];
		$expFileName = explode('/', $file);
		$scriptFullName = $expFileName[count($expFileName) - 1];
		return $scriptName = substr($scriptFullName, 0, (strlen($scriptFullName) - 4));
}
/**
 *
 */
function getFullUrl()
{
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		return $url;
}
/**
 * sayfa tazelemeyi ac/kapat
 *
 * @return void
 */
function setRefresh($state)
{
		$Session = Session::classCache();
		$Session->set('refresh', $state);
}
/**
 * sayfa tazelemenin durumunu dondur
 *
 * @return boolean
 */
function getRefresh()
{
		$Session = Session::classCache();
		return $Session->get('refresh');
}
/**
 * site ziyaretcisinin ip adresini dondurur
 *
 * @return string
 */
function getClientIp()
{
		/**
		 * $_SERVER global'inden veriler
		 * degiskenlere atiliyor
		 *
		 */
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$httpXForwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$httpClientIp = $_SERVER['HTTP_CLIENT_IP'];
		/**
		 * ip adresi tespit ediliyor
		 *
		 */
		if (isset($remoteAddr)) {

				if ($remoteAddr == '::1')
						$ip = '127.0.0.1';
				else
						$ip = $remoteAddr;
		} else if (isset($httpXForwardedFor)) {

				$ip = $httpXForwardedFor;
		} else if (isset($httpClientIp)) {

				$ip = $httpClientIp;
		}

		return $ip;
}
/**
 * site ziyaretcisinin lokal saatini verir
 * parametre : %H saat, %M dakika, %S saniyedir
 *
 * @param string formatted
 * @return string
 */
function getClientDateTime($format = '%Y-%m-%d / %H:%M:%S')
{
		$Session = Session::classCache();
		$clientLocal = setlocale(LC_ALL, NULL);
		setlocale(LC_TIME, '$clientLocal');
		$clientTime = strtotime('+ ' . $Session->get('timeZone') . ' hour');
		// add 1 hour for BST
		$finalStringClientTime = strftime($format, $clientTime);
		return $finalStringClientTime;
}
/**
 * md5 converter
 *
 * @return string
 */
function md5Converter($string)
{
		return md5($string);
}
/**
 * random sayi ureten fonksiyon
 *
 * @param string
 * @return string
 */
function genNumber($values = '100000,999999')
{
		/**
		 * parametreyi parcala
		 *
		 * @var array
		 */
		$numbers = explode(',', $values);
		/**
		 * sayac degiskeni
		 */
		$count = 0;
		/**
		 * gelen degerler arasinda bosluk varsa temizle
		 *
		 */
		foreach ($numbers as $num) {

				$numbers[$count] = trim($num);
				$count++;
		}

		return rand($numbers[0], $numbers[1]);
}
/**
 * ayni key sayisindaki 2 listeyi
 * key'lerine gore birlestir
 *
 * @return array
 * @author andyidol at gmail dot com / PHP.NET
 */
function merge2Array($arrayList1, $arrayList2)
{
		foreach ($arrayList2 as $key => $Value) {
				if (array_key_exists($key, $arrayList1) && is_array($Value))
						$arrayList1[$key] = merge2Array($arrayList1[$key], $arrayList2[$key]);
				else
						$arrayList1[$key] = $Value;
		}
		return $arrayList1;
}
/**
 * iki farkli array'i birlestirir
 * 
 * @return array
 */
function directMerge2Array($arrayList1, $arrayList2)
{
		$resultArray = array_merge_recursive($arrayList1, $arrayList2);
		return $resultArray;
}
/**
 * iki farkli array'i birlestirir ve 
 * aynı veriden birden cok kayit varsa temizler
 * 
 * @return array
 */
function merge2ArrayAndClean($arrayList1, $arrayList2)
{
		$resultArray = array_merge_recursive($arrayList1, $arrayList2);
		return array_unique($resultArray);
}
/**
 * iki array arasindaki farki array olarak donduren metod
 *
 * @param array $array1, $array2
 * @return array
 */
function arrayDifference($array1, $array2)
{
		$ret = NULL;
		foreach ($array1 as $k => $v) {
				if (!isset($array2[$k]))
						$ret[$k] = $v;
				else if (is_array($v) && is_array($array2[$k]))
						$ret[$k] = array_diff_assoc2_deep($v, $array2[$k]);
				else if ((string) $v != (string) $array2[$k])
						$ret[$k] = $v;
		}
		return $ret;
}

/**
 * key'e gore array siralama sinifi
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class SortArrayWithKey
{

		public static $array, $column, $type;

		/**
		 * gonderilen array'i, istenilen kolon bilgisine gore sirala
		 *
		 * @param array, string, string
		 * @return array
		 */
		public static function get($array, $column, $type = 'ASC')
		{
				self::$column = $column;
				self::$type = $type;

				if (is_array($array))
						usort($array, 'self::cmp');

				return $array;
		}
		public static function cmp($a, $b)
		{
				switch (self::$type) {
						case 'ASC' :
								return ($a[self::$column] == $b[self::$column]) ? 0 : (($a[self::$column] < $b[self::$column]) ? -1 : 1);
						case 'DESC' :
								return ($a[self::$column] == $b[self::$column]) ? 0 : (($a[self::$column] < $b[self::$column]) ? 1 : -1);
				}
		}
}

/**
 * istenilen key'in içerdiği değeri
 * array içerisinde ara
 *
 * @return boolean
 */
function findKeyValueInArray($array, $baseKey, $findValue)
{
		$count = 0;
		$resultKey = 0;
		if (!is_array($array)) {
				return;
		}
		foreach ($array as $keyTop) {
				foreach ($keyTop as $key => $value) {
						if ($key == $baseKey) {
								if ($value == $findValue) {
										$resultKey = $count;
								}
						}
				}
				$count++;
		}
		return $resultKey;
}
/**
 * istenilen harfi veya kelimeyi
 * verilen dizi icerisinde arar
 * ve anahtarini dondurur
 *
 * @return int
 */
function findStringInArray($needle = null, $haystack_array = null, $skip = 0)
{
		foreach ((array) $haystack_array as $keyTop => $valueTop) {
				if (is_array($valueTop)) {
						foreach ($valueTop as $key => $eval) {
								if ($skip != 0)
										$eval = substr($eval, $skip);
								if (stristr($eval, $needle) !== false)
										return $keyTop;
						}
				} else {
						if ($skip != 0)
								$eval = substr(strval($valueTop), $skip);
						if (stristr(strval($valueTop), strval($needle)) !== false)
								return $keyTop;
				}
		}
		return false;
}
/**
 * istenilen harfi veya kelimeyi
 * verilen dizi ve dizinin verilen KEY'leri icerisinde arar
 * ve sonucunu dizi olarak dondurur
 * 
 * @return Array
 */
function getArrayWithSearchInArray($needle, $haystack, $keys)
{
		$keyList = explode(',', $keys);

		if ($needle != '') {
				foreach ($haystack as $key => $value) {
						foreach ($keyList as $keyListValue) {
								if (stristr($value[$keyListValue], $needle)) {
										$result[] = $value;
								}
						}
				}
		} else {
				$result = $haystack;
		}
		return $result;
}
/**
 * arrayden istenilen veriyi oku
 * ve dizi olarak dondur
 * 
 * @return array
 */
function getFromArray($readResult, Array $intend)
{
		// intend'i debug et
		if (debugger("Start")) {
				echo "DEBUG : getFromArray() - Intend : ";
				var_dump($intend);
		}

		// gonderilen array'in icerik sayisi kadar dongu yarat
		for ($i = 0; $i < count($readResult); $i++) {
				// intend'i key->value olarak donguye al
				foreach ($intend as $field => $value) {
						// value icinde seri arama istegi var ise; ornegin || ile ayrilmis ise onlari da dongule
						$expValue = explode('||', $value);
						foreach ($expValue as $subValue) {
								//veritabani kolonunda virgulle ayrilmis degerler var mi?
								$expField = explode(",", $readResult[$i][$field]);
								//eger deger virgul ile ayrilmis ise istenilen intend value'sunu virgulle ayrilmis bilgiler icinden ara
								for ($fi = 0; $fi < count($expField); $fi++) {
										if ($expField[$fi] == $subValue) {
												++$keyCount;
												if ($keyCount == count($intend))
														$result[] = $readResult[$i];
										}
								}
						}
				}
				$keyCount = 0;
		}

		if (debugger("Start"))
				echo 'DEBUG : ' . getCallingClass() . '->getFromArray() - Bulunan Kayit Sayisi : ' . count($result) . '<br>';
		return $result;
}
/**
 * son tablo kaydina ait numarayi bul ve yeni kayit uret
 *
 * @return int
 */
function getNewTableCode(Array $params)
{
		$Db = Db::classCache();
		if (!isset($params['columnName']))
				$params['columnName'] = 'code';

		if (isset($params["columnCode"])) {
				$params['condition'] = $params["columnName"] . " = '" . $params["columnCode"] . "'";
		}

		$lastRow = $Db->readSelectedLastRow($params);

		return !is_null($lastRow) ? $lastRow[$params['columnName']] + 1 : 1;
}
/**
 * form isminden tablo ismi cikartan metot
 */
function getFirstUpperCaseWord($string)
{
		preg_match_all('/[A-Z][^A-Z]*/', $string, $results);
		$upperCaseWord = $results[0][0];
		return $upperCaseWord;
}
/**
 * buyuk harfle baslayan kelimeleri
 * dizi haline getiren metot
 */
function splitUpperCaseWords($string)
{
		return preg_split('/(?=[A-Z])/', $string);
}
/**
 * cagiran class'i donduren metot
 * 
 * @author hamstar <https://gist.github.com/hamstar>
 * @return string
 */
function getCallingClass()
{
		//get the trace
		$trace = debug_backtrace();

		// Get the class that is asking for who awoke it
		$class = $trace[1]['class'];

		// +1 to i cos we have to account for calling this function
		for ($i = 1; $i < count($trace); $i++) {
				if (isset($trace[$i])) // is it set?
						if ($class != $trace[$i]['class']) // is it a different class
								return $trace[$i]['class'];
		}
}
/**
 * girilen tarihin haftanın
 * hangi günü olduğunu döndüren metot
 */
function getWeekDayOfTheDate($date)
{
		return date('w', strtotime($date));
}
/**
 * yeni transfer info tekniğine gore ayristirma yapan methot
 * sonucu direkt olarak string olarak gonderir
 * 
 * @param $definitionCode : tc, tf, tt
 * @param $array : POST veya Normal Array
 * @return string
 */
function getTransferInfo($definitionCode, $array)
{
		$transferInfo = array();
		foreach ($array as $key => $value) {

				if (substr($key, 0, 3) == 'tc:') {
						$transferInfo['tc'] = getArrayKeyValue(explode(':', $key), 1);
						$transferInfo['tf'] = getArrayKeyValue(explode('|', $value), 0);
						$transferInfo['tt'] = getArrayKeyValue(explode('|', $value), 1);
				}
		}
		return $transferInfo[$definitionCode];
}
/**
 * tarihler arasindaki farki bulan metot
 */
function getDateTimeDiff($firstValue, $secondValue, $format, $debug = false)
{
		$FirstDate = new DateTime($firstValue);
		$SecondDate = new DateTime($secondValue);

		$Difference = $FirstDate->diff($SecondDate);

		if ($debug) {
				var_dump('getDateTimeDiff(' . $firstValue . ', ' . $secondValue . ', ' . $format);
				var_dump($Difference);
		}

		if ($format == 'y')
				return $Difference->y;
		if ($format == 'm')
				return $Difference->m;
		if ($format == 'w') {
				$weekDiffTemp = strtotime($firstValue) - strtotime($secondValue);
				$weekDiff = floor($weekDiffTemp / 604800);
				return $weekDiff;
		}
		if ($format == 'd')
				return $Difference->d;
		if ($format == 'h')
				return $Difference->h;
		if ($format == 'i')
				return $Difference->i;
		if ($format == 's')
				return $Difference->s;
		if ($format == 'type') {
				if ($FirstDate > $SecondDate)
						return 1;
				if ($FirstDate < $SecondDate)
						return -1;
				if ($FirstDate == $SecondDate)
						return 0;
		}
}
/**
 * Counts the number occurrences of a certain day of the week between a start and end date
 * The $start and $end variables must be in UTC format or you will get the wrong number 
 * of days  when crossing daylight savings time
 * @param - $day - the day of the week such as "Monday", "Tuesday"...
 * @param - $start - a UTC timestamp representing the start date
 * @param - $end - a UTC timestamp representing the end date
 * @return Number of occurences of $day between $start and $end
 * 
 * $start = strtotime("tuesday UTC"); $end = strtotime("3 tuesday UTC");
 * echo date("m/d/Y", $start). " - ".date("m/d/Y", $end). " has ". countDays(0, $start, $end). " Sundays";
 * Outputs something like: 09/28/2010 - 10/19/2010 has 3 Sundays.
 * 
 * @author Phil Mccull : philprogramming.blogspot.com
 * @edited Bulent Gercek <bulentgercek@gmail.com>
 */
function getWeekDayCount($day, $start, $end)
{
		//get the day of the week for start and end dates (0-6)
		$w = array(date('w', strtotime($start)), date('w', strtotime($end)));

		//get partial week day count
		if ($w[0] < $w[1]) {
				$partialWeekCount = ($day >= $w[0] && $day <= $w[1]);
		} else if ($w[0] == $w[1]) {
				$partialWeekCount = $w[0] == $day;
		} else {
				$partialWeekCount = ($day >= $w[0] || $day <= $w[1]);
		}

		//first count the number of complete weeks, then add 1 if $day falls in a partial week.
		return floor(( strtotime($end) - strtotime($start) ) / 60 / 60 / 24 / 7) + $partialWeekCount;
}
/**
 * tarihler arasi istenilen gune gore tarih listesi cikaran metot
 * not : getWeekDayCount() ile birlikte calisir
 */
function getWeekDays($day, $start, $end)
{
		$dayNames = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$dateList = array();

		$count = getWeekDayCount($day, $start, $end);
		//var_dump($day . " (" . $dayNames[$day] . ")- " . $start . " - " . $end . " WeekDayCount : " . $count);

		if ($count >= 1) {
				$startDate = new DateTime($start);

				if (getWeekDayOfTheDate($start) == $day) {
						$dateList[] = $startDate->format("Y-m-d");
						$startNum = 1;
				} else {
						$startNum = 0;
				}

				for ($i = $startNum; $i < $count; $i++) {
						$startDate->modify('next ' . $dayNames[$day]);
						$dateList[] = $startDate->format("Y-m-d");
				}
		}
		return $dateList;
}
/**
 * array uzerinde istenilen anahtardaki 
 * bilgiyi dondurur (explode ve getFromArray icin yaptim)
 * 
 * @return array
 */
function getArrayKeyValue($array, $key)
{
		return($array[$key]);
}
/**
 * sinifin periodluk ders sayisini hesaplayip donduren metot
 */
function getLectureCountByPeriod($Classroom, $period)
{
		switch ($period) {
				case 'weekly': $result = 1;
						break;
				case 'monthly': $result = 4;
						break;
				case 'monthly3': $result = 6;
						break;
				case 'monthly6': $result = 24;
						break;
				case 'monthly12': $result = 48;
						break;
				case 'yearly': $result = 48;
						break;
		}

		return $Classroom->getDayTimeCount() * $result;
}
?>

