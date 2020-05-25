<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
require_once($_MODULES['CRUDENTITY']['CLASS']);

class Employee extends CRUDEntity{
    const table_name = 'employees';
    const id_attr_name = 'idEmployee';
    const search_in = '`name`, `tel`';
    const list_order_by = 'name';
    const what_to_select = '*';

    private $id;
    private $name;
    private $tel;
    private $acceptance;
    private $dismissal;
    
    function __construct($arr) {

        if (is_array($arr)){
            $check_dismissal_date = (bool) preg_match(DATE_REGEXP, $arr['dismissalDate']);
            $check_id = (bool) preg_match(ID_REGEXP, $arr['idEmployee']);
            $check_name = (bool) preg_match(NAME_REGEXP, $arr['name']);
            $check_tel = (bool) preg_match(TEL_REGEXP, $arr['tel']);
            $check_acceptance_date = (bool) preg_match(DATE_REGEXP, $arr['acceptanceDate']);

            if ($check_name) {
                $this->name = $arr["name"];
            }else{
                $this->name = null;
            }
            if ($check_tel) {
                $this->tel = $arr["tel"];
            }else{
                $this->tel = null;
            }
            if ($check_acceptance_date) {
                $this->acceptance = date("Y-m-d", strtotime($arr["acceptanceDate"]));
            }else{
                $this->acceptance = null;
            }

            if ($check_id) {
                $this->id = (int) $arr["idEmployee"];
            }else{
                $this->id = null;
            }
            if ($check_dismissal_date) {
                $this->dismissal = date("Y-m-d", strtotime($arr["dismissalDate"]));
            }else{
                $this->dismissal = null;
            }
        }

    }

    public function Create($arr = null, $nullable = null){
        $arr = [
            'name' => (string) $this->name,
            'tel' => (string) $this->tel,
            'acceptanceDate' => $this->acceptance,
            'dismissalDate' => $this->dismissal,
        ];
        $nullable = [
            'dismissalDate' => (bool) true, 
        ];
        return parent::Create($arr, $nullable);
    }

    public function Update($id = null, $arr = null, $nullable = null){
        $id = $this->id;
        $arr = [
            'name' => (string) $this->name,
            'tel' => (string) $this->tel,
            'acceptanceDate' => $this->acceptance,
            'dismissalDate' => $this->dismissal,
        ];
        $nullable = [
            'dismissalDate' => (bool) true, 
        ];
        parent::Update($id, $arr, $nullable);
    }

    public function Delete($id = null){
        $id = $this->id;
        parent::Delete($id);
    }
    
    public static function GetList($search = null, $where_query = null, $search_in = self::search_in, $table_name = self::table_name, $order_by = self::list_order_by, $what_to_select = self::what_to_select){
        return parent::GetList($search, $where_query, $search_in , $table_name, $order_by, $what_to_select);
    }

    public static function GetByID($id, $table = self::table_name, $id_attr_name = self::id_attr_name, $what_to_select = self::what_to_select){
        return new Employee(parent::GetByID($id, $table, $id_attr_name, $what_to_select));
    }

    public static function GetListWithEmptyJournal(){
        return parent::ExecQuery('SELECT inaguration.idEmployee, inaguration.inagurationDate, employees.idEmployee, employees.name, employees.tel, employees.acceptanceDate, employees.dismissalDate FROM '.self::table_name.' LEFT JOIN inaguration  
	    ON inaguration.idEmployee = employees.idEmployee WHERE inagurationDate IS NULL AND dismissalDate IS NULL'); 
    }

    public function Dismiss(){
        $db = parent::$PDO;
        if($db){
            $table = self::table_name;
            $id_attr_name = self::id_attr_name;
            $id = $this->id;
            $dismissal = date("Y-m-d H:i:s", time());
            $str = $db->prepare("UPDATE $table SET dismissalDate = '$dismissal' WHERE $id_attr_name = $id");
            if (!$str->execute()) {
                throw new Exception('Employee->Dismiss(): Ошибка отправки данных в бд!');
            }  
        }else{
            throw new Exception('Employee->Dismiss(): Нет подключения к бд!');
        }
    }
    

    public function GetID(){
        return $this->id;
    }

    public function GetName(){
        return $this->name;
    }

    public function GetTel(){
        return $this->tel;
    }

    public function GetAcceptanceDate(){
        return $this->acceptance;
    }

    public function GetDismissalDate(){
        return $this->dismissal;
    }

    public function SetName($new){
        if (preg_match(NAME_REGEXP, $new)){
            $this->name = $new;
        }else{
            throw new Exception('Неверное ФИО!');
        }
    }

    public function SetTel($new){
        if (preg_match(TEL_REGEXP, $new)){
            $this->tel = $new;
        }else{
            throw new Exception('Неверный формат телефона!');
        }
    }

    public function SetAcceptanceDate($new){
        if (preg_match(DATE_REGEXP, $new)){
            $this->acceptance = $new;
        }else{
            throw new Exception('Неверный формат даты!');
        }
    }

    public function SetDismissalDate($new){
        if (preg_match(DATE_REGEXP, $new)){
            $this->dismissal = $new;
        }else{
            throw new Exception('Неверный формат даты!');
        }
    }

 }

 //END OF FILE employee.php