<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
require_once($_MODULES['CRUDENTITY']['CLASS']);

class Post extends CRUDEntity{
    const table_name = 'posts';
    const id_attr_name = 'idPost';
    const list_order_by = 'postName';
    const what_to_select = '*';
    const search_in ='`postName`';

    private $id;
    private $name;
    private $salary;
    
    function __construct($arr) {

        if (is_array($arr)){
            $check_id = (bool) preg_match(ID_REGEXP, $arr[self::id_attr_name]);
            $check_name = (bool) preg_match(POST_NAME_REGEXP, $arr['postName']);
            $check_salary = (bool) is_numeric($arr['salary']);

            if ($check_name) {
                $this->name = $arr["postName"];
            }else{
                $this->name = null;
            }
            if ($check_salary) {
                $this->salary = $arr["salary"];
            }else{
                $this->salary = null;
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
            'postName' => (string) $this->name,
            'salary' => (int) $this->salary,
        ];
        return parent::Create($arr, $nullable);
    }

    public function Update($id = null, $arr = null, $nullable = null){
        $id = $this->id;
        $arr = [
            'postName' => (string) $this->name,
            'salary' => (int) $this->salary,
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
        return new Post(parent::GetByID($id, $table, $id_attr_name, '*'));
    }

    public function GetID(){
        return $this->id;
    }

    public function GetName(){
        return $this->name;
    }

    public function GetSalary(){
        return $this->salary;
    }

    public function SetName($new){
        if (preg_match(POST_NAME_REGEXP, $new)){
            $this->name = $new;
        }else{
            throw new Exception('Post->SetName(): Неверное название!');
        }
    }

    public function SetSalary($new){
        if (is_numeric($new)){
            $this->tel = $new;
        }else{
            throw new Exception('Post->SetSalary(): Зарплата должна быть числом!');
        }
    }

 }

 //END OF FILE references/posts/post.php