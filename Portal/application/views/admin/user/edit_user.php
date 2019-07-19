<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<a class="pull-left" href="#"> <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/identity.png"> </a>
			<div class="media-body">
				<h2 class="media-heading">Informationen des Benutzers: <?php echo $userdata['INITIALS']; ?></h2><br />
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<ol class="breadcrumb">
				<li><a href="<?php echo base_url(); ?>/index.php/admin/user">Benutzer</a> </li>
				<li><a href="<?php echo base_url(); ?>/index.php/admin/user/list_all">Liste</a> </li>
				<li class="active">Benutzerprofil</li>
			</ol>   
		</div>
	</div>
</div>
     
<div class="container">
	<div class="row">
		<div class="col-sm-12">	
			<?php if( isset( $data_valid_error ) AND $data_valid_error ): ?>
				<div class="alert alert-danger">
					Es wurden nicht alle Felder korrekt ausgefüllt!
				</div>

			<?php endif; ?>
			
			<?php if( isset( $changes_success ) AND $changes_success ): ?>
				<div class="alert alert-success">
					Benutzer erfolgreich geändert!
				</div>
			<?php endif; ?>

			<?php if(isset($_SESSION['creation_success']) AND $_SESSION['creation_success']): ?>
				<div class= "alert alert-success">
					Benutzer erfolgreich erstellt!
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<?php echo form_open( 'admin/user/edit_user/'.$userdata['id'],  array('class' => 'form-horizontal', 'role' => 'form' ) ); ?>
			
			<div class="col-sm-6">
				<h4>Personalien</h4><hr />		

				<?php if($piwik_exists):?>
				<div class="form-group">
					<div class="col-sm-offset-4  col-sm-8">
						<a href="<?php echo base_url().'index.php/admin/user/user_statistics/'.$userdata['id']?>" class="btn btn-info">Seitenbesuche</a>
					</div>
				</div>	
				<?php endif;?>	
			
				<div class="form-group">
					<label for="first_name" class="col-sm-4 control-label">Vorname</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $userdata['FIRST_NAME']; ?>" placeholder="<?php echo $userdata['FIRST_NAME']; ?>">
						<?php echo(form_error('first_name'))?>
					</div>
				</div>		
				
				<div class="form-group">
					<label for="last_name" class="col-sm-4 control-label">Nachname</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $userdata['LAST_NAME']; ?>" placeholder="<?php echo $userdata['LAST_NAME']; ?>">
						<?php echo(form_error('last_name')) ?>
					</div>
				</div>	

				<div class="form-group">
					<label for="initials" class="col-sm-4 control-label">Initialien (Login)</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="initials" name="initials" value="<?php echo $userdata['INITIALS']; ?>" placeholder="<?php echo $userdata['INITIALS']; ?>" readonly>
					</div>
				</div>	
				
				<div class="form-group" id="right_cohorte">
					<label for="kohorte" class="col-sm-4 control-label">Kohorte</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="kohorte" name="kohorte" value="<?php echo $userdata['kohorte']; ?>" placeholder="Kohorte">
					</div>
				</div>
				
				<div class="form-group">
					<label for="email" class="col-sm-4 control-label">E-Mail</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="email" name="email" value="<?php echo $userdata['email']; ?>" placeholder="Email">
						<?php echo form_error('email')?>
					</div>
				</div>	
							
				<div class="form-group">
					<label class="col-sm-4 control-label">Rolle</label>
					<div class="col-sm-8">
						<select class="form-control" id="role" name="role">
								<option value="admin" id="admin"<?php echo $userdata['ROLE'] === 'admin' ? 'selected':''?>>Administrator</option>
								<option value="priviledged_user" id="priviledged_user"<?php echo $userdata['ROLE'] === 'priviledged_user' ? 'selected':''?>>Privilegierter Benutzer (Indikation)</option>	
								<option value="user" id="user"<?php echo $userdata['ROLE'] === 'user' ? 'selected':''?>>Benutzer (Therapeut)</option>					
								<option value="supervisor" id="supervisor" <?php echo $userdata['ROLE'] == 'supervisor' ? 'selected':''?>>Supervisor</option>
								<option value="patient" id="patient"<?php echo $userdata['ROLE'] === 'patient' ? 'selected':''?>>Patient</option>
						</select>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="password" class="col-sm-4 control-label">Passwort</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="password" name="password" placeholder="Passwort">
						<?php echo(form_error('password')) ?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="passconf" class="col-sm-4 control-label">Wiederholung</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="passconf" name="passconf" placeholder="Passwort">
						<?php echo(form_error('passconf')) ?>
					</div>
				</div>	

				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="change_password"> Passwort setzen
							</label>
						</div>
					</div>
				</div>		
				
			</div><!-- /.col -->


			<div class="col-sm-3" id="other_rights">
				<h4>Zugriffsrechte setzen</h4>
				
				<hr />

				
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_feedback" <?php echo ( $userdata['rechte_feedback'] == 1 ) ? 'checked' : ''; ?>>
							Feedback (Status- und Verlaufsreport)
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_entscheidung" <?php echo ( $userdata['rechte_entscheidung'] == 1 ) ? 'checked' : ''; ?>>
							Entscheidungsregeln / Clinical Support Tools
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_normal" <?php echo ( $userdata['rechte_verlauf_normal'] == 1 ) ? 'checked' : ''; ?>>
							Einzeltherapie Verlauf
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_online" <?php echo ( $userdata['rechte_verlauf_online'] == 1 ) ? 'checked' : ''; ?>>
							Onlinetherapie Verlauf
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_gruppe" <?php echo ( $userdata['rechte_verlauf_gruppe'] == 1 ) ? 'checked' : ''; ?>>
							Gruppentherapie Verlauf
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_seminare" <?php echo ( $userdata['rechte_verlauf_seminare'] == 1 ) ? 'checked' : ''; ?>>
							Seminartherapie Verlauf
						</label>
						
					</div>
				</div>
				<script>
				$(document).ready(function(){
					$('[data-toggle="popover"]').popover();   
				});
				</script>
			</div>

			

			<div class="col-sm-3" id="patient_rights">
				<h4>Patientenrechte setzen</h4>
				<hr />

				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_zw" <?php echo ( $userdata['rechte_zw'] == 1 ) ? 'checked' : ''; ?>>
							Zwischenmessung
						</label>
					</div>
				</div>
			</div>



			<div class="form-group">
				<div class="col-sm-8">
					<button type="submit" class="btn btn-primary">Benutzer ändern</button>
				</div>
			</div>	
		</form>

		<?php echo form_open( 'admin/user/reset_password/'.$userdata['id'],  array('class' => 'form-horizontal', 'role' => 'form' ) ); ?>
			<div class="form-group">
				<div class="col-sm-8" id="other_rights2">
					<button type="submit" class="btn btn-primary">Passwort zurücksetzen</button>
					<?php $popover_content = "Es wird ein zufälliges Passwort erstellt und gesetzt. Zusätzlich wird eine E-Mail an den Therapeuten versendet und informiert diesen über die Änderungen."; ?>
					<a href="#" data-toggle="popover" title="Hinweis" data-content="<?php echo $popover_content; ?>"><span class="glyphicon glyphicon-info-sign"></span></a>

					<script>
					$(document).ready(function(){
						$('[data-toggle="popover"]').popover({html:true});   
					});
					</script>
				</div>
			</div>	
		</form>


		
		
	</div><!-- /.row -->
</div>

<script>
$(function() {
  $("#role").change(function() {
    if ($("#patient").is(":selected") ) {
      $("#patient_rights").show();
	  $("#other_rights").hide();
	  $("#other_rights2").hide();
	  $("#right_cohorte").hide();
    } else {
      $("#patient_rights").hide();
	  $("#other_rights").show();
	  $("#other_rights2").show();
	  $("#right_cohorte").show();
    }
  }).trigger('change');
});
</script>
