<?php
include 'WEB-INF/classes/Start.function.php';

$Db = Db::classCache();
$Db->connect('classautotest');

$Session = Session::classCache();
$Session->set('dbName','classautotest');
$Session->set('username','bulent');
$Session->set('timeZone','3');

$Setting = Setting::classCache();
$Setting->setInterfaceLang('browser');
$languageJSON = $Setting->getInterfaceLang();
$Db->connect('classautotest');


$Db->emptySchool();
echo "Database 'classautotest' truncated.<br>";
?>
