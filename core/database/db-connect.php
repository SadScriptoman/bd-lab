<?
    $db = NULL;
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    try{
        $db = new PDO('mysql:host='.$_CONFIG['DATABASE']['HOST'].';dbname='.$_CONFIG['DATABASE']['NAME'].';charset='.$_CONFIG['DATABASE']['CHARSET'], $_CONFIG['DATABASE']['USERNAME'], $_CONFIG['DATABASE']['PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
    }catch(PDOException $e){
        if($logged){
            echo '<div class="alert alert-danger text-left fade show" role="alert">
                Подключение к бд не удалось: ' . $e->getMessage(). '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
    }
?>