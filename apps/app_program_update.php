<?php
/**
 * classautomate - app_program_update
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
/**
 * sorgu alanini goster
 */
$programValues = School::classCache()->getProgram($_GET['code'])->getInfo();

setExtSmartyVars('programValues', $programValues);
?>
