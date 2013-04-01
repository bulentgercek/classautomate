<?php
/**
 * classautomate - app_content
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();

$Classroom = $School->getClassroom($_GET['code']);
$ClassroomList = $School->getClassroomList();

$studentList = $Classroom->getStudentList();

if ($_GET['code'] != 'sbyRoom') {

		$MakeList = new MakeList('code,name,termCountLimit,termDateLimit,instructor_name,instructor_surname,program_name,saloon_name,startDate,notes,status', 'page', $ClassroomList);
		$classroomInfo = getArrayKeyValue(getFromArray($MakeList->get(), array('code' => $_GET['code'])), 0);

		if ($classroomInfo['status'] == 'active') {
				$lectureCount = array('lectureCount' => $Classroom->getLectureCount());
				$holidayLectureCount = array('holidayLectureCount' => count($Classroom->getLectureCount('holiday')));
				$holidayClassroomCode = array('holidayClassroomCode' => $Classroom->getHolidayStatus('classroom'));

				$classroomInfo = merge2Array($classroomInfo, $lectureCount);
				$classroomInfo = merge2Array($classroomInfo, $holidayLectureCount);
				$classroomInfo = merge2Array($classroomInfo, $holidayClassroomCode);
		}
		/**
		 * Ogrenci son borc durumu cagiriliyor
		 */
		foreach ((array) $studentList as $studentValue) {
				$Student = $School->getStudent($studentValue['code']);
				$cashStatus = $Student->getCashStatus($Classroom, 'studentDebt');
				/**
				 * Diziyi başlatıyoruz, sonrasında öğrencinin borc durumu kontrol ediliyor
				 * uygun sartlarda ise örneğin halen sınıftan atılmamış ise:)
				 * devam ediyoruz, aksi takdirde NONE göndereceğiz.
				 */
				$intend = array(	'debtInfo' => $cashStatus['info'], 'remainingDebt' => $cashStatus['value']);
				switch ($cashStatus['info']) {
						case 'debtInfo_3':
						case 'debtInfo_5':
						case 'debtInfo_8':
								$intend['nextPaymentDate'] = Setting::classCache()->getInterfaceLang()->classautomate->main->none;
								break;
						default:
								$intend['nextPaymentDate'] = $Student->getNextPaymentDateByClassroom($Classroom);
				}
				$studentDebtList[] = $intend;
		}
} else {
		$classroomInfo = array('code' => 'sbyRoom');
}

setExtSmartyVars("classroomInfo", $classroomInfo);
$studentList = merge2Array($studentList, (array) $studentDebtList);
setExtSmartyVars("studentList", $studentList);

$currency = strtoupper(Setting::classCache()->getCurrency());
setExtSmartyVars("currency", $currency);

/*
$Instructor = $School->getInstructor($Classroom->getInfo('instructor'));
Accountant::classCache()->getInstructorPaymentPeriods($Instructor, $Classroom);
*/
?>
