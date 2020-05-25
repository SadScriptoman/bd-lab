<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_CONFIG['DATABASE']['CONNECT']);
    require_once($_MODULES['AUTHORIZATION']['CLASS']);
    require_once($_MODULES['USERS']['CLASS']);
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $ref = (isset($_COOKIE['ref'])) ? $_COOKIE['ref'] : MAIN_PAGE;
        if (isset($_POST['login']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['rep-password'])) {

            if ($_POST['password'] == $_POST['rep-password']){

                try{

                    $user = Array(
                        'idUser' => null,
                        'login' => $_POST['login'],
                        'password' => $_POST['password'],
                        'email' => $_POST['email'],
                        'idGroup' => 4
                    );

                    $user = new User($user);
                    $user->Create();


                    if (!$user->HasErrors()){
                        $auth = new Authorization($_POST['login'], $_POST['password']);
                        $error_msg = $auth->Authorize();
                    }else{
                        $error_msg = $user->GetErrorString();
                    }

                }catch(Exception $e ){
                    echo $e->getMessage(), '<br><br>';
                }
            }

            if (isset($_SESSION['USER']) && !isset($error_msg)) {
                header("Location: ".$ref);
            }
        }
        
    }