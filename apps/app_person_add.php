<?php
/**
 * classautomate - app_person_add
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * sorgu alanini goster
 */
setExtSmartyVars('app_student_add_defPayPeriod', $Setting->getDefPayPeriod());

$paymentPeriodList = array('weekly','monthly','monthly3','monthly6','monthly12','fixed');
$bloodTypeList = array('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', '0+', '0-');

setExtSmartyVars('paymentPeriodList', $paymentPeriodList);
setExtSmartyVars('bloodTypeList', $bloodTypeList);

setExtSmartyVars('position', $_GET["position"]);

setExtSmartyVars("currency", strtoupper($Setting->getCurrency()));

/**
 * siniflarin donem ici ders sayisini aliyoruz
 */
$classroomList = $School->getClassroomList();
foreach ($classroomList as $key => $value) {
		$Classroom = $School->getClassroom($value['code']);
		$classroomDayTimes = $Classroom->getDayTimeList();
		$classroomInfo[$value['code']]['lessonCountWeekly'] = count($classroomDayTimes);
		/**
		 * ders sayisi limiti var mı?
		 */
		if ($value['termCountLimit'] != '') {
				/**
				 * ders sayisi limiti haftalik ders sayisindan buyuk mu?
				 */
				if ($value['termCountLimit'] < count($classroomDayTimes)) {
						/**
						 * o zaman limitimiz limit ders sayisidir
						 */
						$classroomInfo[$value['code']]['lessonLimit'] = $value['termCountLimit'];
				}
		}
		/**
		 * aktif olan sınıflar arasında tarih limiti var mı?
		 */
		if ($value['termDateLimit'] != '0000-00-00 00:00:00' && $value['status'] == 'active') {
				$Fc = new FluxCapacitor( array(
						'classroomCode'=>$value['code'],
						'startDateTime'=>$value['startDate'] . ' ' . $Classroom->getDayTime($value['startDayTime'])->getInfo('time'),
						'limitDateTime'=>$value['termDateLimit']
				));
				/**
				 * limit olarak verilen tarih ile ilk ders tarihi arasindaki ders sayisi
				 * haftalik ders sayisindan buyuk mu?
				 */
				if ($Fc->getLectureCount() < count($classroomDayTimes)) {
						/**
						 * o zaman limitimiz limit ders sayisidir
						 */
						$classroomInfo[$value['code']]['lessonLimit'] = $Fc->getLectureCount();
				}
		}
		/**
		 * eger bu asamaya kadar limit tanımı yapılmamış ise
		 * limitimiz haftalık ders sayısıdır
		 */
		if ($classroomInfo[$value['code']]['lessonLimit']) {
				$classroomInfo[$value['code']]['lessonLimit'] = count($classroomDayTimes);
		}
}

setExtSmartyVars('classroomInfo', $classroomInfo);
?>