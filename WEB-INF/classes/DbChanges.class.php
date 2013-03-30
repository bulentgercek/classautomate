<?php

/**
 * Onemli degisiklikleri isleyen sinif
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class DbChanges
{

		/**
		 *
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 * ilgili db tablo adi
		 */
		private $_dbTable = "changes";

		/**
		 * genel degiskenler
		 */
		private $_determinantsList = array();
		private $_dbChanges = array();
		private $_neededDbChangesFound = array();
		private $_masterChange = array();

		/**
		 * Classın construct methodu yoktur
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
		public static function & classCache()
		{
				if (!self::$_instance) {
						self::$_instance = new DbChanges();
						self::$_instance->readDeterminantsList();
				}
				return self::$_instance;
		}
		/**
		 * takip edilecekler listesini dondur
		 *
		 * @return array
		 */
		function readDeterminantsList()
		{
				/**
				 * dil icin browser ayari uygulaniyor
				 * gerekli JSON verisi okunuyor
				 *
				 * @var array $languageJSON
				 */
				$Setting = Setting::classCache();
				$determinantsListJSON = $Setting->getDeterminantsList();
				$tables = $determinantsListJSON->classautomate->tables;

				foreach ($tables as $key => $value) {
						$this->_determinantsList[$key] = $value;
				}
		}
		/**
		 * degisisikler listesini dondur
		 */
		public function getDeterminantsList()
		{
				return $this->_determinantsList;
		}
		/**
		 * degisiklikleri al ve uygula
		 */
		public function setDbChanges($array)
		{
				$this->_dbChanges = $array;
				/**
				 * eski degismesi gerekenler listesini sifirla
				 */
				$this->_neededDbChangesFound = array();
				/**
				 * database degisimlerini islemeye basla
				 */
				if (debugger("Changes")) {
						echo "<br><b>--------------------- CHANGES ---------------------</b><br><br>";
						echo "DEBUG : " . getCallingClass() . "->Changes->setDbChanges() - dbChanges :";
						d($this->_dbChanges);
				}

				// degisikleri bul
				$this->_findNeededDbChanges();

				if (debugger("Changes")) {
						echo "DEBUG : " . getCallingClass() . "->Changes->setDbChanges() - neededChangesFound :";
						d($this->_neededDbChangesFound);
				}

				if (count($this->_neededDbChangesFound) > 0) {
						if (debugger("Changes")) {
								echo "DEBUG : " . getCallingClass() . "->Changes->setDbChanges() - setDbArrays() :<br>";
						}
						$this->_setDbArrays();
				}

				if (debugger("Changes")) {
						echo "<b>--------------------- /CHANGES --------------------</b><br><br>";
				}
		}
		/**
		 * degisiklikleri tara ve bul
		 */
		private function _findNeededDbChanges()
		{
				foreach ($this->_determinantsList as $determinantsListKey => $determinantsListValue) {
						if ($this->_dbChanges['table'] == $determinantsListKey) {
								foreach ($determinantsListValue as $fieldKey => $fieldName) {
										$foundKey = array_search($fieldName, $this->_dbChanges['tableFields']);
										if (is_int($foundKey)) {
												if ($this->_dbChanges['dbProcess'] == 'add')
														$keyName = $fieldName;
												if ($this->_dbChanges['dbProcess'] == 'update')
														$keyName = $foundKey;
												if ($this->_dbChanges['dbProcess'] == 'delete')
														$keyName = $fieldName;

												$this->_neededDbChangesFound[$fieldName] = $this->_dbChanges['values'][$keyName];
										}
								}
						}
				}
		}
		/**
		 * veritabanı icin dizileri hazirla
		 * ve veritabanına ekle
		 */
		private function _setDbArrays()
		{
				/**
				 * tarih genel olarak simdiki zamandir
				 */
				$changesDateTime = getDateTime('%Y-%m-%d %H:%M:%S');
				/**
				 * masterChange tanimlanmis mi? Not: Bkz. setMasterChange() fonsiyonu aciklamasi
				 */
				if ($this->_masterChange) {
						if (debugger("Changes")) {
								echo "DEBUG : " . getCallingClass() . "->Changes->_setDbArrays()->masterChange : ";
								d($this->_masterChange);
						}
						$changesDateTime = $this->_masterChange['dateTime'];

				} else {
						if ($this->_dbChanges['table'] == 'classroom' && $this->_neededDbChangesFound['status'] == 'active') {
								/**
								 * ÖNEMLİ : SINIF AKTIF EDILIYORSA CALISACAKTIR.
								 * Sinifin aktif edildigi zaman, dersin baslayacagi DAYTIME'dan farkli olmamalidir.
								 * Dolayisiyla CHANGES'e dersin aslen baslayacagi DAYTIME yazılıyor.
								 */
								$School = School::classCache();
								$ClassroomDayTime = $School->getClassroom($this->_dbChanges['tableCode'])->getDayTime($this->_dbChanges['values'][2]);
								$changesDateTime = $this->_dbChanges['values'][1] . ' ' . $ClassroomDayTime->getInfo('time');
						}
				}

				$array = array('code' => getNewTableCode(array("table" => $this->_dbTable)),
						'dateTime' => $changesDateTime,
						'tableName' => $this->_dbChanges['table'],
						'tableCode' => $this->_dbChanges['tableCode'],
						'changeType' => $this->_dbChanges['dbProcess'],
						'currents' => $this->_dbChanges['currents'],
						'phase' => $this->_dbChanges['phase'] ? $this->_dbChanges['phase'] : '0',
						'status' => '',
						'classroom' => '',
						'payment' => '',
						'paymentPeriod' => '',
						'instructor' => '',
						'instructorPayment' => '',
						'instructorPaymentPeriod' => '',
						'asistant' => '',
						'asistantPayment' => '',
						'asistantPaymentPeriod' => '');

				foreach ($this->_neededDbChangesFound as $key => $value) {
						$array[$key] = $value;
				}
				
				/**
				 * status ne olursa olsun eklensin istiyorum
				 * cok kritik bir veri çünkü
				 */
				//$array['status'] = 

				/**
				 * acik okulun database'ine baglaniliyor
				 */
				$Db = Db::classCache();
				$Db->connect(Session::classCache()->get('dbName'));
				$changesColumns = $Db->readTableColumns($this->_dbTable);

				$columns = implode(",", $changesColumns);
				$arrayValues = "'" . implode("','", $array) . "'";

				/**
				 * kayit yapiliyor (debug aciksa yapmayacak)
				 */
				if (debugger("Changes")) {
						echo "DEBUG : " . getCallingClass() . "->Changes->_setDbArrays() - Final Db Array : ";
						d($array);
				} else {
						$Db->insertSql(array('table' => $this->_dbTable, 'columns' => $columns, "values" => $arrayValues));
				}
				/**
				 * masterChange'i tanimlanmis ise sifirla
				 */
				if ($this->_masterChange) {
						$this->removeMasterChange();
				}
		}
		/**
		 * eger bir degisiklik diger bir degisikligin sebebi ile yapiliyorsa
		 * o degislik ARDIL degisiklik sayilir. Ana degisikligin adi MASTERCHANGE'dir.
		 * Bu metot MASTERCHANGE tanimlamak da kullanilir.
		 * 
		 * MASTERCHANGE'in amacı; ana degisikligin bilgilerini ARDIL degisikliklere ulastirmaktir.
		 * 
		 * MASTERCHANGE dizisi sifirlanana kadar kullanilir.
		 */
		public function setMasterChange($dateTime)
		{
				$this->_masterChange['dateTime'] = $dateTime;
		}
		/**
		 * MASTERCHANGE dizisi sifirlaniyor
		 */
		public function removeMasterChange()
		{
				$this->_masterChange = array();
		}
}
