<?php

/**
 * Nesnelerin info degisikliklerini tutan nesne
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Changes
{
		/**
		 * genel degiskenler
		 */
		private $_tableName;
		private $_tableCode;
		private $_startDateTimeLimit;
		private $_endDateTimeLimit;
		private $_list;

		/**
		 * Classın construct methodu
		 */
		public function __construct($tableName, $tableCode)
		{
				$this->_tableName = $tableName;
				$this->_tableCode = $tableCode;
		}
		/**
		 * gerekli durumlarda kullanilmak uzere 
		 * zaman araliklarinin belirlendigi metot
		 */
		public function setTimeLimits($startTimeLimit, $endTimeLimit)
		{
				$this->_startDateTimeLimit = $startTimeLimit;
				$this->_endDateTimeLimit = $endTimeLimit;
		}
		/**
		 * nesnenin degisiklikler listesi
		 */
		public function getList()
		{
				if ($this->_list == null) {
						$this->_list = School::classCache()->getChangesList($this->_tableName, $this->_tableCode);
				}
				return $this->_list;
		}
		/**
		 * filtre basliklarini al ve filterelenen sonucu dondur
		 * 
		 * @param filters array
		 */
		public function getFilteredList($array)
		{
				//d('CHANGEFIELD : ' . $array['changeField']);
				$filterWithValue = false;

				// Oncelikle temel kolonlari belirle ve ona gore bir eleme yap
				$filteredArray = $filteredArrayFirstState = $this->getList();

				// ChangeType verilmediyse tum changeType sonuclu olanlari dondurur
				// Ikinci eleme de changeField'a karsilik gelen degerin
				// bos olmadigi dizi verilerini ele ve liste haline getir
				if (isset($array['changeField'])) {
						foreach ((array)$filteredArrayFirstState as $key => $value) {
								/**
								 * bos statusler icin son status alanini yedekliyoruz (bos degil ise)
								 * sonrada eksik olan yerleri dolduruyoruz
								 */
								if ($value['status'] != '') {
										$tempStatus = $filteredArrayFirstState[$key]['status'];
								} else {
										$filteredArrayFirstState[$key]['status'] = $tempStatus;
								}

								if ($value[$array['changeField']] != '')
										$filteredArraySecondState[] = $filteredArrayFirstState[$key];
								else {
										/**
										 * eger kisi odeme listesi yapiyor isek degismediği halde;
										 * listeye STATUS->ACTIVE yapilanlar da eklenmeli,
										 * cunku ACTIVE status update'lerinin yanına
										 * odeme periodu ve odemesini de yazdim
										 */
										if(isset($array['Person'])) {
												if ($filteredArrayFirstState[$key]['status'] == 'active')
														$filteredArraySecondState[] = $filteredArrayFirstState[$key];
										}
								}
						}
						
						$filteredArray = $filteredArraySecondState;
				}

				// Ucuncu asamada da changeField'a changeValue verilmişse onlari liste haline getir
				if (isset($array['changeField'])) {
						if (isset($array['changeValue'])) {
								$filterWithValue = true;
						}
				}

				if (isset($array['changeField'])) {

						// filtrelenmis dizinin sonuncu karakterini belirle;
						$lastKey = count($filteredArray) - 1;
						$currentKey = 0;

						if (isset($array['Classroom']))
								$classroomCode = $array['Classroom']->getInfo('code');

						$expCurrents = array();

						// degisiklikler aramasi sonuclarini donguye al
						foreach ((array)$filteredArray as $key => $value) {

								// eger istenilen bir veri basligi ve veri degeri varsa (orn. : status , active)
								if ($filterWithValue) {
										// veri degeri virgullu ise parcala (orn: used, active)
										$expDbChangeField = explode(',', $value[$array['changeField']]);

										// dizi haline getirilen veri degerlerini donguye al
										foreach ($expDbChangeField as $expDbChangeFieldKey => $expDbChangeFieldValue) {
												//d($expDbChangeFieldValue . ' ?= ' . $array['changeValue']);
												// veri degeri sorguda istenilen degere esit mi? (active == used ?)
												if ($expDbChangeFieldValue == $array['changeValue']) {

														// veri degeri sorguda istenilene esit ve Person tablosuna ait ise;
														if ($this->_tableName == 'person') {
																// ogrencinin sinif bilgisini al
																$personClassroom = $array['Person']->getInfo('classroom');
																// currents : degisiklik sirasinda ihtiyac duyulan diger verileri barindirir. Degerler <+> ile ayrilir.
																// currents[0] classroom bilgisi : hali hazirda person'ın hangi siniflarda oldugunu yazdirmistik. (Orn: currents : 2,3)
																// currents[1] odeme veya odeme methodu bilgisi : sadece odeme veya odeme methodu degisikliklerinde doldurulur.
																// currents'i ana hatlarina parcala ve dizi yap
																$expCurrents = explode('<+>', $value[currents]);
																// sadece siniflar kismini (currents[0]) parcala ve dizi yap
																$currentClassrooms = explode(',', $expCurrents[0]);
																//d($expCurrents);
																// sorguda belirlenen sinif kodu person'in siniflarinda bulunuyor mu?
																$classroomKey = array_search($classroomCode, $currentClassrooms);
																//d('sorguda belirlenen sinif kodu personin siniflarindaki kodu : ' . $classroomKey);

																if (strlen($classroomKey) != 0) {
																		// bulunuyor ise; 
																		// bak bakalim ilgili key koduna karsilik gelen deger sorguda istenilen verinin key numarasina esit mi?
																		if ($classroomKey == $expDbChangeFieldKey) {
																				// filtereler dizisinde yeni satira gec ve 'array'e ekle' modunu aktif et
																				if (count($filteredList) != 0)
																						$currentKey++;
																				$addToArray = true;
																		}
																} else {
																		// sorguda belirlenen sinif kodu person'in sinif kodlari arasinda yok. o zaman array'e ekleme
																		$addToArray = false;
																}
														}
														
														// veri degeri sorguda istenilene esit ve Classroom tablosuna ait ise;
														if ($this->_tableName == 'classroom') {
																// o zaman, filtereler dizisinde yeni satira gec ve 'array'e ekle' modunu aktif et
																if (count($filteredList) != 0)
																		$currentKey++;
																$addToArray = true;
														}
														/**
														 * diziye ekle islemleri - changeValue geldi ve kontrolden gecti ise!
														 */
														if ($addToArray) {
																$filteredList[$currentKey] = array('field' => $array['changeField'], 'fieldValue' => $value[$array['changeField']], 'currents' => $value['currents']);
																$filteredList[$currentKey] = array_merge($filteredList[$currentKey], array('startDateTime' => $value['dateTime']));

																// Eger son kayita gelmissek, bitis tarihini simdiki tarihten alacak
																if ($key != $lastKey) {
																		$endDateTimeValue = $filteredArray[$key + 1]['dateTime'];
																}	else {
																		$endDateTimeValue = getDateTimeAsFormatted();
																}
																$filteredList[$currentKey] = array_merge($filteredList[$currentKey], array('endDateTime' => $endDateTimeValue));
														}
												} else {
														$addToArray = false;
												}
										}
								} else {
										// changeValue degeri gelmemis ilk basta, sorguda istenmemis
										// o zaman key atlamadan eklemeyi yap
										$currentKey = $key;
										$addToArray = true;

										/**
										 * diziye ekle islemleri - changeValue gelmediyse!
										 */
										if ($addToArray) {
												$filteredList[$currentKey] = array('field' => $array['changeField'], 'fieldValue' => $value[$array['changeField']], 'currents' => $value['currents']);
												$filteredList[$currentKey] = array_merge($filteredList[$currentKey], array('startDateTime' => $value['dateTime']));

												// Eger son kayita gelmissek, bitis tarihini simdiki tarihten alacak
												if ($key != $lastKey)
														$endDateTimeValue = $filteredArray[$key + 1]['dateTime'];
												else
														$endDateTimeValue = getDateTimeAsFormatted();

												$filteredList[$currentKey] = array_merge($filteredList[$currentKey], array('endDateTime' => $endDateTimeValue));
										}
								}
								/**
								 * eger extraFields istegi var ise;
								 * listenin sonuna istenilen degerleri ekle
								 */
								if (isset($array['extraFields'])) {
										$expExtraFields = explode(',', $array['extraFields']);
										foreach ($expExtraFields as $expExtraFieldKey => $expExtraFieldKeyValue) {
												$filteredList[$currentKey] = array_merge($filteredList[$currentKey], array($expExtraFieldKeyValue => $value[$expExtraFieldKeyValue]));
										}
								}
						}
				}
				return $filteredList;
		}
}
