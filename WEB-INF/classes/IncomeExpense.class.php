<?php
/**
 * Gelir-Gider Nesnesi
 * 
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class IncomeExpense 
{
	/**
	 * gelir-gider bilgileri dizisi
	 */
	private $_info;
	
	/**
	 * kod
	 */
	private $_code;
	
	/**
	 * ilgili db tablo adi
	 */
	private $_dbTable = "incexp";
	
	/**
	 * constract metodu
	 * 
	 * @param array
	 */
	public function __construct($code = NULL)
	{
		if ($code != NULL) {
			$this->_code = $code;

		} else {
			$this->_code = getNewTableCode( array ("table"=>$this->_dbTable) );
		}
	}
		
	/**
	 * istenilen gelir-gider bilgisini dondur
	 * 
	 * @return string
	 */
	public function getInfo($key = NULL)
	{
		$this->_updateArrays();

		if ($key == NULL) {
			return $this->_info;

		} else {
			return $this->_info[$key];
		}
	}
	
	/**
	 * istenilen gelir-gider bilgisini
	 * anahtar kelimeye gore guncelle
	 * 
	 * @param array
	 */
	public function setInfo(Array $array)
	{
		$this->_updateArrays();

		if ($this->_info == NULL) {
			switch (getTransferInfo('tt', $_POST)) {
				case 'post':
					SendToDb::add($this);
					break;
				case 'direct': 
                    SendToDb::add($this, $array);
					break;
			}
		} else {
			switch (getTransferInfo('tt', $_POST)) {
				case 'post':
					SendToDb::update($this);
					break;
				case 'direct':
                    $newArray = array_merge($this->_info, $array);
                    SendToDb::update($this, $newArray);
					break;
			}
		}	
	}
	
	/**
	 * nesne tablo adini dondurur
	 */
	public function getDbTableName()
	{
		return $this->_dbTable;
	}
	
	 /**
	  * diziler guncel mi, okundu mu?
	  *
	  * @return void
	  */
	 private function _updateArrays()
	 {
		$intend = array("code" => $this->_code);
		$incomeExpenseList = School::classCache()->getIncomeExpenseList();

		$this->_info = getFromArray($incomeExpenseList, $intend);
		$this->_info = $this->_info[0];
	 }
	 	
 	/**
	 * $_POST formda olup da database'de karsiligi olmayan
	 * degiskenlere gereken veriyi donduren uygulayici metot
	 *
	 * @param $columnName string
	 * @return string
	 */ 
	public function formImplement($columnName, $senderMethod="")
	{
		switch ($columnName) {
			case 'code':
				return $this->_code;
				break;
            
            case 'type':
                if ($senderMethod == "add") {
                    return $_POST['type'];
                }
            
            case 'dateTime':
                if ($senderMethod == "add") {
                    if (!isset($_POST['dateTime']))
                        return getClientDateTime('%Y-%m-%d %H:%M:%S');
                    else
                       return $_POST['dateTime'];
                }
            
            case 'subType':
                if ($senderMethod == "add") {
                    return $_POST['subType'];
                }
                
            case 'onBehalfOf':
                if ($senderMethod == "add") {
                    return $_POST['onBehalfOf'];
                }
                
            case 'classroom':
                if ($senderMethod == "add") {
                    return ($_POST['classroom'] == 'null' ? '0': $_POST['classroom']);
                }
                
            case 'amount':
                if ($senderMethod == "add") {
                    return $_POST['amount'];
                }

            case 'method':
                if ($senderMethod == "add") {
                    return 'cash';
                }
            
            case 'invoiceInfo':
                if ($senderMethod == "add") {
                    return $_POST['invoiceInfo'];
                }
 
		}
    }
}
?>
