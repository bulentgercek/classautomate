<?php
/**
 * classautomate - app_welcome
 * 
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
$Setting = Setting::classCache();
$Session = Session::classCache();
/**
 * Json dosyasini cagir
 */
$logsJson = $Setting->getLogs();
/**
 * dil ve kullanici adini cagir
 */
$language = $Setting->getLanguage('db');
$username = $Session->get('username');
/**
 * loglari diziye aktar
 */
foreach ($logsJson->classautomate->versions as $key => $value) {
		$logs[] = array(
				'no'=>$key, 'date'=>$value->date, 'time'=>$value->time, 
				'updates'=>$value->languages->$language->updates, 'bugs'=>$value->languages->$language->bugs, 'users'=>$value->users->$username
		);
}
setExtSmartyVars('logs', $logs[0]);
?>