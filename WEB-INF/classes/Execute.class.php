<?php
/**
 * Execute Control
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
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
	 * construct yapilamaz
	 *
	 */
	private function __construct() {}

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

	/**
	 * komutu hazirla
	 *
	 * @param string $command
	 */
	public function setCommand($command)
	{
		$this->_command = explode('->', $command);
		$School = School::classCache();
		$Db = Db::classCache();

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
			if (getTransferInfo('tt', $_POST) == 'direct');
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
						$Classroom->setInfo($_POST);
						/**
						 * ilk kayitta program, salon ve egitmen STATUS'leri
						 * USED yapiliyor
						 */
						$Program = $School->getProgram($_POST["program"]);
						if ($Program->getInfo("status") == "notUsed") {
							$Program->setInfo( array ("status"=>"used",'tc:update'=>'addClassroomForm|direct') );
						}
						
						$Saloon = $School->getSaloon($_POST["saloon"]);
						if ($Saloon->getInfo("status") == "notUsed") {
							$Saloon->setInfo( array ("status"=>"used",'tc:update'=>'addClassroomForm|direct') );
						}
						
						$Instructor = $School->getInstructor($_POST["instructor"]);
						if ($Instructor->getInfo("status") == "notUsed") {
							$Instructor->setInfo( array ("status"=>"used",'tc:update'=>'addClassroomForm|direct') );
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
							$Program->setInfo( array ("status"=>"used",'tc:update'=>'updateClassroomForm|direct') );
						}
						
						$Saloon = $School->getSaloon($_POST["saloon"]);
						if ($Saloon->getInfo("status") == "notUsed") {
							$Saloon->setInfo( array ("status"=>"used",'tc:update'=>'updateClassroomForm|direct') );
						}
						
						$Instructor = $School->getInstructor($_POST["instructor"]);
						if ($Instructor->getInfo("status") == "notUsed") {
							$Instructor->setInfo( array ("status"=>"used",'tc:update'=>'updateClassroomForm|direct') );
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

						} else
							
							$Classroom->setInfo(array ("status" => "deleted", 'tc:delete'=>'deleteClassroomForm|direct'));

						break;
						
					case "empty":
						$School->getClassroom($_GET["code"])->emptyClassroom();
						break;
						
					case "freeze":
						$Holiday = new Holiday();
						$date = getClientDateTime('%Y-%m-%d %H:%M:%S');
						$newHolidayValues['type'] = 'classroom';
						$newHolidayValues['startDateTime'] = $date;
						$newHolidayValues['endDateTime'] = date('Y-m-d H:i:s', strtotime("+1 month", strtotime($date)));
						$newHolidayValues['info'] = $_GET['code'];
						$newHolidayValues['tc:add'] = 'addHolidayForm|direct'; 
						$Holiday->setInfo($newHolidayValues);
						break;

					case "unFreeze":
						$date = getClientDateTime('%Y-%m-%d %H:%M:%S');
						$newEndDateTime = date('Y-m-d H:i:s', strtotime("-1 seconds", strtotime($date)));
						$Holiday = $School->getHoliday($_POST['holidayClassroomCode']);
						$Holiday->setInfo(array('endDateTime'=>$newEndDateTime,'tc:unFreeze'=>'deleteClassroomForm|direct'));
						break;
						
				}
				break;

			case "program" :
				switch ($this->_command[1]) {

					case "add" :
						$Program = new Program();
						$Program->setInfo($_POST);
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
						$Saloon->setInfo($_POST);
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
						$Holiday->setInfo($_POST);
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
								$Person->setInfo($_POST);
								break;
							case "instructor" :
								$Person = new Instructor();
								$Person->setInfo($_POST);
								break;
							case "asistant" :
								$Person = new Asistant();
								$Person->setInfo($_POST);
								break;
							case "secretary" :
								$Person = new Secretary();
								$Person->setInfo($_POST);
								break;
							case "cleaner" :
								$Person = new Cleaner();
								$Person->setInfo($_POST);
								break;
						}

						break;

					case "update" :
						switch ($_GET["position"]) {

							case "student" :
								$School->getStudent($_GET["code"])->setInfo($_POST);
								break;
							case "instructor" :
								$School->getInstructor($_GET["code"])->setInfo($_POST);
								break;
							case "asistant" :
								$School->getAsistant($_GET["code"])->setInfo($_POST);
								break;
							case "secretary" :
								$School->getSecretary($_GET["code"])->setInfo($_POST);
								break;
							case "cleaner" :
								$School->getCleaner($_GET["code"])->setInfo($_POST);
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
							
						} else
							$Person->setInfo(array ("status" => "deleted", 'tc:update'=>'deletePersonForm|direct'));
						
						break;
					
				}
				break;
				
			case "rollcall" :

				switch ($this->_command[1]) {
					
					case "activate" :

						foreach($_POST as $key => $value) {

							if (substr($key, 0, 11) == "classActive") {
								
								/** sinifin status bilgisini ACTIVE yap */
								$Classroom = $School->getClassroom($_POST['classroom']);
								
								$tempPostArray['status'] = 'active';
								$tempPostArray['startDate'] = $_POST['date'];
								$tempPostArray['startDayTime'] = $_POST['dayTime'];
								$tempPostArray['tc:update'] = 'activateRollcallForm|direct';
								$Classroom->setInfo($tempPostArray);

								// DbChange'e MASTERCHANGE verisi gonderiliyor
								$masterChange = directMerge2Array(array('masterChangeInfo'=>'classroom|' . $Classroom->getInfo('code')), $tempPostArray);
								DbChanges::classCache()->setMasterChange($masterChange);

								/** 
								 * sinif aktif olduguna gore
								 * butun dayTime bilgilerinin USED yap
								 */
								$dayTimes = $Classroom->getDayTimeList();

								foreach ($dayTimes as $key => $value) {
									$DayTime = $Classroom->getDayTime($value['code']);
									$DayTime->setInfo(array('status'=>'used', 'tc:update'=>'activateRollcallForm|direct'));
								}
								
								/**
								 * sinif aktif edildiginde icinde kayitli olan
								 * ogrencilerin de firstLecture bilgisine 
								 * aktif edilme tarihini isle
								 */
								$studentList = $Classroom->getStudentList();
								
								$selectedDayTime = getArrayKeyValue(getFromArray($School->getDayTimeList(),array('code'=>$_POST['dayTime'])), 0);

								if ($studentList != NULL) {
									foreach ($studentList as $studentListKey => $studentListValue) {
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
										if ($Student->getInfo('firstLecture') == '0000-00-00 00:00:00')
											$Student->setInfo(array('firstLecture'=>$_POST['date'] . ' ' . $selectedDayTime['time'], 'tc:update'=>'activateRollcallForm|direct'));
										
										// status bilgisini guncelle
										$Student->setInfo(array('status'=>$newStudentStatus, 'tc:update'=>'activateRollcallForm|direct'));
							
									}
								}

								// DbChange'e MASTERCHANGE'in islevini bitirdigi haber veriliyor
								DbChanges::classCache()->removeMasterChange();
							}
						}
						break;

					case 'addRemove':
						
						foreach($_POST as $key => $value) {

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
										$selectedDayTime = getArrayKeyValue(getFromArray($School->getDayTimeList(),array('code'=>$_POST['dayTime'])), 0);
										
										$Student = $School->getStudent($tempPostArray['personCode']); 
										if ($Student->getInfo('firstLecture') == '0000-00-00 00:00:00')
											$Student->setInfo(array('firstLecture'=>$tempPostArray['date'] . ' ' . $selectedDayTime['time'], 'tc:update'=>'rollcallUpdateForm|direct'));
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
						$IncomeExpense->setInfo($_POST);
                        break;
					
					case "deleteIncomeExpense":
						$IncomeExpense = $School->getIncomeExpense($_POST['code']);
                        $School->deleteRecord($IncomeExpense);
						break;
				}
				break;
		}
	}
}
