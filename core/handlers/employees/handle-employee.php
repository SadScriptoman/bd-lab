<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if ($logged && ($_SERVER['REQUEST_METHOD'] == "POST")) {

        require_once($_CONFIG['DATABASE']['CONNECT']);
        require_once($_MODULES['EMPLOYEES']['CLASS']);
        $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

        try{
            $id = isset($_POST['idEmployee']) ? $_POST['idEmployee'] : null;
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $search = isset($_POST['search']) ? "&search=".$_POST['search'] : '';
            $ref = 'http://'.$_SERVER["SERVER_NAME"]."/employees".str_replace('&', '?', $search);

            if ($action == $ACTIONS['CRT']){
                $employee = Array(
                    'name' => $_POST['name'],
                    'tel' => "7".preg_replace('/[()\-\+\s]/', '', $_POST['tel']),
                    'acceptanceDate' => $_POST['acceptanceDate'],
                    'dismissalDate' => $_POST['dismissalDate'],
                );
                $employee = new Employee($employee, $db);
                $employee->Create();

            }elseif($id){
                if($action == $ACTIONS['UPD'] || $action == $ACTIONS['UPD_DETAIL']){
                    $employee = Array(
                        'idEmployee' => $id,
                        'name' => $_POST['name'],
                        'tel' => "7".preg_replace('/[()\-\+\s]/', '', $_POST['tel']),
                        'acceptanceDate' => $_POST['acceptanceDate'],
                        'dismissalDate' => $_POST['dismissalDate'],
                    );
                    $employee = new Employee($employee, $db);
                    $employee->Update();

                    if($action == $ACTIONS['UPD_DETAIL']){
                        $ref = 'http://'.$_SERVER["SERVER_NAME"]."/employees/detail?id=".$id;
                    }
                    
                }elseif($action == $ACTIONS['DEL']){
                    $employee = Array(
                        'idEmployee' => $id,
                    );
                    $employee = new Employee($employee, $db);
                    $employee->Delete();
                }elseif($action == $ACTIONS['DISMISS']){
                    $employee = Array(
                        'idEmployee' => $id,
                    );
                    $employee = new Employee($employee, $db);
                    $employee->Dismiss();
                }
            }

            header("Location: ".$ref);

        }catch(Exception $e){
            echo $e->getMessage(), "\n";
        }
         
    }else{
        header('HTTP/1.0 404 Not Found');
        header('Status: 404 Not Found');
    }
?>