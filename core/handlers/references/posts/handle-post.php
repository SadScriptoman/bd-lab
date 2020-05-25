<?
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if ($logged && ($_SERVER['REQUEST_METHOD'] == "POST") && ($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN'])){
 
        require_once($_CONFIG['DATABASE']['CONNECT']);
        require_once($_MODULES['POSTS']['CLASS']);
        $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

        try{

            $id = isset($_POST['idPost']) ? $_POST['idPost'] : null;
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $search = isset($_POST['search']) ? "&search=".$_POST['search'] : '';
            $ref = 'http://'.$_SERVER["SERVER_NAME"]."/references/posts/".str_replace('&', '?', $search);

            if ($action == $ACTIONS['CRT']){
                $post = Array(
                    'postName' => $_POST['postName'],
                    'salary' => $_POST['salary'],
                );
                
                $post = new Post($post);
                $post->Create();
                header("Location: ".$ref);     
            }elseif($id){
                if($action == $ACTIONS['UPD']){
                    $post = Array(
                        'idPost' => $id,
                        'postName' => $_POST['postName'],
                        'salary' => $_POST['salary'],
                    );
                    
                    $post = new Post($post);
                    $post->Update();
                    header("Location: ".$ref);    
                }elseif($action == $ACTIONS['DEL' && $_USER['IS_ADMIN']]){
                    $post = Array(
                        'idPost' => $id,
                    );
                    
                    $post = new Post($post);
                    $post->Delete();
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