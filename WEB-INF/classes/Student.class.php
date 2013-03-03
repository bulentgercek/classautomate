<?php

/**
 * Ogrenci Nesnesi
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Student extends Person
{

		/**
		 * ogrenci bilgiler dizisi
		 */
		protected $_info = NULL;

		/**
		 * ogrenci kodu
		 */
		protected $_code;

		/**
		 * changes nesnesi
		 * 
		 * @var Object
		 */
		protected $Changes;

		/**
		 * ogrencinin istenilen sınıfta 
		 * hangi tarihlerde yer aldigini 
		 * tarih ve saat olarak donduren metot 
		 * 
		 * @return Array
		 */
		public function getActiveDateTimesByClassroom(Classroom $Classroom)
		{
				$classroomCode = $Classroom->getInfo('code');
				return $this->Changes->getFilteredList(array('changeField' => 'classroom', 'changeValue' => $classroomCode, 'Classroom' => $Classroom, 'Person' => $this));
		}
		/**
		 * ogrencinin ilgili sinifta aktif oldugu ders listesini dondur
		 * 
		 * @return Array
		 */
		public function getActiveLectureList(Classroom $Classroom)
		{
				$Fc = new FluxCapacitor();

				$studentActiveDateTimes = $this->getActiveDateTimesByClassroom($Classroom);
				$studentActiveLectureList = array();

				if ($studentActiveDateTimes != null) {
						foreach ($studentActiveDateTimes as $key => $value) {

								$Fc->setValues(array('classroomCode' => $Classroom->getInfo('code'),
										'startDateTime' => $value['startDateTime'],
										'limitDateTime' => $value['endDateTime']));

								$filteredLectures = $Fc->getLecture();

								/**
								 * sinif numaralarını temizle
								 * (active olmayan numaraları atladigimizdan
								 * numaralar atladıgi yerde sifirlaniyor.
								 * dolayisiyle artik dogru olmayan ders no'suna gerek kalmadi.)
								 */
								if ($filteredLectures != null) {
										foreach ($filteredLectures as $fKey => $fValue) {
												unset($filteredLectures[$fKey]['count']);
										}
										$studentActiveLectureList = array_merge($studentActiveLectureList, $filteredLectures);
								}
						}
				}
				return $studentActiveLectureList;
		}
		/**
		 * ogrencinin tum odeme ve odeme metodu degisikliklerini dizi olarak donduren metot
		 * 
		 * @return Array
		 */
		public function getPaymentAndPeriodChanges()
		{
				$result = array();
				$paymentChanges = $this->Changes->getFilteredList(array('changeField' => 'payment', 'extraFields' => 'paymentPeriod', 'Person' => $this));
				$paymentPeriodChanges = $this->Changes->getFilteredList(array('changeField' => 'paymentPeriod', 'extraFields' => 'payment', 'Person' => $this));

				/**
				 * Hangisi daha fazla sayida bilgi iceriyorsa o dogrudur
				 * Eger her ikisi de ayni sayidaysa; o zaman en son kayida sahip olan dogru olacaktir.
				 */
				if (count($paymentChanges) > count($paymentPeriodChanges)) {
						$result = $paymentChanges;
				} else if (count($paymentPeriodChanges) > count($paymentChanges)) {
						$result = $paymentPeriodChanges;
				} else {
						$lastOfPaymentChanges = $paymentChanges[count($paymentChanges) - 1];
						$lastOfPaymentPeriodChanges = $paymentPeriodChanges[count($paymentPeriodChanges) - 1];

						$whoIsVeryLast = getDateTimeDiff($lastOfPaymentChanges['startDateTime'], $lastOfPaymentPeriodChanges['startDateTime'], 'type');

						if ($whoIsVeryLast == 0)
								$result = $paymentChanges;
						if ($whoIsVeryLast == 1)
								$result = $paymentChanges;
						if ($whoIsVeryLast == -1)
								$result = $paymentPeriodChanges;
				}

				/**
				 * finalde dondurulecek diziyi hazirla
				 */
				$finalList = array();

				foreach ($result as $key => $value) {
						$classroomValues = getArrayKeyValue(explode('<+>', $value['currents']), 0);

						if ($value['field'] == 'payment') {
								$paymentValues = $value['fieldValue'];

								if ($value['paymentPeriod'] != '') {
										$paymentPeriodValues = $value['paymentPeriod'];
								} else {
										$paymentPeriodValues = getArrayKeyValue(explode('<+>', $value['currents']), 1);
								}
						}
						if ($value['field'] == 'paymentPeriod') {
								$paymentPeriodValues = $value['fieldValue'];

								if ($value['payment'] != '') {
										$paymentValues = $value['payment'];
								} else {
										$paymentValues = getArrayKeyValue(explode('<+>', $value['currents']), 1);
								}
						}

						$finalList[$key] = array('classroom' => $classroomValues, 'payment' => $paymentValues, 'paymentPeriod' => $paymentPeriodValues,
								'startDateTime' => $value['startDateTime'], 'endDateTime' => $value['endDateTime']);
				}

				return $finalList;
		}
		/**
		 * ogrencinin istenilen sınıfta yer aldigi tarihleri (aktif) baz alarak
		 * getPaymentAndPeriodChanges metodundan yararlanarak
		 * ve filtreleme yaparak, odeme ve odeme metodu degisikliklerini dizi olarak donduren metot 
		 * 
		 * @return Array
		 */
		public function getPaymentAndPeriodChangesByClassroom(Classroom $Classroom)
		{
				$paymentAndPeriodChanges = $this->getPaymentAndPeriodChanges();

				if (debugger('Student'))
						echo '<br>->Tum veriler : ';
				if (debugger('Student'))
						var_dump($paymentAndPeriodChanges);
				if (debugger('Student'))
						echo '<br>->Istenilen veriler : ';
				$classroomCode = $Classroom->getInfo('code');
				$currentKey = 0;

				foreach ($paymentAndPeriodChanges as $key => $value) {
						// Genel degiskenler
						$isAddOk = false;
						$addState = null;
						$newStart = false;
						$codeKey = (string) array_search($classroomCode, explode(',', $value['classroom']));
						if (debugger('Student'))
								var_dump($key);
						/**
						 * istenilen sinif ogrencinin odeme/odeme methodu degisiklikleri listesinde var mi?
						 */
						if ($codeKey != '') {
								$currentPaymentValue = getArrayKeyValue(explode(',', $value['payment']), $codeKey);
								$currentPaymentPeriodValue = getArrayKeyValue(explode(',', $value['paymentPeriod']), $codeKey);

								if (debugger('Student'))
										var_dump('Previous : ' . $previousPaymentValue . ' - ' . $previousPaymentPeriodValue);
								if (debugger('Student'))
										var_dump('Current : ' . $currentPaymentValue . ' - ' . $currentPaymentPeriodValue);

								/**
								 * verilerde (odeme veya odeme methodu) degislik var mi? Yeni kayit mi?
								 */
								if ($currentPaymentValue != $previousPaymentValue || $currentPaymentPeriodValue != $previousPaymentPeriodValue) {

										$isAddOk = true;
										/**
										 * kayit baslatilmis mi?
										 */
										if ($previousState == 'start') {
												/**
												 * kayit onceden acilmis dolayisiyla kapat
												 */
												$addState = 'end';
												/**
												 * yeni kaydi baslat
												 */
												if (debugger('Student'))
														echo "Bu arada onceki kayit kapandi. Yeni kayit var aciyorum.<br>";
												$newStart = true;

												/**
												 * baslatilmamis! o zaman temiz bir tane ac
												 */
										} else {
												if (debugger('Student'))
														echo "Kayit baslatilmamis. Temiz bir tane aciyorum.<br>";
												$addState = 'start';
										}
								} else {
										if (debugger('Student'))
												echo "Önceki kayıtlarla sonuc tamamen ayni. Hic birsey yapma.<br>";
								}

								$previousPaymentValue = $currentPaymentValue;
								$previousPaymentPeriodValue = $currentPaymentPeriodValue;
								/**
								 * istenilen sinif bulunamadi. Ekleme yapilmayacak.
								 */
						} else {
								if (debugger('Student'))
										echo "Kayıt istenilen sınıf değil!<br>";
								$isAddOk = false;
						}

						if ($isAddOk) {
								if ($addState == 'end') {
										$finalList[$currentKey] = array_merge($finalList[$currentKey], array('endDateTime' => $value['startDateTime']));
										$previousState = 'end';
								}

								if ($addState == 'start') {
										if (count($finalList) > 0)
												$currentKey++;
										$finalList[$currentKey] = array('payment' => $currentPaymentValue, 'paymentPeriod' => $currentPaymentPeriodValue,
												'startDateTime' => $value['startDateTime']);
										$previousState = 'start';
								}

								if ($newStart) {
										if (count($finalList) > 0)
												$currentKey++;
										$finalList[$currentKey] = array('payment' => $currentPaymentValue, 'paymentPeriod' => $currentPaymentPeriodValue,
												'startDateTime' => $value['startDateTime']);
										$previousState = 'start';
								}
						}

						/**
						 * son kayit mi? eger son kayitsa ve son olarak acik kayit kalmissa kapat
						 */
						if ($key == count($paymentAndPeriodChanges) - 1) {
								if (debugger('Student'))
										echo "Son Kayıt geldi.<br>";
								if ($previousState == 'start') {
										if (debugger('Student'))
												echo "Acik kayit varmis kapatiyorum.<br>";
										$finalList[$currentKey] = array_merge($finalList[$currentKey], array('endDateTime' => getDateTimeAsFormatted()));
								}
						}
				}

				return $finalList;
		}
		/**
		 * ogrencinin odeme donemi, odeme donemine ait odeme miktari, odeme donemi numarasini
		 * ve odeme donemi ders numarasini aktif derslere gore listeleyen metot
		 * 
		 * @return Array
		 */
		public function getLectureDetailsByClassroom(Classroom $Classroom)
		{
				$Fc = new FluxCapacitor();

				$paymenAndPeriodChangesByClassroom = $this->getPaymentAndPeriodChangesByClassroom($Classroom);
				$lectureDetailsByClassroom = array();

				$paymentTermNo = 0;

				if ($paymenAndPeriodChangesByClassroom != null) {
						foreach ($paymenAndPeriodChangesByClassroom as $key => $value) {
								$lectureNo = 0;

								$Fc->setValues(array('classroomCode' => $Classroom->getInfo('code'),
										'startDateTime' => $value['startDateTime'],
										'limitDateTime' => $value['endDateTime']));

								$filteredLectures = $Fc->getLecture();
								//var_dump($Fc);
								/**
								 * para akis bilgilerini isle
								 * odeme donemine gore diziyi hazirla
								 */
								$lectureCountByPeriod = getLectureCountByPeriod($Classroom, $value['paymentPeriod']);
								$offLectures = 0;

								if ($filteredLectures != null) {
										foreach ($filteredLectures as $Fkey => $FValue) {
												if ($filteredLectures[$Fkey]['lectureStatus'] == 'on') {
														$filteredLectures[$Fkey]['payment'] = $value['payment'];
														$filteredLectures[$Fkey]['paymentPeriod'] = $value['paymentPeriod'];

														if ($value['paymentPeriod'] != 'fixed') {
																if ($filteredLectures[$Fkey]['count'] > $lectureCountByPeriod) {
																		$lectureNo = $filteredLectures[$Fkey]['count'] % $lectureCountByPeriod;
																} else {
																		$lectureNo++;
																}
														} else {
																$lectureNo++;
														}

														if ($lectureNo == 1)
																$paymentTermNo++;

														$filteredLectures[$Fkey]['paymentTermNo'] = $paymentTermNo;
														$filteredLectures[$Fkey]['paymentTermLectureNo'] = $lectureNo;
														$filteredLectures[$Fkey]['paymentTermLectureCount'] = ($filteredLectures[$Fkey]['count'] - $offLectures);
														unset($filteredLectures[$Fkey]['count']);
														unset($filteredLectures[$Fkey]['lectureStatus']);
												} else {
														unset($filteredLectures[$Fkey]);
														$offLectures++;
												}
										}
										$lectureDetailsByClassroom = array_merge($lectureDetailsByClassroom, $filteredLectures);
								}
						}
				}
				return $lectureDetailsByClassroom;
		}
		/**
		 * ogrencinin derslere gore detayli odeme akisini dizi olarak dondur
		 * 
		 * @return Array
		 */
		public function getCashFlowByClassroom(Classroom $Classroom)
		{
				return Accountant::classCache()->getStudentCashFlowByClassroom($this, $Classroom);
		}
		/**
		 * ogrencinin para akisi sonucunu istenilen kritere($type) gore dondur
		 * 
		 * @return String
		 */
		public function getCashStatus(Classroom $Classroom, $type = null)
		{
				return Accountant::classCache()->getStudentCashStatus($this, $Classroom, $type);
		}
		/**
		 * ogrenciyi istenilen siniftan cikartan metot
		 * 
		 * @return void
		 */
		public function removeFromClassroom(Classroom $Classroom)
		{
				// ogrencinin sinif bilgisini al
				$expClassroom = explode(',', $this->getInfo('classroom'));

				// istenmeyen sinifi diziden cikart
				unset($expClassroom[findStringInArray($code, $expClassroom)]);

				// eger tum siniflar cikti ise Bekleme Odasina yerlestir
				if (empty($expClassroom))
						$expClassroom[0] = 'sbyRoom';

				// yeni sinif bilgisini kayitlara isle
				$firstKey = key($expClassroom);

				if (count($expClassroom) > 1) {
						$stringClassroom = implode(',', $expClassroom);
				} else {
						$stringClassroom = $expClassroom[$firstKey];
				}

				$this->setInfo(array('tc:update' => 'updateClassroom|direct', 'classroom' => $stringClassroom));
		}
		/**
		 * İstenilen tarihten başlayarak, öğrencinin sıradaki ödeme tarihini
		 * ders günü olarak döndüren metot
		 * 
		 * @return Date String
		 */
		public function getNextPaymentDateByClassroom(Classroom $Classroom)
		{
				return Accountant::classCache()->getStudentNextPaymentDate($this, $Classroom);
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
				$expColumnName = explode("_", $columnName);

				switch ($columnName) {
						case 'code' :
								return $this->_code;
								break;

						case 'homePhone' :
								return '';
								break;

						case 'classroom' :
								if ($senderMethod == 'add' || $senderMethod == 'update') {
										foreach ($_POST as $k => $v) {
												if (strpos($k, 'payment') !== false) {
														$expClassInfo = explode('_', $k);
														if ($classString == '')
																$reagent = '';
														else
																$reagent = ',';
														$classString .= $reagent . $expClassInfo[1];
												}
										}
								} else {
										return $this->getInfo($columnName);
								}
								return $classString;
								break;

						case 'paymentPeriod' :
								if ($senderMethod == 'add' || $senderMethod == 'update') {
										foreach ($_POST as $k => $v) {
												if (strpos($k, 'method') !== false) {
														$expClassInfo = explode('_', $k);
														if ($paymentPeriodString == '')
																$reagent = '';
														else
																$reagent = ',';
														$paymentPeriodString .= $reagent . $v;
												}
										}
								} else {
										return $this->getInfo($columnName);
								}
								return $paymentPeriodString;
								break;

						case 'payment' :
								if ($senderMethod == 'add' || $senderMethod == 'update') {
										foreach ($_POST as $k => $v) {
												if (strpos($k, 'payment') !== false) {
														$expClassInfo = explode('_', $k);
														if ($paymentString == '')
																$reagent = '';
														else
																$reagent = ',';
														$paymentString .= $reagent . $v;
												}
										}
								} else {
										return $this->getInfo($columnName);
								}
								return $paymentString;
								break;

						case 'status' :
								$newClassrooms = getArrayKeyValue($this->getClassroomChangesInPost(), 'post');

								if ($senderMethod == "add" || $senderMethod == "update") {

										if ($this->isClassroomSbyRoomInPost()) {
												$statusResult = "notUsed";
										} else if ($this->isThereAnyActiveClassroomInPost()) {
												$classroomStatusList = $this->isThereAnyActiveClassroomInPost('list');

												foreach ($classroomStatusList as $key => $value) {
														$Classroom = School::classCache()->getClassroom($value['code']);
														if ($Classroom->getInfo('status') == 'notUsed') {
																$Classroom->setInfo(array('status' => 'used', 'tc:update' => 'updateClassroomForm|direct'));
																$statusResult .= "used";
														} else if ($Classroom->getInfo('status') == 'used') {
																$statusResult .= "used";
														} else if ($Classroom->getInfo('status') == 'active') {
																$statusResult .= "active";
														}
														if ($key < count($classroomStatusList) - 1)
																$statusResult .= ',';
												}
										} else {
												foreach ($newClassrooms as $key => $value) {
														$statusResult .= "used";
														$Classroom = School::classCache()->getClassroom($value);
														if ($Classroom->getInfo('status') == 'notUsed')
																$Classroom->setInfo(array('status' => 'used', 'tc:update' => 'updateClassroomForm|direct'));
														if ($key < count($newClassrooms) - 1)
																$statusResult .= ',';
												}
										}
								}

								if ($senderMethod == "delete") {
										$statusResult = "deleted";
								}

								return $statusResult;
								break;

						case 'recordDate':
								if ($senderMethod == "add") {
										return getDateTimeAsFormatted();
								}

								if ($senderMethod == "update") {
										return $this->getInfo('recordDate');
								}

						case 'bossMode' :
								return '0';

						default:
								return $this->getInfo($columnName);
				}
		}
}

?>
