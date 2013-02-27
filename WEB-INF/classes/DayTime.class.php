<?php
/**
 * Sınıf Gün ve Zamanları Nesnesi
 * 
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class DayTime
{
	/**
	 * sınıf daytime bilgileri
	 */
	private $_info;
	
	/**
	 * sınıf kodu
	 */
	private $_classroom;
	
	/**
	 * dayTime kod numarasi
	 */
	private $_code;
	
	/**
	 * ilgili db tablo adi
	 */
	private $_dbTable = "daytime";
	
	/**
	 * constract metodu
	 * 
	 * @param array
	 */
	public function __construct($classroom, $code = null)
	{
		$this->_classroom = $classroom;

		if ($code == null)
			$this->_code = $this->getNewCode();
		else
			$this->_code = $code;
	}
		
	/**
	 * dayTime listesini dondur
	 * 
	 * @return string
	 */
	public function getInfo($key = null)
	{
		$this->_updateArrays();

		if ($key == NULL) {
			return $this->info();

		} else {
			return $this->_info[$key];
		}
	}
	
	/**
	 * istenilen gun/saat bilgisini
	 * guncelle
	 * 
	 * @param array
	 */
	public function setInfo(Array $array)
	{
		$this->_updateArrays();

		if ($this->_info == NULL) {
			switch (getTransferInfo('tt', $array)) {
				case 'post':
					SendToDb::add($this);
					break;
				case 'direct': 
					SendToDb::add($this, $array);
					break;
			}
		} else {
			switch (getTransferInfo('tt', $array)) {
				case 'post':
					SendToDb::update($this);
					break;
				case 'direct':
					$newArray = array_merge($this->_info, $array);
					SendToDb::update($this, $newArray);
					break;
			}
		}
		School::classCache()->setLoadTable('classrooms', false);
	}
	
	/**
	 * nesne tablo adini dondurur
	 */
	public function getDbTableName()
	{
		return $this->_dbTable;
	}

	/**
	 * yeni dayTime kod numarasini hazirla
	 *
	 * @return void
	 */
	public function getNewCode()
	{
		return getNewTableCode( array ("table"=>$this->_dbTable) );
	}
		
	/**
	 * diziler guncel mi, okundu mu?
	 *
	 * @return void
	 */
	private function _updateArrays()
	{
		$intend = array("code" => $this->_code);
		$dayTimeList = School::classCache()->getClassroom($this->_classroom)->getDayTimeList();
		$this->_info = getFromArray($dayTimeList, $intend);
		$this->_info = $this->_info[0];
	}


	/**
	 * $_POST formda olup da database'de karsiligi olmayan
	 * degiskenlere gereken veriyi donduren uygulayici metot
	 *
	 * @param $columnName string
	 * @return string
	 */
	public function formImplement($columnName, $senderMethod = "")
	{
		switch ($columnName) {
			case 'code' :
				return $this->_code;
				break;
				
			case 'classroom' :
				return $this->_classroom;

			case 'status':
				$School = School::classCache();
				
				switch ($senderMethod) {
					case "add":
						$School->readClassrooms(array('readDayTimes'=>true));
						$Classroom = $School->getClassroom($this->_classroom);
						if ($Classroom->getInfo("status") == "notUsed") {
							return "notUsed";
						} else {
							return "used";
						}

					case "update":
						$Classroom = $School->getClassroom($this->_classroom);
						return $Classroom->getInfo("status");

					case "delete":
						return "deleted";
				}
			
			default:
				return $this->getInfo($columnName);
		}
	}
}
?>
