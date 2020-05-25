<?php
  $page_title = "Конфликт";
  $nav_active = -1;
  $fa = true;

  $ref = (isset($_COOKIE['ref'])) ? $_COOKIE['ref'] : "index";
  require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
  require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

  require_once($_CONFIG['TEMPLATES']['HEADER']);
?>

<main role="main" id="main">
    <div class="container text-center">
        <h1 class="mt-5">
            Ошибка 409!<br>
            <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
        </h1>
        <h3 class="mt-5">Похоже вы уже вошли в аккаунт</h3>
        <p class="mt-2"><a href="<?=$ref?>">Назад</a></p>
    </div>
</main>
<?
  require_once($_CONFIG['TEMPLATES']['FOOTER_BOOTSTRAP']);
?>