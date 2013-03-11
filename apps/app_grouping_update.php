<?php
/**
 * classautomate - app_grouping_update
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * sorgu alanini goster
 */
$groupingValues = School::classCache()->getGrouping($_GET['code'])->getInfo();

setExtSmartyVars('groupingValues', $groupingValues);
?>
