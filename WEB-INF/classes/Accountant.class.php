<?php

/**
 * Tum Muhasebe Islemleri
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class Accountant {

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
		 * Classın construct methodu yoktur
		 *
		 * @return void
		 */
		private function __construct() {
				
		}

		/**
		 * Singleton fonksiyonu
		 *
		 * @access public
		 * @return object
		 */
		public static function & classCache() {
				if (!self::$_instance) {
						self::$_instance = new Accountant();
				}
				return self::$_instance;
		}

		/**
		 * muhasebe yapılacak donemin tarih araliklarini set eden metot
		 */
		public function setTimeLimits($startTimeLimit, $endTimeLimit) {
				$this->_startDateTimeLimit = $startTimeLimit;
				$this->_endDateTimeLimit = $endTimeLimit;
		}

		/**
		 * gelir-giderler listesini dondur
		 * 
		 * @return array
		 */
		public function getIncomeExpenseList($type = null, $Student = null, $Classroom = null) {
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

				return getFromArray($list, $intend);
		}

		/**
		 * bir ogrencinin gelirler toplamini dondur
		 */
		public function getStudentIncomesTotal($Student, $Classroom = null) {
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
		public function calculateIncExpTotal($type) {
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
		public function getProfitList() {
				$languageJSON = Setting::classCache()->getInterfaceLang();

				$incomesTotal = $this->calculateIncExpTotal('+');
				$expensesTotal = $this->calculateIncExpTotal('-');
				/**
				 * en kucuk tarihli degeri belirliyoruz
				 */
				$incomeExpenseList = $this->getIncomeExpenseList();

				$firstDateTime = $incomeExpenseList[0]['dateTime'];
				$lastDateTime = $incomeExpenseList[count($incomeExpenseList) - 1]['dateTime'];

				$listCount = getDateTimeDiff($firstDateTime, $lastDateTime, 'm');

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
		public function getStudentCashFlowByClassroom($Student, $Classroom) {
				/**
				 * ogrenci kod numarasi
				 */
				$studentCode = $Student->getInfo('code');
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
						$studentDebtInfo = 'continue';
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
												$studentDebtInfo = 'fixedPaymentWarning';
												if (debugger('Accountant'))
														var_dump('------------------> Durum : Sabit Ödeme Dönemi Ödeme Uyarısı');
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
												if (debugger('Accountant'))
														var_dump('------------------> Durum : Ilk donem');
												/**
												 * ...ve ogrenci sinifin ilk dersine giriyorsa bu ders DENEME dersi kabul edilecek
												 */
												if ($lectureNo == 1) {
														$studentDebtInfo = 'try';
														$isCalculate = false;
														if (debugger('Accountant'))
																var_dump('------------------> Durum : Deneme Dersi (İlk ders)');
												}
												/**
												 * ...ve ogrenci sinifinin ilk dersinde degil ise;
												 */
												else {
														if (debugger('Accountant'))
																var_dump('------------------> Durum : İlk Ders Değil!');
														/**
														 * ...ve ders saati doldu ise ogrenci siniftan cikarilacak;
														 */
														if (getDateTimeDiff(getTimeAsFormatted(), $endTime, 'type') > 0) {
																$studentDebtInfo = 'noPaymentAndOut';
																$isCalculate = false;
																$isOut = true;
																if (debugger('Accountant'))
																		var_dump('------------------> Durum : Ders sonuna odeme gelmedi. Siniftan cikarildi.');
														}
														/**
														 * ...ve ders saati daha dolmadi ise uyari verilecek, ders saati bitimi beklenecek
														 */
														else {
																$studentDebtInfo = 'minPaymentRestrictWarning';
																if (debugger('Accountant'))
																		var_dump('------------------> Durum : Ders saati dolmadi. Beklenecek.');
																return $cashResult;
														}
												}
										}
										/**
										 * ogrencinin sinifta ilk donemi degil ise;
										 */
										else {
												if (debugger('Accountant'))
														var_dump('------------------> Durum : Diğer Donemler');

												/**
												 * ...ve ogrenci yeni doneminde ve ilk dersi ise; 
												 */
												if ($value['paymentTermLectureNo'] == 1) {
														if (debugger('Accountant'))
																var_dump('------------------> Durum : Diger Donemler -> İlk Ders');
														/**
														 * ...ve onceki doneminden eksi bakiye ile geldi ise;
														 */
														if ($this->_studentMoneyLeftInCase < 0) {
																if (debugger('Accountant'))
																		var_dump('------------------> Durum : Diger Donemler -> İlk Ders ve Eksi Bakiye');
																/**
																 * ...ders sonu gelmisse siniftan cikartma islemini onayla
																 */
																if (getDateTimeDiff(getTimeAsFormatted(), $endTime, 'type') > 0) {
																		$studentDebtInfo = 'newTermNegativeBalanceNoPaymentAndOut';
																		$isCalculate = false;
																		$isOut = true;
																		if (debugger('Accountant'))
																				var_dump('------------------> Durum : Diger Donemler / İlk Ders ve Eksi Bakiye -> Ders sonuna odeme gelmedi. Siniftan cikarildi.');
																}
																/**
																 * ...ders sonu gelmemisse uyari yap, hesapla ve derse girebilsin
																 */
																else {
																		$studentDebtInfo = 'newTermNegativeBalanceMinPaymentRestrictWarning';
																		if (debugger('Accountant'))
																				var_dump('------------------> Durum : Diger Donemler / İlk Ders ve Eksi Bakiye-> Ders saati dolmadi. Beklenecek.');
																		return $cashResult;
																}
														}
														/**
														 * ...ve onceki donemden eksi bakiye ile gelmemis ise;
														 */
														else {
																$studentDebtInfo = 'newTermPaymentWarning';
																if (debugger('Accountant'))
																		var_dump('------------------> Durum : Diger Donemler -> İlk ders odeme gelmedi. Uyari yapildi, hesaplama yapildi. Derse girise izin verildi.');
														}
												}
												/**
												 * ogrenci yeni doneminde ve ilk dersinde degil ise (diger dersler); 
												 */
												else {
														if (debugger('Accountant'))
																var_dump('------------------> Durum : Diger Donemler -> Diger Dersler');
														/**
														 * ...ve ders sonu gelmisse siniftan cikartma islemini onayla
														 */
														if (getDateTimeDiff(getTimeAsFormatted(), $endTime, 'type') > 0) {
																$studentDebtInfo = 'newTermNoPaymentAndOut';
																$isCalculate = false;
																$isOut = true;
																if (debugger('Accountant'))
																		var_dump('------------------> Durum : Diger Donemler -> Ders sonuna odeme gelmedi. Siniftan cikarildi.');
														}
														/**
														 * ...ve ders sonu gelmemisse uyari yap, hesapla ve derse girebilsin
														 */
														else {
																$studentDebtInfo = 'newTermMinPaymentRestrictWarning';
																if (debugger('Accountant'))
																		var_dump('------------------> Durum : Diger Donemler -> Ders saati dolmadi. Beklenecek.');
																return $cashResult;
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
								'minPayment' => $oneLecturePrice,
								'studentMoneyLeftInCase' => $this->_studentMoneyLeftInCase[$studentCode]);

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
		public function getStudentCashStatus($Student, $Classroom, $type = null) {
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
}