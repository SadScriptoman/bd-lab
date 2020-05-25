<script src="<?=src?>/js/jquery-3.4.1.min.js" ></script>
<script src="<?=src?>/bootstrap/bootstrap.bundle.min.js" ></script>

<script type="text/javascript">
   $(document).ready( function(){

        var pass = $("#password");
        var rep_pass = $("#rep-password");

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

        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
        
   });
</script>

</body>
</html>