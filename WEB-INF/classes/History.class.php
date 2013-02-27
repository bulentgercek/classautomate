<?php
/**
 * History Control
 * 
 * @project classautomate.com
 * @author Bulent Gercek <bulentgercek@gmail.com>
 */
class History
{
    /**
     * Bu class'in yedegi
     *
     * @access private
     * @var object
     */
    private static $_instance;
    /** 
     * classın construct methodu tek seferlik icindir
     * 
     * @return void
     */
    private function __construct() {}
    /**
     * Singleton fonksiyonu
     *
     * @access public
     * @return object
     */
    public static function classCache()
    {
        if (!self::$_instance) {
            self::$_instance = new History();
        }
        return self::$_instance;
    }
    /**
     * history bilgisini oku
     *
     * @access public
     * @return void
     */
    public function read()
    {
        echo 'History->read()<br>';
    }
    /**
     * yeni history kaydi yap
     *
     * @access public
     * @return void
     */
    public function add(Array $intend)
    {
    	$Db = Db::classCache();
		$Session = Session::classCache();
		
        //history valueType kolonu
        $valueType = $intend['historyValueType'];
        /**
         * historyIntend icin cloumns degiskenini hazirlamak uzere 
         * history tablosunun kolonlari aliniyor ve string haline getiriliyor
         */
        $columns   = "`" . implode("`,`", $Db->readTableColumns('history')) . "`";
        /**
         * historyIntend icin values degiskenini hazirlamak uzere
         * degiskenler toplaniyor ve values'a ekleniyor
         */
        $values    = "'" . $this->getNewCode();
        $values .= "','" . $this->_getDateTime();
        $values .= "','" . $intend['table'];
		$values .= "','" . $intend['tableCode'];
        $values .= "','" . implode("<+>", $intend['columnsBackup']);
        $values .= "','" . $valueType;
        $values .= "','" . implode("<+>", $intend['valuesBackup']);
        $values .= "','" . $Session->get('username') . "'";
        /**
         * eklenecek tum bilgileri intend dizisine aktar
         */
        $historyIntend = (array(
            'table' => 'history',
            'columns' => $columns,
            "values" => $values
        ));
        /**
         * debug et
         */
        if (debugger("History")) {
            echo 'DEBUG : ' . getCallingClass() . '->History->add($historyIntend) : ';
            var_dump($historyIntend);
        }
        /**
         * eklenecek array nesnesini döndür
         */
        return $historyIntend;
    }
    /**
     * son islemi cagir
     *
     * @return array
     */
    public function getLastProcess()
    {
    	$Db = Db::classCache();
        return $Db->readSelectedLastRow( array("table" => "history", "columnName" => "code") );
    }
    /**
     * son history kaydina ait numarayi bul ve yeni kayit uret
     *
     * @return int
     */
    private function getNewCode()
    {
    	$Db = Db::classCache();
        $lastRow = $Db->readSelectedLastRow( array("table" => "history", "columnName" => "code") );
        
        if ($lastRow != NULL) {
            return $lastRow['code'] + 1;
        } else {
            return 1;
        }
    }
    /** 
     * kullanicinin lokal tarihi ve saati aliniyor
     * 
     * @return string
     */
    private function _getDateTime()
    {
        return getClientDateTime('%Y-%m-%d %H:%M:%S');
    }
}

?>