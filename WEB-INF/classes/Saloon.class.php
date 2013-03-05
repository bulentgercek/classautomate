<?php

/**
 * Salon Nesnesi
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Saloon
{

		/**
		 * salon bilgileri dizisi
		 */
		private $_info;

		/**
		 * sinif kodu
		 */
		private $_code;

		/**
		 * ilgili db tablo adi
		 */
		private $_dbTable = "saloon";

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
		 * istenilen salon bilgisini dondur
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
		 * istenilen salon bilgisini
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
				$saloonList = School::classCache()->getSaloonList();
				$this->_info = getFromArray($saloonList, $intend);
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
						case 'name':
								return $this->getInfo('name');
								break;
						case 'address':
								return $this->getInfo('address');
								break;
						case 'status':
								if ($senderMethod == "add") {
										return "notUsed";
								}
								if ($senderMethod == "update") {
										return $this->getInfo('status');
								}
								if ($senderMethod == "delete") {
										return "deleted";
								}
								break;

						default:
								return $this->getInfo($columnName);
				}
		}
}

?>
