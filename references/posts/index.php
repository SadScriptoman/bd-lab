<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
  require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

  if (!$logged){
    header("HTTP/1.1 401 Unauthorized");
    include($_SERVER['DOCUMENT_ROOT']."/401.php");
    exit;
  }
  else{
    setcookie("ref", $_SERVER['REQUEST_URI']);
    $page_title = "Должности";
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
        require_once($_MODULES['POSTS']['CLASS']);
      ?>
      <?if ($db):?>
        <?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']):?>
        <!--Модальное окно удаления должности-->
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
                  <p>Вы точно хотите удалить должность <span class="postName"></span>?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                  <form action="<?=$_MODULES['POSTS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idPost" name="idPost" value="">
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
                    <h5 class="modal-title" id="modalLabel">Редактировать должность</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                      
                  <form action="<?=$_MODULES['POSTS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idPost" name="idPost" >
                    <input type="hidden" name="action" value="<?=$ACTIONS['UPD']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                          <label for="nameUPD">Название<span class="red-text">*</span></label>
                          <input type="text" class="postName form-control" id="nameUPD" name="postName"  minlength=3 maxlength="100"  pattern="<?=POST_NAME_HTML_REGEXP?>" required >
                          <div class="invalid-feedback">
                            Вы должны ввести название должности на русском, максимум 100 символов.
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="salaryUPD">Оклад<span class="red-text">*</span></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text">₽</div>
                          </div>
                          <input type="number" class="salary form-control" id="salaryUPD" name="salary" min=0 max=2147483647 required > 
                          <div class="invalid-feedback">
                            Вы должны ввести оклад должности, минимум = 0₽
                          </div>                         
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
                    <h5 class="modal-title" id="modalLabel">Добавить должность</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  
                  <form action="<?=$_MODULES['POSTS']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="<?=$ACTIONS['CRT']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                          <label for="nameUPD">Название<span class="red-text">*</span></label>
                          <input type="text" class="postName form-control" id="nameUPD" name="postName"  minlength=3 maxlength="100"  pattern="<?=POST_NAME_HTML_REGEXP?>" required >
                          <div class="invalid-feedback">
                            Вы должны ввести название должности на русском, максимум 100 символов.
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="salaryCRT">Оклад<span class="red-text">*</span></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text">₽</div>
                          </div>
                          <input type="number" class="form-control" id="salaryCRT" name="salary" min=0 max=2147483647 required > 
                          <div class="invalid-feedback">
                            Вы должны ввести оклад должности, минимум = 0₽
                          </div>                         
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
        <?endif;?>
        <div class="mb-3 d-flex justify-content-between">
          <?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']):?>
            <div>
              <button type="button" class="btn btn-sm btn-success mr-3" data-toggle="modal" data-target="#<?=$ACTIONS['CRT']?>_modal"><i class="fas fa-user mr-2"></i>Добавить должность</button>
            </div>
          <?endif;?>
          <div>
            <?if($search):?>
              <a href="index" class="btn btn-sm btn-outline-secondary mr-2">Сбросить</a>
            <?endif;?>
            <form class="form-inline d-inline" id="search-form" action="" method="GET" novalidate>
              <input class="form-control form-control-sm mr-2" type="search" name="search" id="search" placeholder="Поиск должности" aria-label="Поиск" value="<?=$search?>" <? if(!$search) echo("required")?>>
              <button class="btn btn-sm btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="w-100 table ">
            <thead>
              <tr>
                  <th>Название</th>
                  <th class="<?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']) echo 'text-center'; else echo 'text-right';?>">Оклад</th>
                  <?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']):?>
                    <th class="text-right">Действия</th>
                  <?endif;?>
              </tr>
            </thead>
            <tbody>
                <?
                  try{
                    $result = Post::GetList($search);
                  }catch(Exception $e ){
                    echo $e->getMessage(), '<br><br>';
                  }
                  if (count($result)>0):
                    foreach($result as $key => $result_value):?>
                      <tr class="entity-row" id='post_id<?=$result_value['idPost']?>' data-entity='<?=json_encode($result_value)?>'>
                        <td><?=$result_value['postName']?></td>
                        <td class="<?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']) echo 'text-center'; else echo 'text-right';?>"><?=$result_value['salary']?>₽</td>
                        <?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']) :?>
                          <td class="text-right">
                            <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                            <?if($_USER['IS_ADMIN']) :?>
                              <button title="Удалить" class="crud-modal-toggler text-danger" data-toggle="modal" data-target="#<?=$ACTIONS['DEL']?>_modal"><i class="fas fa-trash-alt"></i></button>
                            <?endif;?>
                          </td>
                        <?endif;?>
                      </tr>
                    <?endforeach;
                  else:?>
                    </tbody>
                    </table>
                    <h4 class="text-center mt-5">
                      Не найдено ни одной должности!<br>
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