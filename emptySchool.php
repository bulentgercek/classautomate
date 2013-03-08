<?php
include 'WEB-INF/classes/Start.function.php';
include 'WEB-INF/lib/kint/Kint.class.php';

$Db = Db::classCache();
$Db->connect('classautotest');

$Session = Session::classCache();
$Session->set('dbName', 'classautotest');
$Session->set('username', 'bulent');
$Session->set('timeZone', '3');

$Setting = Setting::classCache();
$Setting->setInterfaceLang('browser');

$Db->connect('classautotest');

if (isset($_POST['onay'])) {
		$Db->emptySchool();
		echo "Database 'classautotest' truncated.<br>";
		exit;
}
?>
<html>
		<script>
		function disp_confirm()
		{
				var r = confirm("Classautotest veritabanı TRUNCATE edilsin mi?");
				if (r === true) {
						document.getElementById('onay').value = 'true';
						document.myform.submit();
				}
		}
		</script>
<body onload='disp_confirm();'>
		<form name="myform" action="emptySchool.php" method='post'>
				<input name='onay' id='onay' type='hidden' value=''>
		</form>
</body>
</html>