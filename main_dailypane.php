<?php
/**
 * classautomate - main / dailypane
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Fc = new FluxCapacitor();
/**
 * gunun tarihini belirle
 */
$date = getDateAsFormatted();
/**
 * gonderilen tarihten haftanın gününü ve
 * günün seçili olan dildeki karşılığını hazırla
 */
$weekDayNo = getWeekDayOfTheDate($date);
$weekDayText = getWeekDayAsText($weekDayNo);
/**
 * secilen gundeki dayTime listesini hazirla
 */
$dailyDayTimeList = getFromArray($School->getDayTimeList(), array('day' => $weekDayNo));
/**
 * tum duzenlemede kullanilacak olan 'beyin' gorevi goren
 * Classrooms dizisi
 */
$Classrooms = array();
/**
 * secilen gun icerisindeki siniflarin ders listelerini cikariyoruz
 */
foreach ((array) $dailyDayTimeList as $key => $value) {

		if (!isset($Classrooms[$value['classroom']])) {
				/**
				 * sinif nesnemizi cagiriyoruz
				 */
				$Classroom = $School->getClassroom($value['classroom']);
				if ($Classroom->getInfo('status') == 'active') {
						/**
						 * sinifin kayit tarihi ile gunun tarihi karsilastiriliyor
						 */
						$currentDateDayTime = $date . ' ' . $value['endTime'];
						$isClassroomRecordOk = getDateTimeDiff($currentDateDayTime, $Classroom->getInfo('recordDate'), 'type');
						/**
						 * eger secilen gunun tarih ve ders bitis saati, 
						 * sinifin kayit tarihinden sonra veya ayni saatte ise
						 * ilgili sinif ile ilgili islem yapilabilecek
						 * 
						 * (bitis tarihi diyoruz boylece sinif ders bitmeden calistirilabilecek)
						 */
						if ($isClassroomRecordOk >= 0) {
								$Classrooms[$value['classroom']]['Classroom'] = $Classroom;
								/**
								 * sinifin guncel durumu diziye ekleniyor
								 */
								$Classrooms[$value['classroom']]['classroomStatus'] = $Classroom->getInfo('status');
						}
				}
		}
}
/**
 * siniflar listemize her sinifin ders listesini aktariyoruz
 */
foreach ((array) $Classrooms as $key => $value) {
		/**
		 * sinif nesnemizi cagiriyoruz
		 */
		$Classroom = $value['Classroom'];

		// ######################### FLUX CAPACITOR VERILERİ ##############################
		/**
		 * eger sinif aktif ise Flux Capacitor diziye ekstra bilgiler ekleyecek;)
		 */
		if ($value['classroomStatus'] == 'active') {
				/**
				 * Flux'a sinifimizi tanimliyoruz
				 */
				$Fc->setValues(array('classroomCode' => $Classroom->getInfo('code')));
				/* @var $startDateTime sinifin baslangic ders ve saati */
				$startDateTime = $Classroom->getStartDateTime();
				/**
				 * Flux'a zaman dilimlerini tanimliyoruz
				 */
				$Fc->setValues(array(
						'startDateTime' => $startDateTime, 'limitDateTime' => $date . ' 23:59:59'
				));
				/**
				 * Flux'dan secilen tarihe ait ders bilgilerini cekiyoruz
				 */
				$lectureList = $Fc->getLecture(array('date' => $date));
				/**
				 * sinifin ders listeside dizimize ekleniyor
				 */
				$Classrooms[$key]['lectureList'] = $lectureList;
		}
}
/**
 * panelde gösterilecek sınıf dizisini hazırla
 */
foreach ((array) $Classrooms as $key=>$value) {

		foreach ((array) $value['lectureList'] as $lectureValue) {
				/**
				 * ilgili dayTime bilgilerini aliyoruz
				 */
				$dayTime = getArrayKeyValue(getFromArray($dailyDayTimeList, array('code'=>$lectureValue['dayTimeCode'])), 0);
				$panelClassroomList[] = array(
						'name'=>$value['Classroom']->getInfo('name'), 
						'startTime'=>getArrayKeyValue($dayTime,'time'),
						'endTime'=>getArrayKeyValue($dayTime,'endTime'),
						'lectureStatus'=>$lectureValue['lectureStatus'],
						'url'=>'main.php?tab=app_rollcall&classroom=' . $value['Classroom']->getInfo('code') . '&dayTime=' . getArrayKeyValue($dayTime,'code') . '&date=' . getDateAsFormatted()
				);
		}
}
setExtSmartyVars('panelClassroomList', $panelClassroomList);
?>