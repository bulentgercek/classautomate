<?php
/**
 * classautomate - app_saloon
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Db = Db::classCache();

$saloonList = getFromArray($School->getSaloonList(), array('status'=>'notUsed||used||active'));

$classroomList = $School->getClassroomList();
/**
 * salon kaydi var mi?
 */
if ($saloonList != NULL) {
	for ($i=0; $i<count($saloonList);$i++) {
		$processStatus = 1;
		
		// salonlari oku ve sinif sayilarini cikart
		$countClasses = count(getFromArray($classroomList, array("saloon"=>$saloonList[$i]['code'])));
	
		if ($countClasses > 0) $processStatus = 0;
		
		$statusProcess[] = array ('status'=>$countClasses, 'process'=>$processStatus);
	}
	
	// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
	$MakeList = new MakeList('code,name,address','page',$saloonList);
	$MakeListSaloons = $MakeList->get();

	//saloonList ve statusProcess arraylerini birlestir
	$finalSaloonsList = merge2Array($MakeListSaloons, $statusProcess);
}

setExtSmartyVars('saloonListArray', $finalSaloonsList);
setExtSmartyVars('saloonCount', count($saloonList));
?>
