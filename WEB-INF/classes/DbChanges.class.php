<?php
/**
 * Onemli degisiklikleri isleyen sinif
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
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
	private function __construct() {}

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
			echo "DEBUG : " . getCallingClass() . "->Changes->setChanges() - dbChanges :";
			var_dump($this->_dbChanges);
		}
		
		// degisikleri bul
		$this->_findDbChanges();
		
		if (debugger("Changes")) {
			echo "DEBUG : " . getCallingClass() . "->Changes->setChanges() - neededChangesFound :";
			var_dump($this->_neededDbChangesFound);
		}
		
		if (count($this->_neededDbChangesFound) > 0) {
			if (debugger("Changes")) {
				echo "DEBUG : " . getCallingClass() . "->Changes->setChanges() - setDbArrays() :<br>";
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
	private function _findDbChanges()
	{
		foreach ($this->_determinantsList as $determinantsListKey => $determinantsListValue) {
			if ($this->_dbChanges['table'] == $determinantsListKey) {
				foreach ($determinantsListValue as $fieldKey=>$fieldName) {
					$foundKey = array_search($fieldName, $this->_dbChanges['tableFields']);
					if (is_int($foundKey)) {
						if ($this->_dbChanges['dbProcess'] == 'add') $keyName = $fieldName;
						if ($this->_dbChanges['dbProcess'] == 'update') $keyName = $foundKey;
						if ($this->_dbChanges['dbProcess'] == 'delete') $keyName = $fieldName;
						
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
	    $changesDateTime = getClientDateTime('%Y-%m-%d %H:%M:%S');
		/**
		 * MASTERCHANGE verilmis mi? Not:Bkz.setMasterChange() fonsiyonu aciklamasi
		 */
		if (empty($this->_masterChange)) {

            if ($this->_dbChanges['table'] == 'classroom' && $this->_neededDbChangesFound['status'] =='active') {
                /**
                 * ÖNEMLİ : SINIF AKTIF EDILIYORSA CALISACAKTIR.
                 * Sinifin aktif edildigi zaman, dersin baslayacagi DAYTIME'dan farkli olmamalidir.
                 * Dolayisiyla CHANGES'e dersin aslen baslayacagi DAYTIME yazılıyor.
                 */
				$ClassroomDayTime = School::classCache()->getClassroom($this->_dbChanges['tableCode'])->getDayTime($this->_dbChanges['values'][2]);
				$changesDateTime = $this->_dbChanges['values'][1] . ' ' . $ClassroomDayTime->getInfo('time');
            }

		} else {
			if (debugger("Changes")) {
				echo "DEBUG : " . getCallingClass() . "->Changes->_setDbArrays()->masterChange : ";
				var_dump($this->_masterChange);
			}
			$masterChangeInfo = explode('|', $this->_masterChange['masterChangeInfo']);
			
			if ($this->_dbChanges['table'] == 'person') {
				$ClassroomDayTime = School::classCache()->getClassroom($masterChangeInfo[1])->getDayTime($this->_masterChange['startDayTime']);
				$changesDateTime = $this->_masterChange['startDate'] . ' ' . $ClassroomDayTime->getInfo('time');
			}
		}

		$array = array(	'code'=>getNewTableCode(array("table"=>$this->_dbTable)), 
						'dateTime'=>$changesDateTime,
						'tableName'=>$this->_dbChanges['table'],
						'tableCode'=>$this->_dbChanges['tableCode'],
						'changeType'=>$this->_dbChanges['dbProcess'],
						'currents'=>$this->_dbChanges['currents'],
						'status'=>'',
						'classroom'=>'',
						'payment'=>'',
						'paymentPeriod'=>'',
						'instructor'=>'',
						'instructorPayment'=>'',
						'instructorPaymentPeriod'=>'',
						'asistant'=>'',
						'asistantPayment'=>'',
						'asistantPaymentPeriod'=>'');
						
		foreach ($this->_neededDbChangesFound as $key => $value) {
			$array[$key]= $value;
		}

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
			var_dump($array);
		} else {
			$Db->insertSql(array('table'=>$this->_dbTable, 'columns'=>$columns, "values"=>$arrayValues));
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
	public function setMasterChange($array) 
	{
		$this->_masterChange = $array;
	}
	
	/**
	 * MASTERCHANGE dizisi sifirlaniyor
	 */
	public function removeMasterChange()
	{
		$this->_masterChange = array();
	}
}
