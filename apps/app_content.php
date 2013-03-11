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
				$Instructor = $School->getInstructor($Classroom->getInfo('instructor'));
				$classroomInfo['lectureCount'] = $Classroom->getLectureCount();
								
				$classroomInfo['holidayLectureCount'] = $Classroom->getLectureCount('holiday');

				$classroomInfo['holidayClassroomCode'] = $Classroom->getHolidayStatus('classroom');
				$classroomInfo['nextLectureDateTime'] =  $Classroom->getNextLectureDateTime();
				$classroomInfo['instructorNextPaymentDateTime'] =  $Instructor->getNextPaymentDateTime($Classroom);
				$classroomInfo['instructorPaymentInCase'] =  $Instructor->getPaymentInCase($Classroom);
		}
		/**
		 * Ogrencinin diger bilgileri hazirlaniyor
		 */
		foreach ((array) $studentList as $studentKey=>$studentValue) {
				$Student = $School->getStudent($studentValue['code']);
				/**
				 * Ogrencinin sınıftaki ilk ders gunu bulunuyor
				 */
				$studentActiveLectureList = $Student->getActiveLectureList($Classroom);
				$dayTimeTime = $Classroom->getDayTime($studentActiveLectureList[0]['dayTimeCode'])->getInfo('time');
				$studentList[$studentKey]['firstLectureDateTime'] = $studentActiveLectureList[0]['date'] . ' ' . $dayTimeTime;

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
								$intend['nextPaymentDateTime'] = Setting::classCache()->getInterfaceLang()->classautomate->main->none;
								break;
						default:
								$intend['nextPaymentDateTime'] = $Student->getNextPaymentDateTimeByClassroom($Classroom);
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
?>
