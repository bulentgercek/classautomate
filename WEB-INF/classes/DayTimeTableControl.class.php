<?php

/**
 * classautomate - DayTime Tablosu icin AJAX kontrol sinifi
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class DayTimeTableControl
{

		/**
		 * sonuc verileri
		 *
		 * @var string $resultString
		 */
		public $resultString;

		/**
		 * veritabani islemi
		 *
		 * @var string
		 */
		public $dbProcess;

		/**
		 * form dayTime bilgileri
		 *
		 * @var string
		 */
		private $_info;

		/**
		 * dayTime objesi
		 */
		private $_DayTime;

		/**
		 * DB ve Session class nesneleri
		 *
		 * @var objects
		 */
		public $_Db;

		/**
		 * construct asamasi
		 *
		 * @return void
		 */
		public function __construct()
		{
				$this->_Db = Db::ClassCache();
				$this->dbProcess = $_GET['process'];
				$this->_info = array('classroom' => $_GET['classroom']);

				/** AJAX cagirilan sinifin klasorunde basladigi icin ROOT klasore iniyoruz */
				chdir('../../');

				if ($_GET['process'] == 'add') {
						$this->_DayTime = School::classCache()->getClassroom($this->_info['classroom'])->getDayTime();
						$this->_info = merge2Array($this->_info, array('day' => $_GET['day'], 'time' => $_GET['time'], 'endTime' => $_GET['endTime'], 'tc:updateClassroom' => 'updateClassroomForm|direct'));
						$this->addDayTime();
				}

				if ($_GET['process'] == 'delete') {
						$this->_info['code'] = $_GET['code'];
						$this->deleteDayTime();
				}
		}
		/**
		 * direkt echo ise sonucu gonder
		 *
		 * @return string
		 */
		public function __toString()
		{
				return (string) $this->resultString;
		}
		/**
		 * ekle
		 */
		public function addDayTime()
		{
				$this->_DayTime->setInfo($this->_info);
				$this->_info['code'] = $this->_DayTime->getInfo('code');

				$this->resultString = $this->_info['code'];
		}
		/**
		 * silme islemi
		 */
		public function deleteDayTime()
		{
				School::classCache()->getClassroom($this->_info['classroom'])->deleteDayTime($this->_info['code']);

				$this->resultString = true;
		}
}

/** baslangic fonksiyonlari */
require 'Start.function.php';

/** veritabani ulasimi icin acik session cagiriliyor */
Session::classCache()->start();

/** kontrol class'ini yarat */
$ajaxResult = new DayTimeTableControl();

/** gonderilen string sonucu  */
echo $ajaxResult;
?>
