<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<a class="pull-left" href="#"> <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/identity.png"> </a>
			<div class="media-body">
				<h2 class="media-heading">Neuen Benutzer anlegen</h2><br />
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<div class="menu">
				<ol class="breadcrumb">
					<li><a href="<?php echo base_url(); ?>/index.php/admin/user">Benutzer</a></li>
					<li><a href="<?php echo base_url(); ?>/index.php/admin/user/list_all">Liste</a></li>
					<li class="active">Benutzer (Neu)</li>
				</ol>        
			</div><!-- end:.usermenu -->
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<?php echo form_open( 'admin/user/new_user',  array('class' => 'form-horizontal', 'role' => 'form' ) ); ?>
			<div class="col-sm-6">	
				<h4>Personalien</h4>
				
				<hr />
				
				<div class="form-group">
					<label for="first_name" class="col-sm-4 control-label">Vorname</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo set_value('first_name'); ?>" placeholder="Vorname">
						<?php echo form_error('first_name'); ?>
					</div>
				</div>		
				
				<div class="form-group">
					<label for="last_name" class="col-sm-4 control-label">Nachname</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo set_value('last_name'); ?>" placeholder="Nachname">
						<?php echo form_error('last_name'); ?>
					</div>
				</div>	

				<div class="form-group">
					<label for="initials" class="col-sm-4 control-label">Initialien (Login)</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="initials" name="initials" value="<?php if(!empty($initial_unique)){if($initial_unique){echo set_value('initials');}} ?>" placeholder="Benutzerlogin">
						<?php if(!empty($initial_unique)){if($initial_unique == false){echo ('Die gewählten Initialien sind bereits in Verwendung!');}} 
							  if(!empty($initials_errors)) {echo($initials_errors);} ?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="kohorte" class="col-sm-4 control-label">Kohorte</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="kohorte" name="kohorte" value="<?php echo set_value('kohorte'); ?>" placeholder="Kohorte">
						<?php echo form_error('kohorte'); ?>
					</div>
				</div>
				
				<div class="form-group">
					<label for="email" class="col-sm-4 control-label">E-Mail</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="Email">
						<?php echo form_error('email'); ?>
					</div>
				</div>	
							
				<div class="form-group">
					<label class="col-sm-4 control-label">Rolle</label>
					<div class="col-sm-8">
						<select class="form-control" id="role" name="role">
								<option value="admin">Administrator</option>
								<option value="priviledged_user">Privilegierter Benutzer (Indikation)</option>	
								<option value="user">Benutzer (Therapeut)</option>					
								<option value="supervisor">Supervisor</option>
						</select>
						<?php echo form_error('role'); ?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="password" class="col-sm-4 control-label">Passwort</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="password" name="password" placeholder="Passwort">
						<?php echo form_error('password'); ?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="passconf" class="col-sm-4 control-label">Wiederholung</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="passconf" name="passconf" placeholder="Passwort">
						<?php echo form_error('passconf'); ?>
					</div>
				</div>	
			</div><!-- /.col -->

				<!--##################################
					Section: Access-Rights-Checkboxes 
					##################################-->

				<div class="col-sm-6">
				<h4>Zugriffsrechte setzen</h4>
				
				<hr />
				
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_feedback" <?php echo ( $access_data['rechte_feedback'] == 1 ) ? 'checked' : ''; ?>>
							Feedback (Status- und Verlaufsreport)
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_entscheidung" <?php echo ( $access_data['rechte_entscheidung'] == 1 ) ? 'checked' : ''; ?>>
							Entscheidungsregeln / Clinical Support Tools
						</label>
					</div>
				</div>
								

				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_normal" <?php echo ( $access_data['rechte_verlauf_normal'] == 1 ) ? 'checked' : ''; ?>>
							Einzeltherapie Verlauf
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_online" <?php echo ( $access_data['rechte_verlauf_online'] == 1 ) ? 'checked' : ''; ?>>
							Onlinetherapie Verlauf
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_gruppe" <?php echo ( $access_data['rechte_verlauf_gruppe'] == 1 ) ? 'checked' : ''; ?>>
							Gruppentherapie Verlauf
						</label>
						
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_verlauf_seminare" <?php echo ( $access_data['rechte_verlauf_seminare'] == 1 ) ? 'checked' : ''; ?>>
							Seminartherapie Verlauf
						</label>
						
					</div>
				</div>

				<script>
				$(document).ready(function(){
					$('[data-toggle="popover"]').popover();   
				});
				</script>


			</div><!-- /.col -->
			
				<br />
				<div class="form-group">
					<div class="col-sm-8">
						<button type="submit" class="btn btn-primary">Benutzer anlegen</button>
					</div>
				</div>	
			</div>
		</form>
	</div><!-- /.row-->
</div>