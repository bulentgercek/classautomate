<?php
/**
 * classautomate - app_accountant
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$School = School::classCache();
$Db = Db::classCache();
/**
 * ana secim degiskenleri
 */
if ($_GET['mainSelect'] == 'income') $typeCode = '+';
if ($_GET['mainSelect'] == 'expense') $typeCode = '-';
if ($_GET['mainSelect'] == 'profit') $typeCode = '=';
if (!isset($_GET['mainSelect'])) $typeCode = '+';
/**
 * filtrelemeyi hazirlamaya basla
 */
$intend['where'] = "type='" . $typeCode . "'";
/**
 * tip listesini hazirla ve ana secim filtrelemesini yap
 */
$incomeExpenseTypeList = $School->getIncomeExpenseTypeList();
$incomeExpenseTypeList = getFromArray($incomeExpenseTypeList, array ('type'=>$typeCode));

$date = function ($dateValue) {
	if ($dateValue == 'now')
		return getDateAsFormatted();
	else {
		$fixedDate = str_replace('/', '-', $dateValue);
		return $fixedDate;
	}
};

/**
 * filtreleme için varsayilan degiskenleri duzenle
 */
if (isset($_GET['subTypeSelection'])) $intend['where'] .= " AND subType='" . $_GET['subTypeSelection'] . "'";
if (isset($_GET['startDate'])) $intend['where'] .=  " AND `dateTime` BETWEEN '" . $date($_GET['startDate']) . " 00:00:00'";
if (isset($_GET['endDate'])) $intend['where'] .=  " AND '" . $date($_GET['endDate']) . " 23:59:59'";
/**
 * filtrelere cikacak listeyi hazirla
 */
$incomeExpenseList = $School->getIncomeExpenseWithCustomDbSearch($intend);

/**
 * incomeExpense dogal listesini, tabloya algilanabilecek sekilde yazmak adina duzenliyoruz
 */
if ($incomeExpenseList != NULL) {
	foreach ($incomeExpenseList as $key => $value) {
		$arrangedIncomeExpenseList[$key]['code'] = $value['code'];
		$arrangedIncomeExpenseList[$key]['dateTime'] = $value['dateTime'];
		
		$subTypeDbInfo = getFromArray($incomeExpenseTypeList, array('code'=>$value['subType']));
	
		$arrangedIncomeExpenseList[$key]['subType'] = $languageJSON->classautomate->app_accountant->$subTypeDbInfo[0]['typeShort'];
		
		if ($value['onBehalfOf'] > 0) {
			$personInfo = getFromArray($School->getPeopleList(), array('code'=>$value['onBehalfOf']));
			$onBehalfOf = $personInfo[0]['name'] . ' ' . $personInfo[0]['surname'];
		} else {
			$onBehalfOf = $Setting->getCompanyName();
		}
		
		if ($value['classroom'] > 0) {
			$Classroom = $School->getClassroom($value['classroom']);
			$classroom = $Classroom->getInfo('name');
		} else {
			$classroom = $languageJSON->classautomate->main->none;
		}
		
		$arrangedIncomeExpenseList[$key]['onBehalfOf'] = $onBehalfOf;
		$arrangedIncomeExpenseList[$key]['classroom'] = $classroom;
		$arrangedIncomeExpenseList[$key]['amount'] = $value['amount'];
		$arrangedIncomeExpenseList[$key]['invoiceInfo'] = $value['invoiceInfo'];
	}
}

setExtSmartyVars("mainSelectTypeCode", $typeCode);
setExtSmartyVars("incomeExpenseList", $arrangedIncomeExpenseList);
setExtSmartyVars("incomeExpenseCount", count($incomeExpenseList));
setExtSmartyVars("incomeExpenseTypeList", $incomeExpenseTypeList);

$MakeList = new MakeList('code,name,surname,classroom','normal', $School->getPeopleList("student"));
$studentList = $MakeList->get();
setExtSmartyVars("studentList", $studentList);

$MakeList = new MakeList('code,name,surname','normal', $School->getPeopleList("instructor"));
$personnelList = $MakeList->get();
setExtSmartyVars("personnelList", $personnelList);

$MakeList = new MakeList('code,name,instructor_code','normal',$School->getClassroomList());
$classroomList = $MakeList->get();
setExtSmartyVars("classroomList", $classroomList);

setExtSmartyVars("currency", strtoupper(Setting::classCache()->getCurrency()));

/**
 * kar durumu hesaplari
 */
$profitList = Accountant::classCache()->getProfitList();

setExtSmartyVars('profitList', $profitList);
?>
