<?php
/**
 * classautomate - main / header
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
/**
 * baslik tarihi methodu cacheleniyor
 * sistem versiyon numarasi methodu cacheleniyor
 */
$mainDate = mainDate();
$systemVersion = systemVersion();
/**
 * okulun adi, baslik tarihi ve versiyon numaralari
 * smarty'e gonderiliyor 
 */
setExtSmartyVars('main_header_fullSchoolName', $Setting->getCompanyName());
setExtSmartyVars('main_header_mainDate', $mainDate);
setExtSmartyVars('main_header_version', $systemVersion);
/**
 * kullanici bilgilerinin gosterimi icin gereken degiskenler
 * smarty'e gonderiliyor
 */
setExtSmartyVars('main_username', $Session->get('username'));
setExtSmartyVars('main_fullName', $Session->get('fullName'));
/**
 * okuldaki siniflarin listesi
 */
$MakeList = new MakeList('main_header',
						array	(	'type' => 'select',
									'separators' => ' (\ \)',
									'shortCharCount' => 2,
									'shortCharStopper' => '.',
									'sortColumn'=>'day'
								));
$classList = $MakeList->get();
setExtSmartyVars('classList', $classList);

/**
 * kisi listesi hazirlaniyor
 */
$School = School::classCache();
$peopleList = $School->getPeopleList();
setExtSmartyVars('peopleList', $peopleList);

?>
