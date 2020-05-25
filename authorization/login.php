<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);
  
    if ($logged){
        header("HTTP/1.1 409 Conflict");
        include($_SERVER['DOCUMENT_ROOT']."/409.php");
        exit;
    }else{
        require_once($_MODULES['AUTHORIZATION']['LOGIN']);
?>
<!doctype html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Вход</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="../core/src/bootstrap/bootstrap.min.css" >
        <link rel="stylesheet" href="../core/src/css/main.css">
    </head>

    <body id='login'>

        <main class="text-center d-flex justify-content-center align-items-center flex-column vh-100">

            <form class="form-signin" method="post" action="login">
                <h1 class="h3 mb-3 font-weight-normal">Войдите</h1>
                <label for="login" class="sr-only  mt-2">Логин</label>
                <input type="text" name="login" id="login" class="form-control  mt-2" placeholder="Логин" value="<?if (isset($_POST["login"])){echo $_POST["login"];}?>" required autofocus>
                <label for="password" class="sr-only  mt-2">Пароль</label>
                <input type="password" name="password" id="password" class="form-control mt-2" placeholder="Пароль" value="<?if (isset($_POST["password"])){echo $_POST["password"];}?>" required>
                <button class="btn btn-primary btn-block mt-2 mb-3" type="submit">Войти</button>
                <? if (isset($error_msg)){?>
                    <p class="mt-3 text-danger" > <?=$error_msg; ?></p>
                <? }?>
                <p>Или <a href="<?=MAIN_PAGE?>/authorization/registration">зарегестрироваться</a></p>
            </form>
        
        </main>

    </body>
    

</html>
<?}