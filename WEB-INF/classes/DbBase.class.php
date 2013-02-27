<?php
/**
 * Alt Duzey Veritabani Kutuphanesi
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
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
	private static $_localMainDb = array('host' => 'localhost', 'username' => 'root', 'password' => '', 'name' => 'classautomate');

	private static $_serverMainDb = array('host' => '10.0.13.82', 'username' => 'classautomate', 'password' => 'Bigmate77', 'name' => 'classautomate');
	/**
	 *
	 * Lokal miyiz yoksa Server da mi?
	 *
	 * @var array
	 */
	private $_mainDb;
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
	private function __construct() {}

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
		$this->_mainDb = ($_SERVER['SERVER_NAME'] == 'localhost' ? self::$_localMainDb : self::$_serverMainDb);
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
			 * acilmasi istenen ana veritabani mi?
			 *
			 */
			if ($dbName == 'classautomate') {
				/**
				 * ana veritabani icin gerekli bilgiler
				 *
				 */
				$currentHost = $this->_mainDb['host'];
				$currentUsername = $this->_mainDb['username'];
				$currentPassword = $this->_mainDb['password'];

			} else {
				/**
				 * okulun veritabani acilacak
				 * okulun veritabani bilgilerini okuyalim
				 *
				 */
				$schoolDb = $this->getSchoolDb($dbName);
				/**
				 * ana veritabanindan okunan okul veritabani icin gerekli bilgiler
				 *
				 */
				$currentHost = $schoolDb['host'];
				$currentUsername = $schoolDb['username'];
				$currentPassword = $schoolDb['password'];

			}
			/**
			 * hazirlanan bilgilere gore DBNAME ile gonderilen veritabanina baglaniyor
			 *
			 */
			$this->_setDbLink($currentHost, $currentUsername, $currentPassword, $this->_currentDbName);
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
	 * ana veritabani ile baglanti kurularak
	 * ilgili okulun veritabani baglanti bilgileri aliniyor
	 * bilgiler return ediliyor
	 *
	 * @return array
	 */
	public function getSchoolDb($dbName)
	{
		/**
		 * Local veya Server'da olup olmadigina bakilip, ona gore CLASSAUTOMATE ana veritabanina baglanti icin gerekli bilgiler ataniyor.
		 *
		 */
		$this->_setDbLink($this->_mainDb['host'], $this->_mainDb['username'], $this->_mainDb['password'], $this->_mainDb['name']);
		/**
		 * ana veritabanindan 'school_main' tablosundan
		 * ilgili okulun veritabani baglanti bilgileri okunuyor
		 *
		 */
		$this->selectSql(array('table' => 'school_main', 'where' => "name = '" . $dbName . "'"));
		/**
		 * classautomate/school_main icinde yer alan
		 * okulun veritabani baglanti bilgileri $row'a aliniyor
		 *
		 */
		$schoolDbArray = $this->getRows();
		/**
		 * Eslenecek basliklari arrayler haline getiriyoruz
		 *
		 */
		$dbHeaders = array('name', 'username', 'password', 'host');
		/**
		 * Esleme yapiliyor
		 *
		 */
		for ($i = 0; $i < $this->getRowCount(); $i++) {
			for ($h = 0; $h < count($dbHeaders); $h++) {
				$schoolDb[$dbHeaders[$h]] = $schoolDbArray[$dbHeaders[$h]];
			}
			/**
			 * Eger lokalde calisiyorsak database bilgilerini
			 * DbControl'un $_mainDb static array degiskeninden aliyoruz (db name disinda)
			 *
			 */
			if ($row[$dbHeaders[3]] == 'localhost') {
				for ($h = 1; $h < count($dbHeaders); $h++) {
					$schoolDb[$dbHeaders[$h]] = $this->_mainDb[$dbHeaders[$h]];
				}
			}
		}
		return $schoolDb;
	}

	/**
	 *
	 * verilen dbName'e gore database connect yapiliyor
	 *
	 * @param $host
	 * @param $username
	 * @param $password
	 * @param $dbName
	 * @return void
	 */
	private function _setDbLink($host, $username, $password, $dbName)
	{
		$this->mysqli = new mysqli($host, $username, $password, $dbName);
		$this->mysqli->set_charset('utf8');
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
			var_dump($sql);
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
				var_dump($this->sqlArray);
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
