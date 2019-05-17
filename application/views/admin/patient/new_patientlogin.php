<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<a class="pull-left" href="#"> <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/identity.png"> </a>
			<div class="media-body">
				<h2 class="media-heading">Neuen Patienten anlegen</h2><br />
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<div class="menu">
				<ol class="breadcrumb">
					<li><a href="<?php echo base_url(); ?>/index.php/admin/user">Patient</a></li>
					<li><a href="<?php echo base_url(); ?>/index.php/admin/user/list_all">Liste</a></li>
					<li class="active">Patienten erstellen</li>
				</ol>        
			</div><!-- end:.usermenu -->
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<?php if(isset($msg)) : ?>
                    <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">&times;</span></button>
                            <strong>Fehler!</strong> Der Patient konnte nicht erstellt werden. Bitte überprüfen Sie die Eingabefelder auf fehlerhafte Eingaben.
                    </div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<?php echo form_open( 'admin/patient/new_patientlogin',  array('class' => 'form-horizontal', 'role' => 'form', 'id' => 'createLogin' )); ?>
			<div class="col-sm-6">	
				<h4>Personalien</h4>
				
				<hr />
				
				<div class="form-group">
					<label for="first_name" class="col-sm-4 control-label">Vorname</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo set_value('first_name'); ?>" placeholder="Vorname" autofocus>
                        <?php echo form_error ('first_name');?>
					</div>
				</div>		
				database		
				<div class="form-group">
					<label for="last_name" class="col-sm-4 control-label">Nachname</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo set_value('last_name'); ?>" placeholder="Nachname">
                        <?php echo form_error ('last_name');?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="initials" class="col-sm-4 control-label">Patientcode (Login)</label>
					<div class="col-sm-8">
					database				<input type="text" class="form-control" name="initials" id="initials" placeholder="Patientencode" />						
                        <?php if(isset($initials_errors)){echo($initials_errors);} ?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="email" class="col-sm-4 control-label">E-Mail</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="Email">
                        <?php echo form_error('email') ?>
					</div>
				</div>	
				
				<div class="form-group">
					<label for="password" class="col-sm-4 control-label">Passwort</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="password" name="password" placeholder="Passwort">
                        <?php echo form_error('password') ?>
					</div>
				</div>	
                <div class="form-group">
					<label for="passconf" class="col-sm-4 control-label">Wiederholung</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="passconf" name="passconf" placeholder="Passwort">
                        <?php echo form_error('passconf') ?>
					</div>
				</div>	

			
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-primary">Benutzer anlegen</button>
					</div>
				</div>				

			</div><!-- /.col -->

			<div class="col-sm-3" id="patient_rights">
				<h4>Patientenrechte setzen</h4>
				<hr />

				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="rechte_zw" <?php echo ('rechte_zw' == 1 ) ? 'checked' : ''; ?>>
							Zwischenmessung
						</label>
					</div>
				</div>
			</div>

		</form>
	</div><!-- /.row-->
</div>


