<div class="container">
    <div class="row">
        <div class="card mx-auto">
            <div class="card-body">
                <h2 class="card-title">Schon gleich viel besser!</h2>
                <p class="card-text">
                    Als nächstes müssen wir eine Datenbank anlegen. Dafür wird deine Konfigurationsdatei überschrieben.<br/>
                    Im Folgenenden brauchst du einen Nutzer mit Schreibrechten deiner Datenbank. (Bei XAMPP ist dies Beispielsweise als Default der Nutzer "root" mit einem leeren Passwort)<br/>
                    Falls du diesen Schritt schon selbst erledigt hast drücke auf <a class="btn btn-primary" href="<?php echo site_url('setup/step3');?>">Weiter</a><br/>
                    Ansonsten fülle folgendes Formular aus:
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <input class="form-control" id="database" type="text" name="database" placeholder="Name der Datenbank">
                        <input class="form-control" id="user" type="text" name="user" placeholder="Nutzer">
                        <input class="form-control" id="password" type="password" name="password" placeholder="Passwort">
                        <div style="display:none;" id="database_error">
                            Datenbank konnte nicht erstellt werden
                        </div>
                        <div class="spinner-border" role="status" id="loading_spinner" style="display:none;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <button id="sub_button" class="btn btn-primary" onclick="setupDatabase();">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setupDatabase(){
        var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
        $('#loading_spinner').show();
        $('#sub_button').prop('disabled',true);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('setup/save_database');?>",
            data: {
                database: $('#database').val(),
                user: $('#user').val(),
                password: $('#password').val(),
                csrf_test_name: csrf_token
            },
            success: function (response) {
                window.location.href = '<?php echo site_url('setup/step3');?>';                
            },
            error: function(response){
                $('#sub_button').prop('disabled',false);
                $('#loading_spinner').hide();
                console.log(response);
                
                $('#database_error').fadeIn(400).delay(1500).fadeOut(400);
                
            }
        });
    }
</script>