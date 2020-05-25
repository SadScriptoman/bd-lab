<?php

class Authorization {
    const table_name = 'users';
    const login_attr_name = 'login';
    const email_attr_name = 'email';
    const password_attr_name = 'password';
    const group_attr_name = 'idGroup';

    const ERRORS = Array(
        'db_connect' => 'Нет подключения к бд!',
        'sql_query_recieve' => 'Ошибка получения данных из бд!',
        'group_recieve' => 'Ошибка получения групп пользователей из бд!',
        'login_incorect' => 'Такое сочетание логин/пароль не найдено!',
        'admin_group_id' => 'Нет idGroup админа!'
    );

    public static $PDO;
    public static $admin_group_id;

    private $login;
    private $password;
    private $error_code;
    

    function __construct($login, $password) {

        $this->login = $login ? $login : null;
        $this->password = $password ? $password : null;

    }

    public function Authorize(){
        $db = self::$PDO;

        $error_msg = null;

        $login = $this->login;
        $password = $this->password;

        $table_name = self::table_name;
        $login_attr_name = self::login_attr_name;
        $email_attr_name = self::email_attr_name;
        $password_attr_name = self::password_attr_name;
        $group_attr_name = self::group_attr_name;

        $str = $db->prepare("SELECT * FROM $table_name WHERE $login_attr_name = '$login' OR $email_attr_name = '$login' LIMIT 1");
        if (!$str->execute()) {
            $this->error_code = 'sql_query_recieve';
            throw new Exception('Authorization->Authorize(): '.self::ERRORS[$this->error_code]);
        }  
        $result = $str->fetch();
        if ($result[$password_attr_name]) {
            if (password_verify($password, $result[$password_attr_name])){
                $_COOKIE['session_id'] = session_id();
                date_default_timezone_set('Europe/Samara');
                $_SESSION['USER'] = [
                    'LOGIN' => $result[$login_attr_name],
                    'EMAIL' => $result[$email_attr_name],
                    'LOGIN_TIME' => date('H:i:s', time()),
                    'GROUP' => (int) $result[$group_attr_name],
                    'IS_ADMIN' => false
                ];
                if ($_SESSION['USER']['GROUP'] == self::$admin_group_id){
                    $_SESSION['USER']['IS_ADMIN'] = true;
                }
            }else {
                $this->error_code = "login_incorect";
            }
        }

        return self::ERRORS[$this->error_code];
    }

    public static function ConnectDB($PDO){
        self::$PDO = $PDO;
        if(!self::$PDO){
            $this->error_code = 'db_connect';
            throw new Exception('Authorization->ConnectDB(): '.self::ERRORS[$this->error_code]);
        }
    }

    public static function SetAdminGroupID($_MODULES){
        self::$admin_group_id = $_MODULES['USERS']['ADMIN_GROUP_ID'];
        if(!self::$admin_group_id){
            $this->error_code = 'admin_group_id';
            throw new Exception('Authorization->SetAdminGroupID(): '.self::ERRORS[$this->error_code]);
        }
    }

    public function GetErrorCode(){
        return $this->error_code;
    }

    public function GetErrorString(){
        return self::ERRORS[$this->error_code];
    }

}

try{
    Authorization::ConnectDB($db);
    Authorization::SetAdminGroupID($_MODULES);
}catch(Exception $e ){
    echo $e->getMessage(), '<br><br>';
}

//END OF FILE