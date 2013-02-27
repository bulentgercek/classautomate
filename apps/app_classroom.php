<?php
/**
 * classautomate - app_classroom
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Db = Db::classCache();
$Setting = Setting::classCache();

$classroomList = getFromArray($School->getClassroomList(), array('status' => 'notUsed||used||active'));

/*
 * classroom kaydi var mi?
 */
if ($classroomList != NULL) {

		for ($i = 0; $i < count($classroomList); $i++) {
				$processStatus = 1;

				// Classroomlari oku ve sinif sayilarini cikart
				$Classroom = $School->getClassroom($classroomList[$i]["code"]);
				$countClasses = count($Classroom->getStudentList());
				$dayCode = $classroomList[$i]['day'];

				if ($countClasses > 0)
						$processStatus = 0;

				$statusProcess[] = array('dayCode' => $dayCode, 'status' => $countClasses, 'process' => $processStatus);
		}

		// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
		$MakeList = new MakeList('code,day,name,program_name,time,instructor_name,instructor_surname,saloon_name', 'page', $classroomList);
		$MakeListResult = $MakeList->get();

		//classroomList ve statusProcess arraylerini birlestir
		$finalClassroomList = merge2Array($MakeListResult, $statusProcess);
}

setExtSmartyVars('classroomListArray', $finalClassroomList);

/**
 * gunler listesi
 */
setExtSmartyVars('daysOfWeek', daysOfWeek());

/** Programlar listesini hazirla */
$programList = $School->getProgramList();

// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
$MakeList = new MakeList('code,name', 'select', $programList);
$MakeListPrograms = $MakeList->get();

/** Egitmenler listesini hazirla */
$instructorList = $School->getInstructorList();
$instructorList = sortArrayWithKey::get($instructorList, "name", 'ASC');

// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
$MakeList = new MakeList('code,name,surname', array('type' => 'select', 'separators' => ' '), $instructorList);
$MakeListInstructors = $MakeList->get();

/** Salonlar listesini hazirla */
$saloonList = $School->getSaloonList();

// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
$MakeList = new MakeList('code,name', 'select', $saloonList);
$MakeListSaloons = $MakeList->get();

setExtSmartyVars('programsList', $MakeListPrograms);
setExtSmartyVars('instructorsList', $MakeListInstructors);
setExtSmartyVars('saloonsList', $MakeListSaloons);

setExtSmartyVars("classroomCount", count($classroomList));
setExtSmartyVars("programCount", count($MakeListPrograms));
setExtSmartyVars("saloonCount", count($MakeListSaloons));
setExtSmartyVars("instructorCount", count($MakeListInstructors));
?>
