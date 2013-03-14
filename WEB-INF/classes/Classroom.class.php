<?php

/**
 * Sinif Nesnesi
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Classroom
{

		/**
		 * sinif bilgileri dizisi
		 */
		private $_info = array();

		/**
		 * sinif kodu
		 */
		private $_code;

		/**
		 * dayTime objesi
		 */
		private $_DayTime;

		/**
		 * ilgili db tablo adi
		 */
		private $_dbTable = "classroom";

		/**
		 * changes nesnesi
		 * 
		 * @var Object
		 */
		private $Changes;
		
		/**
		 * activeLectureList yedegi
		 */
		private $_classroomActiveLectureList;
		
		/**
		 * constract metodu@
		 *
		 * @param array
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
		 * sinif bilgisini dondur
		 *
		 * @return string
		 */
		public function getInfo($key = NULL)
		{
				if ($this->_code != "sbyRoom")
						$this->_updateArrays();

				if ($key == NULL) {
						return $this->_info;
				} else {
						if ($key == "dayTime")
								return $this->_info["dayTime"];
						else
								return $this->_info[$key];
				}
		}
		/**
		 * istenilen sinif bilgisini
		 * anahtar kelimeye gore guncelle
		 *
		 * @param array
		 */
		public function setInfo(Array $array)
		{
				if ($this->_code == "sbyRoom")
						$this->_info = array_merge($array, $this->_info);
				else {
						$this->_updateArrays();

						if ($this->_info == NULL) {
								switch (getTransferInfo('tt', $array)) {
										case 'post':
												SendToDb::add($this);
												/**
												 * sinifa dayTime ekleniyor
												 */
												SendToDb::add($this->getDayTime());
												/**
												 * tum sinif listesini update etmeliyiz
												 */
												School::classCache()->readToArrays('classrooms');
												break;
										case 'direct':
												$newArray = array_merge($this->_info, $array);
												SendToDb::add($this, $newArray);
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
		}
		/**
		 * sinifin dayTime listesi
		 */
		public function getDayTimeList($isDeleteOn = false, $weekDayNo = NULL)
		{
				$dayTimeList = $this->getInfo('dayTime');
				/**
				 * eger silinmis dayTime'lari da listelemek istemiyor
				 * ve dolayisiyla $isDeleteOn = false demis ise;
				 */
				if (!$isDeleteOn) {
						if ($weekDayNo)
								$dayTimeList = getFromArray($dayTimeList, array('status' => 'notUsed||used', 'day'=>$weekDayNo));
						else 
								$dayTimeList = getFromArray($dayTimeList, array('status' => 'notUsed||used'));
				}

				if ($dayTimeList != null) {
						foreach ($dayTimeList as $key => $row) {
								$code[$key] = $row['code'];
								$day[$key] = $row['day'];
								$time[$key] = $row['time'];
						}
						array_multisort($day, SORT_ASC, $time, SORT_ASC, $dayTimeList);
				}

				return $dayTimeList;
		}
		/**
		 * dayTime sayisini dondur
		 * 
		 * @return int
		 */
		public function getDayTimeCount($isDeleteOn = false)
		{
				$dayTimeList = $this->getInfo('dayTime');

				if (!$isDeleteOn) {
						$dayTimeList = getFromArray($dayTimeList, array('status' => 'notUsed||used'));
				} else {
						$dayTimeList = $dayTimeList;
				}
				return count($dayTimeList);
		}
		/**
		 * gun/saat nesnesini dondur
		 * 
		 * @return object
		 */
		public function getDayTime($dayTimeCode = NULL)
		{
				/** sinif gun/saat bilgilerini icinde barindiran nesne yaratiliyor */
				if ($dayTimeCode != null) {
						$dayTimeList = $this->getDayTimeList(true);
						$result = findKeyValueInArray($dayTimeList, "code", $dayTimeCode);

						if ($result != -1) {
								$this->_DayTime[$dayTimeCode] = new DayTime($this->_code, $dayTimeCode);
								$CurrentDayTime = $this->_DayTime[$dayTimeCode];
						} else {
								if ($this->_DayTime[$dayTimeCode] == null) {
										$this->_DayTime[$dayTimeCode] = $dayTimeList[$result];
								} else {
										$CurrentDayTime = $this->_DayTime[$dayTimeCode];
								}
						}
				} else {
						$NewDayTime = new DayTime($this->_code);
						$CurrentDayTime = $this->_DayTime[$NewDayTime->getInfo('code')] = $NewDayTime;
				}
				return $CurrentDayTime;
		}
		/**
		 * kod numarasi verilen dayTime'i sil
		 */
		public function deleteDayTime($dayTimeCode)
		{
				$DayTimeObj = $this->getDayTime($dayTimeCode);

				if ($DayTimeObj->getInfo('status') == 'notUsed')
						SendToDb::delete($DayTimeObj, array('code' => $dayTimeCode));

				if ($DayTimeObj->getInfo('status') == 'used') {
						$DayTimeObj->setInfo(array('status' => 'deleted', 'tc:updateClassroom' => 'updateClassroomForm|direct'));
				}
		}
		/**
		 * 1) Sınıfın aktif edildiği startDate verisini okur
		 * 2) Sınıfın aktif edildiği startDayTime bilgisini dayTime listesinde arar ve saatini belirler
		 * 3) İki veriyisi birleştirerek startDateTime haline getirir döndürür
		 * 
		 * @param string type
		 * @return string time
		 */
		public function getStartDateTime($type = NULL)
		{
				$startDate = $this->getInfo('startDate');
				$startDayTimeTime = $this->getDayTime($this->getInfo('startDayTime'))->getInfo('time');
				/**
				 * secilen tipe gore sonucu döndür
				 */
				switch ($type) {
						case 'date': $result = $startDate; break;
						case 'time': $result = $startDayTimeTime; break;
						default: $result = $startDate . ' ' . $startDayTimeTime; break;
				}
				return $result;

		}
		/**
		 * siniftan tum ogrencileri cikartan metot
		 */
		public function emptyClassroom()
		{
				$studentList = $this->getStudentList();

				foreach ($studentList as $key => $value) {
						$Student = School::classCache()->getStudent($value['code']);
						$Student->removeFromClassroom($this);
				}
		}
		/**
		 * Tatil günlerinin çıkarıldığı ders sayısını döndürür
		 * Eger type parametresi olarak holiday verilir ise sadece
		 * tatil günlerinin sayısını döndürecektir
		 * 
		 * @return string
		 */
		public function getLectureCount($type = NULL)
		{
				$Fc = new FluxCapacitor();
				
				$startDateTime = $this->getStartDateTime();
				$limitDateTime = getDateTime('%Y-%m-%d %H:%M:%S');

				$Fc->setValues(array(
					'classroomCode'=>$this->_code, 'startDateTime'=>$startDateTime, 'limitDateTime'=>$limitDateTime
				));
				$lectureCount = $Fc->getLectureCount() - count($Fc->getHolidayLectureList());

				if ($type == 'holiday') {
						return count($Fc->getHolidayLectureList());
				} else {
						return $lectureCount;
				}
		}
		/**
		 * Verdiğimiz tarihte ve saatte sınıfın sorguladığımız tipte bir tatilde olup olmadığını sorgular
		 * Eğer tatile denk geliyor ise ilişkili olan tatilin kod numarasını döndürür
		 * 
		 * @param holidayType string : official, personnal, classroom, custom
		 * @return array
		 */
		public function getHolidayStatus($holidayType)
		{
				$Fc = new FluxCapacitor();
				$dateTime = getDateTime('%Y-%m-%d %H:%M:%S');

				$Fc->setValues(array(
						'classroomCode'=>$this->_code, 'startDateTime'=>$dateTime, 'limitDateTime'=>$dateTime
				));

				$intersectedHolidayList = $Fc->getIntersectedHolidayList();

				$filterTypeOfIt = getFromArray($intersectedHolidayList, array('type' => $holidayType));

				return ( $intersectedHolidayList[0]['code'] != null ? $intersectedHolidayList[0]['code'] : '0' );
		}
		/**
		 * sinifin durumunun 'active' oldugu
		 * tarihi ve saatleri donduren metot
		 */
		public function getActiveDateTimes()
		{
				return $this->Changes->getFilteredList(array('changeField' => 'status', 'changeValue' => 'active', 'Classroom' => $this));
		}
		/**
		 * sinifin aktif oldugu ders gunlerinin listesini dondur
		 */
		public function getActiveLectureList()
		{
				if (!$this->_classroomActiveLectureList) {
						
						$Fc = new FluxCapacitor();

						$classroomActiveDateTimes = $this->getActiveDateTimes();

						$classroomActiveLectureList = array();

						if ($classroomActiveDateTimes != null) {
								foreach ($classroomActiveDateTimes as $key => $value) {

										$Fc->setValues(array('classroomCode' => $this->getInfo('code'),
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
												foreach ($filteredLectures as $key => $value) {
														unset($filteredLectures[$key]['count']);
												}
												$classroomActiveLectureList = array_merge($classroomActiveLectureList, $filteredLectures);
										}
								}
						}
						$this->_classroomActiveLectureList = $classroomActiveLectureList;
				}
				return $this->_classroomActiveLectureList;
		}
		/**
		 * Tarihe bakarak sıradaki dersin bilgilerini döndürür
		 * 
		 * @return array
		 */
		public function getNextLectureDateTime()
		{
				/**
				 * Flux
				 */
				$Fc = new FluxCapacitor();
				
				$activeLectureList = $this->getActiveLectureList();

				/**
				 * herhangibir ders bilgisi geldi mi?
				 */
				if ($activeLectureList) {
						$period = strtolower(getFirstUpperCaseWord($this->getInfo('instructorPaymentPeriod')));
						$periodMultiplier = getPeriodMultiplier($period);
						$futureLectureCount = 1;
						
						$startDate = getDateTimeAsFormatted();
						$DateTime = new DateTime($startDate);
						$endDate = $DateTime->modify('+' . $periodMultiplier . ' week')->format('Y-m-d H:i:s');

						/**
						 * Flux'a zaman dilimlerini tanimliyoruz
						 */
						$Fc->setValues( array(  'classroomCode' => $this->getInfo('code'),
																		'startDateTime'=>$startDate,
																		'limitDateTime'=>$endDate) );
						/**
						 * Flux'dan gerekli diziyi cekiyoruz,
						 * Bu arada cektiğimiz zaman aralığından tatil varsa,
						 * Tatil ders sayısı kadar eklemeyi de ihmal etmiyoruz.
						 */
						$holidayLectureCount = count($Fc->getHolidayLectureList());
						$nextLectureList = $Fc->getLecture(NULL, $futureLectureCount + $holidayLectureCount);

						$nextLecture = end($nextLectureList);
						return $nextLecture['date'] . ' ' . $this->getDayTime($nextLecture['dayTimeCode'])->getInfo('time');					
				} 
				/**
				 * eger hic ders yapilmamis ise;
				 */
				else {
						return $this->getInfo('startDate') . ' ' . $this->getDayTime($this->getInfo('startDayTime'))->getInfo('time');
				}
		}
		/**
		 * sınıfın limit bilgilerini dizi olarak dondur
		 */
		public function getTermLimits()
		{
				$termDateLimit = $this->getInfo('termDateLimit');
				$termCountLimit = $this->getInfo('termCountLimit');

				if ($termDateLimit != '0000-00-00 00:00:00') {
						$termInfo['type'] = 'date';
						$termInfo['limit'] = $termDateLimit;
				} else if ($termCountLimit != '') {
						$termInfo['type'] = 'count';
						$termInfo['limit'] = $termCountLimit;
				} else {
						$termInfo['type'] = null;
				}

				return $termInfo;
		}
		/**
		 * sinif ogrenci listesini dizi olarak dondur
		 *
		 * @return array
		 */
		public function getStudentList()
		{
				$studentList = School::classCache()->getPeopleList("student");
				return getFromArray($studentList, array("classroom" => $this->_code));
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
				$classroomList = School::classCache()->getClassroomList();
				$this->_info = getFromArray($classroomList, $intend);
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
				/**
				 * gelen kisi tipi ogrenci ise;
				 */
				switch ($columnName) {
						case 'code' :
								return $this->_code;
								break;

						case 'name' :
								if ($senderMethod == "add") {
										return School::classCache()->getProgram($_POST['program'])->getInfo('name');
								}
								break;

						case 'termCountLimit' :
								if ($_POST["termStatus"] == "1") {
										if ($_POST["termLimitChooser"] == "countLimit") {
												return $_POST["countDigit"] . ucfirst($_POST["countType"]);
										} else {
												return '';
										}
								} else {
										return '';
								}

						case 'termDateLimit' :
								if ($_POST["termStatus"] == "1") {
										if ($_POST["termLimitChooser"] == "dateLimit") {
												return str_replace("/", "-", $_POST["dateLimitCalendar"]);
										} else {
												return '0000-00-00';
										}
								} else {
										return '0000-00-00';
								}

						case 'asistant' :
								return '';

						case 'asistantPayment' :
								return 0;

						case 'asistantPaymentPeriod' :
								return '';

						case 'recordDate' :
								return getDateTimeAsFormatted();

						case 'startDate' :
								return $this->getInfo('startDate');

						case 'startDayTime' :
								return $this->getInfo('startDayTime');

						case 'notes' :
								return '';

						case 'status' :
								$School = School::classCache();

								switch ($senderMethod) {
										case "add":
												return "notUsed";

										case "update":
												return $this->getInfo("status");

										case "delete":
												return "deleted";

										default:
												return $this->getInfo($columnName);
								}
				}
		}
}

?>
