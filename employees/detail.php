<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

  if (!$logged){
    header("HTTP/1.1 401 Unauthorized");
    include($_SERVER['DOCUMENT_ROOT']."/401.php");
    exit;
  }
  else if(isset($_GET['id']) || isset($_GET['idEmployee'])){
    require_once($_CONFIG['DATABASE']['CONNECT']);
    require_once($_MODULES['EMPLOYEES']['CLASS']);
    require_once($_MODULES['JOURNAL']['CLASS']);
    require_once($_MODULES['POSTS']['CLASS']);

    $id = (int) $_GET['id'];
    $employee = Employee::GetByID($id);
    $journal = Journal::GetJournal($id);
    $posts = Post::GetList();

    setcookie("ref", $_SERVER['REQUEST_URI']);
    $page_title = $employee->GetName();
    $nav_active = 1;
    $fa = true;

    $search = isset($_GET["search"]) ? preg_replace("/\s$/", '',$_GET["search"]) : NULL;
    $search_get = $search?'&search='.$search:'';

    $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];
    require_once($_CONFIG['TEMPLATES']['HEADER']);
    
?>
  <main role="main" id="main">
    <div class="ml-5 mt-2" style='position: absolute;'>
      <a href='index' class="text-muted text-decoration-none" ><i class="fas fa-angle-left mr-2"></i>Назад</a>
    </div>
    <div class="container mb-5">
      <?if ($db):?>
        <?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']):?>
        <!--Модальное окно удаления сотрудника-->
        <div class="modal fade" id="<?=$ACTIONS['DEL']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Подтверждение удаления назначения</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Вы точно хотите удалить назначение на должность <span class="postName"></span> от <span class="inagurationDate"></span>?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                <form action="<?=$_MODULES['JOURNAL']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                  <input type="hidden" class="idInaguration" name="idInaguration" >
                  <input type="hidden" class="idEmployee" name="idEmployee" value='<?=$id?>'>
                  <input type="hidden" name="action" value="<?=$ACTIONS['DEL']?>">
                  <button type="submit" class="btn btn-primary">Удалить</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <?endif;?>
        <!--Модальное окно редактирования-->
        <div class="modal fade" id="<?=$ACTIONS['UPD']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Редактировать вступление в должность</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <form action="<?=$_MODULES['JOURNAL']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                      <input type="hidden" class="idInaguration" name="idInaguration" >
                      <input type="hidden" class="idEmployee" name="idEmployee" value='<?=$id?>'>
                      <input type="hidden" name="action" value="<?=$ACTIONS['UPD']?>">
                      <div class="modal-body mb-2">
                        <div class="form-group">
                          <label for="inagurationDateUPD">Дата назначения<span class="red-text">*</span></label>
                          <input type="date" class="inagurationDate form-control datepicker" id="inagurationDateUPD" name="inagurationDate" value="" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия" required>
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
         <!--Модальное окно добавления-->
        <div class="modal fade" id="<?=$ACTIONS['CRT']?>_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Новое вступление в должность</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <form action="<?=$_MODULES['JOURNAL']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                      <input type="hidden" class="idEmployee" name="idEmployee" value='<?=$id?>'>
                      <input type="hidden" name="action" value="<?=$ACTIONS['CRT']?>">
                      <div class="modal-body mb-2">
                        <div class="form-group">
                          <label for="inagurationDateUPD">Дата назначения<span class="red-text">*</span></label>
                          <input type="date" class="inagurationDate form-control datepicker" id="inagurationDateUPD" name="inagurationDate" value="" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия" required>
                          <div class="invalid-feedback">
                            Дата в формате дд.мм.гггг
                          </div>
                        </div> 
                        <?if (count($posts)>0):?>
                          <div class="form-group">
                              <label for="idPostUPD">Должность<span class="red-text">*</span></label>
                              <select id="idPostUPD" name="idPost" class="idPost form-control">
                                <?foreach($posts as $key => $post_info):?>
                                  <option value="<?=$post_info["idPost"]?>"<?if(count($journal)>0 && ($journal[0]['idPost'] == $post_info["idPost"])) echo 'selected';?>><?=$post_info["postName"]?></option>
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

        <div class="mb-3 d-flex justify-content-between flex-column">
          <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
            <a class='text-decoration-none' data-toggle="collapse" href="#collapse_info" role="button" aria-expanded="false" aria-controls="collapseExample">
              <h2 class='mb-3 text-secondary'>Данные о сотруднике <i class="fas fa-angle-left collapse-arrow"></i></h2>
            </a>

            <form id="collapse_info" action="<?=$_MODULES['EMPLOYEES']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation collapse mb-3" novalidate>
              <input type="hidden" class="idEmployee" value="<?=$id?>" name="idEmployee" >
              <input type="hidden" name="action" value="<?=$ACTIONS['UPD_DETAIL']?>">
              <div class="mb-4">
                <div class="form-group">
                    <input type="text" class="name form-control" id="nameUPD" name="name" value="<?=$employee->GetName()?>"  pattern="<?=NAME_HTML_REGEXP?>" required >
                    <div class="invalid-feedback">
                      Вы должны ввести ФИО сотрудника, максимум 100 символов.
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">+7</div>
                      </div>
                      <input type="tel" class="tel form-control" id="telUPD" name="tel" value="<?=preg_replace("/^\+?[78]/", '' ,$employee->GetTel())?>" pattern="<?=TEL_HTML_REGEXP?>" value="" required>
                    </div>
                    <div class="invalid-feedback">
                      Телефон введен неверно
                    </div>
                </div>
                <div class="form-group">
                  <label for="acceptanceDateUPD">Дата принятия<span class="red-text">*</span></label>
                  <input type="date" class="acceptanceDate form-control datepicker" id="acceptanceDateUPD" name="acceptanceDate" value="<?=$employee->GetAcceptanceDate()?>"  pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия" required>
                  <div class="invalid-feedback">
                    Дата в формате дд.мм.гггг
                  </div>
                </div>
                <div class="form-group">
                  <label for="dismissalDateUPD">Дата увольнения</label>
                  <input type="date" class="dismissalDate form-control datepicker" id="dismissalDateUPD" name="dismissalDate" value="<?=$employee->GetDismissalDate()?>" pattern="<?=DATE_HTML_REGEXP?>" placeholder="Дата принятия">
                  <div class="invalid-feedback">
                    Дата в формате дд.мм.гггг
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Обновить</button>
            </form>  
          <?endif;?>
          <h2 class='<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'mt-3';?> mb-3 text-secondary'>Журнал продвижения по службе</h2>
          <div class="mb-3 d-flex justify-content-between">
            <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
              <button type="button" class="btn btn-sm btn-success mr-3" data-toggle="modal" data-target="#<?=$ACTIONS['CRT']?>_modal"><i class="fas fa-file-alt mr-2"></i>Новое вступление в должность</button>
            <?endif;?>
            <div class='text-dark'>
              <?if($employee->GetDismissalDate()):?>  
                Текущее состояние: <strong>уволен</strong>
              <?elseif(count($journal)>0):?> 
                Текущая должность: <strong><?=$journal[0]['postName']?></strong>
              <?endif;?>
            </div>
          </div>
          <div class="table-responsive">
            <table class="w-100 table employees-work-table">
              <thead>
                <tr>
                  <th>Должность</th>
                  <th class="text-center">Дата вступления</th>
                  <th class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>">Оклад</th>
                  <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                    <th class="text-right">Действия</th>
                  <?endif;?>
                </tr>
              </thead>
              <tbody>
              <?if($employee->GetDismissalDate()):?>  
                <tr>
                  <td>Увольнение</td>
                  <td class="text-center"><?=date("d.m.Y", strtotime($employee->GetDismissalDate()))?></td>
                  <td class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>">-</td>
                  <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                    <td class="text-right">
                      -
                    </td>
                  <?endif;?>
                </tr>
              <?endif;?>
              <?if (count($journal)>0):?>
                <?foreach($journal as $key => $journal_value):?>
                  <tr class="entity-row" id='journal_id<?=$result_value['idInaguration']?>' data-entity='<?=json_encode($journal_value)?>'>
                    <td><?=$journal_value['postName']?></td>
                    <td class="text-center"><?=date("d.m.Y", strtotime($journal_value['inagurationDate']))?></td>
                    <td class="<?if($_USER['GROUP'] != GUEST_GROUP_ID) echo 'text-center'; else echo 'text-right';?>"><?=$journal_value['salary']?>₽</td>
                    <?if($_USER['GROUP'] != GUEST_GROUP_ID):?>
                      <td class="text-right">
                        <button title="Редактировать" class="crud-modal-toggler text-muted mr-2" data-toggle="modal" data-target="#<?=$ACTIONS['UPD']?>_modal"><i class="fas fa-edit"></i></button>
                        <?if($_USER['GROUP'] == DIRECTOR_GROUP_ID || $_USER['IS_ADMIN']):?>
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
                  Сотрудник не вступил ни в одну должность!<br>
                  <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                </h4>
            <?endif;?>
              </tbody>
            </table>
          </div>
        </div>
      <?endif;?>
    </div>
</main>
<?
require_once($_CONFIG['TEMPLATES']['FOOTER_CRUD']);
}
?>