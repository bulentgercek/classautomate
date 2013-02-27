<?php
/**
 * Egitmen Personel Nesnesi
 *
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class Instructor extends Person
{
	/**
	 * kisi bilgileri dizisi
	 */
	protected $_info = NULL;
	
	/**
	 * egitmen personel kodu
	 */
	protected $_code;
	
	/**
	 * changes nesnesi
	 * 
	 * @var Object
	 */	
	protected $Changes;
	
    /**
     * egitmenin dahil oldugun siniflarin listesini dondur
     */
    public function getClassroomList($flat = false)
    {
        $classroomCodes = array();
        $classroomList = getFromArray(School::classCache()->getClassroomList(), array('instructor'=>$this->_code));

        if ($flat) {
            foreach ((array)$classroomList as $key => $value) {
                $classroomCodes[] = $value['code'];
            }
            $result = implode(',', $classroomCodes);
        } else {
            $result = $classroomList;
        }
        
        return $result;
    }
    		
	/**
	 * $_POST formda olup da database'de karsiligi olmayan
	 * degiskenlere gereken veriyi donduren uygulayici metot
	 *
	 * @param $columnName string
	 * @return string
	 */
	public function formImplement($columnName, $senderMethod = "")
	{
		$expColumnName = explode("_", $columnName);
		switch ($columnName) {
			case 'code' :
				return $this->_code;
				break;
				
			case 'job' :
				return "";
				break;
				
			case 'homePhone' :
				return '';
				break;

            case 'classroom' :
                return "";
                break;
                
			case 'paymentPeriod' :
				return "percent";
				break;

			case 'payment' :
				return "0";
				break;

			case 'status' :
				if ($senderMethod == "add") {
					return "notUsed";
				}
				if ($senderMethod == "update") {
					return $this->getInfo('status');
				}
				if ($senderMethod == "delete") {
					return "deleted";
				}
				break;
			
			case 'recordDate':
				return getDateTimeAsFormatted();
				
			case 'bossMode' :
				return '0';
				break;
				
			default:
				return $this->getInfo($columnName);
		}

	}

}
?>