<?php

/**
 * classautomate - app_rollcall
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Fc = FluxCapacitor::classCache();
/**
 * gunun tarihini belirle
 */
if ($_GET['date'] == 'now') {
	$date = getDateAsFormatted();
} else {
	$fixedDate = str_replace('/', '-', $_GET['date']);
	$date = $fixedDate;
}
/**
 * gonderilen tarihten haftanın gününü ve
 * günün seçili olan dildeki karşılığını hazırla
 */
$weekDayNo = getWeekDayOfTheDate($date);
$weekDayText = getWeekDayAsText($weekDayNo);
/**
 * secilen gundeki dayTime listesini hazirla
 */
$dailyDayTimeList = getFromArray($School->getDayTimeList(), array('day'=>$weekDayNo));
/**
 * tum duzenlemede kullanilacak olan 'beyin' gorevi goren
 * Classrooms dizisi
 */
$Classrooms = array();
/**
 * secilen gun icerisindeki siniflarin ders listelerini cikariyoruz
 */
foreach ((array)$dailyDayTimeList as $key => $value) {
		
		if (!isset($Classrooms[$value['classroom']])) {
				/**
				 * sinif nesnemizi cagiriyoruz
				 */
				$Classroom = $School->getClassroom($value['classroom']);
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
/**
 * siniflar listemize her sinifin ders listesini aktariyoruz
 */
foreach ((array)$Classrooms as $key => $value) {
				/**
				 * sinif nesnemizi cagiriyoruz
				 */
				$Classroom = $value['Classroom'];
				$classroomDayTimeList = $Classroom->getDayTimeList();
				
        // ######################### FLUX CAPACITOR VERILERİ ##############################
        /**
         * eger sinif aktif ise Flux Capacitor diziye ekstra bilgiler ekleyecek;)
         */
        if ($value['classroomStatus'] == 'active') {        
            /**
						 * Flux'a sinifimizi tanimliyoruz
						 */
						$Fc->setValues( array('classroomCode'=>$Classroom->getInfo('code')) );

						/* @var $dayTimeKey sinifin baslangic ders kodunun sinif dizisindeki pozisyonu */
						$dayTimeKey = $Fc->getStartDayTimeKey();
						
						/* @var $dayTimeTime sinifin ilk baslangic (aktif edildigi) saati */
						$dayTimeTime = $classroomDayTimeList[$dayTimeKey]['time'];

						/**
						 * Flux'a zaman dilimlerini tanimliyoruz
						 */
						$Fc->setValues( array(  'startDateTime'=>$Classroom->getInfo('startDate') . ' ' . $dayTimeTime,
																		'limitDateTime'=>$date . '23:59:59') );

						/**
						 * Flux'dan secilen tarihe ait ders bilgilerini cekiyoruz
						 */
						$lectureList = $Fc->getLecture( array('date'=>$date) );
						/**
						 * sinifin ders listeside dizimize ekleniyor
						 */
						$Classrooms[$key]['lectureList'] = $lectureList; 
				}
}
/**
 * son olarak sayfada kullanilacak olan diziyi hazirliyoruz
 */
$activeClassroomList = array();
$notActiveClassroomList = array();
$activeNo = 0;
$notActiveNo = 0;
/**
 * sonuclari sinif->dayTime dongusu ile aliyoruz
 */
foreach ((array)$Classrooms as $topValue) {
		/**
		 * sinif nesnemizi cagiriyoruz
		 */
		$Classroom = $topValue['Classroom'];
		$lectureList = $topValue['lectureList'];
		$dayTimeList = $Classroom->getDayTimeList();

		/**
		 * sinifin egitmenini cagir
		 */
		$Instructor = $School->getInstructor($Classroom->getInfo('instructor'));
    /**
     * sinifin programini cagir
     */
    $Program = $School->getProgram($Classroom->getInfo('program'));
		/**
		 * eger sinif active ise activeClassroomList dizisi burada doldurluyor
		 */
		foreach ((array)$lectureList as $listValue) {

				$dayTime = getFromArray($dayTimeList, array('code'=>$listValue['dayTimeCode']));
				$dayTime = $dayTime[0];

				$activeClassroomList[$activeNo]['code'] = $Classroom->getInfo('code');
				$activeClassroomList[$activeNo]['dayTimeCode'] = $listValue['dayTimeCode'];
				$activeClassroomList[$activeNo]['classTopName'] = $Classroom->getInfo('name');
				$activeClassroomList[$activeNo]['className'] = $Classroom->getInfo('name') . ' (' .  $dayTime['time'] . '-' . $dayTime['endTime'] . ') ';
				$activeClassroomList[$activeNo]['instructor'] = $Instructor->getInfo('name') . " " . $Instructor->getInfo('surname');
				$activeClassroomList[$activeNo]['program'] = $Program->getInfo('name');
				$activeClassroomList[$activeNo]['time'] = $dayTime['time'];
				$activeClassroomList[$activeNo]['endTime'] = $dayTime['time'];
				/**
				 * sinifin ders numarasi
				 */
				$activeClassroomList[$activeNo]['lectureNo'] = $listValue['count'];
				/**
				 * eger tatile denk geliyor ise;
				 * tatil tipi personel'midir?
				 */
				if ($listValue['type'] == 'personnel') {
						/**
						 * ...tatil kaydindaki egitmen sinifin egitmeni midir?
						 */
						if ($listValue['infoCode'] == $Instructor->getInfo['code']) {
								$lectureStatus = $listValue['lectureStatus'];
								/**
								 * ogretmenin izin gunu yerine baska bir egitmen giriyor mudur?
								 */
								if (isset($listValue['personnelReplacement'])) {
										$InstructorReplacement = $School->getInstructor($listValue['personnelReplacement']);
										$activeClassroomList[$activeNo]['instructor'] .= ' -> ' . $InstructorReplacement->getInfo('name') . ' ' . $InstructorReplacement->getInfo('surname');
								}
						} 
						/**
						 * tatil kaydinda ki egitmen sinifin egitmeni degil ise;
						 */
						else {
								$lectureStatus = 'on';
						}
				}
				/**
				 * okul tatile denk geliyor ancak personel izni yok ise;
				 */
				else {
						$lectureStatus = $listValue['lectureStatus'];
				}
				/**
				 * dersin durumu diziye ekleniyor
				 */
				$activeClassroomList[$activeNo]['lectureStatus'] = $lectureStatus;
				/** 
				 * sinifin hali hazirda yoklama bilgisi varsa okunuyor
				 * ve diziye isleniyor 
				 */
				$rollcallsByDayTime = $School->getRollcallsByDate( array('date'=>$date, 'dayTime'=>$listValue['code']) );            
				$activeClassroomList[$activeNo]['participants'] = count($rollcallsByDayTime);
				/**
				 * son olarak sinifin guncel durumu da listeye ekleniyor
				 */
				$activeClassroomList[$activeNo]['classroomStatus'] = $Classroom->getInfo('status'); 
				/**
				 * sayaci arttiriyoruz
				 */
				$activeNo++;
		}
		/**
		 * aktif olmayan siniflarin listesi olan notActiveClassroomList burada dolduruluyor
		 */
		if ($topValue['classroomStatus'] != 'active') {
				foreach ($dayTimeList as $dayTimeKey => $dayTimeValue) {
						$notActiveClassroomList[$notActiveNo]['code'] = $Classroom->getInfo('code');
						$notActiveClassroomList[$notActiveNo]['dayTimeCode'] = $dayTimeValue['code'];
						$notActiveClassroomList[$notActiveNo]['classTopName'] = $Classroom->getInfo('name');
						$notActiveClassroomList[$notActiveNo]['className'] = $Classroom->getInfo('name') . ' (' .  $dayTimeValue['time'] . '-' . $dayTimeValue['endTime'] . ') ';
						$notActiveClassroomList[$notActiveNo]['instructor'] = $Instructor->getInfo('name') . " " . $Instructor->getInfo('surname');
						$notActiveClassroomList[$notActiveNo]['program'] = $Program->getInfo('name');
						$notActiveClassroomList[$notActiveNo]['time'] = $dayTimeValue['time'];
						$notActiveClassroomList[$notActiveNo]['endTime'] = $dayTimeValue['endTime'];
						/**
						 * son olarak sinifin guncel durumu da listeye ekleniyor
						 */
						$notActiveClassroomList[$notActiveNo]['classroomStatus'] = $Classroom->getInfo('status'); 
						/**
						 * actif olmayan siniflarin sayacini arttiriyoruz
						 */
						$notActiveNo++;
				}
		}
}
/** 
 * ######################### OGRENCI YOKLAMA LİSTESİ ##############################
 * sinif - gun/saat secimi yapilmissa ilgili sinifin ogrenci listesi hazirlaniyor
 */
if ($_GET['classroom'] != "all") {
	$Classroom = $School->getClassroom($_GET['classroom']);
	setExtSmartyVars("studentList", $Classroom->getStudentList());
	setExtSmartyVars("classroomInfo", $Classroom->getInfo());

	/** yoklama */
	$rollcalls = $School->getRollcallsByDate( array('date'=>$date, 'dayTime'=>$_GET['dayTime']) );

	foreach ((array)$Classroom->getStudentList() as $key => $value) {

		$rollcallResult = getFromArray( $rollcalls, array('personCode'=>$value[code]) );

		if ($rollcallResult[0]['code'] == null) {
				$rollcallResultCode = "0";
		} else {
				$rollcallResultCode = $rollcallResult[0]['code'];
		}
		$rollcallList[] = array ('studentCode'=>$value[code], 'rollcallCode'=>$rollcallResultCode);
	}
	setExtSmartyVars('rollcallList', $rollcallList);
}
/**
 * smarty degiskenlerini gonder
 */
setExtSmartyVars('weekDayText', $weekDayText);
setExtSmartyVars('date', $date);
setExtSmartyVars('activeClassroomCount', count($activeClassroomList));
setExtSmartyVars('activeClassroomList', $activeClassroomList);
setExtSmartyVars('notActiveClassroomCount', count($notActiveClassroomList));
setExtSmartyVars('notActiveClassroomList', $notActiveClassroomList);
setExtSmartyVars('classroomCount',  count($activeClassroomList) + count($notActiveClassroomList));
setExtSmartyVars('classroom', $_GET['classroom']);
?>
