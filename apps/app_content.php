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

/**
 * #51 secilen sınıf Session'a gönderiliyor 
 */
Session::classCache()->set('classroomSelection', $_GET['code']);
/**
 * sınıf sbyRoom değil ise;
 */
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
				$totalLecturePrice = 0;

				/**
				 * Ogrencinin sınıftaki ilk ders gunu bulunuyor
				 */
				$studentLectureDetailsByClassroom = $Student->getLectureDetailsByClassroom($Classroom);
				
				$dayTimeTime = $Classroom->getDayTime($studentLectureDetailsByClassroom[0]['dayTimeCode'])->getInfo('time');
				$studentList[$studentKey]['firstLectureDateTime'] = $studentLectureDetailsByClassroom[0]['date'] . ' ' . $dayTimeTime;

				$cashStatus = $Student->getCashStatus($Classroom, 'studentDebt');
				/**
				 * ogrencinin guncel odeme periyodu içindeki girdiği derslerin listesini aliyoruz
				 */
				$studentCashFlowByPeriod = getFromArray($Student->getCashFlowByClassroom($Classroom), array('paymentTermNo'=>$cashStatus['paymentTermNo']));
				/**
				 * listeyi dondurerek bu ana kadar ki ders ucretlerinin toplamini buluyoruz
				 */
				foreach ((array)$studentCashFlowByPeriod as $value) {
						$totalLecturePrice += $value['lecturePrice'];
				}
				/**
				 * ogrencinin son durumunu yorumlayarak 
				 * kalan borcu belirliyoruz
				 */
				switch ($cashStatus['info']) {
						/**
						 * Ödeme Olmadığından Sınıftan Çıkarıldı
						 */
						case 'debtInfo_3':
								$remainingDebt = 0;
								break;
						default:
								$remainingDebt = $cashStatus['payment'] - ($totalLecturePrice + $cashStatus['studentMoneyLeftInCase']);
				}
				/**
				 * Diziyi başlatıyoruz, sonrasında öğrencinin borc durumu kontrol ediliyor
				 * uygun sartlarda ise örneğin halen sınıftan atılmamış ise:)
				 * devam ediyoruz, aksi takdirde NONE göndereceğiz.
				 */
				$intend = array(	'debtInfo' => $cashStatus['info'], 'remainingDebt' => abs($remainingDebt));
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

				//s($Student->getChangeList());
				//s($Student->getActiveDateTimesByClassroom($Classroom));
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
