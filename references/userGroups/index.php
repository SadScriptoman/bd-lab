<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
  require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

  if (!$logged || !$_USER['IS_ADMIN']){
    header("HTTP/1.1 401 Unauthorized");
    include($_SERVER['DOCUMENT_ROOT']."/401.php");
    exit;
  }
  else {
    setcookie("ref", $_SERVER['REQUEST_URI']);
    $page_title = "Группы пользователей";
    $nav_active = 3;
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
        require_once($_MODULES['USER_GROUPS']['CLASS']);
      ?>
      <?if ($db):?>
        <!--Модальное окно удаления должности-->
          <div class="modal fade" id="<?=$ACTIONS['DEL']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalLabel">Подтверждение удаления группы</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Вы точно хотите удалить группу <span class="groupName"></span>? Это может привести к серьезным ошибкам!</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                  <form action="<?=$_MODULES['USER_GROUPS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idGroup" name="idGroup" value="">
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
        <!--Модальное окно редактирования должности-->
        <div class="modal fade" id="<?=$ACTIONS['UPD']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Редактировать группу</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                      
                  <form action="<?=$_MODULES['USER_GROUPS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idGroup" name="idGroup" >
                    <input type="hidden" name="action" value="<?=$ACTIONS['UPD']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                          <label for="nameUPD">Название<span class="red-text">*</span></label>
                          <input type="text" class="groupName form-control" id="nameUPD" name="groupName"  minlength=3 maxlength="100"  pattern="<?=POST_NAME_HTML_REGEXP?>" required >
                          <div class="invalid-feedback">
                            Вы должны ввести название группы, максимум 100 символов.
                          </div>
                      </div>              
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                      <button type="submit" class="btn btn-primary">Обновить</button>
                    </div>
                  </form>
              </div>
          </div>
        </div>
        <!--Модальное окно добавления должности-->
        <div class="modal fade" id="<?=$ACTIONS['CRT']?>_modal" tabindex="1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Добавить группу</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  
                  <form action="<?=$_MODULES['USER_GROUPS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="<?=$ACTIONS['CRT']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                          <label for="nameUPD">Название<span class="red-text">*</span></label>
                          <input type="text" class="groupName form-control" id="nameUPD" name="groupName"  minlength=3 maxlength="100"  pattern="<?=NAME_HTML_REGEXP?>" required >
                          <div class="invalid-feedback">
                            Вы должны ввести название группы, максимум 100 символов.
                          </div>
                      </div>               
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
          <div class="d-flex">
            <button type="button" class="btn btn-sm btn-success mr-3" data-toggle="modal" data-target="#<?=$ACTIONS['CRT']?>_modal"><i class="fas fa-user mr-2"></i>Добавить группу</button>
            
          </div>
          <div>
            <?if($search):?>
              <a href="index" class="btn btn-sm btn-outline-secondary mr-2">Сбросить</a>
            <?endif;?>
            <form class="form-inline d-inline" id="search-form" action="" method="GET" novalidate>
              <input class="form-control form-control-sm mr-2" type="search" name="search" id="search" placeholder="Поиск группы" aria-label="Поиск" value="<?=$search?>" <? if(!$search) echo("required")?>>
              <button class="btn btn-sm btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>
               
        <table class="w-100 table">
          <thead>
            <tr>
                <th>Название</th>
                <th class="text-right">Действия</th>
            </tr>
          </thead>
          <tbody>
              <?
                try{
                  $result = UserGroup::GetList($search);
                }catch(Exception $e ){
                  echo $e->getMessage(), '<br><br>';
                }
                if (count($result)>0):
                  foreach($result as $key => $result_value):?>
                    <tr class="entity-row" id='group_id<?=$result_value['idGroup']?>' data-entity='<?=json_encode($result_value)?>'>
                      <td><?=$result_value['groupName']?> (<?=$result_value['idGroup']?>)</td>
                      <td class="text-right">
                        <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                        <?if ($_USER['IS_ADMIN']): ?>
                          <button title="Удалить" class="crud-modal-toggler text-danger" data-toggle="modal" data-target="#<?=$ACTIONS['DEL']?>_modal"><i class="fas fa-trash-alt"></i></button>
                        <?endif;?>
                      </td>
                    </tr>
                  <?endforeach;
                else:?>
                  </tbody>
                  </table>
                  <h4 class="text-center mt-5">
                    Не найдено ни одной группы пользователей!<br>
                    <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                  </h4>
                <?endif;?>
          </tbody>
        </table>
      <?endif;?>
    </div>
</main>

<?
require_once($_CONFIG['TEMPLATES']['FOOTER_CRUD']);
}
?>