<?php
/**
 * classautomate - app_classroom_update
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Db = Db::classCache();
/**
 * sorgu alanini goster
 */
$classroomValues = $School->getClassroom($_GET['code'])->getInfo();
setExtSmartyVars('classroomValues', $classroomValues);

setExtSmartyVars('daysOfWeek', daysOfWeek());

/** Programlar listesini hazirla */
$programList = $School->getProgramList();

// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
$MakeList = new MakeList('code,name', 'select', $programList);
$MakeListPrograms = $MakeList->get();

/** Egitmenler listesini hazirla */
$instructorList = $School->getInstructorList();
$instructorList = SortArrayWithKey::get($instructorList, "name", 'ASC');

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

$termLimitCountTypes = array("lesson");
setExtSmartyVars('termLimitCountTypes', $termLimitCountTypes);

/** dayTime listesini hazirla */
$dayTimeList = $School->getClassroom($_GET['code'])->getDayTimeList();
setExtSmartyVars('dayTimeList', $dayTimeList);
?>
