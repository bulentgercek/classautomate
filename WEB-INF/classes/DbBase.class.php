<?php

/**
 * Alt Duzey Veritabani Kutuphanesi
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class DbBase
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
		 *
		 * MySQL'e yapılan bağlantı degiskeni
		 *
		 * @var obje
		 */
		public $mysqli;

		/**
		 *
		 * Hic degismeyecek olan ana database(classautomate) bilgileri
		 *
		 * @var array
		 */
		private static $_serverPrefix = 'cleswach_';
		private static $_mainDbName = 'classautomate';
		private static $_intraIp = '192.168.1.2';
		private static $_localMysql = array('host' => 'localhost', 'username' => 'root', 'password' => '');
		private static $_serverMysql = array('host' => 'localhost', 'username' => 'master', 'password' => 'Bigmate77');
		private static $_intraMysql = array('host' => '192.168.1.2', 'username' => 'root', 'password' => 'Bigmate77');

		/**
		 *
		 * Lokal miyiz yoksa Server da mi?
		 *
		 * @var array
		 */
		private $_dbSettings;

		/**
		 *
		 * Guncel veritabani ismi ve resource link kodu icin static degiskenler yaratiliyor
		 *
		 * @var string
		 */
		public $_currentDbName;

		/**
		 *
		 * sql isleminin sonucu cikan array
		 *
		 * @var array
		 */
		public $sqlArray;

		/**
		 *
		 * sql sorgusu result degiskeni
		 *
		 * @var object
		 */
		public $sqlResult;

		/**
		 *
		 * classın construct methodu yoktur
		 * dolayısıyla new şeklinde çağrılamaz
		 * bunun için de fonksiyon private şeklinde tanımlandi
		 *
		 * @return void
		 */
		private function __construct()
		{
				
		}
		/**
		 *
		 * Singleton fonksiyonu
		 *
		 * @access public
		 * @return object
		 */
		public static function classCache()
		{
				if (!self::$_instance) {
						self::$_instance = new Db();
				}
				return self::$_instance;
		}
		/**
		 *
		 * verilen dbName'e gore database connect yapiliyor
		 *
		 * @param $dbName
		 * @return void
		 */
		public function connect($dbName)
		{
				/**
				 * lokal'de miyiz yoksa server'da mi?
				 *
				 * @var array
				 */
				$this->_dbSettings = ($_SERVER['SERVER_NAME'] == 'localhost' ? self::$_localMysql : self::$_serverMysql);
				$this->_dbSettings = ($_SERVER['SERVER_NAME'] == self::$_intraIp ? self::$_intraMysql : $this->_dbSettings);
				$this->_dbSettings['name'] = $dbName;
				/**
				 * ayrica server'da isek;
				 * username ve database adı için prefix kullanmamız gerekiyor
				 */
				if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != self::$_intraIp) {
						$this->_dbSettings['username'] = self::$_serverPrefix . $this->_dbSettings['username'];
						$this->_dbSettings['name'] = self::$_serverPrefix . $dbName;
				}
				/**
				 * istenilen database acilmaya calisiliyor.
				 * eger istenilen veritabani hali hazirda acik ise
				 * islem yapmadan bitir
				 *
				 */
				if ($this->_currentDbName != $dbName) {
						if (!empty($this->_currentDbName)) {
								/**
								 * farkli bir database acilmaya calisiliyor, eskisini kapat
								 *
								 */
								if (debugger("DbBase"))
										echo 'DEBUG : ' . getCallingClass() . '->DbBase->connect(' . $dbName . '), DbBase->close(' . $this->_currentDbName . ')<br>';
								$this->close();
						}
						/**
						 * acilacak database ismi belli oldu
						 *
						 */
						$this->_currentDbName = $dbName;
						/**
						 * hazirlanan bilgilere gore DBNAME ile gonderilen veritabanina baglaniyor
						 *
						 */
						$this->mysqli = new mysqli($this->_dbSettings['host'], $this->_dbSettings['username'], $this->_dbSettings['password'], $this->_dbSettings['name']);
						$this->mysqli->set_charset('utf8');
						/**
						 * Acik olan database baglantisi dbName'e gore kapatiliyor
						 *
						 */
						if (debugger("DbBase"))
								echo 'DEBUG : ' . getCallingClass() . '->DbBase->connect(' . $this->_currentDbName . ')<br>';
				}
		}
		/**
		 *
		 * select sorgusu olustur
		 *
		 * @return void
		 */
		public function selectSql($intend)
		{
				$sqlString = 'SELECT *';
				/**
				 * tabloyu ekle
				 */
				$sqlString .= ' FROM ' . $intend['table'];
				/**
				 * sorguda $where tanimlanmis mi?
				 */
				if ($intend['where'] != '')
						$sqlString .= ' WHERE ' . $intend['where'];
				/**
				 * sorguda $orderby tanimlanmis mi?
				 */
				if ($intend['orderby'] != '')
						$sqlString .= ' ORDER BY ' . $intend['orderby'];
				/**
				 * sorguda limit tanimlanmis mi?
				 */
				if ($intend['limit'] != '')
						$sqlString .= ' LIMIT ' . $intend['limit'];
				/**
				 * sorguyu calistir
				 */
				if (debugger("DbBase"))
						echo 'DEBUG : ' . getCallingClass() . '->DbBase->selectSql(' . $sqlString . ')<br>';
				$this->setSql($sqlString);
		}
		/**
		 *
		 * insert sorgusu olustur
		 *
		 * @return void
		 */
		public function insertSql(Array $intend)
		{
				$sqlString = 'INSERT INTO ' . $intend['table'];
				/**
				 * sorgudaki SET bilgisini al
				 */
				if ($intend['columns'] != '')
						$sqlString .= ' (' . $intend['columns'] . ') ';
				/**
				 * sorgudaki guncellenek veriyi al
				 */
				if ($intend['values'] != '')
						$sqlString .= ' VALUES (' . $intend['values'] . ')';
				/**
				 * sorguyu calistir
				 */
				if (debugger("DbBase"))
						echo 'DEBUG : ' . getCallingClass() . '->DbBase->insertSql(' . $sqlString . ')<br>';

				$this->setSql($sqlString);
		}
		/**
		 *
		 * update sorgusu olustur
		 *
		 * @return void
		 */
		public function updateSql(Array $intend)
		{
				$sqlString = 'UPDATE ' . $intend['table'];
				/**
				 * sorgudaki SET bilgisini al
				 */
				if ($intend['set'] != '')
						$sqlString .= ' SET ' . $intend['set'];
				/**
				 * sorgudaki guncellenek veriyi al
				 */
				if ($intend['where'] != '')
						$sqlString .= ' WHERE ' . $intend['where'];
				/**
				 * sorguyu calistir
				 */
				if (debugger("DbBase"))
						echo 'DEBUG : ' . getCallingClass() . '->DbBase->updateSql(' . $sqlString . ')<br>';

				$this->setSql($sqlString);
		}
		/**
		 *
		 * update sorgusu olustur
		 *
		 * @return void
		 */
		public function deleteSql(Array $intend)
		{
				$sqlString = 'DELETE FROM ' . $intend['table'];
				/**
				 * sorgudaki silinecek veriyi al
				 */
				if ($intend['where'] != '')
						$sqlString .= ' WHERE ' . $intend['where'];
				/**
				 * sorguyu calistir
				 */
				if (debugger("DbBase"))
						echo 'DEBUG : ' . getCallingClass() . '->DbBase->deleteSql(' . $sqlString . ')<br>';

				$this->setSql($sqlString);
		}
		/**
		 * bir tabloyu sifirlayan metot
		 * 
		 * @return Void
		 */
		public function truncateTable(Array $intend)
		{
				$sqlString = 'TRUNCATE TABLE ' . $intend['table'];
				/**
				 * sorguyu calistir
				 */
				if (debugger("DbBase"))
						echo 'DEBUG : ' . getCallingClass() . '->DbBase->truncateTable(' . $sqlString . ')<br>';

				$this->setSql($sqlString);
		}
		/**
		 *
		 * veritabani baglantisi var mi?
		 *
		 * @return boolean
		 */
		public function getConnection()
		{
				if (!empty($this->_currentDbName))
						return $this->_currentDbName;
				else
						return false;
		}
		/**
		 *
		 * acik olan database baglantisi dbName'e gore kapatiliyor
		 *
		 */
		public function close()
		{
				if (!empty($this->_currentDbName)) {
						$this->mysqli->close();
						/**
						 * Acik olan database baglantisi dbName'e gore kapatiliyor
						 *
						 */
						if (debugger("DbBase"))
								echo 'DEBUG : ' . getCallingClass() . '->DbBase->close(' . $this->_currentDbName . ')<br>';
						$this->_currentDbName = NULL;
				}
		}
		/**
		 *
		 * sql sorgusunu query haline getiren ve
		 *
		 * @param string $sql
		 */
		public function setSql($sql)
		{
				if (debugger("DbSqlStrings"))
						d($sql);
				$this->sqlResult = $this->mysqli->query($sql);
		}
		/**
		 *
		 * sql sonucunu donduren fonksiyon
		 *
		 * @return array
		 */
		public function getRows($format = 'fieldBase')
		{
				if ($this->mysqli->field_count != 0 && self::getRowCount() > 0) {
						if (self::getRowCount() != 0) {
								/**
								 * sql array'ini sifirla
								 *
								 */
								$this->sqlArray = NULL;
								/**
								 * sql satir sayisini esitle
								 *
								 */
								$fieldCount = $this->mysqli->field_count;
								/**
								 * sql array'ini hazirla
								 *
								 * sql satir sayisi 1'den buyuk mu?
								 */
								if (debugger("DbBase"))
										echo 'DEBUG : ' . getCallingClass() . '->DbBase->getRows(ColumnCount = ' . $fieldCount . ' / RowCount : ' . self::getRowCount() . ')<br><br>';


								if ($format == 'fieldBase' || $format == 'noBase') {
										if ($this->getRowCount() > 1) {
												while ($row = $this->sqlResult->fetch_assoc()) {
														$this->sqlArray[] = $row;
												}
										} else {
												if ($format == 'fieldBase')
														$this->sqlArray = $this->sqlResult->fetch_assoc();
												if ($format == 'noBase')
														$this->sqlArray[] = $this->sqlResult->fetch_assoc();
										}
								}

								if ($format == 'rowBase') {
										$fields = self::getColumns();

										if ($this->getColumnCount() == 1) {
												while ($row = $this->sqlResult->fetch_assoc()) {
														$this->sqlArray[] = $row[$fields[0]];
												}
										}
										if ($this->getColumnCount() == 2) {
												while ($row = $this->sqlResult->fetch_assoc()) {
														$this->sqlArray[$row[$fields[0]]] = $row[$fields[1]];
												}
										}
								}

								if ($format == "code") {
										
								}
								/**
								 * sql array'ini dondur
								 *
								 */
						} else {
								$this->sqlArray = NULL;
						}

						if (debugger("DbBase")) {
								d($this->sqlArray);
						}
						return $this->sqlArray;
				}
		}
		/**
		 *
		 * sql sonucunu satir sayisi dondur
		 *
		 * @return string numeric
		 */
		public function getRowCount()
		{
				return $this->mysqli->affected_rows;
		}
		/**
		 *
		 * sql sonucunu kolonlarin isimlerini dondurur
		 *
		 * @return array
		 */
		public function getColumns()
		{
				$fields = $this->sqlResult->fetch_fields();

				foreach ($fields as $field) {
						$array[] = $field->name;
				}

				return $array;
		}
		/**
		 *
		 * sql sonucunu kolonlarin sayisini dondurur
		 *
		 * @return string numeric
		 */
		public function getColumnCount()
		{
				return $this->mysqli->field_count;
		}
}

?>
