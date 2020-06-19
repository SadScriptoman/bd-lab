<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
require_once($_MODULES['CRUDENTITY']['CLASS']);

class Journal extends CRUDEntity{
    const table_name = 'inaguration';
    const table_posts = 'posts';
    const id_attr_name = 'idInaguration';
    const id_attr_name_posts = 'idPost';
    const list_order_by = self::table_name.'.inagurationDate DESC';
    const what_to_select = self::id_attr_name.', inaguration.idEmployee, inaguration.idPost, inaguration.inagurationDate, posts.idPost, posts.postName, posts.salary';

    private $id;
    private $arr;
    
    function __construct($arr) {
        $check_id = (bool) preg_match(ID_REGEXP, $arr[self::id_attr_name]);

        if ($check_id) {
            $this->id = (int) $arr[self::id_attr_name];
        }else{
            $this->id = null;
        }

        if (is_array($arr)) {
            $this->arr = (array) $arr;
        }else{
            $this->arr = null;
        }

    }

    public function Create($arr = null, $nullable = null){
        return parent::Create($this->arr, $nullable);
    }

    public function Update($id = null, $arr = null, $nullable = null){
        parent::Update($this->id, $this->arr, $nullable);
    }

    public function Delete($id = null){
        parent::Delete($this->id);
    }

    public static function GetJournal($id){
        return parent::ExecQuery('SELECT '.self::what_to_select.' FROM '.self::table_name.' INNER JOIN '.self::table_posts.'  
	    ON inaguration.idPost = posts.idPost WHERE inaguration.idEmployee = '.$id.' ORDER BY '.self::list_order_by);
    }

    public function GetID(){
        return $this->id;
    }

    public function GetArray(){
        return $this->arr;
    }

    public function SetArray($new){
        if (is_array($new)){
            $this->arr = $new;
        }else{
            throw new Exception('Journal->SetArray(): Новое значение должно быть массивом!');
        }
    }

 }

 //END OF FILE journal.php