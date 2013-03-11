<?php

/**
 * Gelir-Gider Tipi Nesnesi
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class IncomeExpenseType
{

		/**
		 * gelir-gider tipi bilgileri dizisi
		 */
		private $_info;

		/**
		 * kod
		 */
		private $_code;

		/**
		 * ilgili db tablo adi
		 */
		private $_dbTable = "incexp_types";

		/**
		 * constract metodu
		 * 
		 * @param array
		 */
		public function __construct($code = NULL)
		{
				if ($code != NULL) {
						$this->_code = $code;
				} else {
						$classautomateLastCode = getNewTableCode(array("dbName" => "classautomate", "table" => $this->_dbTable));
						$this->_code = getNewTableCode(array("table" => $this->_dbTable)) + $classautomateLastCode;
				}
		}
		/**
		 * istenilen gelir-gider tipi bilgisini dondur
		 * 
		 * @return string
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
		 * istenilen gelir-gider tipi bilgisini
		 * anahtar kelimeye gore guncelle
		 * 
		 * @param array
		 */
		public function setInfo(Array $array)
		{
				$this->_updateArrays();

				if ($this->_info == NULL) {
						switch (getTransferInfo('tt', $_POST)) {
								case 'post':
										SendToDb::add($this);
										break;
								case 'direct':
										SendToDb::add($this, $array);
										break;
						}
				} else {
						switch (getTransferInfo('tt', $_POST)) {
								case 'post':
										SendToDb::update($this);
										break;
								case 'direct':
										$newArray = array_merge($this->_info, $array);
										SendToDb::update($this, $newArray);
										break;
						}
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
				$incomeExpenseTypeList = School::classCache()->getIncomeExpenseTypeList();
				$this->_info = getFromArray($incomeExpenseTypeList, $intend);
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
						case 'code':
								return $this->_code;
								break;
				}
		}
}

?>
