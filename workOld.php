<?php
include 'WEB-INF/classes/Start.function.php';

$Db = Db::classCache();
$Db->connect('classautotest');


$Session = Session::classCache();
$Session->set('dbName','classautotest');

$Setting = Setting::classCache();
$Setting->setInterfaceLang('browser');
$languageJSON = $Setting->getInterfaceLang();

/**
                <!-- {foreach from=$classList item=$classList}
                	{html_options values=$classList.name output=$classList.code}
                {/foreach} -->
*/


// ORNEK : OGRENCILERI OKU
$People = new People();
$peopleArray = $People->getAll();
//var_dump($peopleArray);

var_dump(sortArrayWithKey::get($peopleArray, "birthDate", 'DESC'));
// ORNEK : A+ KANGRUPLU OGRENCILERI OKU
//$Students = new People( array('position' => 'student', 'bloodType' => 'A-') );
//$studentsArray = $Students->get();
//var_dump($studentsArray);

// ORNEK : EGITMENLERI OKU
//$Instructors = new People( array('position' => 'student') );
//$instructorArray = $Instructors->get();
//var_dump($instructorArray);

// ORNEK : ISTENILEN KODLU KISILERI OKU
//$Person1 = new Person(1);
//$Person1Array = $Person1->get();
//var_dump($Person1Array);
//var_dump($Person1Array['name']);

// ORNEK : OGRENCILERIN KOD NUMARASI VE SADECE ISIMLERINI ARRAY'e GONDER
//foreach ($studentsArray as $key => $value) {
//	$studentsJustNames[$value[code]] = $value[name];
//}
//var_dump($studentsJustNames);

// ORNEK : ISTENILEN KODLU SINIFI OKU
//$Classroom = new Classroom(1);
//$classRoomArray = $Classroom->get();
//var_dump($classRoomArray);

// ORNEK : TUM SINIFLARI OKU
//$Classrooms = new Classrooms();
//$classRoomsArray = $Classrooms->getAll();
//$classRoomsArraySorted = $Classrooms->sortWithColumn("day");
//var_dump($classRoomsArray);
//var_dump($classRoomsArraySorted);

//ORNEK : HAFTANIN 3.GUNU OLAN SINIFLARI OKU
//$ClassroomsDay3 = new Classrooms( array('day' => '3') );
//$classRoomsDay3 = $ClassroomsDay3->get();
//var_dump($classRoomsDay3);

//ORNEK : FORMATA UYGUN LISTE
//$MakeList = new MakeList('classrooms->code,instructor_name,instructor_surname,time','select');
//$ClassList = $MakeList->get();
//var_dump($ClassList);

// ORNEK : ISTENILEN SINIFIN OGRENCI LISTESI
/*
$Classroom1 = new Classroom(1);
$StudentListC1 = $Classroom1->getStudents();
var_dump($StudentListC1);
*/

// ORNEK : ISTENILEN KODLU PROGRAMI OKU
/*
$Program = new Program(2);
$programArray = $Program->get();
var_dump($programArray);
*/

// ORNEK : TUM PROGRAMLARI OKU
/*
$Programs = new Programs();
$programArray = $Programs->getAll();
var_dump($programArray);
*/

// ORNEK : ISTENILEN KODLU PROGRAMI OKU
//$ProgramsClassrooms = new Program(2);
//$programClassroomsArray = $ProgramsClassrooms->getClassrooms();
//var_dump($programClassroomsArray);

// ORNEK : ISTENILEN ISIMLI PROGRAMlARI OKU
/*
$ProgramsNamed = new Programs( array ("name"=>"Salsa On1"));
$programsNamedArray = $ProgramsNamed->get();
var_dump($programsNamedArray);
*/
exit;
// ORNEK : ICERIGINDE SALSA KELIMESI OLAN PROGRAM ISIMLERINI BUL
$SearchedPrograms = new Programs( array('where'=>"(name LIKE '%2%')", 'orderby'=>'name ASC') );
$programsResult = $SearchedPrograms->get();
var_dump($programsResult);

echo "<br>========================================<br>";
// ORNEK : DATABASE SETTINGS TABLOSUNDAN TABLO->KOLON FORMATLI OKUYARAK YAPILAN LISTE
$MakeList = new MakeList('main_header', array('type' => 'select', 'separators' => ' \-\ \.'));
$ClassList = $MakeList->get();
var_dump($ClassList);

echo "<br>========================================<br>";
// ORNEK : OZEL ARRAY'LI FORMATA UYGUN SIRA NO'LU LISTE
$array1[] = array( 'code'=>'1', 'name'=>'Bülent', 'surname'=>'Gerçek' );
$array1[] = array( 'code'=>'2', 'name'=>'Banu', 'surname'=>'Özbek' );
var_dump($array1);

$MakeList = new MakeList('code,name,surname', array('type' => 'select', 'separators' => ' '),$array1);
$list1 = $MakeList->get();
var_dump($list1);

echo "<br>========================================<br>";
// ORNEK : OZEL ARRAY'LI FORMATA UYGUN LISTE
$array2[] = array( 'payment'=> '50', 'paymentdate'=>'2011-07-11' );
$array2[] = array( 'payment'=> '100', 'paymentdate'=>'2011-08-05' );
var_dump($array2);

$MakeList = new MakeList('payment,paymentdate', array('type' => 'select', 'separators' => ',') ,$array2);
$list2 = $MakeList->get();
var_dump($list2);

echo "<br>========================================<br>";
// ORNEK : IKI LISTEYI BIRLESTIRME
echo "Array Birlestirme : list1 + list2";
var_dump(merge2Array($array1,$array2));
?>
