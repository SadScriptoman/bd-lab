<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
  require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

  if (!$logged){
    header('Location: authorization/login');
  }
  else{
    setcookie("ref", $_SERVER['REQUEST_URI']);
    $page_title = "Главная";
    $nav_active = 0;
    require_once($_CONFIG['TEMPLATES']['HEADER']);
    require_once($_CONFIG['DATABASE']['CONNECT']);
    require_once($_MODULES['USER_GROUPS']['CLASS']);

    $groups = UserGroup::GetList();
?>

  <main role="main" id="main">
    <div class="container mb-5">
      <h1>Здравствуйте, <?=$_USER["LOGIN"]?>!</h1>
      <p>Вы успешно авторизовались 
        <?if (count($groups)>0):?> 
          как <?=$groups[$_USER["GROUP"]-1]['groupName']?>.
        <?endif;?> 
      </p>
    </div>
</main>

<?
require_once($_CONFIG['TEMPLATES']['FOOTER_BOOTSTRAP']);
}
?>