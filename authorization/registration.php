<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']); 
    
    if ($logged){
        header("HTTP/1.1 409 Conflict");
        include($_SERVER['DOCUMENT_ROOT']."/409.php");
        exit;
    }else{
        require_once($_MODULES['AUTHORIZATION']['REGISTRATION']);
?>
<!doctype html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Регистрация</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="../core/src/bootstrap/bootstrap.min.css" >
        <link rel="stylesheet" href="../core/src/css/main.css">
    </head>

    <body id='login'>
        <main class="text-center d-flex justify-content-center align-items-center flex-column vh-100">

            <form class="form-signin needs-validation" method="post" action="registration" id="registration" novalidate>
                <h1 class="h3 mb-3 font-weight-normal">Регистрация</h1>

                <div class="form-group">
                    <input type="text" name="login" id="login" class="form-control mt-2" pattern="<?=LOGIN_HTML_REGEXP?>" maxlength="20" minlength="4" placeholder="Логин" value="<?if (isset($_POST["login"])){echo $_POST["login"];}?>" required autofocus>
                    <div class="invalid-feedback">Логин должен быть больше 4 символов и может содержать только латинские буквы и цифры</div>
                </div>
                <div class="form-group">
                    <input type="text" name="email" id="email" class="form-control mt-2" pattern="<?=EMAIL_HTML_REGEXP?>"  placeholder="Email" value="<?if (isset($_POST["email"])){echo $_POST["email"];}?>" required>
                    <div class="invalid-feedback">Введите корректный email</div>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control mt-2" pattern="<?=PASSWORD_HTML_REGEXP?>"  placeholder="Пароль" value="<?if (isset($_POST["password"])){echo $_POST["password"];}?>" required>
                    <div class="invalid-feedback">Пароль должен быть меньше 6 символов, содержать латинские буквы, цифры и спец символы (~!@#$%^&*()+-`';:<>/\|)</div>
                </div>
                <div class="form-group">
                    <input type="password" name="rep-password" id="rep-password" class="form-control mt-2" minlength="6" maxlength="20" placeholder="Повторите пароль" value="<?if (isset($_POST["rep-password"])){echo $_POST["rep-password"];}?>" required>
                    <div class="invalid-feedback">Пароли не совпадают</div>
                </div>
                <button class="btn btn-primary btn-block mt-2 mb-3" type="submit">Зарегестрироваться</button>
                <? if (isset($error_msg)){?>
                    <p class="mt-3 text-danger" > <?=$error_msg; ?></p>
                <? }?>
                <p>Или <a href="<?=MAIN_PAGE?>/authorization/login">войти</a></p>
            </form>

        </main>
<?
    require_once($_CONFIG['TEMPLATES']['FOOTER_REG']);
    }
?>