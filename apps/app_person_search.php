<?php
/**
 * classautomate - app_person_search
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
$Db = Db::classCache();
$School = School::classCache();
$finalSearchList = array();

$peopleResult = getArrayWithSearchInArray($_GET['search'], $School->getPeopleList(), 'name,surname');
$peopleResult = getFromArray($peopleResult, array('status'=>'notUsed||used||active'));

$getClassroomNames = function($classroomList) {
    if (!empty($classroomList)) {
        foreach (explode(',', $classroomList) as $code) {
            $classroomNames[] = School::classCache()->getClassroom($code)->getInfo('name');
        }
        return implode(',', $classroomNames);
    } else {
        $languageJSON = Setting::classCache()->getInterfaceLang();
        return $languageJSON->classautomate->main->none;
    }
};

/**
 * kişi "active" degilse silme butonu ekle 
 */
if ($peopleResult != NULL) {
	
	foreach ((array)$peopleResult as $key => $value) {
		$processStatus = 0;
		
        if ($value['position'] == 'student') {
       		// ogrencinin siniflarindan bir tanesi bile aktif ise Delete butonu false olacak
       		$expStatus = explode(',', $value['status']);
            if (!is_bool(findStringInArray('notUsed', $expStatus))) $processStatus = 1;
            $classroomList = $value['classroom'];
            $classroom = $getClassroomNames($classroomList);
        }
        
        if ($value['position'] == 'instructor') {
            $Instructor = $School->getInstructor($value['code']);
            $classroomList = $Instructor->getClassroomList(true);
            $classroom = $getClassroomNames($classroomList);
        }
        
		$statusProcess[] = array ('classroom'=>$classroom, 'process'=>$processStatus);
	}

	/** hazirlanan array'i kullanarak listeyi istenilen basliklarda uret */
	$MakeList = new MakeList('code,name,surname,position','page', $peopleResult);
	$MakeListPeople = $MakeList->get();
	
	/** arama sonucu ile ilgili statusProcess arraylerini birlestir */
	$finalPersonList = merge2Array($MakeListPeople, $statusProcess);
	$statusProcess = NULL;
	
	/** dongu sonucu ana arama listesine ekleme yapiliyor */
	foreach($finalPersonList as $key => $value) {
		array_push($finalSearchList, $value);	
	}
}

setExtSmartyVars('personSearchResultArray', $finalSearchList);
setExtSmartyVars("peopleCount", count($finalSearchList));
setExtSmartyVars('search', $_GET['search']);
?>
