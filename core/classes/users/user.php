<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
require_once($_MODULES['CRUDENTITY']['CLASS']);

class User extends CRUDEntity{
    const table_name = 'users';
    const id_attr_name = 'idUser';
    const search_in = '`login`, `email`';
    const list_order_by = 'login';
    const what_to_select = 'idUser, login, email, idGroup';

    const ERRORS = Array(
        'login_occupied' => 'Логин уже занят!',
        'email_occupied' => 'Email уже занят!',
        'login_format' => 'Неверный формат логина!',
        'email_format' => 'Неверный формат Email!',
        'password_format' => 'Неверный формат пароля!',
        'group_format' => 'Неверный формат id группы пользователя!',
    );

    private $id = null;
    private $login = null;
    private $email = null;
    private $password_hash = null;
    private $group = null;
    private $error_code = null;

    /*function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    }*/
    
    function __construct($arr) {

        if ($arr['login']) $check_login = (bool) preg_match(LOGIN_REGEXP, $arr['login']);
        else $check_login = false;
        if ($arr['password']) $check_pass = (bool) preg_match(PASSWORD_REGEXP, $arr['password']);
        else $check_pass = false;
        if ($arr['email']) $check_email = (bool) preg_match(EMAIL_REGEXP, $arr['email']);
        else $check_email = false;
        if ($arr['idGroup']) $check_group = (bool) preg_match(GROUP_REGEXP, $arr['idGroup']);
        else $check_group = false;
        if ($arr['idUser']) $check_id = (bool) preg_match(GROUP_REGEXP, $arr['idUser']);
        else $check_id = false;

        if ($check_login) {
            $this->login = $arr["login"];
        }

        if ($check_pass) {
            $this->password_hash = password_hash($arr['password'], PASSWORD_DEFAULT);
        }

        if ($check_email) {
            $this->email = $arr["email"];
        }

        if ($check_group) {
            $this->group = (int) $arr["idGroup"];
        }

        if ($check_id) {
            $this->id = (int) $arr["idUser"];
        }

    }

    public function Create($arr = null, $nullable = null){
        $db = parent::$PDO;
        $arr = [
            'login' => (string) $this->login,
            'password' => (string) $this->password_hash,
            'email' => (string) $this->email,
            'idGroup' => (int) $this->group,
        ];
        if(self::CheckLogin($this->login, 0)){
            if(self::CheckEmail($this->email, 0)){
                return parent::Create($arr, $nullable);
            }else{
                $this->error_code = 'email_occupied';
            }
        }else{
            $this->error_code = 'login_occupied';
        } 
    }

    public function Update($id = null, $arr = null, $nullable = null){
        $arr = [
            'login' => (string) $this->login,
            'password' => (string) $this->password_hash,
            'email' => (string) $this->email,
            'idGroup' => (int) $this->group,
        ];
        if(self::CheckLogin($this->login, 1)){
            if(self::CheckEmail($this->email, 1)){
                parent::Update($this->id, $arr, $nullable);
            }else{
                $this->error_code = 'email_occupied';
            } 
        }else{
            $this->error_code = 'login_occupied';
        } 
    }

    public function Delete($id = null){
        $id = $this->id;
        parent::Delete($id);
    }

    public static function GetList($search = null, $where_query = null, $search_in = self::search_in, $table_name = self::table_name, $order_by = self::list_order_by, $what_to_select = self::what_to_select){
        return parent::GetList($search, $where_query, $search_in , $table_name, $order_by, $what_to_select);
    }

    public function UpdatePassword(){
        $db = parent::$PDO;
        if($db){
            $table = self::table_name;
            $id_attr_name = self::id_attr_name;
            $id = $this->id;
            $password = $this->password_hash;
            $str = $db->prepare("UPDATE $table SET password = '$password' WHERE $id_attr_name = $id");
            try{
                $str->execute();
            }catch(Exception $e ){
                echo 'User->UpdatePassword(): '.parent::ERRORS['sql_query_send'], "<br><br>Текст ошибки:<br>" ,$e->getMessage(), '<br><br>';
                throw new Exception();
            }
        }else{
            throw new Exception('User->UpdatePassword(): '.parent::ERRORS['db_connection']);
        }
    }

    public static function CheckLogin($login, $count = 0){
        $db = parent::$PDO;
        if($db){
            $table = self::table_name;
            $id_attr_name = self::id_attr_name;
            $str = $db->prepare("SELECT $id_attr_name FROM $table where login = '$login'");
            try{
                $str->execute();
                $result = $str->fetchAll();
            }catch(Exception $e ){
                echo 'User::CheckLogin(): '.parent::ERRORS['sql_query_recieve'].'<br><br>Текст запроса: <br>',"SELECT idUser FROM users where login = '$login' OR email = '$email'", "<br><br>Текст ошибки:<br>" ,$e->getMessage(), '<br><br>';
                throw new Exception();
            }
            if (count($result)>$count){
                return false;
            }else{
                return true;
            }
        }else{
            throw new Exception('User::CheckLogin(): '.parent::ERRORS['db_connection']);
        }
    }

    public static function CheckEmail($email, $count = 0){
        $db = parent::$PDO;
        if($db){
            $table = self::table_name;
            $id_attr_name = self::id_attr_name;
            $str = $db->prepare("SELECT $id_attr_name FROM $table where email = '$email'");
            try{
                $str->execute();
                $result = $str->fetchAll();
            }catch(Exception $e ){
                echo 'User::CheckEmail(): '.parent::ERRORS['sql_query_recieve'].'<br><br>Текст запроса: <br>',"SELECT idUser FROM users where login = '$login' OR email = '$email'", "<br><br>Текст ошибки:<br>" ,$e->getMessage(), '<br><br>';
                throw new Exception();
            }
            if (count($result)>$count){
                return false;
            }else{
                return true;
            }
        }else{
            throw new Exception('User::CheckLogin(): '.parent::ERRORS['db_connection']);
        }
    }


    public function GetID(){
        return $this->id;
    }

    public function GetLogin(){
        return $this->login;
    }

    public function GetPasswordHash(){
        return $this->password_hash;
    }

    public function GetEmail(){
        return $this->email;
    }

    public function GetGroupID(){
        return $this->group;
    }

    public function HasErrors(){
        return $this->error_code ? true : false;
    }

    public function GetErrorCode(){
        return $this->error_code;
    }

    public function GetErrorString(){
        return self::ERRORS[$this->error_code];
    }

    public function SetLogin($new){
        if (preg_match(LOGIN_REGEXP, $new)){
            $this->login = $new;
        }else{
            $this->error_code = 'login_format';
            throw new Exception('User->SetLogin(): '.self::ERRORS['login_format']);
        }
    }

    public function SetPassword($new){
        if (preg_match(PASSWORD_REGEXP, $new)){
            $this->password_hash = password_hash($new, PASSWORD_DEFAULT);
        }else{
            $this->error_code = 'password_format';
            throw new Exception('User->SetPassword(): '.self::ERRORS['password_format']);        
        }
    }

    public function SetEmail($new){
        if (preg_match(EMAIL_REGEXP, $new)){
            $this->email = $new;
        }else{
            $this->error_code = 'email_format';
            throw new Exception('User->SetEmail(): '.self::ERRORS['email_format']); 
        }
    }

    public function SetGroupID($new){
        if (preg_match(GROUP_REGEXP, $new)){
            $this->group = $new;
        }else{
            $this->error_code = 'group_format';
            throw new Exception('User->SetGroupID(): '.self::ERRORS['group_format']); 
        }
    }

 }

 //END OF FILE user.php