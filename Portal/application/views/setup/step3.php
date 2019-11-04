<div class="container">
    <div class="row">
        <div class="card mx-auto">
            <div class="card-body">
                <h2 class="card-title">Datenbank wurde erstellt!</h2>
                <p class="card-text">
                    Jetzt brauchen wir einen Admin-Account, mit dem du alle anderen Nutzer erstellen und dein Portal verwalten kannst.<br/>
                    Falls du diesen Schritt schon selbst erledigt hast drücke auf <a class="btn btn-primary" href="<?php echo site_url('setup/step4');?>">Weiter</a><br/>
                    Ansonsten fülle folgendes Formular aus:
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <input class="form-control" id="name" type="text" name="name" placeholder="Name des Admin">
                        <input class="form-control" id="email" type="email" name="email" placeholder="Email">
                        <input class="form-control" id="password" type="password" name="password" placeholder="Passwort">
                        <div style="display:none;" id="database_error">
                            Admin konnte nicht erstellt werden
                        </div>
                        <button class="btn btn-primary" onclick="saveAdmin();">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function saveAdmin(){
        var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('setup/save_admin');?>",
            data: {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                csrf_test_name: csrf_token
            },
            success: function (response) {
                window.location.href = '<?php echo site_url('setup/step4');?>';
            },
            error: function(response){
                console.log(response);
                
                $('#database_error').fadeIn(400).delay(1500).fadeOut(400);
            }
        });
    }
</script>