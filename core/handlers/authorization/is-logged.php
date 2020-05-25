<?
    if (isset($_COOKIE['session_id'])) {
        session_id($_COOKIE['session_id']);
    }
    session_start();
    
    if (isset($_SESSION['USER'])){
        $logged = TRUE;
        $_USER = $_SESSION['USER'];
        if (isset($_SESSION['USER_GROUPS']))
            $_USER['USER_GROUPS'] = $_SESSION['USER_GROUPS'];
    }else{
        $logged = FALSE;
    }
?>