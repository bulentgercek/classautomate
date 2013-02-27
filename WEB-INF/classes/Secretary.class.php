<?php

/**
 * Sekreter Personel Nesnesi
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Secretary extends Person
{

		/**
		 * kisi bilgileri dizisi
		 */
		protected $_info = NULL;

		/**
		 * sekreter kodu
		 */
		protected $_code;

		/**
		 * changes nesnesi
		 * 
		 * @var Object
		 */
		protected $Changes;

		/**
		 * $_POST formda olup da database'de karsiligi olmayan
		 * degiskenlere gereken veriyi donduren uygulayici metot
		 *
		 * @param $columnName string
		 * @return string
		 */
		public function formImplement($columnName, $senderMethod = "")
		{
				$expColumnName = explode("_", $columnName);
				switch ($columnName) {
						case 'code' :
								return $this->_code;
								break;

						case 'job' :
								return "";
								break;

						case 'homePhone' :
								return '';
								break;

						case 'height' :
								return "";
								break;

						case 'weight' :
								return "";
								break;

						case 'shoeSize' :
								return "";
								break;

						case 'classroom' :
								return "";
								break;

						case 'paymentPeriod' :
								return "percent";
								break;

						case 'payment' :
								return "0";
								break;

						case 'status' :
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

						case 'recordDate':
								return getDateTimeAsFormatted();

						case 'bossMode' :
								return '0';
								break;

						default:
								return $this->getInfo($columnName);
				}
		}
}

?>
