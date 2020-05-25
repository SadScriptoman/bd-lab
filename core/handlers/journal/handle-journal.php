<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if ($logged && ($_SERVER['REQUEST_METHOD'] == "POST") && ($_USER['GROUP'] != GUEST_GROUP_ID)){
 
        require_once($_CONFIG['DATABASE']['CONNECT']);
        require_once($_MODULES['JOURNAL']['CLASS']);
        $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

        try{

            $id = isset($_POST['idInaguration']) ? $_POST['idInaguration'] : null;
            $idEmployee = isset($_POST['idEmployee']) ? $_POST['idEmployee'] : null;
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $ref = 'http://'.$_SERVER["SERVER_NAME"]."/employees/detail?id=".$idEmployee;

            if ($action == $ACTIONS['CRT']){
                $inaguration = Array(
                    'idEmployee' => $idEmployee,
                    'idPost' => $_POST['idPost'],
                    'inagurationDate' => $_POST['inagurationDate'],
                );
                
                $inaguration = new Journal($inaguration);
                $inaguration->Create();
                header("Location: ".$ref);     
            }elseif($id){
                if($action == $ACTIONS['UPD']){
                    $inaguration = Array(
                        'idInaguration' => $id,
                        'idEmployee' => $idEmployee,
                        'inagurationDate' => $_POST['inagurationDate'],
                    );
                    
                    $inaguration = new Journal($inaguration);
                    $inaguration->Update();
                    header("Location: ".$ref);     
                }elseif($action == $ACTIONS['DEL']){
                    $inaguration = Array(
                        'idInaguration' => $id,
                    );
                    
                    $inaguration = new Journal($inaguration);
                    $inaguration->Delete();
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