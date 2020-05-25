<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if ($logged && ($_SERVER['REQUEST_METHOD'] == "POST") && $_USER['IS_ADMIN']){
 
        require_once($_CONFIG['DATABASE']['CONNECT']);
        require_once($_MODULES['USERS']['CLASS']);
        $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

        try{

            $id = isset($_POST['idUser']) ? $_POST['idUser'] : null;
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $search = isset($_POST['search']) ? "&search=".$_POST['search'] : '';
            $ref = 'http://'.$_SERVER["SERVER_NAME"]."/users".str_replace('&', '?', $search);

            if ($action == $ACTIONS['CRT']){
                if (isset($_POST['password']) && isset($_POST['rep-password']) && ($_POST['rep-password'] == $_POST['password'])){
                    $user = Array(
                        'login' => $_POST['login'],
                        'password' => $_POST['password'],
                        'email' => $_POST['email'],
                        'idGroup' => $_POST['idGroup'],
                    );
                    
                    $user = new User($user);
                    $user->Create();
                    if (!$user->HasErrors())
                        header("Location: ".$ref);
                    else
                        echo $user->GetErrorString();
                }else{
                    throw new Exception('Не было получено повторение пароля или пароли не одинаковы!');
                }       
            }elseif($id){
                if($action == $ACTIONS['UPD']){
                    $user = Array(
                        'idUser' => $id,
                        'login' => $_POST['login'],
                        'password' => isset($_POST['password']) ? $_POST['password'] : NULL,
                        'email' => $_POST['email'],
                        'idGroup' => $_POST['idGroup'],
                    );
                    $user = new User($user);
                    $user->Update();
                    if (!$user->HasErrors())
                        header("Location: ".$ref);
                    else
                        echo $user->GetErrorString();
                }elseif($action == $ACTIONS['DEL']){
                    $user = Array(
                        'idUser' => $id,
                    );
                    $user = new User($user);
                    $user->Delete();
                    if (!$user->HasErrors())
                        header("Location: ".$ref);
                    else
                        echo $user->GetErrorString();
                }elseif($action == $ACTIONS['UPD_PASS']){
                    $user = Array(
                        'idUser' => $id,
                        'password' => $_POST['password'],
                    );
                    $user = new User($user);
                    $user->UpdatePassword();
                    if (!$user->HasErrors())
                        header("Location: ".$ref);
                    else
                        echo $user->GetErrorString();
                }

            }else{
                throw new Exception('Неверно указан action!');
            }

        }catch(Exception $e){
            echo $e->getMessage(), "\n";
        }
    }
    else{
        header('HTTP/1.0 404 Not Found');
        header('Status: 404 Not Found');
    }
?>