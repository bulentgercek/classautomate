<?php
/**
 * classautomate - app_person_add
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * sorgu alanini goster
 */
setExtSmartyVars('app_student_add_defPayPeriod', $Setting->getDefPayPeriod());

$paymentPeriodList = array('weekly','monthly','monthly3','monthly6','monthly12','fixed');
$bloodTypeList = array('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', '0+', '0-');

setExtSmartyVars('paymentPeriodList', $paymentPeriodList);
setExtSmartyVars('bloodTypeList', $bloodTypeList);

setExtSmartyVars('position', $_GET["position"]);
?>