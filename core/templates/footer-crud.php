
<script src="<?=src?>/js/jquery-3.4.1.min.js" ></script>
<script src="<?=src?>/js/mask.min.js" ></script>
<script src="<?=src?>/js/custom-file-input.js" ></script>
<script src="<?=src?>/bootstrap/bootstrap.bundle.min.js" ></script>


<script type="text/javascript">
   $(document).ready( function(){

      bsCustomFileInput.init()
      let forms = document.getElementsByClassName('needs-validation');
      if (forms != null){
        let validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }
      let search_form = document.getElementById('search-form');
      if (search_form != null){
        search_form.addEventListener('submit', function(event) {
          if (search_form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
        }, false);
      }
      
      let pass = $(".password");
      let rep_pass = $(".rep-password");
      function check_match(pass, rep_pass){
          if(rep_pass.val() == pass.val()) {
              rep_pass.attr("minlength", "6");
          }
          else{
              rep_pass.attr("minlength", "999999");
          }
      }
      check_match(pass, rep_pass);
      rep_pass.bind("input", function(event) {
        check_match(pass, rep_pass);
      });
      pass.bind("input", function(event) {
        check_match(pass, rep_pass);
      });
      let vars = [];
      let hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
      let search = '';
      for(let i = 0; i < hashes.length; i++)
      {
        let hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
        if (vars['search']){
          search = '?search='+vars['search'];
        }
      }

      $('.crud-modal-toggler').on("click", function(e) {
        let row = $(this).parents('.entity-row');
        let entity_data = row ? $.makeArray((row.data('entity')))[0] : $.makeArray(($(this).data('entity')))[0];
        let modal = $($(this).data('target'));
        if (modal && entity_data) {
          $.each(entity_data, function(key, val){
            let elem = modal.find('.'+key);
            if(elem.length && val){
              if (val.match(<?=TEL_REGEXP?>)){
                val = val.replace(/^\+?[78]/, '');
              }
              if (elem[0].tagName.toLowerCase() === 'input'){
                elem.val(val);
              }else if (elem[0].tagName.toLowerCase() === 'select'){
                let select = elem.children();
                $.each(select, function(key2, val2){
                  if (val2.tagName.toLowerCase() === 'option' && $(val2).val() == val){
                    $(val2).attr('selected', 'selected');
                  }
                });
              }else{
                elem.text(val);
              }
            }
            if (elem.hasClass('tel')){
              elem.mask("(999) 999-99-99");
            }
          });
        }
        });
    
    $(".tel").mask("(999) 999-99-99");
    
   });
</script>

</body>
</html>