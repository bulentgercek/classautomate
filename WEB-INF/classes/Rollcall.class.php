<?php

/**
 * Yoklama Nesnesi
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Rollcall
{

		/**
		 * yoklama bilgileri
		 */
		private $_info;

		/**
		 * rollcall kodu
		 */
		private $_code;

		/**
		 * ilgili db tablo adi
		 */
		private $_dbTable = "rollcall";

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
						$this->_code = getNewTableCode(array("table" => $this->_dbTable));
				}
		}
		/**
		 * istenilen yoklama bilgisini dondur
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
		 * istenilen yoklama bilgisini
		 * anahtar kelimeye gore guncelle
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
				$this->_info = School::classCache()->readRollcallByCode($this->_code);
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

						default:
								return $this->getInfo($columnName);
				}
		}
}

?>
