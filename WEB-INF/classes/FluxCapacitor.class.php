<?php

/**
 * Ders sayilarini hesaplayan sinif
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class FluxCapacitor
{

		/**
		 *
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 * genel degiskenler
		 */
		private $_startDateTime;
		private $_limitDateTime;
		private $_classroomCode;
		private $_lectureList;
		private $_holidayLectureList;

		/**
		 * Classın construct methodu yoktur
		 *
		 * @return void
		 */
		private function __construct()
		{
				
		}
		/**
		 * Singleton fonksiyonu
		 *
		 * @access public
		 * @return object
		 */
		public static function & classCache()
		{
				if (!self::$_instance) {
						self::$_instance = new FluxCapacitor();
				}
				return self::$_instance;
		}
		/**
		 * Baslangic ve bitis tarihleri veriliyor
		 */
		public function setValues($values)
		{
				if ($values['startDateTime'] != NULL)
						$this->_startDateTime = $values['startDateTime'];
				if ($values['limitDateTime'] != NULL)
						$this->_limitDateTime = $values['limitDateTime'];
				if ($values['classroomCode'] != NULL)
						$this->_classroomCode = $values['classroomCode'];
				/**
				 * degerlerde en ufak bir degisiklik olursa 
				 * hafizaya alinmis butun degerleri sifirla 
				 */
				$this->_lectureList = NULL;
				$this->_holidayLectureList = NULL;
		}
		/**
		 * Baslangic ve bitis tarihleri arasindaki
		 * hafta sayisini dondurur
		 * 
		 * @return string int
		 */
		public function getWeekDiff()
		{
				return getDateTimeDiff($this->_limitDateTime, $this->_startDateTime, 'w');
		}
		/**
		 * istenen sinif koduna gore sinif ders sayisini dondur
		 * 
		 * @return string int
		 */
		public function getDayTimeCount()
		{
				$Classroom = School::classCache()->getClassroom($this->_classroomCode);
				return $Classroom->getDayTimeCount();
		}
		/**
		 * istenilen sinifin baslangic tarihini dondurur
		 * 
		 * @return string int
		 */
		public function getStartDate()
		{
				$Classroom = School::classCache()->getClassroom($this->_classroomCode);
				return $Classroom->getInfo('startDate');
		}
		/**
		 * istenilen sinifin baslangic tarihini dondurur
		 * 
		 * @return string int
		 */
		public function getStartDayTimeKey()
		{
				$Classroom = School::classCache()->getClassroom($this->_classroomCode);
				return findKeyValueInArray($Classroom->getDayTimeList(), 'code', $Classroom->getInfo('startDayTime'));
		}
		/**
		 * verilen tarih ve saat araliklarina gore
		 * tatil gunlerini karsilastirarak
		 * denk gelen tatil gunlerini ve ders saatleri
		 * donduren metot
		 * 
		 * @return array
		 */
		public function getHolidayLectureList()
		{
				if ($this->_holidayLectureList == NULL) {

						$holidayList = School::classCache()->getHolidayList();
						$Classroom = School::classCache()->getClassroom($this->_classroomCode);
						$ClassroomDayTimeList = $Classroom->getDayTimeList();

						$holidayLectureList = array();
						foreach ($ClassroomDayTimeList as $ClassroomDayTimeListValue) {

								if ($holidayList != null) {
										foreach ($holidayList as $holidayValue) {

												$expStartDateTime = explode(' ', $holidayValue['startDateTime']);
												$expEndDateTime = explode(' ', $holidayValue['endDateTime']);

												$hStartDate = $expStartDateTime[0];
												$hEndDate = $expEndDateTime[0];
												$hStartTime = $expStartDateTime[1];
												$hEndTime = $expEndDateTime[1];
												$hType = $holidayValue['type'];
												$hInfo = $holidayValue['info'];

												$expStartDateTime = explode(' ', $this->_startDateTime);
												$expLimitDateTime = explode(' ', $this->_limitDateTime);

												$limitDate = $expLimitDateTime[0];

												/**
												 * Asama 1 : Kesisen tarihleri tespit et, sınırları sınıfın başladığı gün ile
												 * verilen limit (şimdiki) zaman arasında sınırla.
												 * Böylece tarih hesaplarında (Örn : Gün sayımları) gereksiz bilgileri hesap etmeyeceğiz.
												 */
												// if hStartDate <= now
												if (getDateTimeDiff($hStartDate, $limitDate, 'type') == -1 ||
														getDateTimeDiff($hStartDate, $limitDate, 'type') == 0) {

														// if hStartDate > cStartDate
														if (getDateTimeDiff($hStartDate, $Classroom->getInfo('startDate'), 'type') == 1) {
																$iStartDate = $hStartDate;
																$iStartTime = $hStartTime;
														} else if (getDateTimeDiff($hStartDate, $Classroom->getInfo('startDate'), 'type') == -1) {
																// if hStartDate < cStartDate
																$iStartDate = $Classroom->getInfo('startDate');
																$iStartTime = '00:00:00';
														} else if (getDateTimeDiff($hStartDate, $Classroom->getInfo('startDate'), 'type') == 0) {
																// if hStartDate == cStartDate
																$iStartDate = $Classroom->getInfo('startDate');
																$iStartTime = $hStartTime;
														}
														// if hEndDate > now
														if (getDateTimeDiff($hEndDate, $limitDate, 'type') == 1) {
																$iEndDate = $limitDate;
																$iEndTime = '23:59:59';
														} else {
																// if hEndDate <= now
																$iEndDate = $hEndDate;
																$iEndTime = $hEndTime;
														}

														/**
														 * Asama 2 : Tatillerle kesisen ders gunlerini hazirla
														 */
														$weekDayList = getWeekDays($ClassroomDayTimeListValue['day'], $iStartDate, $iEndDate);

														/**
														 * Asama 3 : Cikan tatil olacak ders gunleri listesi degerlerinin yanına 
														 * ders saatlerini ekle (kesisen saatler dışındaki tarihleri listeden cikart)
														 */
														$iStartDateWithTime = $iStartDate . " " . $iStartTime;
														$iEndDateWithTime = $iEndDate . " " . $iEndTime;

														foreach ($weekDayList as $weekDayListKey => $weekDayListValue) {
																$lessonDateWithTime = $weekDayListValue . ' ' . $ClassroomDayTimeListValue['time'];
																$lessonDateWithEndTime = $weekDayListValue . ' ' . $ClassroomDayTimeListValue['endTime'];

																if (getDateTimeDiff($iStartDateWithTime, $lessonDateWithTime, 'type') == 1 ||
																		getDateTimeDiff($iEndDateWithTime, $lessonDateWithEndTime, 'type') == -1) {
																		unset($weekDayList[$weekDayListKey]);
																} else {
																		$weekDayList[$weekDayListKey] = $weekDayListValue . '<+>' . $ClassroomDayTimeListValue['code'] . '<+>' . $hType . '<+>' . $hInfo;
																}
														}
														// $lectureList : Tatillerle kesisen ders gunlerini ve saatlerini tek bir array içerisine birleştir			
														$holidayLectureList = directMerge2Array($holidayLectureList, $weekDayList);
												}
												// listeyi temize cek ve tarih-dayTimeCode dizisine donustur
												foreach ($holidayLectureList as $holidayLectureListKey => $holidayLectureListValue) {
														$expHolidayLectureListValue = explode('<+>', $holidayLectureListValue);
														$this->_holidayLectureList[$holidayLectureListKey]['date'] = $expHolidayLectureListValue[0];
														$this->_holidayLectureList[$holidayLectureListKey]['dayTimeCode'] = $expHolidayLectureListValue[1];
														$this->_holidayLectureList[$holidayLectureListKey]['type'] = $expHolidayLectureListValue[2];
														$this->_holidayLectureList[$holidayLectureListKey]['info'] = $expHolidayLectureListValue[3];
												}
										}
								}
						}
				}
				return $this->_holidayLectureList;
		}
		/**
		 * belirlenen tarihler arasindaki 
		 * ders sayisini donduren metot
		 * 
		 * @return string int
		 */
		public function getLectureCount()
		{
				return count($this->getLecture());
		}
		/**
		 * verilen tarihte ders varsa
		 * dersin durumunu döndüren metot
		 */
		public function getLecture(Array $array = NULL)
		{
				if ($this->_lectureList == NULL) {

						$Classroom = School::classCache()->getClassroom($this->_classroomCode);
						$ClassroomDayTimeList = $Classroom->getDayTimeList();

						// sinifin donem limitleri varsa oku ve diziye dondur
						$termInfo = $Classroom->getTermLimits();

						// diziler hazirlaniyor
						$lectureList = array();
						$lectureListSorter = array();
						$weekDayListSorter = array();
						$maxCount = false;

						foreach ($ClassroomDayTimeList as $ClassroomDayTimeListValue) {

								$weekDayList = getWeekDays($ClassroomDayTimeListValue['day'], $this->_startDateTime, $this->_limitDateTime);

								$isTimeOk = true;

								foreach ($weekDayList as $weekDayListKey => $weekDayListValue) {
										/**
										 * sinif bir limit berlirlenmis mi?
										 */
										if ($termInfo['type'] != null) {
												/**
												 * belirlenen limit tarih limiti mi?
												 */
												if ($termInfo['type'] == 'date') {
														$lessonDateWithTime = $weekDayListValue . ' ' . $ClassroomDayTimeListValue['time'];

														if (getDateTimeDiff($lessonDateWithTime, $termInfo['limit'], 'type') == 1)
																$isTimeOk = false;
														else
																$isTimeOk = true;
												}

												/**
												 * belirlenen limit ders sayisi mi?
												 */
												if ($termInfo['type'] == 'count') {
														$maxCount = getArrayKeyValue(explode(getFirstUpperCaseWord($termInfo['limit']), $termInfo['limit']), 0);
												}
										}

										if ($isTimeOk) {
												$weekDayList[$weekDayListKey] = $weekDayListValue . '<+>' . $ClassroomDayTimeListValue['code'];
												$weekDayListSorter[$weekDayListKey] = $weekDayListValue . '<+>' . $ClassroomDayTimeListValue['time'] . '-' . $ClassroomDayTimeListValue['endTime'];
										} else {
												unset($weekDayList[$weekDayListKey]);
										}
								}

								// $lectureList : ders gunlerini ve saatlerini tek bir array içerisine birleştir			
								$lectureList = directMerge2Array($lectureList, $weekDayList);
								$lectureListSorter = directMerge2Array($lectureListSorter, $weekDayListSorter);
						}

						/**
						 * siraya diziciyi duzenli (saate gore) siraya diz
						 */
						asort($lectureListSorter);

						/**
						 * siraya dizici ile ders sirasini sirala
						 */
						$lectureList = sortArrayByArray($lectureList, $lectureListSorter, 'key');

						/**
						 * ders limiti verilmis ise limit otesindeki dersleri sil
						 */
						if ($maxCount) {
								foreach ($lectureList as $key => $value) {
										if ($key > $maxCount)
												unset($lectureList[$key]);
								}
						}

						/**
						 * sinifin startDate ve startDateTime'i dikkate alarak duzenle
						 */
						$startDayTimeTime = $ClassroomDayTimeList[$this->getStartDayTimeKey()]['time'];
						$startDate = $Classroom->getInfo('startDate');

						/**
						 * once cikarilacaklar listesini olustur
						 */
						foreach ($ClassroomDayTimeList as $key => $value) {
								if ($value['time'] < $startDayTimeTime)
										$removeList[] = $value['code'];
						}
						/**
						 * sonrada listedekileri listeden cikart
						 */
						foreach ($lectureList as $key => $value) {
								$expValue = explode('<+>', $value);
								if ($expValue[0] == $startDate) {
										foreach ((array) $removeList as $removeListValue) {
												if ($removeListValue == $expValue[1])
														unset($lectureList[$key]);
										}
								}
						}
						$lectureList = array_values($lectureList);

						/**
						 * tatil olan derslerin listesini al
						 * ve son olarak ders listesine STATUS hanesi olarak isle
						 */
						$holidayLectureList = $this->getHolidayLectureList();

						foreach ($lectureList as $lectureListKey => $lectureListValue) {
								$lectureStatus = on;
								$expLectureListValue = explode('<+>', $lectureListValue);
								$this->_lectureList[$lectureListKey]['count'] = $lectureListKey + 1;
								$this->_lectureList[$lectureListKey]['date'] = $expLectureListValue[0];
								$this->_lectureList[$lectureListKey]['dayTimeCode'] = $expLectureListValue[1];

								// hic tatil olmadiginda hata verdiginden NULL kontrolu yapmak zorunda kaldim
								if ($holidayLectureList != NULL) {
										foreach ($holidayLectureList as $holidayLectureListKey => $holidayLectureListValue) {
												if ($holidayLectureListValue['date'] == $this->_lectureList[$lectureListKey]['date'] &&
														$holidayLectureListValue['dayTimeCode'] == $this->_lectureList[$lectureListKey]['dayTimeCode']) {
														$lectureStatus = 'off';

														// Eger personel tatili veya sınıf secilmis ve tatil denmişse 
														$this->_lectureList[$lectureListKey]['type'] = $holidayLectureListValue['type'];
														$expHolidayInfoValue = explode('|', $holidayLectureListValue['info']);
														if ($holidayLectureListValue['type'] == 'personnel') {
																$this->_lectureList[$lectureListKey]['infoCode'] = $expHolidayInfoValue[0];
																if ($expHolidayInfoValue[1] != 0) {
																		$this->_lectureList[$lectureListKey]['personnelReplacement'] = $expHolidayInfoValue[1];
																		$lectureStatus = 'on';
																}
														}
														if ($holidayLectureListValue['type'] == 'classroom') {
																$this->_lectureList[$lectureListKey]['infoCode'] = $expHolidayInfoValue[0];
																if ($expHolidayInfoValue[0] != $this->_classroomCode)
																		$lectureStatus = 'on';
														}
												}
										}
								}
								$this->_lectureList[$lectureListKey]['lectureStatus'] = $lectureStatus;
						}
				}

				if ($array != NULL) {
						$result = getFromArray($this->_lectureList, $array);
						return $result;
				} else {
						return $this->_lectureList;
				}
		}
		/**
		 * verilen tarihler arasina denk gelen
		 * tatillerin listesini donduren metot
		 */
		public function getIntersectedHolidayList()
		{
				$holidayList = School::classCache()->getHolidayList();

				if ($holidayList != null) {
						foreach ($holidayList as $holidayKey => $holidayValue) {
								$hStartDateTime = $holidayValue['startDateTime'];
								$hEndDateTime = $holidayValue['endDateTime'];

								// if startDateTime <= hEndDateTime(now) 
								if (getDateTimeDiff($this->_startDateTime, $hEndDateTime, 'type') == -1 ||
										getDateTimeDiff($this->_startDateTime, $hEndDateTime, 'type') == 0) {

										// if startDateTime <= hStartDateTime(now)
										if (getDateTimeDiff($this->_startDateTime, $hStartDateTime, 'type') == 1 ||
												getDateTimeDiff($this->_startDateTime, $hStartDateTime, 'type') == 0) {

												// if limitDateTime <= hEndDateTime(now)
												if (getDateTimeDiff($this->_limitDateTime, $hEndDateTime, 'type') == -1 ||
														getDateTimeDiff($this->_limitDateTime, $hEndDateTime, 'type') == 0) {

														$intersectedHolidayList[] = $holidayList[$holidayKey];
												}
										}
								}
						}
				}
				return $intersectedHolidayList;
		}
}
