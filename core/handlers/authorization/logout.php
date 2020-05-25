<?
    if (isset($_COOKIE['session_id'])) {
        session_id($_COOKIE['session_id']);
        $_COOKIE['session_id'] = NULL;
    }
    session_start();
    session_destroy();
    $ref = (isset($_COOKIE['ref'])) ? $_COOKIE['ref'] : MAIN_PAGE;
    header("Location: ".$ref);
?>