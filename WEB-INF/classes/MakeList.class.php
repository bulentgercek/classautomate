<?php
/**
 * MakeList : Public
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class MakeList
{
	/**
	 * kelime kisaltma harf sayisi
	 * ve kisaltma harfi
	 *
	 * @var numeric
	 * @var string
	 */
	private $_shortCharCount;
	private $_shortCharStopper;
	/**
	 * liste tipi
	 *
	 * @var array
	 */
	private $_typeArray;
	// ex. : page, select, ...
	/**
	 * database'e alternatif array
	 *
	 * @var array
	 */
	private $_array = NULL;
	/**
	 * tablo formati
	 *
	 * @var array
	 */
	private $_format;
	// ex. : day,time,name_L,surname_S
	/**
	 * formatlanmis liste sonucu
	 *
	 * @var array
	 */
	private $_result;
	/**
	 * MakeList construct
	 *
	 * @return void
	 */
	public function __construct($format, $type = '', $array = '')// $array ile veritabani disi veri de alinabilir
	{
		/**
		 * alternatif array
		 */
		if ($array != '')
			$this->_array = $array;
		/**
		 * istenen listenin turu ve liste bilgileri
		 */
		if (gettype($type) == 'string') {
			$this->_typeArray['type'] = $type;
			// ex. : page, select, ...
		} else if (gettype($type) == 'array') {
			$this->_typeArray = $type;
			// ex. : page, select, ...
		}
		$this->_shortCharCount = $this->_typeArray[shortCharCount];
		$this->_shortCharStopper = $this->_typeArray[shortCharStopper];
		/**
		 * formatin icinde '->' isareti veya ',' virgul var mi?
		 *
		 * yok ise database'den oku
		 */
		if (debugger("MakeList"))
			echo '<br><b>==> MakeList <==</b><br>';
		if (debugger("MakeList"))
			echo 'Format : ' . $format . '<br>';
		if (debugger("MakeList"))
			echo 'Type : ' . $this->_typeArray['type'] . '<br>';
		if (strpos($format, '->') == false && strpos($format, ',') == false) {
			$formatPosition = $this->getDbFormat($format);
		} else {
			$formatPosition = $format;
		}
		if (debugger("MakeList"))
			echo 'Format Position : ' . $formatPosition . '<br>';
		/**
		 * gelen formati '->' kismindan parcalara ayir
		 */
		$this->_format = $this->splitFormat($formatPosition);
		/**
		 * listeyi yarat
		 */
		$this->set();
	}

	/**
	 * liste yarat
	 *
	 * @return void
	 */
	public function set()
	{
		// array dendigi halde array'in icinde veri var mi?
		if ($this->_format['values'] != NULL) {
			// format nesnelerini ayir
			$expFormatValues = explode(',', $this->_format['values']);
		} else {
			$expFormatValues = NULL;
		}
		/**
		 * degerleri veritabanindan oku
		 */
		for ($loopCount = 0; $loopCount < count($expFormatValues); $loopCount++) {

			$this->setItemValues($this->_format['table'], $expFormatValues[$loopCount], $loopCount);

		}
		/**
		 * istenilen tipe gore arrayi donustur
		 */
		if ($this->_result != NULL) {
			if ($this->_typeArray['type'] == 'select') {
				$this->_result = $this->selectConvert($this->_result);
			}
		}
	}

	/**
	 * array'i SELECT formatina cevir
	 *
	 * @return array
	 */
	public function selectConvert($array)
	{
		/**
		 * verisayisinin kontrolu icin gereken sayac baslatiliyor
		 */
		$counter = 1;
		/**
		 * gelen array'de ki ilk secenek html option id olarak kullanilacak
		 */
		$idKey = '';
		/**
		 * separatorleri array'den parcalayarak yeni array'e yerlestir
		 */
		$expSeparators = explode('\\', $this->_typeArray[separators]);

		/**
		 * listenin index'i ve alt arrayleri (0,1 => array)
		 */
		foreach ($array as $key => $value) {
			/**
			 * listenin alt arraylerinin degerleri (code,name =>)
			 */
			foreach ($value as $subKey => $subValue) {

				if (debugger("MakeList"))
					echo 'counter : ' . $counter . ' value count : ' . count($value);
				/**
				 * yeni index sirasi geldi mi? (0 => 1,2,3 ise 1 => 1,2,3)
				 */
				if ($counter == count($value)) {
					/**
					 * sayaci sifirla
					 */
					$counter = 0;
					/**
					 * sonuncu separator tanimlanmis mi? (Ornegin sonunda actigimiz bir parantezi kapatmak isteyebiliriz)
					 */
					if ($expSeparators[count($value) - 2] != '') {
						/**
						 * son seperator konulacak
						 */
						$separator = $expSeparators[count($value) - 2];

					} else {
						/**
						 * tanimlanmamis! o zaman separator konulmayacak.
						 */
						$separator = '';
					}
					/**
					 * yeni index sirasi gelmemis sayac saymaya devam ediyor
					 */
				} else {

					if (debugger("MakeList"))
						echo ' separator : ' . $expSeparators[$counter - 2];
					/**
					 * yeni separatoru al
					 */
					$separator = $expSeparators[$counter - 2];
				}
				/**
				 * yeni index mi basliyor?
				 */
				if ($counter == 1) {
					/**
					 * o zaman yeni index'e kadar kullanilacak olan KEY'i belirle
					 * bu KEY, SELECT listesinde HTML ID olarak kullanilacak
					 */
					$idKey = $subValue;
					/**
					 * deger KEY olarak kullanildigi icin stringe ekleme
					 */
					$currentSubValue = "";

				} else {
					/**
					 * yeni index baslamiyor, stringe yeni degerleri eklemeye devam ediyoruz
					 */
					$currentSubValue = $subValue;

				}

				if (debugger("MakeList"))
					echo ' idKey = ' . $idKey . ' currentSubValue : ' . $currentSubValue . '<br>';
				/**
				 * string eklemesi yapiliyor, array stringi genislemeye devam ediyor
				 */
				$cArray[$idKey] .= $currentSubValue . $separator;
				/**
				 * sayaci arttir
				 */
				$counter++;
			}
		}

		return $cArray;
	}

	/**
	 * formati '->' ile parcala
	 * 'table' ve 'values' olarak bolerek array'e gonder
	 *
	 * @return array
	 */
	public function splitFormat($formatString)
	{
		if ($this->_array == NULL) {

			if (debugger("MakeList"))
				echo 'Library : Database or String' . $type . '<br>';
			if (debugger("MakeList"))
				echo 'Format String : ' . $formatString . '<br>';
			/**
			 * tablo ve degisken isimleri format icinden ayristiriliyor
			 */
			$expFormatString = explode('->', $formatString);
			/**
			 * ayristirilan isimler array icerisine yerlestiriliyor
			 */
			$listFormatArray['table'] = $expFormatString[0];
			$listFormatArray['values'] = $expFormatString[1];

		} else {

			if (debugger("MakeList"))
				echo 'Library : Array' . $type . '<br>';
			if (debugger("MakeList"))
				var_dump($this->_array);
			// eger alternatif array tanimlanmis ise TABLE bilgisini bos gonder
			$listFormatArray['table'] = NULL;
			$listFormatArray['values'] = $formatString;

		}

		return $listFormatArray;
	}

	/**
	 * final listeyi dondur
	 *
	 * @return array
	 */
	public function get()
	{
		return $this->_result;
	}

	/**
	 * okulun veritabaninda kayitli olan listeleme formati
	 *
	 * @return string
	 */
	public function getDbFormat($dbValue)
	{
		$Setting = Setting::classCache();
		$value = $Setting->getFormat($dbValue);
		if ($value != "")
			return $value;
		else
			exit($dbValue . ' not found in database Setting table.');
	}

	/**
	 * verileri veritabanindan veya alternatif array'den oku
	 *
	 * @return void
	 */
	public function setItemValues($listTable, $listColumn, $itemNo)
	{
		/**
		 * istenilen tabloyu tamamen oku
		 */
		if ($this->_array == NULL) {
			$methodName = "get" . $listTable . "List";
			eval('$TableArray = School::classCache()->' . $methodName . '();');

			if (isset($this->_typeArray['sortColumn'])) {
				$TableArray = sortArrayWithKey::get($TableArray, $this->_typeArray['sortColumn'], 'ASC');
			}

		} else {
			$TableArray = $this->_array;
		}
		/**
		 * alt basliklar icin gelen kolon bilgisini parcala
		 */
		$expColumn = explode('_', $listColumn);
		/**
		 *
		 */
		if (count($expColumn) > 1)
			$arrayKeyForColumn = $expColumn[0] . '_' . $expColumn[1];
		else
			$arrayKeyForColumn = $expColumn[0];
		/**
		 * sifir degerli sayac yarat
		 */
		$counter = 0;
		/**
		 * verileri counter'a gore sayarak array icine yerlestir
		 */
		if ($TableArray != NULL) {
			foreach ($TableArray as $key => $value) {
				// gelen verinin '_' ile ayrilmis kolon bilgisi baska bir veritabanina karsilik geliyor mu?
				$arrayValue = $this->getConnection($expColumn[0], $value[$expColumn[0]], $expColumn[1]);
				// sonuc array'ine kisaltma islemi varsa yaparak gonder
				$this->_result[$counter][$arrayKeyForColumn] = $this->setLength($arrayValue, $expColumn[count($expColumn) - 1]);
				// sayac
				$counter++;
			}
		}
	}

	/**
	 * konnektor baglantisi var mi?
	 *
	 * @return string
	 */
	public function getConnection($column, $columnValue, $columnSubValue)
	{
		/**
		 * eger connection yok ise deger, gerisin geri donecek
		 */
		$connectionResult = $columnValue;
		/**
		 * baglantilari olan nesneler
		 * karsiliklarini oku ve baglanti sonucu olarak geri gonder
		 */
		switch ($column) {
			case 'name' :
				$connectionResult = $columnValue;
				break;

			case 'day' :
				$connectionResult = $this->getConnectDay($columnValue);
				break;

			case 'time' :
				$connectionResult = substr($columnValue, 0, strlen($columnValue) - 3);
				break;

			case 'program' :
				$Program = School::classCache()->getProgram($columnValue);
				$connectionResult = $Program->getInfo($columnSubValue);
				break;

			case 'instructor' :
				$Instructor = School::classCache()->getInstructor($columnValue);
				$connectionResult = $Instructor->getInfo($columnSubValue);
				break;

			case 'instructorAsistant' :
				$InstructorAsistant = School::classCache()->getAsistant($columnValue);
				$connectionResult = $Instructor->getInfo($columnSubValue);
				break;

			case 'saloon' :
				$Saloon = School::classCache()->getSaloon($columnValue);
				$connectionResult = $Saloon->getInfo($columnSubValue);
				break;
                
            case 'status' :
                $connectionResult = $columnValue;
		}

		return $connectionResult;
	}

	/**
	 * gun sayisini dile uygun kelimeye donustur
	 *
	 * @return string
	 */
	public function getConnectDay($dayNo)
	{
		$Setting = Setting::classCache();
		$languageJSON = $Setting->getInterfaceLang();
		return (string)$languageJSON->classautomate->dayOfWeek[$dayNo];
	}

	/**
	 * gelen kelimeyi boyutlandir
	 *
	 * @return string
	 */
	public function setLength($value, $lengthType)
	{
		/**
		 * eger kisaltma tipi S ise kisaltmayi maksimum sayiya gore gerceklestir
		 * yok ise gerisin geri gonder
		 */
		if ($lengthType == 'S')
			return substr($value, 0, $this->_shortCharCount + 1) . $this->_shortCharStopper;
		else
			return $value;
	}

}
?>