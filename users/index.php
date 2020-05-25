<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if (!$logged || !$_USER['IS_ADMIN']){
      header("HTTP/1.1 401 Unauthorized");
      include($_SERVER['DOCUMENT_ROOT']."/401.php");
      exit;
    }
    else{
      setcookie("ref", $_SERVER['REQUEST_URI']);
      $page_title = "Пользователи";
      $nav_active = 2;
      $fa = true;

      $search = isset($_GET["search"]) ? preg_replace("/\s$/", '',$_GET["search"]) : NULL;
      $search_get = $search?'&search='.$search:'';

      $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

      require_once($_CONFIG['TEMPLATES']['HEADER']);
?>

<main role="main" id="main">
  <div class="container mb-5">
    <?php
      require_once($_CONFIG['DATABASE']['CONNECT']);
      require_once($_MODULES['USERS']['CLASS']);
      require_once($_MODULES['USER_GROUPS']['CLASS']);

      try{
        $groups = UserGroup::GetList();
      }catch(Exception $e ){
        echo $e->getMessage(), '<br><br>';
      }
    ?>
    <?if ($db):?>
      <!--Модальное окно удаления сотрудника-->
        <div class="modal fade" id="<?=$ACTIONS['DEL']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Подтверждение удаления</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Вы точно хотите удалить пользователя с логином <span class='login'></span>?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                <form action="<?=$_MODULES['USERS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                  <input type="hidden" class='idUser' name="idUser" value="">
                  <input type="hidden" name="action" value="<?=$ACTIONS['DEL']?>">
                  <?if ($search):?>
                    <input type="hidden" name="search" value="<?=$search?>">
                  <?endif;?>
                  <button type="submit" class="btn btn-primary">Удалить</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="<?=$ACTIONS['UPD_PASS']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Обновить пароль</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="<?=$_MODULES['USERS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                <input type="hidden" class='idUser' name="idUser" value="">
                <input type="hidden" name="action" value="<?=$ACTIONS['UPD_PASS']?>">
                <?if ($search):?>
                  <input type="hidden" name="search" value="<?=$search?>">
                <?endif;?>
                <div class="modal-body">
                  <p>Обновление пароля пользователя с логином <span class='login'></span></p>
                  <div class="form-group">
                    <label for="passwordUPD">Пароль<span class="red-text">*</span></label>
                    <input type="password" name="password" id="passwordUPD" class="form-control" pattern="<?=PASSWORD_HTML_REGEXP?>"  required>
                    <div class="invalid-feedback">Пароль должен быть больше 6 символов, содержать латинские буквы, цифры и спец символы (~!@#$%^&*()+-`';:<>/\|)</div>
                  </div>
                  <div class="form-group">
                    <label for="rep-passwordUPD">Повторите пароль<span class="red-text">*</span></label>
                    <input type="password" name="rep-password" id="rep-passwordUPD" class="form-control"  required>
                    <div class="invalid-feedback">Пароли не совпадают!</div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                  <button type="submit" class="btn btn-primary">Обновить</button>
                </form>
                </div>
              </form>
            </div>
          </div>
        </div>
      <!--Модальное окно редактирования пользователя-->
      <div class="modal fade" id="<?=$ACTIONS['UPD']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalLabel">Редактировать пользователя</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <form action="<?=$_MODULES['USERS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                  <input type="hidden" class='idUser' name="idUser" value="">
                  <input type="hidden" name="action" value="<?=$ACTIONS['UPD']?>">
                  <?if ($search):?>
                    <input type="hidden" name="search" value="<?=$search?>">
                  <?endif;?>
                  <div class="modal-body mb-2">
                    <div class="form-group">
                        <label for="loginUPD">Логин<span class="red-text">*</span></label>
                        <input type="text" class="login form-control" id="loginUPD" name="login"  minlength=3 maxlength="100" value=""  pattern="<?=LOGIN_HTML_REGEXP?>" required >
                        <div class="invalid-feedback">
                          Вы должны ввести логин, максимум 100 латинских символов.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailUPD">Email<span class="red-text">*</span></label>
                        <input type="email" class="email form-control" id="emailUPD" name="email" value="" pattern="<?=EMAIL_HTML_REGEXP?>" required >
                        <div class="invalid-feedback">
                          Вы должны ввести email, максимум 100 символов.
                        </div>
                    </div>
                    <?if (count($groups)>0):?>
                      <div class="form-group">
                          <label for="idGroupUPD">Группа<span class="red-text">*</span></label>
                          <select id="idGroupUPD" name="idGroup" class="idGroup form-control">
                            <?foreach($groups as $key => $group_info):?>
                              <option value="<?=$group_info["idGroup"]?>"><?=$group_info["groupName"]?></option>
                            <?endforeach;?>
                          </select>
                      </div>
                    <?endif;?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                  </div>
                </form>
            </div>
        </div>
      </div>
      <!--Модальное окно создания пользователя-->
      <div class="modal fade" id="<?=$ACTIONS['CRT']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalLabel">Добавить пользователя</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <form action="<?=$_MODULES['USERS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                  <input type="hidden" name="action" value="<?=$ACTIONS['CRT']?>">
                  <?if ($search):?>
                    <input type="hidden" name="search" value="<?=$search?>">
                  <?endif;?>
                  <div class="modal-body mb-2">
                  <div class="form-group">
                        <label for="loginCRT">Логин<span class="red-text">*</span></label>
                        <input type="text" class="form-control" id="loginCRT" name="login"  minlength=3 maxlength="100" value=""  pattern="<?=LOGIN_HTML_REGEXP?>" required >
                        <div class="invalid-feedback">
                          Вы должны ввести логин, максимум 100 латинских символов.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailCRT">Email<span class="red-text">*</span></label>
                        <input type="email" class="form-control" id="emailCRT" name="email" value="" pattern="<?=EMAIL_HTML_REGEXP?>" required >
                        <div class="invalid-feedback">
                          Вы должны ввести email, максимум 100 символов.
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="passwordCRT">Пароль<span class="red-text">*</span></label>
                      <input type="password" name="password" id="passwordCRT" class="form-control" pattern="<?=PASSWORD_HTML_REGEXP?>"  required>
                      <div class="invalid-feedback">Пароль должен быть больше 6 символов, содержать латинские буквы, цифры и спец символы (~!@#$%^&*()+-`';:<>/\|)</div>
                    </div>
                    <div class="form-group">
                      <label for="rep-passwordCRT">Повторите пароль<span class="red-text">*</span></label>
                      <input type="password" name="rep-password" id="rep-passwordCRT" class="form-control"  required>
                      <div class="invalid-feedback">Пароли не совпадают!</div>
                    </div>
                    <?if (count($_USER["USER_GROUPS"])>0):?>
                      <div class="form-group">
                          <label for="idGroupCRT">Группа<span class="red-text">*</span></label>
                          <select id="idGroupCRT" name="idGroup" class="form-control">
                            <?foreach($_USER["USER_GROUPS"] as $key => $group_info):?>
                              <option value="<?=$group_info["idGroup"]?>"><?=$group_info["groupName"]?></option>
                            <?endforeach;?>
                          </select>
                      </div>
                    <?endif;?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                  </div>
                </form>
            </div>
        </div>
      </div>
      <div class="mb-3 d-flex justify-content-between">
        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#<?=$ACTIONS['CRT']?>_modal"><i class="fas fa-user mr-2"></i>Добавить пользователя</button>
        <div>
          <?if($search):?>
            <a href="index" class="btn btn-sm btn-outline-secondary mr-2">Сбросить</a>
          <?endif;?>
          <form class="form-inline d-inline" id="search-form" action="" method="GET" novalidate>
            <input class="form-control form-control-sm mr-2" type="search" name="search" id="search" placeholder="Поиск пользователя" aria-label="Поиск" value="<?=$search?>" <? if(!$search) echo("required")?>>
            <button class="btn btn-sm btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="w-100 table users-table">
          <thead>
            <tr>
                <th class="text-left">Логин</th>
                <th class="text-center">Email</th>
                <th class="text-center">Группа</th>
                <th class="text-right">Действия</th>
            </tr>
          </thead>
          <tbody>
              <? $result = User::GetList($search);
                if (count($result)>0):
                  foreach($result as $key => $result_value):?>
                    <tr class="entity-row" id='user_id<?=$result_value['idUser']?>' data-entity='<?=json_encode($result_value)?>'>
                      <td><?=$result_value['login']?> </td>
                      <td class="text-center"><?=$result_value['email']?> </td>
                      <td class="text-center"><?=$groups[$result_value['idGroup']-1]["groupName"]?> (<?=$result_value['idGroup']?>)</td>
                      <td class="text-right">
                        <button title="Сбросить пароль" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD_PASS']?>_modal"><i class="fas fa-undo-alt"></i></button>
                        <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                        <button title="Удалить" class="crud-modal-toggler text-danger" data-toggle="modal" data-target="#<?=$ACTIONS['DEL']?>_modal"><i class="fas fa-trash-alt"></i></button>
                      </td>
                    </tr>
                  <?endforeach;
                else:?>
                  </tbody>
                  </table>
                  <h4 class="text-center mt-5">
                    Не найдено ни одного пользователя!<br>
                    <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                  </h4>
                <?endif;?>
          </tbody>
        </table>
      </div>
    <?endif;?>
  </div>
</main>

<?
require_once($_CONFIG['TEMPLATES']['FOOTER_CRUD']);
}
?>