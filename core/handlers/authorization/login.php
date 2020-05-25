<?  
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_CONFIG['DATABASE']['CONNECT']);
    require_once($_MODULES['AUTHORIZATION']['CLASS']);
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $ref = (isset($_COOKIE['ref'])) ? $_COOKIE['ref'] : MAIN_PAGE;
        if (isset($_POST['login']) && isset($_POST['password'])) {

            $auth = new Authorization($_POST['login'], $_POST['password']);

            try{
                $error_msg = $auth->Authorize();
            }catch(Exception $e ){
                echo $e->getMessage(), '<br><br>';
            }

            if (isset($_SESSION['USER']) && !isset($error_msg)) {
                header("Location: ".$ref);
            }
        }
    }