
<?php

/**
 * Tum Muhasebe Islemleri
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Accountant
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
		private $_startDateTimeLimit;
		private $_endDateTimeLimit;

		/**
		 * ogrencilerin kalan paralari bu degiskende saklanacak
		 */
		private $_studentMoneyLeftInCase;
		/**
		 * ogrencilerin para akislari bu degiskende saklanacak
		 */
		private $_studentCashFlow;

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
						self::$_instance = new Accountant();
				}
				return self::$_instance;
		}
		/**
		 * muhasebe yapılacak donemin tarih araliklarini set eden metot
		 */
		public function setTimeLimits($startTimeLimit, $endTimeLimit)
		{
				$this->_startDateTimeLimit = $startTimeLimit;
				$this->_endDateTimeLimit = $endTimeLimit;
		}
		/**
		 * gelir-giderler listesini dondur
		 * 
		 * @return array
		 */
		public function getIncomeExpenseList($type = null, Student $Student = null, Classroom $Classroom = null)
		{
				$list = School::classCache()->getIncomeExpenseList();
				$intend = array();

				if ($type != null) {
						$intend['type'] = $type;
				}
				if ($Student != null) {
						$intend['onBehalfOf'] = $Student->getInfo('code');
				}
				if ($Classroom != null) {
						$intend['classroom'] = $Classroom->getInfo('code');
				}

				if ($intend)
						return getFromArray($list, $intend);
				else
						return $list;
		}
		/**
		 * bir ogrencinin gelirler toplamini dondur
		 */
		public function getStudentIncomesTotal(Student $Student, Classroom $Classroom = null)
		{
				$list = $this->getIncomeExpenseList('+', $Student, $Classroom);
				$totalValue = 0;
				if ($list != null) {
						foreach ($list as $value) {
								$totalValue += $value['amount'];
						}
				}
				return $totalValue;
		}
		/**
		 * gider veya giderlerin toplamlarını hesapla aylara böl
		 * 
		 * @return array
		 */
		public function calculateIncExpTotal($type)
		{
				$total = array();
				$incomeExpenseList = $this->getIncomeExpenseList($type);

				$firstYear = getArrayKeyValue(explode('-', $incomeExpenseList[0]['dateTime']), 0);
				$firstMonth = getArrayKeyValue(explode('-', $incomeExpenseList[0]['dateTime']), 1);

				$totalCount = 0;

				if ($incomeExpenseList != NULL) {
						foreach ($incomeExpenseList as $key => $value) {
								$expDate = explode('-', $value['dateTime']);

								if ($key == 0) {
										$tempYear = $firstYear;
										$tempMonth = $firstMonth;
								}

								if ($expDate[0] == $tempYear) {
										if ($expDate[1] == $tempMonth) {
												$currentTotalValue = $total[$totalCount]['total'] + $value['amount'];
										} else {
												$totalCount++;
												$currentTotalValue = $value['amount'];
										}

										$total[$totalCount] = array('type' => $type, 'year' => $expDate[0],
												'month' => $expDate[1],
												'total' => $currentTotalValue);
								} else {
										$total[++$totalCount] = array('type' => $type, 'year' => $expDate[0],
												'month' => $expDate[1],
												'total' => $value['amount']);
								}
								$tempYear = $expDate[0];
								$tempMonth = $expDate[1];
						}
				}
				return $total;
		}
		/**
		 * gider ve gelir listesini alır ve aylara göre liste çıkarır
		 * ay listesini seçilmiş olan dile göre ayarlar
		 * 
		 * @return array
		 */
		public function getProfitList()
		{
				$languageJSON = Setting::classCache()->getInterfaceLang();

				$incomesTotal = $this->calculateIncExpTotal('+');
				$expensesTotal = $this->calculateIncExpTotal('-');

				/**
				 * en kucuk tarihli degeri belirliyoruz
				 */
				$incomeExpenseList = $this->getIncomeExpenseList();

				$firstDateTime = $incomeExpenseList[0]['dateTime'];
				$lastDateTime = $incomeExpenseList[count($incomeExpenseList) - 1]['dateTime'];

				$listCount = getDateTimeDiff($firstDateTime, $lastDateTime, 'm') + 1;

				$firstYear = getArrayKeyValue(explode('-', $incomeExpenseList[0]['dateTime']), 0);
				$firstMonth = getArrayKeyValue(explode('-', $incomeExpenseList[0]['dateTime']), 1);

				for ($i = 0; $i <= $listCount; $i++) {
						if ($i == 0) {
								$currentYear = $firstYear;
								$currentMonth = $firstMonth;
						} else {
								if ($currentMonth == 12) {
										$currentYear++;
										$currentMonth = 1;
								} else {
										$currentMonth++;
								}
						}
						$currentMonth = sprintf("%02d", $currentMonth);

						$profitListIncomesTotal = getArrayKeyValue(getArrayKeyValue(getFromArray($incomesTotal, array('year' => $currentYear, 'month' => $currentMonth)), 0), 'total');
						$profitListExpensesTotal = getArrayKeyValue(getArrayKeyValue(getFromArray($expensesTotal, array('year' => $currentYear, 'month' => $currentMonth)), 0), 'total');
						
						if ($profitListIncomesTotal == '')
								$profitListIncomesTotal = 0;
						if ($profitListExpensesTotal == '')
								$profitListExpensesTotal = 0;

						$profitList[$i] = array('profitSeason' => $languageJSON->classautomate->month[$currentMonth - 1] . ' ' . $currentYear,
								'incomesTotal' => $profitListIncomesTotal,
								'expensesTotal' => $profitListExpensesTotal,
								'profitTotal' => $profitListIncomesTotal - $profitListExpensesTotal
						);
				}

				return $profitList;
		}
		/**
		 * verilen tarih aralıklarına gore 
		 * ogrenci borc listesini donemlere gore donduren metot
		 * 
		 * @return array
		 */
		public function getStudentCashFlowByClassroom(Student $Student, Classroom $Classroom)
		{
				/**
				 * ogrenci kod numarasi
				 */
				$studentCode = $Student->getInfo('code');
				$classroomCode = $Classroom->getInfo('code');
				$cashResult = array();
				/**
				 * debug
				 */
				if (debugger('Accountant'))
						echo '<br><b>' . $Student->getInfo('code') . ' Nolu ögrencinin ' . $Classroom->getInfo('code') . ' nolu sınıfa ait odeme/odeme donemi bilgileri (Aktif Oldugu Dönemler)</b> : ';
				$studentLectureDetailsByClassroom = $Student->getLectureDetailsByClassroom($Classroom);

				if (debugger('Accountant'))
						var_dump($studentLectureDetailsByClassroom);

				/**
				 * ogrencinin kasaya giren toplam odemelerini hazirla
				 */
				if (!isset($this->_studentMoneyLeftInCase[$studentCode])) {
						$this->_studentMoneyLeftInCase[$studentCode] = $this->getStudentIncomesTotal($Student, $Classroom);
						if (debugger('Accountant'))
								var_dump('studentIncomesTotal : ' . $this->_studentMoneyLeftInCase[$studentCode]);
				} else {
						return $this->_studentCashFlow[$studentCode][$classroomCode];
				}

				foreach ($studentLectureDetailsByClassroom as $key => $value) {
						/**
						 * ders numarasi
						 */
						$lectureNo = $key + 1;
						/**
						 * degiskenler varsayilanlara donusturuluyor
						 */
						$isCalculate = true;
						$isOut = false;
						/**
						 * dersin baslangic ve bitis zamanlari aliniyor
						 */
						$endTime = $Classroom->getDayTime($value['dayTimeCode'])->getInfo('endTime');

						/**
						 * borc bilgisi varsayilan olarak CONTINUE kabul ediliyor
						 * ve payment degiskeni yaratiliyor (FIXED icin sifirlanacak)
						 */
						$studentDebtInfo = 'debtInfo_1'; //continue
						$payment = $value['payment'];

						/**
						 * ogrencinin odeme periodu icinde (maksimum) kac ders hakki oldugu hesaplaniyor
						 * eger fixed degil ise;
						 */
						if ($value['paymentPeriod'] != 'fixed') {
								/**
								 * odeme periodunu kullarak haftalik sabit deger ile period icindeki hafta sayisi carpiliyor
								 * Orn. : Haftada 2 ders * 4 (Aylik) = 8 ders
								 */
								$lectureCountByPeriod = getLectureCountByPeriod($Classroom, $value['paymentPeriod']);
						}
						/**
						 * eger odeme periodu fixed ise;
						 */ else {
								/**
								 * odeme donemindeki ders sayisi kacinci ders ise o kadar olacak.
								 * Yani 2/2, 3/3 diye gidecek. Her ders, o ders icin hakki bir kez daha genisleyecek.
								 * Sonsuzlugun anlami budur;)
								 */
								$lectureCountByPeriod = $value['paymentTermLectureNo'];
						}


						if (debugger('Accountant'))
								var_dump('paymentTermLectureNo : ' . $value['paymentTermLectureNo'] . '/' . $lectureCountByPeriod);
						/**
						 * ogrencinin bir ders icin ne kadar odemesi gerektigi hesaplaniyor
						 * eger fixed ise;
						 */
						if ($value['paymentPeriod'] == 'fixed') {
								/**
								 * fixed odemenin ilk gunu ise bir derslik ucret odemenin tamami olacak
								 */
								if ($value['paymentTermLectureNo'] == 1) {
										$oneLecturePrice = $payment;
								}
								/**
								 * fixed odemenin ilk gunu degilse fixed odeme degisene kadar
								 * odeme ucreti sifir olacak
								 */ else {
										$payment = 0;
								}
						}
						/**
						 * eger fixed degil ise;
						 * odeme ucreti, period icinde kac ders varsa bolunerek
						 * tek ders ucreti bulunacak 
						 */ else {
								$oneLecturePrice = $payment / $lectureCountByPeriod;
						}


						/**
						 * ogrencinin gecen her ders biriken borcu hesaplaniyor
						 */
						if ($value['paymentPeriod'] != 'fixed') {
								$totalLectureDebtByTerm = $value['paymentTermLectureNo'] * $oneLecturePrice;
						} else {
								$totalLectureDebtByTerm = $payment;
						}
						if (debugger('Accountant'))
								var_dump('totalLectureDebtByTerm : ' . $totalLectureDebtByTerm . ' / ' . $payment);

						///////////// DURUMA GORE ODEME ISLEMLERI ////////////////////////////////////
						/**
						 * ogrencinin kasada parasi yok, kalmadi veya eksiye dustu ise;
						 */
						if ($this->_studentMoneyLeftInCase[$studentCode] <= 0) {

								/**
								 * odeme yontemi FIXED ise;
								 */
								if ($value['paymentPeriod'] == 'fixed') {
										if ($value['paymentTermLectureNo'] >= 1) {
												$studentDebtInfo = 'debtInfo_10'; //fixedPaymentWarning
										}
								}
								/**
								 * odeme yontemi FIXED degil ise;
								 */
								else {
										/**
										 * Ogrencinin siniftaki ilk donemi ise;
										 */
										if ($lectureNo == $value['paymentTermLectureNo']) {
												/**
												 * ...ve ogrenci sinifin ilk dersine giriyorsa bu ders DENEME dersi kabul edilecek
												 */
												if ($lectureNo == 1) {
														$studentDebtInfo = 'debtInfo_2'; //try
														$isCalculate = false;
												}
												/**
												 * ...ve ogrenci sinifinin ilk dersinde degil ise;
												 */
												else {
														/**
														 * ...ve ders saati doldu ise ogrenci siniftan cikarilacak;
														 */
														if (getDateTimeDiff(getTimeAsFormatted(), $endTime, 'type') > 0) {
																$studentDebtInfo = 'debtInfo_3'; //noPaymentAndOut
																$isCalculate = false;
																$isOut = true;
														}
														/**
														 * ...ve ders saati daha dolmadi ise uyari verilecek, ders saati bitimi beklenecek
														 */
														else {
																$studentDebtInfo = 'debtInfo_4'; //minPaymentRestrictWarning
																$isCalculate = false;
														}
												}
										}
										/**
										 * ogrencinin sinifta ilk donemi degil ise;
										 */
										else {
												/**
												 * ...ve ogrenci yeni doneminde ve ilk dersi ise; 
												 */
												if ($value['paymentTermLectureNo'] == 1) {
														/**
														 * ...ve onceki doneminden eksi bakiye ile geldi ise;
														 */
														if ($this->_studentMoneyLeftInCase < 0) {
																/**
																 * ...ders sonu gelmisse siniftan cikartma islemini onayla
																 */
																if (getDateTimeDiff(getTimeAsFormatted(), $endTime, 'type') > 0) {
																		$studentDebtInfo = 'debtInfo_5'; //newTermNegativeBalanceNoPaymentAndOut
																		$isCalculate = false;
																		$isOut = true;
																}
																/**
																 * ...ders sonu gelmemisse uyari yap, hesapla ve derse girebilsin
																 */
																else {
																		$studentDebtInfo = 'debtInfo_6'; //newTermNegativeBalanceMinPaymentRestrictWarning
																		$isCalculate = false;
																}
														}
														/**
														 * ...ve onceki donemden eksi bakiye ile gelmemis ise;
														 */
														else {
																$studentDebtInfo = 'debtInfo_7'; //newTermPaymentWarning
														}
												}
												/**
												 * ogrenci yeni doneminde ve ilk dersinde degil ise (diger dersler); 
												 */
												else {
														/**
														 * ...ve ders sonu gelmisse siniftan cikartma islemini onayla
														 */
														if (getDateTimeDiff(getTimeAsFormatted(), $endTime, 'type') > 0) {
																$studentDebtInfo = 'debtInfo_8'; //newTermNoPaymentAndOut
																$isCalculate = false;
																$isOut = true;
														}
														/**
														 * ...ve ders sonu gelmemisse uyari yap, hesapla ve derse girebilsin
														 */
														else {
																$studentDebtInfo = 'debtInfo_9'; //newTermMinPaymentRestrictWarning
																$isCalculate = false;
														}
												}
										}
								}
						}
						///////////// BORC HESAPLAMA BOLUMU ////////////////////////////////////
						/**
						 * hesaplama yapilacak mi?
						 */
						if ($isCalculate) {
								/**
								 * ogrencinin kasadaki parasi bitmediyse;
								 */
								if ($this->_studentMoneyLeftInCase[$studentCode] > 0) {
										/**
										 * kasadaki paradan biriken ders ucretlerini cikart
										 */
										$this->_studentMoneyLeftInCase[$studentCode] -= $oneLecturePrice;
								}

								if (debugger('Accountant'))
										var_dump('studentMoneyLeftInCase : ' . $this->_studentMoneyLeftInCase[$studentCode]);
						}
						/**
						 * donemlik borc bilgilerini diziye ekle
						 */
						$cashResult[] = array('lectureNo' => $lectureNo,
								'date' => $value['date'],
								'dayTime' => $value['dayTimeCode'],
								'paymentTermNo' => $value['paymentTermNo'],
								'paymentTermLectureNo' => $value['paymentTermLectureNo'],
								'paymentTermLectureCount' => $value['paymentTermLectureCount'],
								'studentDebtInfo' => $studentDebtInfo,
								'paymentPeriod' => $value['paymentPeriod'],
								'payment' => $value['payment'],
								'lecturePrice' => $oneLecturePrice,
								'studentMoneyLeftInCase' => $this->_studentMoneyLeftInCase[$studentCode]);
						/**
						 * diziyi ana degiskene yedekle
						 */
						$this->_studentCashFlow[$studentCode][$classroomCode] = $cashResult;
						
						if (debugger('Accountant'))
								var_dump($cashResult[count($cashResult) - 1]);

						/**
						 * ogrencinin siniftan cikartma islemi onaylanmissa uygula
						 */
						if ($isOut) {
								if (debugger('Accountant'))
										var_dump('Ogrenci siniftan cikariliyor : ' . $Student->getInfo('code') . ' / ' . $Student->getInfo('name') . ' ' . $Student->getInfo('surname'));
								return $cashResult;
						}
				}
				return $cashResult;
		}
		/**
		 * ogrencinin para akisinin guncel, final degerini istenilen kritere($type) gore dondurur
		 * 
		 * @return Array
		 */
		public function getStudentCashStatus(Student $Student, Classroom $Classroom, $type = null)
		{
				$cashFlowList = $this->getStudentCashFlowByClassroom($Student, $Classroom);
				$cashStatus['info'] = $cashFlowList[count($cashFlowList) - 1]['studentDebtInfo'];
				$cashStatus['value'] = $cashFlowList[count($cashFlowList) - 1]['studentMoneyLeftInCase'];

				switch ($type) {
						case 'schoolClaim': case 'studentDebt':
								if ($cashStatus['value'] < 0)
										$result['value'] = abs($cashStatus['value']);
								else
										$result['value'] = 0; break;

						case 'schoolDebt': case 'studenClaim':
								if ($cashStatus['value'] < 0)
										$result['value'] = 0;
								else
										$result['value'] = $cashStatus['value']; break;

						case null: $result['value'] = $cashStatus['value'];
				}
				$result['info'] = $cashStatus['info'];

				return $result;
		}
		/**
		 * İstenilen tarihten başlayarak, öğrencinin sıradaki ödeme tarihini
		 * ders günü olarak döndüren metot
		 * 
		 * @return Date String
		 */
		public function getStudentNextPaymentDateTime(Student $Student, Classroom $Classroom)
		{
				/**
				 * Flux
				 */
				$Fc = new FluxCapacitor();
				/**
				 * degiskenler hazirlaniyor
				 */
				$lectureList = $Student->getLectureDetailsByClassroom($Classroom);
				/**
				 * en azindan bir ders yapilmis olmasi gerekiyor
				 */
				if ($lectureList) {
						$finalKey = count($lectureList) - 1;

						$lectureNo = $lectureList[$finalKey]['paymentTermLectureNo'];
						$paymentPeriod = $lectureList[$finalKey]['paymentPeriod'];
						$lectureTotal = getLectureCountByPeriod($Classroom, $paymentPeriod);
						$periodMultiplier = getPeriodMultiplier($paymentPeriod);
						/**
						 * eger odeme donemi ders gunu son gunde ise;
						 */
						if ($lectureNo == $lectureTotal)
								$futureLectureCount = 1;
						/**
						 * eger odeme donemi ders gunu son ders gunune gelmedi ise;
						 */
						if ($lectureNo < $lectureTotal)
								$futureLectureCount = ($lectureTotal - $lectureNo) + 1;

						if (debugger('Accountant'))
								var_dump($futureLectureCount . ' ders ilerideki tarihi bulursak o NEXT PAYMENT DATE dir');

						$startDate = getDateTimeAsFormatted();
						$DateTime = new DateTime($startDate);
						$endDate = $DateTime->modify('+' . $periodMultiplier . ' week')->format('Y-m-d H:i:s');
						/**
						 * Flux'a zaman dilimlerini tanimliyoruz
						 */
						$Fc->setValues( array(  'classroomCode' => $Classroom->getInfo('code'),
																		'startDateTime'=>$startDate,
																		'limitDateTime'=>$endDate) );
						/**
						 * Flux'dan gerekli diziyi cekiyoruz,
						 * Bu arada cektiğimiz zaman aralığından tatil varsa,
						 * Tatil ders sayısı kadar eklemeyi de ihmal etmiyoruz.
						 */
						$holidayLectureCount = count($Fc->getHolidayLectureList());
						$nextPaymentLectureList = $Fc->getLecture(NULL, $futureLectureCount + $holidayLectureCount);

						$nextPaymentLecture = end($nextPaymentLectureList);
						$nextPaymentDateTime = $nextPaymentLecture['date'] . ' ' . $Classroom->getDayTime($nextPaymentLecture['dayTimeCode'])->getInfo('time');
				} else {
						$nextPaymentDateTime = Setting::classCache()->getInterfaceLang()->classautomate->main->none;
				}
				return $nextPaymentDateTime;
		}
		/**
		 * egitmen odeme periodlarini ve kazanılan parayı ders dizisi olarak işleyen metot
		 * 
		 * @return Array
		 */
		public function getClassroomPaymentPeriods(Classroom $Classroom)
		{
				/**
				 * genel diziler ve veriler okunuyor, hazirlaniyor
				 */
				$lectureList = $Classroom->getActiveLectureList();
				$period = strtolower(getFirstUpperCaseWord($Classroom->getInfo('instructorPaymentPeriod')));
				$periodMultiplier = getPeriodMultiplier($period);
				$periodLectureCount = getLectureCountByPeriod($Classroom, $period);
				/**
				 * degiskenler tanimlaniyor
				 */
				$lectureNo = 0;
				$periodCount = 1;
				/**
				 * period ders listesi hazirlaniyor
				 */
				foreach ($lectureList as $key => $value) {
						/**
						 * ders tatil degil ise;
						 */
						if ($lectureList[$key]['lectureStatus'] != 'off') {
								/**
								 * odeme donemi sonu geldi mi?
								 */
								if ($lectureNo == $periodLectureCount) {
										/**
										 * degerleri sifirla ve yeni odeme donemini baslat
										 */
										$lectureNo = 0; $periodCount++;
								}
								/**
								 * eger ilk gunse period o gun baslamis
								 */
								$lectureList[$key]['lectureNo'] = ++$lectureNo; 
								$lectureList[$key]['periodNo'] = $periodCount;
								/**
								 * activeLectureList'den gelen ders durumu 
								 * gereksiz oldugundan diziden cikariliyor
								 */
								unset($lectureList[$key]['lectureStatus']);
						} 
						/**
						 * ders tatil ise;
						 */
						else {
								unset($lectureList[$key]);
						}
				}
				/**
				 * sinifin ogrencilerinin ders odemeleri toplanarak period ders listesine islenecek
				 */
				$studentList = $Classroom->getStudentList();

				foreach ((array)$studentList as $key => $value) {
						$Student = School::classCache()->getStudent($value['code']);
						$studentCashFlow = $this->getStudentCashFlowByClassroom($Student, $Classroom);

						/**
						 * Dersler tek tek geciliyor derslerden gelen para toplanarak ana diziye ekleniyor
						 */
						$periodEarnedTotal = 0;
						foreach ($lectureList as $key => $value) {
								$currentLectureFlow = getFromArray($studentCashFlow, array('date'=>$value['date'], 'dayTime'=>$value['dayTimeCode']));
								$lectureList[$key]['earnedMoney'] += $currentLectureFlow[0]['lecturePrice'];
								$periodEarnedTotal += $lectureList[$key]['earnedMoney'];
								$lectureList[$key]['earnedMoneyTotal'] = $periodEarnedTotal;
								
								if ($lectureList[$key]['lectureNo'] == $periodLectureCount) {
										$lectureList[$key]['earnedMoneyByPeriod'] = $periodEarnedTotal;
								}
						}
				}
				return $lectureList;
		}
		/**
		 * getClassroomPaymentPeriods metodundan gelen diziyi kullanarak
		 * periodlara bolen sadelestirilmis odeme dizisi hazırlayan metot
		 * 
		 * @return Array
		 */
		public function getClassroomPayments(Classroom $Classroom)
		{
				/**
				 * sınıfın derslere gore ayrilmis periyodik gelir listesi
				 */
				$classroomLecturesWithPayments = $this->getClassroomPaymentPeriods($Classroom);

				/**
				 * derslere gore ayrilmis liste sadelestiriliyor
				 */
				foreach ($classroomLecturesWithPayments as $value) {
						/**
						 * Egitmenin alacağı para her ders bitiminde katlanarak artacaktır
						 * Period sonunu (Ay sonunu) beklememizin sebebi
						 * sonradan aklıma eğitmenin her an sınıftan çıkabilecek veya
						 * sınıfın her an kapanabilecek olmasıdır
						 * Böylece hesap unutulmayacaktır her an ödeme bilinir
						 */
						$periodPaymentDate = $Classroom->getDayTime($value['dayTimeCode'])->getInfo('endTime');
						$periodPaymentList[] = array(		'periodNo'=>$value['periodNo'],
																						'periodPaymentDate'=>$value['date'] . ' ' . $periodPaymentDate,
																						'paymentTotal'=>$value['earnedMoneyTotal']);
				}
				return $periodPaymentList;
		}
		/**
		 * egitmenin siradaki odeme tarihini donduren
		 */
		public function getInstuctorNextPaymentDateTime(Classroom $Classroom)
		{
				/**
				 * Flux
				 */
				$Fc = new FluxCapacitor();
				/**
				 * egitmen
				 */
				$Instructor = School::classCache()->getInstructor($Classroom->getInfo('instructor'));
				/**
				 * sinifin aktif oldugu ders listesi
				 */
				$lectureList = $this->getClassroomPaymentPeriods($Classroom);
				/**
				 * en azindan bir ders yapilmis olmasi gerekiyor
				 */
				if ($lectureList) {
						$finalKey = count($lectureList) - 1;

						$lectureNo = $lectureList[$finalKey]['lectureNo'];
						$paymentPeriod = strtolower(getFirstUpperCaseWord($Classroom->getInfo('instructorPaymentPeriod')));
						$lectureTotal = getLectureCountByPeriod($Classroom, $paymentPeriod);
						$periodMultiplier = getPeriodMultiplier($paymentPeriod);
						/**
						 * eger odeme donemi ders gunu son gunde ise;
						 */
						if ($lectureNo == $lectureTotal)
								$futureLectureCount = 1;
						/**
						 * eger odeme donemi ders gunu son ders gunune gelmedi ise;
						 */
						if ($lectureNo < $lectureTotal)
								$futureLectureCount = ($lectureTotal - $lectureNo) + 1;

						if (debugger('Accountant'))
								var_dump($futureLectureCount . ' ders ilerideki tarihi bulursak o NEXT PAYMENT DATE dir');
								
						$startDate = getDateTimeAsFormatted();
						$DateTime = new DateTime($startDate);
						$endDate = $DateTime->modify('+' . $periodMultiplier . ' week')->format('Y-m-d H:i:s');
						/**
						 * Flux'a zaman dilimlerini tanimliyoruz
						 */
						$Fc->setValues( array(  'classroomCode' => $Classroom->getInfo('code'),
																		'startDateTime'=>$startDate,
																		'limitDateTime'=>$endDate) );
						/**
						 * Flux'dan gerekli diziyi cekiyoruz,
						 * Bu arada cektiğimiz zaman aralığından tatil varsa,
						 * Tatil ders sayısı kadar eklemeyi de ihmal etmiyoruz.
						 */
						$holidayLectureCount = count($Fc->getHolidayLectureList());
						$nextPaymentLectureList = $Fc->getLecture(NULL, $futureLectureCount + $holidayLectureCount);

						$nextPaymentLecture = end($nextPaymentLectureList);
						$nextPaymentDateTime = $nextPaymentLecture['date'] . ' ' . $Classroom->getDayTime($nextPaymentLecture['dayTimeCode'])->getInfo('time');
				} else {
						$nextPaymentDateTime = Setting::classCache()->getInterfaceLang()->classautomate->main->none;
				}
				return $nextPaymentDateTime;
		}
		/**
		 * Eğitmen gelirleri listesini alır, dizinin son rakamı toplam gelir kabul edilir,
		 * Eğitmene ödenenen giderleri bu toplam gelirden çıkarır,
		 * ve ödeme şekline göre(Percent, Fixed) sonucu verir
		 */
		public function getInstructorPaymentInCase(Classroom $Classroom)
		{
				/**
				 * egitmen ve gerekli tüm bilgileri çağırılıyor
				 */
				$Instructor = School::classCache()->getInstructor($Classroom->getInfo('instructor'));
				/**
				 * ilgili sınıfta biriken toplam getiri
				 */
				$classroomPayments = $this->getClassroomPayments($Classroom);
				$classroomTotalIncomeInCase = $classroomPayments[count($classroomPayments)-1]['paymentTotal'];
				/**
				 * egitmene sınıfla ilgili olarak yapılan ödemeler (giderler)
				 */
				$instructorExpenseList = getFromArray(School::classCache()->getIncomeExpenseList(), array(
						'type'=>'-', 'subType'=>'6', 'classroom'=>$Classroom->getInfo('code')
				));
				
				$instructorTotalExpenses = 0;
				foreach ($instructorExpenseList as $value) {
						$instructorTotalExpenses += $value['amount'];
				}
				/**
				 * egitmenin getirisini anlaşılan ödeme şekline göre değerlendiriyoruz
				 */
				$paymentType = getArrayKeyValue(splitUpperCaseWords($Classroom->getInfo('instructorPaymentPeriod')), 0);
				$instructorPayment = $Classroom->getInfo('instructorPayment');

				if ($paymentType == 'percent') {
						$instructorPaymentInCase = ($classroomTotalIncomeInCase * $instructorPayment) / 100;
				}
				/**
				 * egitmenin kasada kalan parasından, bugüene kadar kendisine yapılan ödemeleri çıkartıyoruz
				 */
				return $instructorPaymentInCase - $instructorTotalExpenses;
		}
}
