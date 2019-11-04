<div class="media bottom_spacer">
    <a class="pull-left" href="#">
        <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/identity.png">
    </a>
    <div class="media-body">
        <h2 class="media-heading">Profil</h2>
    </div>
</div>
<div class="row">
<?php echo validation_errors(); ?>
	<div class="col-sm-4">	
		<?php echo form_open( 'login/change_profile',  array('class' => 'form-horizontal', 'role' => 'form' ) ); ?>	
		
					<div class="form-group">
				<label for="first_name" class="col-sm-4 control-label">Vorname</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name; ?>">
				</div>
			</div>		
			
			<div class="form-group">
				<label for="last_name" class="col-sm-4 control-label">Nachname</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="last_name" name="last_name"  value="<?php echo $last_name; ?>">
				</div>
			</div>	

			<div class="form-group">
				<label for="initials" class="col-sm-4 control-label">Initialien (Login)</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="initials" name="initials" value="<?php echo $initials; ?>" readonly>
				</div>
			</div>	
			
			<div class="form-group">
				<label for="email" class="col-sm-4 control-label">E-Mail</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="email" name="email"  placeholder="Email" value="<?php echo $email; ?>">
				</div>
			</div>	

      <input type="hidden" id="role" name="role" value="<?php echo $role;?>">
			
            <div class="form-group">
                <div class="col-sm-8">
                    <input type="submit" class="btn btn-primary">
                </div>
            </div>
		</form>     
	</div>
</div>
<div class="row">
    <div class="col-sm-4">
    <div class="col-sm-8">
        <button class="btn btn-primary" data-toggle="modal" data-target="#password_modal">Passwort ändern</button>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="password_modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Passwort ändern</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>Aktuelles Passwort:</label>
            <input type="password" id="current_password" name="current_password" class="form-control">
        </div>  
        <div class="form-group">
            <label>Neues Passwort:</label>
            <input type="password" id="new_password" name="new_password" class="form-control">
        </div> 
        <div class="form-group">
            <label>Neues Passwort Wiederholung:</label>
            <input type="password" id="new_password_repeat" name="new_password_repeat" class="form-control">
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="saveChanges()">Speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
      </div>
    </div>
  </div>
</div>

<script>
    
</script>
