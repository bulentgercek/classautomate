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
						var_dump($addCriteria);
				}

				/**
				 * current verisini hazirla
				 */
				$currents = self::getCurrents($currentsArray);

				/**
				 * degisiklikleri tespit etmek uzere DBCHANGES'e gonder
				 */
				$DbChanges->setDbChanges(array('table' => $tableName, 'tableCode' => $tableCode, 'dbProcess' => $process, 'currents' => $currents, 'tableFields' => $columnsBackup, 'values' => $valuesBackup));

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
						var_dump($commonDbValues);
				}

				/**
				 * update formundan gelen veriler ile
				 * veritabanı kaynaklı veriler karşılaştırılıyor
				 * degisiklige ugrayan kolonlar yeni bir array icerisine atiliyor
				 */
				$arrayDifference = arrayDifference($updateNewValues, $commonDbValues[0]);
				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->update() - arrayDifference : ";
						var_dump($arrayDifference);
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
								var_dump($updateCriteria);
						}

						/**
						 * current verisini hazirla
						 * sadece update kismi icin gecerli olan arrayDifference'i arrayin icine ekle
						 */
						$currents = self::getCurrents(array_merge($currentsArray, array('arrayDifference' => $arrayDifference)));

						/**
						 * degisiklikleri tespit etmek uzere DBCHANGES'e gonder
						 */
						$DbChanges->setDbChanges(array('table' => $tableName, 'tableCode' => $tableCode, 'dbProcess' => $process, 'currents' => $currents, 'tableFields' => $columnsBackup, 'values' => $valuesBackup));

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
						var_dump($commonDbValues);
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
						var_dump($deleteCriteria);
				}
				/**
				 * current verisini hazirla
				 */
				$currents = self::getCurrents($currentsArray);

				/**
				 * UYARI : Silme islemi bir kaydi hic yokmus gibi ortadan kaldiriyor
				 *         dolayisiyla CHANGE kaydi tutmanin da bir anlami olmadigina karar verdim.
				 *         Bu islem DISABLE edildi.
				 */
				//$DbChanges->setDbChanges(array('table'=>$tableName, 'tableCode'=>$tableCode, 'dbProcess'=>$process, 'currents'=>$currents, 'tableFields'=>$columnsBackup, 'values'=>$valuesBackup));

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
						var_dump($infoArray);
				}
				/**
				 * form verilerini formImplement metodu destegi ile
				 * isleyerek database ile esdeger yapida liste olustur
				 */
				$dbTableColumns = $Db->readTableColumns($object->getDbTableName());

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->arrayImplement() - dbTableColumns : ";
						var_dump($dbTableColumns);
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
						var_dump($valuesArray);
				}

				return $valuesArray;
		}
		/**
		 * tablo adina gore CHANGES tablosuna
		 * yedeklenecek bilgileri hazirlayan metot
		 */
		public static function getCurrents($array)
		{
				if ($array['tableName'] == 'person') {
						$currents = $array['valuesArray']['classroom'];

						/**
						 * eger UPDATE islemi yapiliyor ise
						 */
						if (isset($array['arrayDifference'])) {
								$isPaymentChanged = isset($array['arrayDifference']['payment']);
								$isPaymentPeriodChanged = isset($array['arrayDifference']['paymentPeriod']);
								/**
								 * payment degisikligi gelmis ise CURRENTS'a paymentPeriod eklenecek,
								 * eger paymentPeriod degisikligi gelmis ise CURRENTS'a payment eklenecek.
								 * Hicbiri degismemis veya ikisi de degismis ise hicbirsey eklenmeyecek.
								 */
								if ($isPaymentChanged) {
										$currents .= '<+>' . $array['valuesArray']['paymentPeriod'];
								} else if ($isPaymentPeriodChanged) {
										$currents .= '<+>' . $array['valuesArray']['payment'];
								}
						}
				}

				if (debugger("SendToDb")) {
						echo "DEBUG : " . getCallingClass() . "->SendToDb->getCurrents() - currents : ";
						var_dump($currents);
				}

				return $currents;
		}
}

