<?php
/**
 * classautomate - app_holiday_update
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * sorgu alanini goster
 */
$School = School::classCache();
$Holiday = $School->getHoliday($_GET['code']);
$holidayList = $Holiday->getInfo();
$instructorList = $School->getInstructorList();
$classroomList = $School->getClassroomList();

$explodedInfo = explode('|', $holidayList['info']);
$expStartDateTime = explode(' ', $holidayList['startDateTime']);
$expEndDateTime = explode(' ', $holidayList['endDateTime']);
$holidayList['startDate'] = $expStartDateTime[0];
$holidayList['startTime'] = $expStartDateTime[1];
$holidayList['endDate'] = $expEndDateTime[0];
$holidayList['endTime'] = $expEndDateTime[1];

unset($holidayList['startDateTime']);
unset($holidayList['endDateTime']);

if ($holidayList['type'] == 'official') {
	$holidayList['subject'] = $explodedInfo[0];
}
if ($holidayList['type'] == 'personnel') {
	$holidayList['subject'] = 'personnel';
	$holidayList['holidayPersonnel'] = $explodedInfo[0];
	$holidayList['backupPersonnel'] = $explodedInfo[1];
}
if ($holidayList['type'] == 'classroom') {
	$holidayList['subject'] = 'classroom';
	$holidayList['holidayClassroom'] = $explodedInfo[0];
}
if ($holidayList['type'] == 'custom') {
	$holidayList['subject'] = 'custom';
	$holidayList['customSubject'] = $explodedInfo[0];
}

unset($holidayList['info']);
setExtSmartyVars('holidayList', $holidayList);

$holidaySubjects = $School->getHolidaySubjectList();

setExtSmartyVars('instructorList', $instructorList);
setExtSmartyVars('classroomList', $classroomList);
setExtSmartyVars('subjectList', $holidaySubjects);
?>