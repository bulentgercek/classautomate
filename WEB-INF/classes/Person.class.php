<?php

/**
 * Person : Public
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
abstract class Person
{

		/**
		 * ilgili db tablo adi
		 */
		private $_dbTable = "person";

		/**
		 * genel construct metodu
		 */
		public function __construct($code = "")
		{
				if ($code != NULL) {
						$this->_code = $code;
						/**
						 * kisiye ait bir CHANGES class'i yarat ve
						 * tarih limitlerini kayit tarihi ile gunumuz olarak ayarla
						 */
						$this->Changes = new Changes($this->getDbTableName(), $this->_code);
				} else {
						$this->_code = getNewTableCode(array("table" => $this->_dbTable));
				}
		}
		/**
		 * kisinin degisiklikler listesini dondur
		 */
		public function getChangeList()
		{
				return $this->Changes->getList();
		}
		/**
		 * kisinin secilen sinifa gore durumunun 'active' oldugu
		 * tarihi ve saatleri donduren metot
		 * 
		 * @param object
		 */
		public function getActiveDateTimesByClassroom($Classroom)
		{
				return $this->Changes->getFilteredList(array('changeField' => 'status', 'changeValue' => 'active', 'Classroom' => $Classroom, 'Person' => $this));
		}
		/**
		 * info array'inin icerigini degistir
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
										SendToDb::update($this, array_merge($this->_info, $array));
										break;
						}
				}
		}
		/**
		 * info array'inden bilgi cagir
		 */
		public function getInfo($key = NULL)
		{
				$this->_updateArrays();

				if ($key == NULL) {
						return $this->_info;
				} else {
						return $this->_info[$key];
				}
		}
		/**
		 * nesne tablo adini dondurur
		 */
		public function getDbTableName()
		{
				return $this->_dbTable;
		}
		/**
		 * diziler guncel mi, okundu mu?
		 *
		 * @return void
		 */
		private function _updateArrays()
		{
				$intend = array("code" => $this->_code);
				$personList = School::classCache()->getPeopleList();
				$this->_info = getFromArray($personList, $intend);
				$this->_info = $this->_info[0];
		}
		/**
		 * abstract formImplement
		 */
		public abstract function formImplement($columnName, $senderMethod = "");
		/**
		 * kisi $_POST verisinde sinif olarak SbyRoom secilmis mi?
		 *
		 * @return boolean
		 */
		public function isClassroomSbyRoomInPost()
		{
				if ($_POST["classCounter"] == 1) {
						foreach ($_POST as $key => $value) {
								if (stripos($key, "sbyRoom") !== false)
										return true;
						}
						return false;
				}
		}
		/**
		 * post verisinde sinif kaydi farki var mi?
		 * 
		 * @return boolean
		 */
		public function isClassroomChangedInPost()
		{
				$postClassrooms = NULL;
				foreach ($_POST as $key => $value) {
						if (stripos($key, "payment_") !== false) {
								$expKey = explode("_", $key);
								$postClassrooms[] = $expKey[1];
						}
				}
				$currentClassrooms = explode(",", $this->getInfo("classroom"));

				if ($postClassrooms != NULL) {
						if (arrayDifference($currentClassrooms, $postClassrooms) != NULL)
								return true;
						else
								return false;
				} else {
						return false;
				}
		}
		/**
		 * post verisinde sinif kaydi farki var mi?
		 * 
		 * @return boolean
		 */
		public function isStatusChangedToDeletedInPost()
		{
				$tcAction = splitUpperCaseWords(getTransferInfo('tc', $_POST));
				if ($tcAction == 'delete')
						return true;
		}
		/**
		 * post verisinde degisen classroom kayitlarini dondur
		 * eger kayit degisti ise degisenler array olarak,
		 * aksi takdirde NULL olarak donecek
		 * 
		 * @return array
		 */
		public function getClassroomChangesInPost()
		{
				$postClassrooms = NULL;
				foreach ($_POST as $key => $value) {
						if (stripos($key, "payment_") !== false) {
								$expKey = explode("_", $key);
								$postClassrooms[] = $expKey[1];
						}
				}
				$currentClassrooms = explode(",", $this->getInfo("classroom"));

				$finalResults["removed"] = arrayDifference($currentClassrooms, $postClassrooms);
				$finalResults["post"] = $postClassrooms;
				$finalResults["current"] = $currentClassrooms;

				return $finalResults;
		}
		/**
		 * sinif kayitlarinda aktif sinif var mi?
		 *
		 * @return boolean
		 */
		public function isThereAnyActiveClassroomInPost($returnType = 'boolean')
		{
				$result = false;
				$postClassroomChanges = getArrayKeyValue($this->getClassroomChangesInPost(), 'post');
				foreach ($postClassroomChanges as $key => $value) {
						if ($returnType == 'boolean') {
								if (School::classCache()->getClassroom($value)->getInfo('status') == 'active')
										$result = true;
						}
						if ($returnType == 'list') {
								$result[$key] = array('code' => $value, 'status' => School::classCache()->getClassroom($value)->getInfo('status'));
						}
				}
				return $result;
		}
}

?>
