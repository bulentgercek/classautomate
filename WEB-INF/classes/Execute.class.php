<?php

/**
 * Execute Control
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Execute
{

		/**
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 * gonderilen komut degiskeni
		 *
		 * @var array
		 */
		private $_command;

		/**
		 * form action sayfasi
		 *
		 * @var string
		 */
		private $_action = NULL;
		
		/**
		 * kuyruk dizisi
		 * 
		 * @var array
		 */
		private $_queue = NULL;

		/**
		 * construct yapilamaz
		 *
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
		public static function classCache()
		{
				if (!self::$_instance) {
						self::$_instance = new Execute();
				}
				return self::$_instance;
		}
		/*
		 * Execute icin bir islem yapilmasi sirasinda "İşin bitince bunları da uygula" dediğimiz
		 * kuyruğa yapılacak işi yerleştirdiğimiz metot
		 */
		public function setQueue($jobObject, array $job, $jobTc=NULL, $jobTt=NULL, $jobFormName=NULL, $masterChange=NULL)
		{
				if (debugger("Execute")) {
						echo 'DEBUG : ' . getCallingClass() . '->Execute->setQueue : ';
						d($jobObject);
						d($job);
				}
				$this->_queue[] = array(
						'jobObject'=>$jobObject, 'jobTc'=>$jobTc, 'jobTt'=>$jobTt, 'jobFormName'=>$jobFormName, 'job'=>$job, 'masterChange'=>$masterChange
				);
		}
		/**
		 * Kuyruğa yerleştirilmiş olan işleri sırasi ile yapan metot
		 */
		public function applyQueue()
		{
				if ($this->_queue) {
						foreach ($this->_queue as $queueKey=>$queueJob) {
								if (debugger("Execute")) {
										echo 'DEBUG : Execute->applyQueue : Job No = ' . ++$count . ' / ' . count($this->_queue);
										d($queueJob);
								}
								/**
								 * masterChange verilmiş mi?
								 */
								if ($queueJob['masterChange']) {
										$masterChange['object']	=	$queueJob['masterChange']['object'];
										$masterChange['dateTime']	= $queueJob['masterChange']['dateTime'];
										DbChanges::classCache()->setMasterChange($masterChange['object'], $masterChange['dateTime']);
								}
								if ($queueJob['jobTc']) {
										$tcField = 'tc:' . $queueJob['jobTc'];
										$tFormNameAndType = $queueJob['formName'] . '|' . $queueJob['jobTt'];
										$jobIntend = merge2Array($queueJob['job'], array($tcField=>$tFormNameAndType));
								} else {
										$jobIntend =$queueJob['job'];
								}
								/**
								 * kuyruk islemini uygula
								 */
								$queueJob['jobObject']->setInfo($jobIntend);
								/**
								 * kuyruktan işlemi yapılanı sil
								 */
								unset($this->_queue[$queueKey]);
								/**
								 * islemi yapılan nesneye ait dizilerin icerigini tazele
								 */
								switch (get_parent_class($queueJob['jobObject'])) {
										case 'Classroom': $arrayReadCode = 'classrooms'; break;
										case 'Changes': $arrayReadCode = 'changes'; break;
										case 'Holiday': $arrayReadCode = 'holidays'; break;
										case 'Person': $arrayReadCode = 'people'; break;
										case 'Program': $arrayReadCode = 'programs'; break;
										case 'Saloon': $arrayReadCode = 'saloons'; break;
										case 'HolidaySubject': $arrayReadCode = 'holidaySubjects'; break;
										case 'IncomeExpenseType': $arrayReadCode = 'incomeExpenseTypes'; break;
								}
								School::classCache()->readToArrays($arrayReadCode, true);
								if (debugger("Execute")) {
										echo 'DEBUG : Execute->applyQueue : ' . get_parent_class($queueJob['jobObject']) . ' listesi yenilendi.<br>';
								}
						}
						/**
						 * kuyruk uygulanirken yeni veri atilmis olabilir
						 * denetlemek icin metodu tekrarla
						 */
						$this->applyQueue();
				}
		}
		/**
		 * komutu hazirla
		 *
		 * @param string $command
		 */
		public function setCommand($command)
		{
				$this->_command = explode('->', $command);
				$School = School::classCache();

				/** komut dizisi debug ediliyor */
				if (debugger("Execute")) {
						echo 'DEBUG : ' . getCallingClass() . '->Execute->setCommand : ';
						for ($i = 0; $i < count($this->_command); $i++) {
								echo " " . $this->_command[$i];
						}
						echo '<br>';
				}

				/**
				 * transfer tipi DIRECT ise sayfa tazelemesi aktif hale getiriliyor
				 */
				if (!debugger("SendToDb")) {
						if (getTransferInfo('tt', $_POST) == 'direct' || getTransferInfo('tt', $_POST) == 'post')
								setRefresh('true');
				}

				/** komut degerlendirmeleri */
				switch ($this->_command[0]) {

						case "classroom" :

								switch ($this->_command[1]) {

										case "add":
												/**
												 * sinif kaydini yap
												 */
												$Classroom = new Classroom();
												$this->setQueue($Classroom, $_POST);
												/**
												 * ilk kayitta program, salon ve egitmen STATUS'leri
												 * USED yapiliyor
												 */
												$Program = $School->getProgram($_POST["program"]);
												if ($Program->getInfo("status") == "notUsed") {
														$this->setQueue($Program, array("status" => "used"), 'update', 'direct', 'addClassroomForm');
												}

												$Saloon = $School->getSaloon($_POST["saloon"]);
												if ($Saloon->getInfo("status") == "notUsed") {
														$this->setQueue($Saloon, array("status" => "used"), 'update', 'direct', 'addClassroomForm');
												}

												$Instructor = $School->getInstructor($_POST["instructor"]);
												if ($Instructor->getInfo("status") == "notUsed") {
														$this->setQueue($Instructor, array("status" => "used"), 'update', 'direct', 'addClassroomForm');
												}
												break;

										case "update":
												$School->getClassroom($_GET["code"])->setInfo($_POST);
												/**
												 * sinif'ta degisiklik yapildiginda
												 * program, salon ve egitmen STATUS'leri
												 * USED yapiliyor
												 */
												$Program = $School->getProgram($_POST["program"]);
												if ($Program->getInfo("status") == "notUsed") {
														$this->setQueue($Program, array("status" => "used"), 'update', 'direct', 'updateClassroomForm');
												}

												$Saloon = $School->getSaloon($_POST["saloon"]);
												if ($Saloon->getInfo("status") == "notUsed") {
														$this->setQueue($Saloon, array("status" => "used"), 'update', 'direct', 'updateClassroomForm');
												}

												$Instructor = $School->getInstructor($_POST["instructor"]);
												if ($Instructor->getInfo("status") == "notUsed") {
														$this->setQueue($Instructor, array("status" => "used"), 'update', 'direct', 'updateClassroomForm');
												}
												break;

										case "delete":
												$Classroom = $School->getClassroom($_POST["code"]);

												if ($Classroom->getInfo("status") == "notUsed") {

														/**
														 * once CHANGES bilgileri siliniyor, sonra dayTimes, sonra da sinif
														 */
														$School->deleteChanges($Classroom);

														foreach ($Classroom->getDayTimeList() as $key => $value) {
																$DayTime = $Classroom->getDayTime($value['code']);
																$School->deleteRecord($DayTime);
														}
														$School->deleteRecord($Classroom);
												}
												else
														$this->setQueue($Classroom, array("status" => "deleted"), 'delete', 'direct', 'deleteClassroomForm');

												break;

										case "empty":
												$School->getClassroom($_GET["code"])->emptyClassroom();
												break;

										case "freeze":
												$Holiday = new Holiday();
												$date = getDateTime('%Y-%m-%d %H:%M:%S');
												$newHolidayValues['type'] = 'classroom';
												$newHolidayValues['startDateTime'] = $date;
												$newHolidayValues['endDateTime'] = date('Y-m-d H:i:s', strtotime("+1 month", strtotime($date)));
												$newHolidayValues['info'] = $_GET['code'];
												$newHolidayValues['tc:add'] = 'addHolidayForm|direct';
												$this->setQueue($Holiday, $newHolidayValues);
												break;

										case "unFreeze":
												$date = getDateTime('%Y-%m-%d %H:%M:%S');
												$newEndDateTime = date('Y-m-d H:i:s', strtotime("-1 seconds", strtotime($date)));
												$Holiday = $School->getHoliday($_POST['holidayClassroomCode']);
												$this->setQueue($Holiday, array('endDateTime' => $newEndDateTime), 'unFreeze', 'direct', 'deleteClassroomForm');
												break;
								}
								break;

						case "program" :
								switch ($this->_command[1]) {

										case "add" :
												$Program = new Program();
												$this->setQueue($Program, $_POST);
												break;

										case "update" :
												$School->getProgram($_GET["code"])->setInfo($_POST);
												break;

										case "delete" :
												$Program = $School->getProgram($_POST['code']);
												$School->deleteRecord($Program);
												break;
								}
								break;

						case "saloon" :
								switch ($this->_command[1]) {

										case "add" :
												$Saloon = new Saloon();
												$this->setQueue($Saloon, $_POST);
												break;

										case "update" :
												$School->getSaloon($_GET["code"])->setInfo($_POST);
												break;

										case "delete" :
												$Saloon = $School->getSaloon($_POST['code']);
												$School->deleteRecord($Saloon);
												break;
								}
								break;

						case "holiday" :
								switch ($this->_command[1]) {

										case "add" :
												$Holiday = new Holiday();
												$this->setQueue($Holiday, $_POST);
												break;

										case "update" :
												$School->getHoliday($_GET["code"])->setInfo($_POST);
												break;

										case "delete":
												$Holiday = $School->getHoliday($_POST['code']);
												$School->deleteRecord($Holiday);
												break;
								}
								break;

						case "person" :
								switch ($this->_command[1]) {

										case "add" :
												switch ($_GET["position"]) {

														case "student" :
																$Person = new Student();
																$this->setQueue($Person, $_POST);
																break;
														case "instructor" :
																$Person = new Instructor();
																$this->setQueue($Person, $_POST);
																break;
														case "asistant" :
																$Person = new Asistant();
																$this->setQueue($Person, $_POST);
																break;
														case "secretary" :
																$Person = new Secretary();
																$this->setQueue($Person, $_POST);
																break;
														case "cleaner" :
																$Person = new Cleaner();
																$this->setQueue($Person, $_POST);
																break;
												}

												break;

										case "update" :
												switch ($_GET["position"]) {

														case "student" :
																$this->setQueue($School->getStudent($_GET["code"]), $_POST);
																break;
														case "instructor" :
																$this->setQueue($School->getInstructor($_GET["code"]),$_POST);
																break;
														case "asistant" :
																$this->setQueue($School->getAsistant($_GET["code"]),$_POST);
																break;
														case "secretary" :
																$this->setQueue($School->getSecretary($_GET["code"]),$_POST);
																break;
														case "cleaner" :
																$this->setQueue($School->getCleaner($_GET["code"]),$_POST);
																break;
												}

												break;

										case "delete" :

												switch ($_POST["position"]) {

														case "student" :
																$Person = $School->getStudent($_POST["code"]);
																break;
														case "instructor" :
																$Person = $School->getInstructor($_POST["code"]);
																break;
														case "asistant" :
																$Person = $School->getAsistant($_POST["code"]);
																break;
														case "secretary" :
																$Person = $School->getSecretary($_POST["code"]);
																break;
														case "cleaner" :
																$Person = $School->getCleaner($_POST["code"]);
																break;
												}

												if ($Person->getInfo("status") == "notUsed") {
														/**
														 * once CHANGES bilgileri siliniyor, sonra da kisi
														 */
														$School->deleteChanges($Person);
														$School->deleteRecord($Person);
												}
												else
														$this->setQueue($Person, array('status'=>'deleted'), 'update', 'direct', 'deletePersonForm');
												break;
								}
								break;

						case "rollcall" :

								switch ($this->_command[1]) {

										case "activate" :

												foreach ($_POST as $key => $value) {

														if (substr($key, 0, 11) == "classActive") {

																/** sinifin status bilgisini ACTIVE yap */
																$Classroom = $School->getClassroom($_POST['classroom']);

																$tempPostArray['status'] = 'active';
																$tempPostArray['startDate'] = $_POST['date'];
																$tempPostArray['startDayTime'] = $_POST['dayTime'];
																$tempPostArray['tc:update'] = 'activateRollcallForm|direct';
																$this->setQueue($Classroom, $tempPostArray);

																/**
																 * sinif aktif olduguna gore
																 * butun dayTime bilgilerinin USED yap
																 */
																$dayTimes = $Classroom->getDayTimeList();

																foreach ($dayTimes as $key => $value) {
																		$DayTime = $Classroom->getDayTime($value['code']);
																		$DayTime->setInfo(array('status' => 'used', 'tc:update' => 'activateRollcallForm|direct'));
																}
																
																// masterChange tanımla
																$masterChange['object'] = $Classroom;
																$classroomDayTimeTime = $Classroom->getDayTime($Classroom->getInfo('startDayTime'))->getInfo('time');
																$masterChange['dateTime'] = $Classroom->getInfo('startDate') . ' ' . $classroomDayTimeTime;
																/**
																 * sinif aktif edildiginde icinde kayitli olan
																 * ogrencilerin de firstLecture bilgisine 
																 * aktif edilme tarihini isle
																 */
																$studentList = $Classroom->getStudentList();
																$selectedDayTime = getArrayKeyValue(getFromArray($School->getDayTimeList(), array('code' => $_POST['dayTime'])), 0);

																if ($studentList != NULL) {
																		foreach ($studentList as $studentListValue) {
																				$Student = $School->getStudent($studentListValue['code']);

																				$expStudentClassroom = explode(',', $Student->getInfo('classroom'));
																				$expStudentStatus = explode(',', $Student->getInfo('status'));

																				foreach ($expStudentClassroom as $key => $value) {
																						if ($Classroom->getInfo('code') == $value) {
																								$expStudentStatus[$key] = 'active';
																						}
																				}
																				$newStudentStatus = implode(',', $expStudentStatus);
																				// lecture ilk kez giriliyorsa update et
																				if ($Student->getInfo('firstLecture') == '0000-00-00 00:00:00') {
																						$this->setQueue($Student, array('firstLecture' => $_POST['date'] . ' ' . $selectedDayTime['time']), 'update', 'direct', 'activateRollcallForm', $masterChange);
																				}
																				// status bilgisini guncelle
																				$this->setQueue($Student, array('status' => $newStudentStatus), 'update', 'direct', 'activateRollcallForm', $masterChange);
																		}
																}
														}
												}
												break;

										case 'addRemove':

												foreach ($_POST as $key => $value) {

														if (substr($key, 0, 9) == "rollCheck") {
																$tempPostArray['date'] = $_POST['date'];

																$expRollCheck = explode('_', $key);
																$tempPostArray['personCode'] = $expRollCheck[1];

																$tempPostArray['classroom'] = $_POST['classroom'];
																$tempPostArray['dayTime'] = $_POST['dayTime'];
																$tempPostArray['tc:addRemove'] = 'addRemoveRollcallForm|direct';

																if ($expRollCheck[2] != 0) {
																		if ($_POST[$key] == "off") {
																				$tempPostArray['code'] = $expRollCheck[2];
																				$Rollcall = $School->getRollcall($expRollCheck[2]);
																				$School->deleteRecord($Rollcall);
																		}
																} else {
																		if ($_POST[$key] == "on") {
																				$Rollcall = new Rollcall();
																				$Rollcall->setInfo($tempPostArray);

																				/**
																				 * Eger sinif ogrenci daha sinifa dahil edilmeden aktif
																				 * hale getirilmiş mi ogren
																				 * sonradan da ogrencinin firstLecture bilgisine 
																				 * yeni tarihi isle 
																				 */
																				$selectedDayTime = getArrayKeyValue(getFromArray($School->getDayTimeList(), array('code' => $_POST['dayTime'])), 0);

																				$Student = $School->getStudent($tempPostArray['personCode']);
																				if ($Student->getInfo('firstLecture') == '0000-00-00 00:00:00') {
																						$this->setQueue($Student, array('firstLecture' => $tempPostArray['date'] . ' ' . $selectedDayTime['time']), 'update', 'direct', 'rollcallUpdateForm', $masterChange);
																				}
																		}
																}
																$tempPostArray = array();
														}
												}
												break;
								}
								break;

						case 'accountant':

								switch ($this->_command[1]) {

										case "addIncomeExpense":
												$IncomeExpense = new IncomeExpense();
												$this->setQueue($IncomeExpense, $_POST);
												break;

										case "deleteIncomeExpense":
												$IncomeExpense = $School->getIncomeExpense($_POST['code']);
												$School->deleteRecord($IncomeExpense);
												break;
								}
								break;
				}
				/**
				 * son olarak kuyruğa atılmış olan işlemleri uyguluyoruz
				 */
				$this->applyQueue();
		}
}
