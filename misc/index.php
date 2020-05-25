<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config/config.php");
    require_once($_MODULES['AUTHORIZATION']['IS_LOGGED']);

    if (!$logged || (!$_USER['IS_ADMIN'] && !$_USER['GROUP'] == DIRECTOR_GROUP_ID)){
      header("HTTP/1.1 401 Unauthorized");
      include($_SERVER['DOCUMENT_ROOT']."/401.php");
      exit;
    }
    else{
      setcookie("ref", $_SERVER['REQUEST_URI']);
      $page_title = "Дополнительно";
      $nav_active = 4;
      $fa = true;

      $ACTIONS = $_CONFIG['APPLICATION']['ACTIONS'];

      require_once($_CONFIG['TEMPLATES']['HEADER']);
?>

<main role="main" id="main">
  <div class="container mb-5">
    <?php
      require_once($_CONFIG['DATABASE']['CONNECT']);
      if ($db){      
      
      require_once($_MODULES['CRUDENTITY']['CLASS']);

      $search = isset($_GET["search"]) ? preg_replace("/\s$/", '',$_GET["search"]) : NULL;
      $search_get = $search?'&search='.$search:'';

      $query = isset($_POST["query"]) ? $_POST["query"] : NULL;
      $salary = isset($_POST["salary"]) ? (int) $_POST["salary"] : NULL;

      if(strlen($salary) && is_numeric($salary)){
        $query = 'CALL `GET_EMPLOYEES_WITH_SALARY_MORE_THAN`('.$salary.', @p1); SELECT @p1 AS `result_counter`;';
      }
        
      if (strlen($query)){
        try{
          $query_result = CRUDEntity::ExecQuery($query);
        }catch(Exception $e ){
          echo '<span class="red-text">Ошибка при попытке получить результат: <br>'.$e->getMessage(), '</span><br>';
        }
      }
    ?>
    <div class="modal fade" id="salary_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Введите оклад</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="" enctype="multipart/form-data" method="POST" class="mt-2 needs-validation" novalidate>
            <div class="modal-body">
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
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div>
      <h3 class='text-secondary'>Запросы</h3>
      <form action="" enctype="multipart/form-data" method="POST" class="mt-2 needs-validation" novalidate>
        <input type="hidden" name="query" value="CALL `GET_AVG_SALARY`(@p); SELECT @p AS `result`;" required>                        
        <button type="submit" class="btn btn-sm btn-outline-secondary">Cредний оклад</button>
      </form>
      <button title="Редактировать" class="mt-2 btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#salary_modal">Количество сотрудников с заданным окладом или выше</button>
    </div>

    <?if($_USER['IS_ADMIN']):?>
        <form action="" enctype="multipart/form-data" method="POST" class="mt-4 needs-validation" novalidate>
            <div class="form-group">
                <label for="query">Запрос в базу данных<span class="red-text">*</span></label>
                <input type="text" class="query form-control" id="query" name="query" value="<?=$query?>" minlength=0 required>                        
                <div class="invalid-feedback">
                  Строка не должна быть пустой!
                </div>  
            </div>  
            <button type="submit" class="btn btn-sm btn-primary">Отправить</button>
        </form>
    <?endif;?>

    <?if (isset($query_result) && count($query_result)>0):?>
      <?$keys = array_keys($query_result[0]);
        if(count($keys) > 1):?>
          <div class="table-responsive mt-4">
              <table class="w-100 table">
                <thead>
                  <tr>
                      <?foreach($keys as $num => $key):?>
                          <th class="text-center"><?=$key?></th>
                      <?endforeach;?>
                  </tr>
                </thead>
                <tbody>
                    <?
                      foreach($query_result as $key => $result_value):?>
                          <tr>
                              <?foreach($result_value as $key => $val):?>
                                  <td class="text-center"><?=($val) ? $val : 'NULL'?> </td>
                              <?endforeach;?>
                          </tr>
                      <?endforeach;?>
                </tbody>
              </table>
          </div>
        <?else:?>
          <div class='mt-4'>
            <h3 class='text-secondary'>Результат: <?if ($query_result[0][$keys[0]]) echo $query_result[0][$keys[0]]; else echo '0';?></h3>
          </div>
        <?endif;?>
    <?endif;?>
  </div>
</main>

<?
require_once($_CONFIG['TEMPLATES']['FOOTER_CRUD']);
}
}
?>