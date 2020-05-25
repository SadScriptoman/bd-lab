<?php

abstract class CRUDEntity {
    const ERRORS = Array(
        'bd_connection' => 'Нет подключения к бд!',
        'sql_query_send' => 'Ошибка отправки данных в бд!',
        'sql_query_recieve' => 'Ошибка получения данных из бд!',
    );

    public static $PDO;

    public function Create($arr, $nullable = null){
        $db = self::$PDO;
        if($db){
            $table = $this::table_name;
            if (!is_array($nullable) || count($nullable) == 0){
                $arr = array_filter( $arr, 'strlen' );
            }else{
                $arr = array_filter($arr, 
                    static function($var, $key){
                        if ($var){
                            return true;
                        }
                        else if ($nullable[$key]){
                            $var = null;
                            return true;
                        }else{
                            return false;
                        }
                    }, ARRAY_FILTER_USE_BOTH
                );
            }
            $query_keys = implode(', ', array_keys($arr));
            $query_values = '';
            $flag = false;
            foreach($arr as $val){
                if (is_string($val) && $val != ''){
                    if ($flag){
                        $query_values = $query_values.", '".$val."'";
                    }
                    else{
                        $query_values = $query_values."'".$val."'";
                        $flag = true;
                    }
                }else if(!$val){
                    if ($flag){
                        $query_values = $query_values.", ".'NULL';
                    }
                    else{
                        $query_values = $query_values.'NULL';
                        $flag = true;
                    }
                }else{
                    if ($flag){
                        $query_values = $query_values.", ".$val;
                    }
                    else{
                        $query_values = $query_values.$val;
                        $flag = true;
                    }
                }
            }
            print_r($arr);
            $str = $db->prepare("INSERT INTO $table ($query_keys) VALUES ($query_values)");
            try{
                $str->execute();
                return $db->lastInsertId();
            }catch(Exception $e ){
                return false;
                echo 'CRUDEntity->Add(): '.self::ERRORS['sql_query_send'].'<br><br>Текст запроса: <br>',"INSERT INTO $table ($query_keys) VALUES ($query_values)", "<br><br>Текст ошибки:<br>" ,$e->getMessage(), '<br><br>';
                throw new Exception();
            }
        }else{
            return false;
            throw new Exception('CRUDEntity->Add(): '.self::ERRORS['bd_connection']);
        }
    }

    public function Update($id, $arr, $nullable = null){
        $db = self::$PDO;
        if($db){
            $table = $this::table_name;
            $id_attr_name = $this::id_attr_name;
            $flag = false;
            $query = '';
            if (!is_array($nullable) || count($nullable) == 0){
                $arr = array_filter( $arr, 'strlen' );
            }else {
                $arr = array_filter($arr, 
                    function($var, $key) use ($nullable){
                        if ($var){
                            return true;
                        }
                        else if ($nullable[$key]){
                            return true;
                        }else{
                            return false;
                        }
                    }, ARRAY_FILTER_USE_BOTH
                );
            }
            foreach($arr as $key => $val){
                if (is_string($val) && $val != ''){
                    if ($flag){
                        $query = $query.", ".$key." = '".$val."'";
                    }
                    else{
                        $query = $query.$key." = '".$val."'";
                        $flag = true;
                    }
                }else if(!$val){
                    if ($flag){
                        $query = $query.", ".$key." = ".'NULL';
                    }
                    else{
                        $query = $query.$key." = ".'NULL';
                        $flag = true;
                    }
                }else if($val){
                    if ($flag){
                        $query = $query.", ".$key." = ".$val;
                    }
                    else{
                        $query = $query.$key." = ".$val;
                        $flag = true;
                    }
                }
            }

            $str = $db->prepare("UPDATE $table SET $query WHERE $id_attr_name = $id");
            try{
                $str->execute();
            }catch(Exception $e ){
                echo 'CRUDEntity->Update(): '.self::ERRORS['sql_query_send'].'<br><br>Текст запроса: <br>',"UPDATE $table SET $query WHERE $id_attr_name = $id", "<br><br>Текст ошибки:<br>" ,$e->getMessage(), '<br><br>';
                throw new Exception();
            }
        }else{
            throw new Exception('CRUDEntity->Update(): '.self::ERRORS['bd_connection']);
        }
    }

    public function Delete($id){
        $db = self::$PDO;
        if($db){
            $table = $this::table_name;
            $id_attr_name = $this::id_attr_name;
            $str = $db->prepare("DELETE FROM $table WHERE $id_attr_name = $id");  
            if (!$str->execute()) {
                throw new Exception('CRUDEntity->Delete(): '.self::ERRORS['sql_query_send']);
            }  
        }else{
            throw new Exception('CRUDEntity->Delete(): '.self::ERRORS['bd_connection']);
        }
    }

    public function Print(){
        print_r($this);
    }

    public static function ExecQuery($query){
        $db = self::$PDO;
        if($db){
            if(is_string($query) && !(preg_match('/;\s/',$query))){
                $str = $db->prepare($query);
                if ($str->execute()){
                    $result = $str->fetchAll(PDO::FETCH_ASSOC);
                    return $result;
                }else{
                    throw new Exception('CRUDEntity::ExecQuery(): '.self::ERRORS['sql_query_recieve']);
                }
            }else if(is_string($query)) {
                $exploded = explode('; ',$query);
                $str = null;
                foreach($exploded as $key => $val){
                    $str = $db->query($val);
                    if (!$str){
                        throw new Exception('CRUDEntity::ExecQuery(): '.self::ERRORS['sql_query_recieve']);
                    }
                }
                
                $result = $str->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
        }else{
            throw new Exception('CRUDEntity::ExecQuery(): '.self::ERRORS['bd_connection']);
        }

    }

    public static function GetList($search, $where_query, $search_in, $table, $order_by, $what_to_select='*'){
        $db = self::$PDO;
        if($db){
            if ($search){
                if(!preg_match('/^([+]?[0-9\s\-\(\)]{2,25})*$/', $search)){
                  $search_query = "+*".preg_replace("/\s/", '*+*', $search).'*';
                }else{
                  $search_unscaped = preg_replace("/[()\-\+\=]/", '', $search); 
                  $search_query = '+*'.preg_replace("/\s+/", '*+*', $search_unscaped)."*"; 
                }

                if (!is_null($where_query)){
                    $str = $db->prepare("SELECT $what_to_select FROM $table WHERE $where_query
                    AND MATCH($search_in)
                    AGAINST('$search_query' IN BOOLEAN MODE)");
                }else{
                    $str = $db->prepare("SELECT $what_to_select FROM $table WHERE
                    MATCH($search_in)
                    AGAINST('$search_query' IN BOOLEAN MODE)");
                }
            }else{
                if (!is_null($where_query)){
                    $str = $db->prepare("SELECT $what_to_select FROM $table WHERE $where_query ORDER BY $order_by");
                }else{
                    $str = $db->prepare("SELECT $what_to_select FROM $table ORDER BY $order_by");

                }
            }
            if ($str->execute()){
                $result = $str->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }else{
                throw new Exception('CRUDEntity::GetList(): '.self::ERRORS['sql_query_recieve']);
            }
        }else{
            throw new Exception('CRUDEntity::GetList(): '.self::ERRORS['bd_connection']);
        }
    }

    public static function GetByID($id, $table, $id_attr_name, $what_to_select = '*'){
        $db = self::$PDO;
        if (is_numeric($id)){
            $id = (int) $id;
            $str = $db->prepare("SELECT $what_to_select FROM $table WHERE $id_attr_name = $id");
            if ($str->execute()){
                $result = $str->fetch(PDO::FETCH_ASSOC);
                return $result;
            }else{
                throw new Exception('CRUDEntity::GetByID(): '.self::ERRORS['sql_query_recieve']);
            }
        }
    }

    public static function ConnectDB($PDO){
        self::$PDO = $PDO;
        if(!self::$PDO){
            throw new Exception('CRUDEntity::ConnectBD(): '.self::ERRORS['bd_connection']);
        }
    }
}

try{
    CRUDEntity::ConnectDB($db);
}catch(Exception $e ){
    echo $e->getMessage(), '<br><br>';
}

//END OF FILE crudentity.php