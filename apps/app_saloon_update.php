<?php
/**
 * classautomate - app_saloon_update
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * sorgu alanini goster
 */
$saloonValues = School::classCache()->getSaloon($_GET['code'])->getInfo();

setExtSmartyVars('saloonValues', $saloonValues);
?>