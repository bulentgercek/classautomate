<?php
/**
 * classautomate - app_grouping
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Db = Db::classCache();

$groupingList = getFromArray($School->getGroupingList(), array('status'=>'notUsed||used||active'));
$classroomList = getFromArray($School->getClassroomList(), array('status'=>'notUsed||used||active'));

/**
 * grouping kaydi var mi?
 */
if ($groupingList != NULL) {
	
	for ($i=0; $i<count($groupingList);$i++) {
		$processStatus = 1;
		
		// Groupinglari oku ve sinif sayilarini cikart
		$countClasses = count(getFromArray($classroomList, array("grouping"=>$groupingList[$i]['code'])));

		if ($countClasses > 0) $processStatus = 0;
		
		$statusProcess[] = array ('status'=>$countClasses, 'process'=>$processStatus);
	}
	// hazirlanan array'i kullanarak listeyi istenilen basliklarda uret
	$MakeList = new MakeList('code,name', 'page', $groupingList);
	$MakeListGroupings = $MakeList->get();
	
	//groupingList ve statusProcess arraylerini birlestir
	$finalGroupingsList = merge2Array($MakeListGroupings, $statusProcess);
}
setExtSmartyVars('groupingListArray', $finalGroupingsList);
setExtSmartyVars('groupingCount', count($groupingList));

?>