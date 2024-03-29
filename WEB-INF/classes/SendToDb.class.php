<?php

/**
 * SendToDb : Public
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class SendToDb
{
		/**
		 * bilgileri ekle
		 *
		 * @param object
		 * @return void
		 */
		public static function add($object, $nonPOST = array())
		{
				/** islemi belirle */
				$process = 'add';
				/**
				 * singleton siniflar
				 */
				$Db = Db::classCache();
				$DbChanges = DbChanges::classCache();
				/**
				 * tableName ve dizi bilgisini implement metodu ile tamamla
				 */
				$tableName = $object->getDbTableName();
				$dbTableColumns = $Db->readTableColumns($tableName);
				$valuesArray = self::arrayImplement($object, $nonPOST, $process);
				$currentsArray = array('tableName' => $tableName, 'valuesArray' => $valuesArray);
				/**
				 * tableCode, subCode, columns ve values degiskenlerini hazirla
				 */
				$tableCode = $valuesArray["code"];

				$columnsBackup = $dbTableColumns;
				$columns = implode(",", $columnsBackup);
				unset($columnsBackup[0]);

				$valuesBackup = $valuesArray;
				$values = "'" . implode("','", $valuesArray) . "'";
				unset($valuesBackup["code"]);
				/**
				 * eklenecek tum bilgileri criteria dizisine aktar
				 */
				$addCriteria = ( array('table' => $tableName, 'columns' => $columns, "values" => $values, "tableCode" => $tableCode, "columnsBackup" => $columnsBackup, "valuesBackup" => $valuesBackup, "historyValueType" => 'add') );
				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->add() - addCriteria : ";
						d($addCriteria);
				}
				/**
				 * gelen veriler arasında recordType bilgisi var mı kontrol et
				 * var ise dondur ve phase bilgisi olarak degerlendir
				 */
				$phaseInfo = self::getPhaseInfo($nonPOST);
				/**
				 * current verisini hazirla
				 */
				$currents = self::getCurrents($currentsArray, $phaseInfo);
				/**
				 * veritabanına gönderilecek phase değerini belirle
				 */
				$phase = $phaseInfo ? '1' : '0';
				/**
				 * degisiklikleri tespit etmek ve uygulamak uzere CHANGES'e gonder
				 */
				$object->Changes->add(array('table' => $tableName, 'tableCode' => $tableCode, 'dbProcess' => $process, 'currents' => $currents, 'phase' => $phase, 'tableFields' => $columnsBackup, 'values' => $valuesBackup));
				/**
				 * veritabanina ekle, debugger aciksa kayit yapmayacak
				 */
				if (!debugger("SendToDb")) {
						/**
						 * ana islemi yap
						 */
						$Db->addToDb($addCriteria);
						/**
						 * tazeleme aktif
						 */
						if (empty($nonPOST)) {
								setRefresh('true');
						}
				}
		}
		/**
		 * bilgileri guncelle
		 *
		 * @param object
		 * @return void
		 */
		public static function update($object, $nonPOST = array())
		{
				/** islemi belirle */
				$process = 'update';

				/**
				 * singleton db
				 */
				$Db = Db::classCache();
				$DbChanges = DbChanges::classCache();

				$tableName = $object->getDbTableName();
				$updateNewValues = self::arrayImplement($object, $nonPOST, $process);
				$currentsArray = array('tableName' => $tableName, 'valuesArray' => $updateNewValues);
				/**
				 * ilgili code ile veritabanı bilgileri array'den alınıyor
				 */
				switch ($tableName) {
						case "classroom" :
								$commonArray = School::classCache()->getClassroomList();
								break;
						case "grouping" :
								$commonArray = School::classCache()->getGroupingList();
								break;
						case "holiday" :
								$commonArray = School::classCache()->getHolidayList();
								break;
						case "person" :
								$commonArray = School::classCache()->getPeopleList();
								break;
						case "program" :
								$commonArray = School::classCache()->getProgramList();
								break;
						case "saloon" :
								$commonArray = School::classCache()->getSaloonList();
								break;
						case 'daytime':
								$commonArray = School::classCache()->getDayTimeList();
								break;
						case 'rollcall':
								$commonArray[0] = School::classCache()->getRollcall($object->getInfo('code'))->getInfo();
				}

				$commonDbValues = getFromArray($commonArray, array('code' => $updateNewValues['code']));

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->update() - commonDbValues : ";
						d($commonDbValues);
				}
				/**
				 * update formundan gelen veriler ile
				 * veritabanı kaynaklı veriler karşılaştırılıyor
				 * degisiklige ugrayan kolonlar yeni bir array icerisine atiliyor
				 */
				$arrayDifference = arrayDifference($updateNewValues, $commonDbValues[0]);
				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->update() - arrayDifference : ";
						d($arrayDifference);
				}
				if ($arrayDifference != NULL) {
						/**
						 * set ve where degiskenlerine degisiklikleri isle
						 */
						$tableCode = $updateNewValues['code'];

						$columnsBackup = array();
						$valuesBackup = array();

						foreach ($arrayDifference as $key => $value) {
								$i++;
								if ($i != count($arrayDifference)) {
										$comma = ',';
								} else {
										$comma = '';
								}
								$set .= $key . '=\'' . $value . '\'' . $comma;
								$columnsBackup[] = $key;
								$valuesBackup[] = $value;
						}
						$where = 'code=\'' . $updateNewValues['code'] . '\'';
						/**
						 * eklenecek tum bilgileri criteria dizisine aktar
						 */
						$updateCriteria = ( array('table' => $tableName, 'set' => $set, "where" => $where, "tableCode" => $tableCode, "columnsBackup" => $columnsBackup, "valuesBackup" => $valuesBackup, "historyValueType" => "update"));
						/**
						 * veritabanina ekle
						 */
						if (debugger("SendToDb")) {
								echo "DEBUG : " . getCallingClass() . "->SendToDb->update() - updateCriteria : ";
								d($updateCriteria);
						}
						/**
						 * current verisini hazirla
						 * sadece update kismi icin gecerli olan arrayDifference'i arrayin icine ekle
						 */
						$currents = self::getCurrents(array_merge($currentsArray, array('arrayDifference' => $arrayDifference)));
						/**
						 * degisiklikleri tespit etmek uzere ve uygulamak üzere DBCHANGES'e gonder
						 */
						$object->Changes->add(array('table' => $tableName, 'tableCode' => $tableCode, 'dbProcess' => $process, 'currents' => $currents, 'tableFields' => $columnsBackup, 'values' => $valuesBackup));
						/**
						 * veritabanini guncelle, debugger aciksa guncelleme yapmayacak
						 */
						if (!debugger("SendToDb")) {
								/**
								 * ana islemi yap
								 */
								$Db->updateToDb($updateCriteria);
								/**
								 * tazeleme $_POST verisi geldi ise aktif
								 */
								if (empty($nonPOST)) {
										setRefresh('true');
								}
						}
				}
		}
		/**
		 * bilgiyi sil
		 *
		 * @param object
		 * @return void
		 */
		public static function delete($object, $nonPOST = array())
		{
				/** islemi belirle */
				$process = 'delete';
				/**
				 * singleton class'lar
				 */
				$Db = Db::classCache();
				$DbChanges = DbChanges::classCache();
				$tableName = $object->getDbTableName();
				/**
				 * ilgili code ile veritabanı bilgileri array'den alınıyor
				 */
				switch ($tableName) {
						case "classroom" :
								$commonArray = School::classCache()->getClassroomList();
								break;
						case "grouping" :
								$commonArray = School::classCache()->getGroupingList();
								break;
						case "holiday" :
								$commonArray = School::classCache()->getHolidayList();
								break;
						case "person" :
								$commonArray = School::classCache()->getPeopleList();
								break;
						case "program" :
								$commonArray = School::classCache()->getProgramList();
								break;
						case "saloon" :
								$commonArray = School::classCache()->getSaloonList();
								break;
						case 'daytime':
								$commonArray = School::classCache()->getDayTimeList();
								break;
						case 'rollcall':
								$commonArray[0] = School::classCache()->getRollcall($object->getInfo('code'))->getInfo();
								break;
						case 'incexp':
								$commonArray = School::classCache()->getIncomeExpenseList();
								break;
				}

				$commonDbValues = getFromArray($commonArray, array('code' => $object->getInfo('code')));
				$currentsArray = array('tableName' => $tableName, 'valuesArray' => $commonDbValues);

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->delete() - commonDbValues : ";
						d($commonDbValues);
				}
				/**
				 * tableCode, columns ve values degiskenlerini hazirla
				 */
				$tableCode = $object->getInfo("code");
				$where = 'code=\'' . $tableCode . '\'';

				$dbTableColumns = $Db->readTableColumns($tableName);

				$columnsBackup = $dbTableColumns;
				unset($columnsBackup[0]);

				$valuesBackup = $commonDbValues[0];
				unset($valuesBackup['code']);
				/**
				 * eklenecek tum bilgileri criteria dizisine aktar
				 */
				$deleteCriteria = ( array('table' => $tableName, "where" => $where, "tableCode" => $tableCode, "columnsBackup" => $columnsBackup, "valuesBackup" => $valuesBackup, "historyValueType" => 'delete'));
				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->delete() - deleteCriteria : ";
						d($deleteCriteria);
				}
				/**
				 * current verisini hazirla
				 */
				$currents = self::getCurrents($currentsArray);
				/**
				 * UYARI : Silme islemi bir kaydi hic yokmus gibi ortadan kaldiriyor
				 *         dolayisiyla CHANGES kaydi tutmanin da bir anlami olmadigina karar verdim. (02.2013)
				 *         Bu islem DISABLE edildi.
				 */
				//$object->Changes->add(array('table'=>$tableName, 'tableCode'=>$tableCode, 'dbProcess'=>$process, 'currents'=>$currents, 'tableFields'=>$columnsBackup, 'values'=>$valuesBackup));

				/**
				 * veritabanindan sil, debugger aciksa silme islemi yapmayacak
				 */
				if (!debugger("SendToDb")) {
						/**
						 * ana islemi yap
						 */
						$Db->deleteFromDb($deleteCriteria);

						/**
						 * tazeleme $_POST verisi geldi ise aktif
						 */
						if (empty($nonPOST)) {
								setRefresh('true');
						}
				}
		}
		/**
		 * database kolonlarina bakip
		 * implement fonksiyonu ile dizi hazirlayan metot
		 * 
		 * @return array
		 */
		public static function arrayImplement($object, $nonPOST = array(), $process)
		{
				/**
				 * singleton db
				 */
				$Db = Db::classCache();

				if (empty($nonPOST))
						$infoArray = $_POST;
				else
						$infoArray = $nonPOST;

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->arrayImplement() - infoArray : ";
						d($infoArray);
				}
				/**
				 * form verilerini formImplement metodu destegi ile
				 * isleyerek database ile esdeger yapida liste olustur
				 */
				$dbTableColumns = $Db->readTableColumns($object->getDbTableName());

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->arrayImplement() - dbTableColumns : ";
						d($dbTableColumns);
				}

				foreach ($dbTableColumns as $dbColumn) {

						if (array_key_exists($dbColumn, $infoArray)) {
								$valuesArray[$dbColumn] = $infoArray[$dbColumn];
						} else {
								$valuesArray[$dbColumn] = $object->formImplement($dbColumn, $process);
						}
				}

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->arrayImplement() - valuesArray : ";
						d($valuesArray);
				}

				return $valuesArray;
		}
		/**
		 * gonderilen form bilgisinde recordType olup olmadigina bakar
		 * var ise Phase bilgisi olarak döndürür
		 */
		public static function getPhaseInfo($array)
		{
				/**
				 * gelen veri post mu? değil mi?
				 */
				$sentArray = empty($array) ? $_POST : $array;
				foreach ($sentArray as $key => $value) {
						if (stristr($key, 'recordType')) {
								$classroomCode = getArrayKeyValue(explode('_', $key), 1);
								$phaseInfo[$classroomCode] = $value;
						}
				}
				return $phaseInfo;
		}
		/**
		 * tablo adina gore CHANGES tablosuna
		 * yedeklenecek bilgileri hazirlayan metot
		 */
		public static function getCurrents($array, $phaseInfo = NULL)
		{
				if (!$phaseInfo) {
						if ($array['tableName'] == 'person') {
								$currents = $array['valuesArray']['classroom'];
								/**
								 * eger UPDATE islemi yapiliyor ise
								 */
								if (isset($array['arrayDifference'])) {
										$isPaymentChanged = isset($array['arrayDifference']['payment']);
										$isPaymentPeriodChanged = isset($array['arrayDifference']['paymentPeriod']);
										$isStatusChangedToActive = $array['arrayDifference']['status'] == 'active' ? true : false;
										/**
										 * payment degisikligi gelmis ise CURRENTS'a paymentPeriod eklenecek,
										 * eger paymentPeriod degisikligi gelmis ise CURRENTS'a payment eklenecek.
										 * Hicbiri degismemis veya ikisi de degismis ise hicbirsey eklenmeyecek.
										 */
										if ($isPaymentChanged) {
												$currents .= '<+>' . $array['valuesArray']['paymentPeriod'];
										} else if ($isPaymentPeriodChanged) {
												$currents .= '<+>' . $array['valuesArray']['payment'];
										} else if ($isStatusChangedToActive) {
												$currents .= '<+>' . $array['valuesArray']['paymentPeriod'];
												$currents .= '<+>' . $array['valuesArray']['payment'];
										}
								}
						}
				} else {
						if ($array['tableName'] == 'person') {
								foreach ($phaseInfo as $key => $value) {
										$classroomKeys[] = $key;
										$values[] = str_replace(',', '|', $value);
								}
								$classrooms = implode(',', $classroomKeys);
								$phaseValues = implode(',', $values);
								$currents = $classrooms . '<+>' . $phaseValues;
						}
				}
				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->getCurrents() - currents : ";
						d($currents);
				}
				return $currents;
		}
}

