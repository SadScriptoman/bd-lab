<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if ($logged && ($_SERVER['REQUEST_METHOD'] == "POST") && $_USER['IS_ADMIN']){
 
        require_once($_CONFIG['DATABASE']['CONNECT']);
        require_once($_MODULES['USER_GROUPS']['CLASS']);
        $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

        try{

            $id = isset($_POST['idGroup']) ? $_POST['idGroup'] : null;
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $search = isset($_POST['search']) ? "&search=".$_POST['search'] : '';
            $ref = 'http://'.$_SERVER["SERVER_NAME"]."/references/userGroups/".str_replace('&', '?', $search);

            if ($action == $ACTIONS['CRT']){
                $user_group = Array(
                    'groupName' => $_POST['groupName'],
                );
                
                $user_group = new UserGroup($user_group);
                $user_group->Create();
                header("Location: ".$ref);     
            }elseif($id){
                if($action == $ACTIONS['UPD']){
                    $user_group = Array(
                        'idGroup' => $id,
                        'groupName' => $_POST['groupName'],
                    );
                    
                    $user_group = new UserGroup($user_group);
                    $user_group->Update();
                    header("Location: ".$ref);    
                }elseif($action == $ACTIONS['DEL']){
                    $user_group = Array(
                        'idGroup' => $id,
                    );
                    
                    $user_group = new UserGroup($user_group);
                    $user_group->Delete();
                    header("Location: ".$ref);
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