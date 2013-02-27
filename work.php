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
//var_dump(sortArrayWithKey::get($studentList, "birthDate", 'DESC'));

// ORNEK : A+ KAN GRUBUNA SAHİP OGRENCILERI OKU
//$studentWithBloodType = Db::classCache()->getFromArray($studentList, array("bloodType"=>"A+"));
//var_dump($studentWithBloodType);

// ORNEK : A+ VE A- KAN GRUBUNA SAHİP OGRENCILERI OKU
//$studentWithBloodType = getFromArray($studentList, array("bloodType"=>"A+", "bloodType"=>"A-"));
//var_dump($studentWithBloodType);

// ORNEK : EGITMENLERI OKU
//$instructorList = $School->getPeopleList("instructor");
//var_dump($instructorList);

// ORNEK : ISTENILEN KOD NUMARALI KISILERI OKU
//$Student = $School->getStudent(2);
//var_dump($Student->getInfo("name") . " " . $Student->getInfo("surname"));

//$Instructor = $School->getInstructor(1);
//var_dump($Instructor->getInfo("name") . " " . $Instructor->getInfo("surname"));

// ORNEK : OGRENCILERIN KOD NUMARASI VE SADECE ISIM, SOYADLARINDAN DİZİ YARAT
/*foreach ($studentList as $key=>$value) {
	$studentsWithCodeAndNames[$value["code"]] = $value["name"] . " " . $value["surname"];
}*/
//var_dump($studentsWithCodeAndNames);

// ORNEK : ISTENILEN KODLU SINIFIN BİLGİLERİNİ OKU
//$Classroom = $School->getClassroom(1);
//var_dump($Classroom->getInfo());

// ORNEK : TUM SINIFLARI OKU - SIRALA
//$classroomList = $School->getClassroomList();
//var_dump($classroomList);
//var_dump(sortArrayWithKey::get($classroomList, "time", 'DESC'));

// ORNEK : İSTENİLEN SINIF İÇİN, VERITABANINDAN, YENİ GUN/SAAT KAYITLARININ SAYISINI OGREN
//$dayTimeList = $School->getDayTimeList();
//var_dump($dayTimeList);

//ORNEK : HAFTANIN 3.GUNU OLAN SINIFLARI OKU
//$classroomWithDay = getFromArray($dayTimeList, array("day"=>"1"));
//var_dump($classroomWithDay);

//ORNEK : FORMATA UYGUN LISTE
//$MakeList = new MakeList('Classroom->code,instructor_name,instructor_surname,time', array('type' => 'select', 'separators' => ' \ '));
//$ClassList = $MakeList->get();
//var_dump($ClassList);

// ORNEK : ISTENILEN SINIFIN OGRENCI LISTESI
//var_dump($School->getClassroom(1)->getStudentList());

// ORNEK : ISTENILEN SINIFIN DERS GUNLERI
//var_dump($School->getClassroom(1)->getInfo("dayTime"));

// ORNEK : ISTENILEN SINIFIN DERS GUN/SAAT NESNESİ
var_dump($School->getClassroom(1)->getDayTime()->getInfo());

// ORNEK : TUM PROGRAMLARI OKU
//var_dump(School::classCache()->getProgramList());

// ORNEK : ISTENILEN KODLU PROGRAMI OKU
//var_dump(School::classCache()->getProgram(1)->getInfo());

// ORNEK : ISTENILEN ISIMLI PROGRAMLARI OKU
//$programList = School::classCache()->getProgramList();
//var_dump(getFromArray($programList, array("name"=>"Bale")));

// ORNEK : ICERIGINDE SALSA KELIMESI OLAN PROGRAM ISIMLERINI BUL
//$programList = School::classCache()->getProgramList();
//var_dump(findStringInArray("ot", $programList));

// ORNEK : DATABASE SETTINGS TABLOSUNDAN TABLO->KOLON FORMATLI OKUYARAK YAPILAN LISTE
//$MakeList = new MakeList('main_header', array('type' => 'select', 'separators' => ' \-\ \.'));
//$ClassList = $MakeList->get();
//var_dump($ClassList);

echo "Work Finished.<br>";
exit;
// ORNEK : OZEL ARRAY'LI FORMATA UYGUN SIRA NO'LU LISTE
$array1[] = array( 'code'=>'1', 'name'=>'Bülent', 'surname'=>'Gerçek' );
$array1[] = array( 'code'=>'2', 'name'=>'Banu', 'surname'=>'Özbek' );
var_dump($array1);

$MakeList = new MakeList('code,name,surname', array('type' => 'select', 'separators' => ' '),$array1);
$list1 = $MakeList->get();
var_dump($list1);

// ORNEK : OZEL ARRAY'LI FORMATA UYGUN LISTE
$array2[] = array( 'payment'=> '50', 'paymentdate'=>'2011-07-11' );
$array2[] = array( 'payment'=> '100', 'paymentdate'=>'2011-08-05' );
var_dump($array2);

$MakeList = new MakeList('payment,paymentdate', array('type' => 'select', 'separators' => ',') ,$array2);
$list2 = $MakeList->get();
var_dump($list2);

// ORNEK : IKI LISTEYI BIRLESTIRME
echo "Array Birlestirme : list1 + list2";
var_dump(merge2Array($array1,$array2));

echo "Special Arrays Finished.<br>";
?>
