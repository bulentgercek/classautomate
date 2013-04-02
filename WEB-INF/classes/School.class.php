<?php

/**
 * Okul Nesnesi Sinifi
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class School
{

		/**
		 * class yedegi
		 */
		private static $_instance;

		/**
		 * sinif, program nesnelerini barindiran diziler
		 */
		private $_classroom, $_program, $_saloon, $_holiday, $_holidaySubject;
		private $_incomeExpense, $_incomeExpenseType;

		/**
		 * kisi pozisyonuna gore nesnelerini barindiran diziler
		 */
		private $_student, $_instructor, $_asistant, $_secretary, $_cleaner;

		/**
		 * sinif, program listesi dizileri
		 */
		private $_classroomList, $_dayTimeList, $_programList, $_saloonList;
		private $_holidayList, $_holidaySubjectList = array();
		private $_rollcallListByDate, $_incomeExpenseList, $_incomeExpenseTypeList = array();
		private $_personChangesList, $_classroomChangesList;

		/**
		 * array yuklemesi kontrol dizisi
		 */
		private $_isArrayRead;

		/**
		 * tum kisiler listesi arrayi
		 */
		private $_peopleList;

		/**
		 *
		 * construct metodu
		 */
		private function __construct()
		{
				$this->_initSbyRoom();
		}
		/**
		 * singleton metodu
		 */
		public static function classCache()
		{
				if (!self::$_instance) {
						self::$_instance = new School();
				}
				return self::$_instance;
		}
		/**
		 * classroom nesnesi yarat
		 *
		 * @return object
		 */
		public function getClassroom($code)
		{
				$this->readToArrays('classrooms');
				$result = findKeyValueInArray($this->_classroomList, "code", $code);

				if ($result != -1 || $code == "sbyRoom") {
						if ($this->_classroom[$code] == NULL) {
								$this->_classroom[$code] = new Classroom($code);
						}
						return $this->_classroom[$code];
				}
				else
						trigger_error($code . " numaralı sınıf kodu classList dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * tatil nesnesi yarat
		 *
		 * @return object
		 */
		public function getHoliday($code)
		{
				$this->readToArrays('holidays');
				$result = findKeyValueInArray($this->_holidayList, "code", $code);

				if ($result != -1) {
						if ($this->_holiday[$code] == NULL) {
								$this->_holiday[$code] = new Holiday($code);
						}
						return $this->_holiday[$code];
				}
				else
						trigger_error($code . " numaralı tatil kodu holidayList dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * tatil konusu nesnesi yarat
		 *
		 * @return object
		 */
		public function getHolidaySubject($code)
		{
				$this->readToArrays('holidaySubjects');
				$result = findKeyValueInArray($this->_holidaySubjectList, "code", $code);

				if ($result != -1) {
						if ($this->_holidaySubject[$code] == NULL) {
								$this->_holidaySubject[$code] = new HolidaySubject($code);
						}
						return $this->_holidaySubject[$code];
				}
				else
						trigger_error($code . " numaralı tatil kodu holidaySubjectList dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * incomeExpense nesnesi yarat
		 *
		 * @return object
		 */
		public function getIncomeExpense($code)
		{
				$this->readToArrays('incomeExpenseTypes');
				$result = findKeyValueInArray($this->_incomeExpenseList, "code", $code);

				if ($result != -1) {
						if ($this->_incomeExpense[$code] == NULL) {
								$this->_incomeExpense[$code] = new IncomeExpense($code);
						}
						return $this->_incomeExpense[$code];
				}
				else
						trigger_error($code . " numaralı gelir-gider kodu incomeExpense dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * incomeExpense nesnesi yarat
		 *
		 * @return object
		 */
		public function getIncomeExpenseType($code)
		{
				$this->readToArrays('incomeExpenseTypes');
				$result = findKeyValueInArray($this->_incomeExpenseTypeList, "code", $code);

				if ($result != -1) {
						if ($this->_incomeExpenseType[$code] == NULL) {
								$this->_incomeExpenseType[$code] = new IncomeExpenseType($code);
						}
						return $this->_incomeExpense[$code];
				}
				else
						trigger_error($code . " numaralı tatil kodu incomeExpenseType dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * program nesnesi yarat
		 *
		 * @return object
		 */
		public function getProgram($code)
		{
				$this->readToArrays('programs');
				$result = findKeyValueInArray($this->_programList, "code", $code);

				if ($result != -1) {
						if ($this->_program[$code] == NULL) {
								$this->_program[$code] = new Program($code);
						}
						return $this->_program[$code];
				}
				else
						trigger_error($code . " numaralı program kodu programList dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * rollcall nesnesi yarat
		 *
		 * @return object
		 */
		public function getRollcall($code)
		{
				$result = findKeyValueInArray($this->_rollcallListByDate, "code", $code);

				if ($result != -1) {
						$this->_rollcall[$code] = new Rollcall($code);
						return $this->_rollcall[$code];
				}
		}
		/**
		 * tarihe gore rollcall listesi arrayi yarat ve döndür
		 *
		 * @return array
		 */
		public function getRollcallsByDate($array = NULL)
		{
				if ($array != NULL)
						$this->readRollcallsByDate($array);

				return $this->_rollcallListByDate;
		}
		/**
		 * salon nesnesi yarat
		 *
		 * @return object
		 */
		public function getSaloon($code)
		{
				$this->readToArrays('saloons');
				$result = findKeyValueInArray($this->_saloonList, "code", $code);

				if ($result != -1) {
						if ($this->_saloon[$code] == NULL) {
								$this->_saloon[$code] = new Saloon($code);
						}
						return $this->_saloon[$code];
				}
				else
						trigger_error($code . " numaralı salon kodu saloonList dizisinde bulunamadı.", E_USER_WARNING);
		}
		/**
		 * kod numarasina gore ogrenci nesnesi yarat
		 *
		 * @return object
		 */
		public function getStudent($code)
		{
				if ($this->_isPositionExist($code, "student")) {
						if ($this->_student[$code] == NULL)
								$this->_student[$code] = new Student($code);
				}
				return $this->_student[$code];
		}
		/**
		 * kod numarasina gore egitmen personel nesnesi yarat
		 *
		 * @return object
		 */
		public function getInstructor($code)
		{
				if ($this->_isPositionExist($code, "instructor")) {
						if ($this->_instructor[$code] == NULL)
								$this->_instructor[$code] = new Instructor($code);
				}
				return $this->_instructor[$code];
		}
		/**
		 * kod numarasina gore asistan nesnesi yarat
		 *
		 * @return object
		 */
		public function getAsistant($code)
		{
				if ($this->_isPositionExist($code, "asistant")) {
						if ($this->_asistant[$code] == NULL)
								$this->_asistant[$code] = new Asistant($code);
				}
				return $this->_asistant[$code];
		}
		/**
		 * kod numarasina gore temizlikçi personel nesnesi yarat
		 *
		 * @return object
		 */
		public function getSecretary($code)
		{
				if ($this->_isPositionExist($code, "secretary")) {
						if ($this->_secretary[$code] == NULL)
								$this->_secretary[$code] = new Secretary($code);
				}
				return $this->_secretary[$code];
		}
		/**
		 * kod numarasina gore temizlikçi personel nesnesi yarat
		 *
		 * @return object
		 */
		public function getCleaner($code)
		{
				if ($this->_isPositionExist($code, "cleaner")) {
						if ($this->_cleaner[$code] == NULL)
								$this->_cleaner[$code] = new Cleaner($code);
				}
				return $this->_cleaner[$code];
		}
		/**
		 * sinif dizisini dondur
		 *
		 * @return array
		 */
		public function getClassroomList()
		{
				$this->readToArrays('classrooms');
				return $this->_classroomList;
		}
		/**
		 * degisiklikler listesini dondur (istenilen tabloya gore)
		 */
		public function getChangesList($tableName, $tableCode)
		{
				$this->readToArrays('changes');
				if ($tableName == 'person')
						return getFromArray($this->_personChangesList, array('tableCode' => $tableCode));
				if ($tableName == 'classroom')
						return getFromArray($this->_classroomChangesList, array('tableCode' => $tableCode));
		}
		/**
		 * sinif gun/saat dizisini dondur
		 *
		 * @return array
		 */
		public function getDayTimeList()
		{
				$this->readToArrays('dayTimes');
				return $this->_dayTimeList;
		}
		/**
		 * tatil dizisini dondur
		 *
		 * @return array
		 */
		public function getHolidayList()
		{
				$this->readToArrays('holidays');
				return $this->_holidayList;
		}
		/**
		 * tatil konuları dizisini dondur
		 *
		 * @return array
		 */
		public function getHolidaySubjectList()
		{
				$this->readToArrays('holidaySubjects');
				return $this->_holidaySubjectList;
		}
		/**
		 * gelir-gider dizisini dondur
		 *
		 * @return array
		 */
		public function getIncomeExpenseList()
		{
				$this->readIncomesExpenses();
				return $this->_incomeExpenseList;
		}
		/**
		 * gelir gider listesini oku
		 * 
		 * @param array
		 */
		public function getIncomeExpenseWithCustomDbSearch($intend = null)
		{
				$criteria = array('table' => 'incexp', 'orderby' => 'dateTime ASC');
				if (!$intend == null)
						$criteria = array_merge($criteria, $intend);

				return Db::classCache()->readTableFromDb($criteria, 'noBase');
		}
		/**
		 * gelir-gider dizisini dondur
		 *
		 * @return array
		 */
		public function getIncomeExpenseTypeList()
		{
				$this->readIncomeExpenseTypes();
				return $this->_incomeExpenseTypeList;
		}
		/**
		 * egitmen dizisini dondur
		 *
		 * @return array
		 */
		public function getInstructorList()
		{
				$this->readToArrays('people');
				return $this->getPeopleList("instructor");
		}
		/**
		 * program dizisini dondur
		 *
		 * @return array
		 */
		public function getProgramList()
		{
				$this->readToArrays('programs');
				return $this->_programList;
		}
		/**
		 * yoklama listesini dondur
		 */
		public function getRollcallListByDate()
		{
				return $this->_rollcallListByDate;
		}
		/**
		 * salon dizisini dondur
		 *
		 * @return array
		 */
		public function getSaloonList()
		{
				$this->readToArrays('saloons');
				return $this->_saloonList;
		}
		/**
		 * kisi dizisini dondur
		 *
		 * @return array
		 */
		public function getPeopleList($type = "")
		{
				$this->readToArrays('people');
				if ($type == "")
						return $this->_peopleList;

				if ($type == "student" || $type == "instructor" || $type == "asistant" || $type == "cleaner")
						return getFromArray($this->_peopleList, array("position" => $type));
				else
						trigger_error($type . " türünde bir pozisyon bilgisi bulunmamaktadır.", E_USER_WARNING);
		}
		/**
		 * dizi okuyucusu
		 *
		 * @return void
		 */
		public function readToArrays($arrayReadCode, $readAgain = false)
		{
				switch ($arrayReadCode) {

						case 'classrooms':
								if (!$this->_isArrayRead['classrooms'] || $readAgain == true) {
										$this->readClassrooms();
								}
								break;

						case 'changes':
								if (!$this->_isArrayRead['changes'] || $readAgain == true) {
										$this->readChanges();
								}
								break;

						case 'holidays':
								if (!$this->_isArrayRead['holidays'] || $readAgain == true) {
										$this->readHolidays();
								}
								break;

						case 'people':
								if (!$this->_isArrayRead['people'] || $readAgain == true) {
										$this->readPeople();
								}
								break;

						case 'programs':
								if (!$this->_isArrayRead['programs'] || $readAgain == true) {
										$this->readPrograms();
								}
								break;

						case 'saloons':
								if (!$this->_isArrayRead['saloons'] || $readAgain == true) {
										$this->readSaloons();
								}
								break;

						case 'holidaySubjects':
								if (!$this->_isArrayRead['holidaySubjects'] || $readAgain == true) {
										$this->readHolidaySubjects();
								}
								break;

						case 'incomeExpenseTypes':
								if (!$this->_isArrayRead['incomeExpenseTypes'] || $readAgain == true) {
										$this->readIncomeExpenseTypes();
								}
								break;
				}
		}
		/**
		 * veritabanindan kisileri oku
		 *
		 * @return void
		 */
		public function readPeople()
		{
				$criteria = array('table' => 'person');
				$Db = Db::classCache();
				$this->_peopleList = $Db->readTableFromDb($criteria, 'noBase');
				$this->_isArrayRead['people'] = true;
		}
		/**
		 * veritabanindan siniflari oku
		 *
		 * @return void
		 */
		public function readClassrooms($array = null)
		{
				$Db = Db::classCache();

				$criteria = array('table' => classroom);
				$this->_classroomList = $Db->readTableFromDb($criteria, 'noBase');

				if (!isset($array['readDayTimes'])) {
						$this->readDaysTimes();
				}

				if ($this->_classroomList != null) {
						if (!isset($array['readDayTimes'])) {
								foreach ($this->_classroomList as $classKey => $classValue) {
										if ($this->_dayTimeList != null) {
												foreach ($this->_dayTimeList as $dayTimeKey => $dayTimeValue) {
														if ($classValue["code"] == $dayTimeValue["classroom"]) {
																unset($dayTimeValue["classroom"]);
																$this->_classroomList[$classKey]["dayTime"][] = $dayTimeValue;
														}
												}
										}
								}
						}
				}
				$this->_isArrayRead["classrooms"] = true;
		}
		/**
		 * degisikleri oku
		 * 
		 * @param array
		 */
		public function readChanges($intend = null)
		{
				$tableList = array('person', 'classroom');

				foreach ($tableList as $tableName) {
						$criteria = array('table' => 'changes',
								'where' => 'tableName=\'' . $tableName . '\'',
								'orderby' => 'dateTime ASC');

						switch ($tableName) {
								case 'person':
										$this->_personChangesList = Db::classCache()->readTableFromDb($criteria, 'noBase');
										break;

								case 'classroom':
										$this->_classroomChangesList = Db::classCache()->readTableFromDb($criteria, 'noBase');
										break;
						}
				}
				$this->_isArrayRead['changes'] = true;
		}
		/**
		 * veritabanindan sınıf gun/saat bilgilerini oku
		 *
		 * @return void
		 */
		public function readDaysTimes()
		{
				$Db = Db::classCache();
				$criteria = array('table' => daytime);
				$this->_dayTimeList = SortArrayWithKey::get($Db->readTableFromDb($criteria, 'noBase'), "time", 'ASC');
		}
		/**
		 * veritabanindan tatilleri oku
		 *
		 * @return void
		 */
		public function readHolidays()
		{
				$criteria = array('table' => holiday);
				$Db = Db::classCache();
				$this->_holidayList = $Db->readTableFromDb($criteria, 'noBase');
				$this->_isArrayRead["holidays"] = true;
		}
		/**
		 * tatil konularini kullanici arayuz diline uygun olarak oku
		 */
		public function readHolidaySubjects()
		{
				$Db = Db::classCache();
				$dbList = array('classautomate', Session::classCache()->get('dbName'));
				$tempDbResult = array();

				for ($i = 0; $i <= count($dbList); $i++) {
						$Db->connect($dbList[$i]);
						$Db->selectSql(array('table' => 'holiday_subjects', 'where' => "language='" . Setting::classCache()->getLanguage('db') . "'", 'orderby' => 'code ASC'));
						$tempDbResult = $Db->getRows("noBase");
						if ($tempDbResult != NULL)
								$this->_holidaySubjectList = array_merge_recursive($this->_holidaySubjectList, $tempDbResult);
				}
				$this->_isArrayRead["holidaySubjects"] = true;
		}
		/**
		 * gelir gider listesini oku
		 * 
		 * @param array
		 */
		public function readIncomesExpenses()
		{
				$criteria = array('table' => 'incexp', 'orderby' => 'code ASC');
				$this->_incomeExpenseList = Db::classCache()->readTableFromDb($criteria, 'noBase');
		}
		/**
		 * tatil konularini kullanici arayuz diline uygun olarak oku
		 */
		public function readIncomeExpenseTypes()
		{
				$Db = Db::classCache();
				$dbList = array('classautomate', Session::classCache()->get('dbName'));
				$this->_incomeExpenseTypeList = array();
				$tempDbResult = array();

				for ($i = 0; $i < count($dbList); $i++) {
						$Db->connect($dbList[$i]);
						$Db->selectSql(array('table' => 'incexp_types', 'orderby' => 'code ASC'));
						$tempDbResult = $Db->getRows("noBase");

						if ($tempDbResult != NULL)
								$this->_incomeExpenseTypeList = array_merge_recursive($this->_incomeExpenseTypeList, $tempDbResult);
				}

				$this->_isArrayRead["incomeExpenseTypes"] = true;
		}
		/**
		 * veritabanindan programlari oku
		 *
		 * @return void
		 */
		public function readPrograms()
		{
				$criteria = array('table' => program);
				$Db = Db::classCache();
				$this->_programList = $Db->readTableFromDb($criteria, 'noBase');
				$this->_isArrayRead['programs'] = true;
		}
		/**
		 * tarihe gore yoklama listesini oku
		 * 
		 * @param array
		 */
		public function readRollcallsByDate($array)
		{
				$criteria = array('table' => 'rollcall', 'where' => "date='" . $array['date'] . "' AND dayTime='" . $array['dayTime'] . "'", 'orderby' => 'code ASC');
				$Db = Db::classCache();
				$this->_rollcallListByDate = $Db->readTableFromDb($criteria, 'noBase');
		}
		/**
		 * koda gore yoklama oku
		 * 
		 * @param array
		 */
		public function readRollcallByCode($code)
		{
				$criteria = array('table' => 'rollcall', 'where' => "code='" . $code . "'");
				$Db = Db::classCache();
				return $Db->readTableFromDb($criteria, 'noBase');
		}
		/**
		 * veritabanindan programlari oku
		 *
		 * @return void
		 */
		public function readSaloons()
		{
				$criteria = array('table' => saloon);
				$Db = Db::classCache();
				$this->_saloonList = $Db->readTableFromDb($criteria, 'noBase');
				$this->_isArrayRead['saloons'] = true;
		}
		/**
		 * kayıt sil
		 *
		 * @return void
		 */
		public function deleteRecord($object)
		{
				SendToDb::delete($object);
		}
		/**
		 * CHANGES kayıtlarını sil
		 *
		 * @return void
		 */
		public function deleteChanges($object)
		{
				$Db = Db::classCache();
				$intend = array('table' => 'changes');
				$intend['where'] .= "tableName='" . $object->getDbTableName() . "' AND tableCode='" . $object->getInfo('code') . "'";
				$Db->deleteSql($intend);
		}
		/**
		 * sbyroom sınıfı nesnesini yarat
		 */
		private function _initSbyRoom()
		{
				$this->_classroom["sbyRoom"] = new Classroom("sbyRoom");
				$languageJSON = Setting::classCache()->getInterfaceLang();
				$this->_classroom["sbyRoom"]->setInfo(array("name" => $languageJSON->classautomate->main_header->sbyRoom));
		}
		/**
		 * istenilen pozisyona gore gereken kisi nesnesi yarat
		 *
		 * @return object
		 */
		private function _isPositionExist($code, $position)
		{
				$this->readToArrays('people');

				$result = findKeyValueInArray($this->_peopleList, "code", $code);

				if ($result != -1) {
						if ($this->_peopleList[$result]["position"] == $position) {
								return true;
						}
						else
								trigger_error($code . " numaralı kisi '" . $position . "' pozisyonunda degil.", E_USER_WARNING);
				}
				else
						trigger_error($code . " numaralı kisi peopleList dizisinde bulunamadı.", E_USER_WARNING);

				return false;
		}
		/**
		 * classroom obje dizisini dondur
		 *
		 * @return array
		 */
		public function showObjects($obj)
		{
				return var_dump($this->$obj);
		}
		/**
		 * array check disardan kontrol metodu
		 */
		public function setLoadTable($key, $value = true)
		{
				$this->_isArrayRead[$key] = $value;
		}
		/**
		 * siniflarin tarih ve ders sayisi kontrolunu yap
		 * limitleri asan sinifi bosalt ve sil
		 */
		public function checkClassroomsLimits()
		{
				$Fc = new FluxCapacitor();

				$classroomList = $this->getClassroomList();
				$dateTime = getDateTimeAsFormatted();

				foreach ((array) $classroomList as $key => $value) {

						$Classroom = $this->getClassroom($value['code']);
						$isClassroomOk = true;
						$maxCount = 0;
						/**
						 * sinif aktif ise limit kontrolu yap
						 */
						if ($Classroom->getInfo('status') == 'active') {

								$termInfo = $Classroom->getTermLimits();
								/**
								 * sinif bir limit berlirlenmis mi?
								 */
								if ($termInfo['type'] != null) {
										/**
										 * belirlenen limit tarih limiti mi?
										 */
										if ($termInfo['type'] == 'date') {
												if (getDateTimeDiff($dateTime, $termInfo['limit'], 'type') == 1)
														$isClassroomOk = false;
												else
														$isClassroomOk = true;
										}

										/**
										 * belirlenen limit ders sayisi mi?
										 */
										if ($termInfo['type'] == 'count') {
												$maxCount = getArrayKeyValue(explode(getFirstUpperCaseWord($termInfo['limit']), $termInfo['limit']), 0);

												if ($maxCount) {
														$classroomActiveDateTimes = $Classroom->getActiveDateTimes();

														$classroomActiveLectureList = array();

														if ($classroomActiveDateTimes != null) {
																foreach ($classroomActiveDateTimes as $key => $value) {

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
																				foreach ($filteredLectures as $key => $value) {
																						unset($filteredLectures[$key]['count']);
																				}
																				$classroomActiveLectureList = array_merge($classroomActiveLectureList, $filteredLectures);
																		}
																}
														}
														if (count($classroomActiveLectureList) > $maxCount)
																$isClassroomOk = false;
												}
										}

										if (!$isClassroomOk) {
												$Classroom->emptyClassroom();
												$this->deleteRecord($Classroom);
										}
								}
						}
				}
		}
}

?>
