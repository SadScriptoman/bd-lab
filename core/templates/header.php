<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?=$page_title?></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href=<?=src."/bootstrap/bootstrap.min.css"?> >
    <link rel="stylesheet" href=<?=src."/css/main.css"?>>
    <link rel="stylesheet" href=<?=src."/css/all.css"?>> 

  </head>
  <body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/"><h3>Отдел кадров</h3></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            <? if ($logged):?>
              <li class="nav-item <? if ($nav_active == 1) echo "active"; ?>">
                  <a class="nav-link" href="<?=MAIN_PAGE?>/employees/">Список сотрудников</a>
              </li>
              <?if ($_USER['IS_ADMIN']): ?>
                <li class="nav-item <? if ($nav_active == 2) echo "active"; ?>">
                    <a class="nav-link" href="<?=MAIN_PAGE?>/users/">Пользователи</a>
                </li>
                <li class="nav-item dropdown <? if ($nav_active == 3) echo "active"; ?>">
                  <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Справочники</a>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?=MAIN_PAGE?>/references/posts/">Должности</a>
                      <a class="dropdown-item" href="<?=MAIN_PAGE?>/references/userGroups/">Группы пользователей</a>
                  </div>
                </li>
              <?else:?>
                <li class="nav-item <? if ($nav_active == 3) echo "active"; ?>">
                    <a class="nav-link" href="<?=MAIN_PAGE?>/references/posts/">Должности</a>
                </li>
              <?endif;?>
              <?if ($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']):?>
                <li class="nav-item <? if ($nav_active == 4) echo "active"; ?>">
                  <a class="nav-link" href="<?=MAIN_PAGE?>/misc/">Дополнительно</a>
                </li>
              <? endif;?>
          <? endif;?>
        </ul>
          
        <? if (!$logged):?>
          <span class="mr-3 d-block mb-2 mt-2" style="color: white;">09:00 - 23:00, ПН-ВС</span>
          <a class="btn btn-outline-light" href="<?=MAIN_PAGE?>/authorization/login">Войти<i class="fas fa-sign-in-alt ml-2"></i></a>
        <? else:?>
          <span class="mr-3 d-block mb-2 mt-2" style="color: white;">Вы вошли как: <?=$_USER['LOGIN']?>, в <?=$_USER['LOGIN_TIME']?> </span>
          <button class="btn btn-outline-light" data-toggle="modal" data-target="#logout_modal">Выйти<i class="fas fa-sign-out-alt ml-2"></i></button>
        <? endif;?>
      </div>
    </nav>
</header>
<div class="modal fade" id="logout_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Вы точно хотите выйти?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>После этого вам снова нужно будет войти в аккаунт!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
        <a title="Выйти" href='<?=$_MODULES['AUTHORIZATION']['LOGOUT']?>' class="btn btn-primary" rel="nofollow">Выйти</a>
      </div>
    </div>
  </div>
</div>