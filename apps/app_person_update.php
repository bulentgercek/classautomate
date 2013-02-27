<?php
/**
 * classautomate - app_student_update
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * sorgu alanini goster
 */
setExtSmartyVars('app_student_update_defPayPeriod', $Setting->getDefPayPeriod());

$paymentPeriodList = array('weekly','monthly','monthly3','monthly6','monthly12','fixed');
$bloodTypeList = array('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', '0+', '0-');

setExtSmartyVars('paymentPeriodList', $paymentPeriodList);
setExtSmartyVars('bloodTypeList', $bloodTypeList);

switch ($_GET["position"]) {
	case "student":
		$personValues = School::classCache()->getStudent($_GET["code"])->getInfo();
		break;
	case "instructor":
		$personValues = School::classCache()->getInstructor($_GET["code"])->getInfo();
		break;
	case "asistant":
		$personValues = School::classCache()->getAsistant($_GET["code"])->getInfo();
		break;
	case "secretary":
		$personValues = School::classCache()->getSecretary($_GET["code"])->getInfo();
		break;
	case "cleaner":
		$personValues = School::classCache()->getCleaner($_GET["code"])->getInfo();
		break;
}
setExtSmartyVars('personValues', $personValues);
setExtSmartyVars('position', $_GET["position"]);
?>
