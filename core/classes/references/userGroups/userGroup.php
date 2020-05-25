<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
require_once($_MODULES['CRUDENTITY']['CLASS']);

class UserGroup extends CRUDEntity{
    const table_name = 'userGroups';
    const id_attr_name = 'idGroup';
    const list_order_by = 'idGroup';
    const what_to_select = '*';
    const search_in ='`groupName`';

    private $id;
    private $name;
    
    function __construct($arr) {

        if (is_array($arr)){
            $check_id = (bool) preg_match(ID_REGEXP, $arr[self::id_attr_name]);
            $check_name = (bool) preg_match(NAME_REGEXP, $arr['groupName']);

            if ($check_name) {
                $this->name = $arr["groupName"];
            }else{
                $this->name = null;
            }
            if ($check_id) {
                $this->id = (int) $arr[self::id_attr_name];
            }else{
                $this->id = null;
            }

        }

    }

    public function Create($arr = null, $nullable = null){
        $arr = [
            'groupName' => (string) $this->name,
        ];
        return parent::Create($arr, $nullable);
    }

    public function Update($id = null, $arr = null, $nullable = null){
        $id = $this->id;
        $arr = [
            'groupName' => (string) $this->name,
        ];
        parent::Update($id, $arr, $nullable);
    }

    public function Delete($id = null){
        $id = $this->id;
        parent::Delete($id);
    }

    public static function GetList($search = null, $where_query = null, $search_in = self::search_in, $table_name = self::table_name, $order_by = self::list_order_by, $what_to_select = self::what_to_select){
        return parent::GetList($search, $where_query, $search_in, $table_name, $order_by, $what_to_select);
    }

    public static function GetByID($id, $table = self::table_name, $id_attr_name = self::id_attr_name, $what_to_select = '*'){
        return new UserGroup(parent::GetByID($id, $table, $id_attr_name, '*'));
    }

    public function GetID(){
        return $this->id;
    }

    public function GetName(){
        return $this->name;
    }

    public function SetName($new){
        if (preg_match(NAME_REGEXP, $new)){
            $this->name = $new;
        }else{
            throw new Exception('UserGroup->SetName(): Неверное название!');
        }
    }

 }

 //END OF FILE references/userGroups/userGroup.php