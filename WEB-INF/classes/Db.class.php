<?php

/**
 * Genel Veritabani Kutuphanesi
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class Db extends DbBase
{

		/**
		 *
		 * Bu class'in yedegi
		 *
		 * @access private
		 * @var object
		 */
		private static $_instance;

		/**
		 *
		 * classın construct methodu yoktur
		 * dolayısıyla new şeklinde çağrılamaz
		 * bunun için de fonksiyon private şeklinde tanımlandi
		 *
		 * @return void
		 */
		private function __construct()
		{
				
		}
		/**
		 *
		 * Singleton fonksiyonu
		 *
		 * @access public
		 * @return object
		 */
		public static function & classCache()
		{
				if (!self::$_instance) {
						self::$_instance = new Db();
				}
				return self::$_instance;
		}
		/**
		 *
		 * tabloya ekle
		 *
		 * @param $intend array
		 * @return void
		 */
		public function addToDb(Array $intend)
		{
				/**
				 * acik okulun database'ine baglaniliyor
				 */
				$this->connect(Session::classCache()->get('dbName'));
				/**
				 * kayit yapiliyor
				 */
				$this->insertSql($intend);
				/**
				 * history kaydi yap
				 * veritabanina ekle
				 */
				$this->insertSql(History::classCache()->add($intend));
				/**
				 * debug
				 */
				if (debugger("Db"))
						echo 'DEBUG : ' . getCallingClass() . '->' . getCallingClass() . '->Db->addToDb (' . $intend['table'] . ')<br>';
		}
		/**
		 *
		 * tabloyu guncelle
		 *
		 */
		public function updateToDb(Array $intend)
		{
				/**
				 * acik okulun database'ine baglaniliyor
				 */
				$Session = Session::classCache();
				$this->connect($Session->get('dbName'));
				/**
				 * kayit yapiliyor
				 */
				$this->updateSql($intend);
				/**
				 * history kaydi yap
				 * veritabanina ekle
				 */
				$this->insertSql(History::classCache()->add($intend));
				/**
				 * debug
				 */
				if (debugger("Db"))
						echo 'DEBUG : ' . getCallingClass() . '->Db->addToDb (' . $intend['table'] . ')<br>';
		}
		/**
		 *
		 * tablodan sil
		 *
		 * @
		 */
		public function deleteFromDb(Array $intend)
		{
				/**
				 * acik okulun database'ine baglaniliyor
				 */
				$Session = Session::classCache();
				$this->connect($Session->get('dbName'));
				/**
				 * silme yapiliyor
				 */
				$this->deleteSql($intend);
				/**
				 * history kaydi yap
				 * veritabanina ekle
				 */
				$this->insertSql(History::classCache()->add($intend));
				/**
				 * debug
				 */
				if (debugger("Db"))
						echo 'DEBUG : ' . getCallingClass() . '->Db->deleteFromDb (' . $intend['table'] . ')<br>';
		}
		/**
		 *
		 * acik okulun database'inden tablo istenilen formatta okunuyor
		 *
		 * @param $intent array
		 * @param $arrayFormat string
		 * @return array
		 */
		public function readTableFromDb(Array $intend, $arrayFormat = 'fieldBase', $dbName = "session")
		{
				/**
				 * debug
				 */
				if (debugger("Db"))
						echo 'DEBUG : ' . getCallingClass() . '->Db->readTableFromDb (' . $intend['table'] . " - " . $arrayFormat . ")<br>";
				/**
				 * acik okulun database'ine baglaniliyor
				 */
				$Session = Session::classCache();

				if ($dbName == "session")
						$this->connect($Session->get('dbName'));
				else if ($dbName == "classautomate")
						$this->connect("classautomate");

				$this->selectSql($intend);

				return $this->getRows($arrayFormat);
		}
		/**
		 *
		 * istenen tablonun sadece kolonlarinin isimlerini okuyan metot
		 *
		 * @return array
		 */
		public function readTableColumns($table, $arrayFormat = 'fieldBase', $dbName = "session")
		{
				$sqlString = 'SHOW COLUMNS FROM ' . $table;
				if (debugger("Db"))
						echo 'DEBUG : ' . getCallingClass() . '->DB->readTableColumns(' . $sqlString . ')<br>';
				/**
				 * acik okulun database'ine baglaniliyor
				 */
				if ($dbName == "session")
						$this->connect(Session::classCache()->get('dbName'));
				else if ($dbName == "classautomate")
						$this->connect("classautomate");

				$this->setSql($sqlString);

				$columnsInfo = $this->getRows($arrayFormat);

				foreach ($columnsInfo as $k => $v) {
						foreach ($v as $vk => $vv) {
								if ($vk == 'Field')
										$columns[] = $vv;
						}
				}

				return $columns;
		}
		/**
		 *
		 * istenen tablonun, istenen kolonunun son degerini okunuyor
		 *
		 * @return array
		 */
		public function readSelectedLastRow($params, $arrayFormat = 'fieldBase')
		{
				/**
				 * acik okulun database'ine baglaniliyor
				 */
				$Session = Session::classCache();
				if (!isset($params['columnName']))
						$params['columnName'] = 'code';

				if (!isset($params['dbName']))
						$this->connect($Session->get('dbName'));
				else
						$this->connect("classautomate");

				if (!isset($params['condition'])) {
						$this->selectSql(array('table' => $params['table'], 'orderby' => $params['columnName'] . ' DESC', 'limit' => '0,1'));
				} else {
						$this->selectSql(array('table' => $params['table'], 'orderby' => $params['columnName'] . ' DESC', 'where' => $params['condition'], 'limit' => '0,1'));
				}

				return $this->getRows();
		}
		/**
		 * okulun veritabanini HOLIDAY ve SETTING disinda sifirlayan metot
		 */
		public function emptySchool()
		{
				$tables = array('changes', 'classroom', 'daytime', 'history', 'incexp',
						'person', 'program', 'rollcall', 'saloon');

				foreach ($tables as $tableName) {
						$this->truncateTable(array('table' => $tableName));
				}
		}
}

?>
