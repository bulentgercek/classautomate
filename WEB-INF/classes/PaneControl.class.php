<?php

/**
 * classautomate - pano veritabani kontrolu
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class PaneControl
{

		/**
		 * sonuc verileri
		 *
		 * @var string $resultString
		 */
		public $resultString;

		/**
		 * panelin durumu
		 *
		 * @var string
		 */
		public $paneState;

		/**
		 * panel islemi
		 *
		 * @var string
		 */
		public $paneProcess;

		/**
		 * DB ve Session class nesneleri
		 *
		 * @var objects
		 */
		public $Db;
		public $Session;

		/**
		 * construct asamasi
		 *
		 * @return void
		 */
		public function __construct()
		{
				$this->Db = Db::ClassCache();
				$this->Session = Session::classCache();
				$this->paneProcess = $_GET['process'];

				/** klasor duzeyini duzelt */
				chdir('../../');

				if ($_GET['process'] == 'get') {
						$this->get();
				} else if ($_GET['process'] == 'set') {
						$this->paneState = $_GET['state'];
						$this->set();
				}
		}
		/**
		 * direkt echo ise sonucu gonder
		 *
		 * @return string
		 */
		public function __toString()
		{
				//return (string)$this->paneProcess;
				return (string) $this->resultString;
		}
		/**
		 * session'dan bilgileri oku
		 * panelin sonucunu hazirla
		 *
		 * @return void
		 */
		public function get()
		{
				$this->resultString = $this->Session->get('paneState');
		}
		/**
		 * session'a panelin guncel durumunu isle
		 *
		 * @return void
		 */
		public function set()
		{
				$this->Session->set('paneState', $this->paneState);
		}
}

/** baslangic fonksiyonlari */
require 'Start.function.php';

/** session yaratiliyor */
Session::classCache()->start();

/**
 * panel kontrol class'ini yarat
 *
 * @var object
 */
$PaneControl = new PaneControl();

/** gonderilen string sonucu  */
echo $PaneControl;
?>
