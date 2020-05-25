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
    $page_title = "Сотрудники";
    $nav_active = 1;
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
        require_once($_MODULES['EMPLOYEES']['CLASS']);

        try{
          $employees_active = Employee::GetList($search, 'dismissalDate IS NULL');
        }catch(Exception $e ){
          echo $e->getMessage(), '<br><br>';
        }
        try{
          $employees_dismissed = Employee::GetList($search, 'dismissalDate IS NOT NULL');
        }catch(Exception $e ){
          echo $e->getMessage(), '<br><br>';
        }
        try{
          $employees_empty = Employee::GetListWithEmptyJournal();
        }catch(Exception $e ){
          echo $e->getMessage(), '<br><br>';
        }

        require_once(FUNCTIONS . '/format-phone.php');
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
                  <p>Вы точно хотите удалить сотрудника с именем <span class="name"></span>?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                  <form action="<?=$_MODULES['EMPLOYEES']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idEmployee" name="idEmployee" value="">
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
          <div class="modal fade" id="<?=$ACTIONS['DISMISS']?>_modal" tabindex="0" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalLabel">Подтверждение увольнения</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Вы точно хотите уволить сотрудника с именем <span class="name"></span>?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                  <form action="<?=$_MODULES['EMPLOYEES']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idEmployee" name="idEmployee" >
                    <input type="hidden" name="action" value="<?=$ACTIONS['DISMISS']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <button type="submit" class="btn btn-primary">Уволить</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <!--Модальное окно редактирования сотрудника-->
      <div class="modal fade" id="<?=$ACTIONS['UPD']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalLabel">Редактировать сотрудника</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <form action="<?=$_MODULES['EMPLOYEES']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" class="idEmployee" name="idEmployee" >
                    <input type="hidden" name="action" value="<?=$ACTIONS['UPD']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                        <label for="nameUPD">ФИО<span class="red-text">*</span></label>
                        <input type="text" class="name form-control" id="nameUPD" name="name"  minlength=3 maxlength="100" value=""  pattern="<?=NAME_HTML_REGEXP?>" required >
                        <div class="invalid-feedback">
                          Вы должны ввести ФИО сотрудника, максимум 100 символов.
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="telUPD">Телефон<span class="red-text">*</span></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text">+7</div>
                          </div>
                          <input type="tel" class="tel form-control" id="telUPD" name="tel" pattern="<?=TEL_HTML_REGEXP?>" value="" required>
                        </div>
                        <div class="invalid-feedback">
                          Телефон введен неверно
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="acceptanceDateUPD">Дата принятия<span class="red-text">*</span></label>
                        <input type="date" class="acceptanceDate form-control datepicker" id="acceptanceDateUPD" name="acceptanceDate" value="" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия" required>
                        <div class="invalid-feedback">
                          Дата в формате дд.мм.гггг
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="dismissalDateUPD">Дата увольнения</label>
                        <input type="date" class="dismissalDate form-control datepicker" id="dismissalDateUPD" name="dismissalDate" value="" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия">
                        <div class="invalid-feedback">
                          Дата в формате дд.мм.гггг
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
        <!--Модальное окно добавления сотрудника-->
        <div class="modal fade" id="<?=$ACTIONS['CRT']?>_modal" tabindex="1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Добавить сотрудника</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  
                  <form action="<?=$_MODULES['EMPLOYEES']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>

                    <input type="hidden"  name="action" value="<?=$ACTIONS['CRT']?>">
                    <?if ($search):?>
                      <input type="hidden" name="search" value="<?=$search?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                          <label for="nameCRT">ФИО<span class="red-text">*</span></label>
                          <input type="text" class="form-control" class="name" id="nameCRT" name="name"  minlength=3 maxlength="100" value=""  pattern="<?=NAME_HTML_REGEXP?>" required >
                          <div class="invalid-feedback">
                            Вы должны ввести ФИО сотрудника, максимум 100 символов.
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="telCRT">Телефон<span class="red-text">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">+7</div>
                            </div>
                            <input type="tel" class="tel form-control" id="telCRT" name="tel" pattern="<?=TEL_HTML_REGEXP?>" value="" required>
                          </div>
                          <div class="invalid-feedback">
                            Телефон введен неверно
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="acceptanceDateCRT">Дата принятия<span class="red-text">*</span></label>
                        <input type="date" class="form-control datepicker" id="acceptanceDateCRT" name="acceptanceDate" value="" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия" required>
                        <div class="invalid-feedback">
                          Дата в формате дд.мм.гггг
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="dismissalDateCRT">Дата увольнения</label>
                        <input type="date" class="form-control datepicker" id="dismissalDateCRT" name="dismissalDate" value="" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия">
                        <div class="invalid-feedback">
                          Дата в формате дд.мм.гггг
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
            <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
              <button type="button" class="btn btn-sm btn-success mr-3" data-toggle="modal" data-target="#<?=$ACTIONS['CRT']?>_modal"><i class="fas fa-user mr-2"></i>Добавить сотрудника</button>
            <?endif;?>
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
              <li class="nav-item">
                <a class="btn btn-sm nav-link active mr-3" id="pills-work-tab" data-toggle="pill" href="#pills-work" role="tab" aria-controls="pills-work" aria-selected="true">Действующие</a>
              </li>
              <li class="nav-item">
                <a class="btn btn-sm nav-link mr-3" id="pills-dismissed-tab" data-toggle="pill" href="#pills-dismissed" role="tab" aria-controls="pills-dismissed" aria-selected="false">Уволенные</a>
              </li>
              <?if (count($employees_empty)>0):?>
                <li class="nav-item">
                  <a class="btn btn-sm nav-link text-danger" id="pills-undef-tab" data-toggle="pill" href="#pills-undef" role="tab" aria-controls="pills-undef" aria-selected="false">Не задана должность  <i class="fas fa-exclamation-triangle"></i></a>
                </li>
              <?endif;?>
            </ul>
          </div>
          <div>
            <?if($search):?>
              <a href="index" class="btn btn-sm btn-outline-secondary mr-2">Сбросить</a>
            <?endif;?>
            <form class="form-inline d-inline" id="search-form" action="" method="GET" novalidate>
              <input class="form-control form-control-sm mr-2" type="search" name="search" id="search" placeholder="Поиск сотрудника" aria-label="Поиск" value="<?=$search?>" <? if(!$search) echo("required")?>>
              <button class="btn btn-sm btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>
        
        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active table-responsive" id="pills-work" role="tabpanel" aria-labelledby="pills-work-tab">        
            <table class="w-100 table employees-work-table">
              <caption class='t-caption'>Кол-во сотрудников: <?=count($employees_active)?></caption>
              <thead>
                <tr>
                    <th>ФИО</th>
                    <th class="text-center">Телефон</th>
                    <th class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>">Дата принятия</th>
                    <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                      <th class="text-right">Действия</th>
                    <?endif;?>
                </tr>
              </thead>
              <tbody>
                  <?if (count($employees_active)>0):
                      foreach($employees_active as $key => $result_value):?>
                        <tr class="entity-row" data-entity='<?=json_encode($result_value)?>'>
                          <td><a href='detail?id=<?=$result_value['idEmployee']?>'><?=$result_value['name']?></a></td>
                          <td class="text-center"><?=format_phone($result_value['tel'])?></td>
                          <td class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>"><?=date("d.m.Y", strtotime($result_value['acceptanceDate']))?></td>
                          <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                            <td class="text-right">
                              <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                              <button title="Уволить" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['DISMISS']?>_modal"><i class="fas fa-user-times"></i></button>
                              <?if ($_USER['IS_ADMIN']): ?>
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
                        Не найдено ни одного сотрудника!<br>
                        <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                      </h4>
                    <?endif;?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane fade table-responsive" id="pills-dismissed" role="tabpanel" aria-labelledby="pills-dismissed-tab">
            <table class="w-100 table employees-table">
              <caption class='t-caption'>Кол-во сотрудников: <?=count($employees_dismissed)?></caption>
              <thead>
                <tr>
                    <th>ФИО</th>
                    <th class="text-center">Телефон</th>
                    <th class="text-center">Дата принятия</th>
                    <th class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>">Дата увольнения</th>
                    <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                      <th class="text-right">Действия</th>
                    <?endif;?>
                </tr>
              </thead>
              <tbody>
                <?if (count($employees_dismissed)>0):
                  foreach($employees_dismissed as $key => $result_value):?>
                    <tr class="entity-row" data-entity='<?=json_encode($result_value)?>'>
                      <td><a href='detail?id=<?=$result_value['idEmployee']?>'><?=$result_value['name']?></a></td>
                      <td class="text-center"><?=format_phone($result_value['tel'])?></td>
                      <td class="text-center"><?=date("d.m.Y", strtotime($result_value['acceptanceDate']))?></td>
                      <td class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>"><?=date("d.m.Y", strtotime($result_value['dismissalDate']))?></td>
                      <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                        <td class="text-right">
                          <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                          <?if ($_USER['IS_ADMIN']): ?>
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
                    Не найдено ни одного сотрудника!<br>
                    <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                  </h4>
                <?endif;?>
              </tbody>
            </table>
          </div>

          <?if (count($employees_empty)>0):?>
            <div class="tab-pane fade table-responsive" id="pills-undef" role="tabpanel" aria-labelledby="pills-undef-tab">
              <table class="w-100 table employees-work-table">
                <caption class='t-caption' >Кол-во сотрудников: <?=count($employees_empty)?></caption>
                <thead>
                  <tr>
                      <th>ФИО</th>
                      <th class="text-center">Телефон</th>
                      <th class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>">Дата принятия</th>
                      <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                        <th class="text-right">Действия</th>
                      <?endif;?>
                  </tr>
                </thead>
                <tbody>
                  <?foreach($employees_empty as $key => $result_value):?>
                    <tr class="entity-row" data-entity='<?=json_encode($result_value)?>'>
                      <td><a href='detail?id=<?=$result_value['idEmployee']?>'><?=$result_value['name']?></a></td>
                      <td class="text-center"><?=format_phone($result_value['tel'])?></td>
                      <td class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>"><?=date("d.m.Y", strtotime($result_value['acceptanceDate']))?></td>
                      <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                        <td class="text-right">
                          <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                          <button title="Уволить" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['DISMISS']?>_modal"><i class="fas fa-user-times"></i></button>
                          <?if ($_USER['IS_ADMIN']): ?>
                            <button title="Удалить" class="crud-modal-toggler text-danger" data-toggle="modal" data-target="#<?=$ACTIONS['DEL']?>_modal"><i class="fas fa-trash-alt"></i></button>
                          <?endif;?>
                        </td>
                      <?endif;?>
                    </tr>
                  <?endforeach;?>
                </tbody>
              </table>
            </div>
          <?endif;?>
        </div>
      <?endif;?>
    </div>
</main>

<?
require_once($_CONFIG['TEMPLATES']['FOOTER_CRUD']);
}
?>