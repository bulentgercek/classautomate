<?php

/**
 * classautomate - app_holiday
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Db = Db::classCache();
$holidayList = $School->getHolidayList();
$peopleList = $School->getPeopleList();
$instructorList = $School->getInstructorList();
$classroomList = $School->getClassroomList();

$holidaySubjects = $School->getHolidaySubjectList();

for ($i = 0; $i < count($holidayList); $i++) {
		$expHolidayInfo = explode('|', $holidayList[$i]['info']);

		if ($holidayList[$i]['type'] == "personnel") {
				$personArray = getFromArray($peopleList, array("code" => $expHolidayInfo[0]));
				$personArray = $personArray[0];
				$reason[$i]['reason'] = $languageJSON->classautomate->app_holiday->personnelList . " - " . $personArray['name'] . " " . $personArray['surname'];
		} else if ($holidayList[$i]['type'] == "official") {
				$holidayKey = findKeyValueInArray($holidaySubjects, 'code', $expHolidayInfo[0]);
				$reason[$i]['reason'] = $holidaySubjects[$holidayKey]['subject'];
		} else if ($holidayList[$i]['type'] == "classroom") {
				$classroomArray = getFromArray($classroomList, array("code" => $expHolidayInfo[0]));
				$reason[$i]['reason'] = $languageJSON->classautomate->app_holiday->classroomList . " - " . $classroomArray[0]['name'];
		} else if ($holidayList[$i]['type'] == "custom") {
				$reason[$i]['reason'] = $expHolidayInfo[0];
		}
}

// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
$MakeList = new MakeList('code,startDateTime,endDateTime', 'page', $holidayList);
$MakeListHolidays = $MakeList->get();

//holidaysList ve reason arraylerini birlestir
if ($MakeListHolidays != NULL) {
		$finalHolidaysList = merge2Array($MakeListHolidays, $reason);
}

setExtSmartyVars('instructorList', $instructorList);
setExtSmartyVars('classroomList', $classroomList);
setExtSmartyVars('holidayList', $finalHolidaysList);
setExtSmartyVars('subjectList', $holidaySubjects);
setExtSmartyVars('holidayCount', count($finalHolidaysList));
?>