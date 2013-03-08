<?php
include 'WEB-INF/classes/Start.function.php';

$Db = Db::classCache();
$Db->connect('classautotest');

$Session = Session::classCache();
$Session->set('dbName','classautotest');
$Session->set('username','bulent');
$Session->set('timeZone','3');

$Setting = Setting::classCache();
$Setting->setInterfaceLang('browser');
$languageJSON = $Setting->getInterfaceLang();

echo "Work Started.<br>";

$School = School::classCache();

// ORNEK : OGRENCILERI OKU VE SIRALA
//$studentList = $School->getPeopleList("student");
//d(SortArrayWithKey::get($studentList, "birthDate", 'DESC'));

// ORNEK : A+ KAN GRUBUNA SAHİP OGRENCILERI OKU
//$studentWithBloodType = Db::classCache()->getFromArray($studentList, array("bloodType"=>"A+"));
//d($studentWithBloodType);

// ORNEK : A+ VE A- KAN GRUBUNA SAHİP OGRENCILERI OKU
//$studentWithBloodType = getFromArray($studentList, array("bloodType"=>"A+", "bloodType"=>"A-"));
//d($studentWithBloodType);

// ORNEK : EGITMENLERI OKU
//$instructorList = $School->getPeopleList("instructor");
//d($instructorList);

// ORNEK : ISTENILEN KOD NUMARALI KISILERI OKU
//$Student = $School->getStudent(2);
//d($Student->getInfo("name") . " " . $Student->getInfo("surname"));

//$Instructor = $School->getInstructor(1);
//d($Instructor->getInfo("name") . " " . $Instructor->getInfo("surname"));

// ORNEK : OGRENCILERIN KOD NUMARASI VE SADECE ISIM, SOYADLARINDAN DİZİ YARAT
/*foreach ($studentList as $key=>$value) {
	$studentsWithCodeAndNames[$value["code"]] = $value["name"] . " " . $value["surname"];
}*/
//d($studentsWithCodeAndNames);

// ORNEK : ISTENILEN KODLU SINIFIN BİLGİLERİNİ OKU
//$Classroom = $School->getClassroom(1);
//d($Classroom->getInfo());

// ORNEK : TUM SINIFLARI OKU - SIRALA
//$classroomList = $School->getClassroomList();
//d($classroomList);
//d(SortArrayWithKey::get($classroomList, "time", 'DESC'));

// ORNEK : İSTENİLEN SINIF İÇİN, VERITABANINDAN, YENİ GUN/SAAT KAYITLARININ SAYISINI OGREN
//$dayTimeList = $School->getDayTimeList();
//d($dayTimeList);

//ORNEK : HAFTANIN 3.GUNU OLAN SINIFLARI OKU
//$classroomWithDay = getFromArray($dayTimeList, array("day"=>"1"));
//d($classroomWithDay);

//ORNEK : FORMATA UYGUN LISTE
//$MakeList = new MakeList('Classroom->code,instructor_name,instructor_surname,time', array('type' => 'select', 'separators' => ' \ '));
//$ClassList = $MakeList->get();
//d($ClassList);

// ORNEK : ISTENILEN SINIFIN OGRENCI LISTESI
//d($School->getClassroom(1)->getStudentList());

// ORNEK : ISTENILEN SINIFIN DERS GUNLERI
//d($School->getClassroom(1)->getInfo("dayTime"));

// ORNEK : ISTENILEN SINIFIN DERS GUN/SAAT NESNESİ
d($School->getClassroom(1)->getDayTime()->getInfo());

// ORNEK : TUM PROGRAMLARI OKU
//d(School::classCache()->getProgramList());

// ORNEK : ISTENILEN KODLU PROGRAMI OKU
//d(School::classCache()->getProgram(1)->getInfo());

// ORNEK : ISTENILEN ISIMLI PROGRAMLARI OKU
//$programList = School::classCache()->getProgramList();
//d(getFromArray($programList, array("name"=>"Bale")));

// ORNEK : ICERIGINDE SALSA KELIMESI OLAN PROGRAM ISIMLERINI BUL
//$programList = School::classCache()->getProgramList();
//d(findStringInArray("ot", $programList));

// ORNEK : DATABASE SETTINGS TABLOSUNDAN TABLO->KOLON FORMATLI OKUYARAK YAPILAN LISTE
//$MakeList = new MakeList('main_header', array('type' => 'select', 'separators' => ' \-\ \.'));
//$ClassList = $MakeList->get();
//d($ClassList);

echo "Work Finished.<br>";
exit;
// ORNEK : OZEL ARRAY'LI FORMATA UYGUN SIRA NO'LU LISTE
$array1[] = array( 'code'=>'1', 'name'=>'Bülent', 'surname'=>'Gerçek' );
$array1[] = array( 'code'=>'2', 'name'=>'Banu', 'surname'=>'Özbek' );
d($array1);

$MakeList = new MakeList('code,name,surname', array('type' => 'select', 'separators' => ' '),$array1);
$list1 = $MakeList->get();
d($list1);

// ORNEK : OZEL ARRAY'LI FORMATA UYGUN LISTE
$array2[] = array( 'payment'=> '50', 'paymentdate'=>'2011-07-11' );
$array2[] = array( 'payment'=> '100', 'paymentdate'=>'2011-08-05' );
d($array2);

$MakeList = new MakeList('payment,paymentdate', array('type' => 'select', 'separators' => ',') ,$array2);
$list2 = $MakeList->get();
d($list2);

// ORNEK : IKI LISTEYI BIRLESTIRME
echo "Array Birlestirme : list1 + list2";
d(merge2Array($array1,$array2));

echo "Special Arrays Finished.<br>";
?>
