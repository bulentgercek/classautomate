<?php
/**
 * classautomate - app_program
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
$School = School::classCache();
$Db = Db::classCache();

$programList = getFromArray($School->getProgramList(), array('status'=>'notUsed||used||active'));
$classroomList = getFromArray($School->getClassroomList(), array('status'=>'notUsed||used||active'));

/**
 * program kaydi var mi?
 */
if ($programList != NULL) {
	
	for ($i=0; $i<count($programList);$i++) {
		$processStatus = 1;
		
		// Programlari oku ve sinif sayilarini cikart
		$countClasses = count(getFromArray($classroomList, array("program"=>$programList[$i]['code'])));

		if ($countClasses > 0) $processStatus = 0;
		
		$statusProcess[] = array ('status'=>$countClasses, 'process'=>$processStatus);
	}
	// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
	$MakeList = new MakeList('code,name', 'page', $programList);
	$MakeListPrograms = $MakeList->get();
	
	//programList ve statusProcess arraylerini birlestir
	$finalProgramsList = merge2Array($MakeListPrograms, $statusProcess);
}
setExtSmartyVars('programListArray', $finalProgramsList);
setExtSmartyVars('programCount', count($programList));

?>