
<div class="row justify-content-center">
<div class="col-md-6 align-self-center" style="margin-top:10%;">
		<?php echo form_open('./patient/sb_dynamic/overview', array('method' => 'post', 'role' => 'form'));?>	
			<input type="hidden" id="post_patientcode" class="form-control" name="patientcode" placeholder="Patientencode">
			<input type="hidden" id="post_instance" class="form-control" name="instance" placeholder="Sitzungsnummer">
			<input type="hidden" id="post_therapist" class="form-control"  name="therapist" placeholder="Therapeut">
			<input type="hidden" id="post_skipped" class="form-control"  name="skipped" placeholder="Therapeut" value="check_for_skip">
			<div id="not_subject" class="alert alert-warning sr-only">
				Der Patient <span id="subject"></span> ist kein eingetragener Patient.
			</div>
			<div id="not_therapist" class="alert alert-warning sr-only">
				Der zugewiesen Therapeut für diesen Patient ist <span id="correct_therapist"></span>. Sie haben jedoch <span id="wrong_therapist"></span> eingegeben. <button type="submit" class="btn btn-link btn-sm">Trotzdem fortfahren?</button>
			</div>
			<div id="not_valid_therapist" class="alert alert-danger sr-only">
				Der eingegebene Therapeut <span id="invalid_therapist"></span> existiert nicht.
			</div>
			<div id="not_instance" class="alert alert-warning sr-only">
				Ihre aktuelle Sizungsnummer ist <span id="correct_instance"></span>. Wenn Sie trotzdem mit der Sitzung <span id="wrong_instance"></span> fortfahren wollen klicken Sie <button type="submit" class="btn btn-link btn-sm">hier</button>.
				Falls die letzte Sitzung als Papierbogen eingeben worden ist, kann es sein, dass diese noch nicht in das System übertragen wurde. Sie können in diesem Fall auf weiter klicken.
				Bitte beachten Sie, dass Doppelsitzungen als Einzelsitzungen gezählt werden!  
			</div>
			<div id="low_instance" class="alert alert-danger sr-only">
				Die nächste Sitzung ist <span id="current_instance"></span>. Die eingegebene Sitzungsnummer <span id="entered_instance"></span> sollte bereits stattgefunden haben. Bitte beachten Sie, dass Doppelsitzungen als Einzelsitzungen gezählt werden!
			</div>
			<div id="timeout_error" class="alert alert-danger sr-only">
				Die Daten konnten nicht übertragen werden, bitte versuchen Sie es erneut oder wenden Sie sich an <a href="mailto:<?php echo $this -> config -> item( 'email_address_main' ); ?>"><?php echo $this -> config -> item( 'email_address_main' ); ?></a>
			</div>
			<div id="battery_error" class="alert alert-danger sr-only">
				Es wurde keine Fragebogenbatterie für das Stundenbogensystem gefunden. Stellen sie sicher, dass diese existiert und dem SB-System zugewiesen ist.
			</div>
			<div id="request_not_filled" class="alert alert-danger sr-only">
				<h2 class="alert-heading">Die Fallkonzpetion des Patienten <span id="request_patient"></span> fehlt!</h2>
				<p>Bitte reichen Sie diese ein bevor Sie fortfahren oder wenden Sie sich an <a href="mailto:<?php echo $this -> config -> item( 'email_address_main' ); ?>"><?php echo $this -> config -> item( 'email_address_main' ); ?></a></p>
			</div>
			<div id="gas_not_filled" class="alert alert-danger sr-only">
				<h2 class="alert-heading">Die GAS des Patienten <span id="gas_patient"></span> fehlt!</h2>
				<p>Bitte tragen Sie diese ein bevor Sie fortfahren oder wenden Sie sich an <a href="mailto:<?php echo $this -> config -> item( 'email_address_main' ); ?>"><?php echo $this -> config -> item( 'email_address_main' ); ?></a></p>
			</div>
		</form>
		<div class="card ">

			<div class="card-header">
				<h3 class="card-title"></h3>
			</div>
			
			<div class="card-body">
				<?php if( isset( $error ) && $error ): ?>
					<div class="alert alert-danger">
						<?php if( $error_code === 403 ): ?>
							Authentifizierung fehlgeschlagen!
						<?php else: ?>
							Ein Fehler ist aufgetreten.
						<?php endif; ?>
					</div><!-- end:.error -->
				<?php endif; ?>
				<form id="credentials" onsubmit="validate_credentials(event);">
					<div class="form-group">
						<label for="patientcode">Patientencode</label>
						<input type="text" class="form-control" id="patientcode" name="patientcode" placeholder="Patientencode">
					</div>
					
					<div class="form-group">
						<label for="instance">Sitzungsnummer</label>
						<input type="number" class="form-control" id="instance" name="instance" placeholder="Sitzungsnummer">
					</div>
					
					<div class="form-group">
						<label for="therapist">Therapeut</label>
						<input type="text" class="form-control" id="therapist" name="therapist" placeholder="Therapeut">
					</div>
				</form>
				
				<!--<div class="form-group">
					<label for="password">Passwort</label>
					<input type="password" class="form-control" id="password" name="password" placeholder="Passwort (Therapeut)">
				</div>-->
				<br />
				<button type="button" id="submit_start" class="btn btn-primary btn-block" name="submit_start" onclick="validate_credentials(event);">Bestätigen</button>
			</div>
		</div>
	</div>
</div>
<div class="row justify-content-center">
<a class="text-center" href="<?php echo site_url();?>"><h1> Zurück zum Portal </h1></a>

<script>
	$(function(){
		$( document ).idleTimer( 300 * 1000);

		$( document ).on( "idle.idleTimer", function(event, elem, obj){
					window.location = './patient/sb_dynamic/start';
			});

		setTimeout(function() {
			window.location = './patient/sb_dynamic/start';
		}, 7200 * 1000);
	});
	function set_errors_to_hidden(){
		$('#not_subject').addClass('sr-only');
		$('#not_therapist').addClass('sr-only');
		$('#not_instance').addClass('sr-only');
		$('#not_valid_therapist').addClass('sr-only');
		$('#low_instance').addClass('sr-only');
		$('#timeout_error').addClass('sr-only');
		$('#request_not_filled').addClass('sr-only');
		$('#gas_not_filled').addClass('sr-only');
		$('#battery_error').addClass('sr-only');
	}
	function validate_credentials(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		set_errors_to_hidden();
		validate(function (output){
			
			if(output.includes('testpatient')){
				window.location = './test';
			}
			if(output.includes('sb_dynamic')){
				window.location = './overview';
			}
			try{
				var data= JSON.parse(output);
				for(var i = 0; i < data.length; i++){
					switch(data[i][0]){
						case 'not_subject':
							$('#not_subject').removeClass('sr-only');
							$('#subject').html(data[i][1]);
							break;
						case 'not_therapist':
							$('#not_therapist').removeClass('sr-only');
							$('#wrong_therapist').html(data[i][1]);
							$('#correct_therapist').html(data[i][2]);
							break;
						case 'not_instance':
							$('#not_instance').removeClass('sr-only');
							$('#wrong_instance').html(data[i][1]);
							$('#correct_instance').html(data[i][2]);
							break;
						case 'not_valid_therapist':
							$('#not_valid_therapist').removeClass('sr-only');
							$('#invalid_therapist').html(data[i][1]);
							break;
						case 'low_instance':
							$('#low_instance').removeClass('sr-only');
							$('#entered_instance').html(data[i][1]);
							$('#current_instance').html(data[i][2]);
						case 'post':
							$('#post_patientcode').val(data[i][1]);
							$('#post_instance').val(data[i][2]);
							$('#post_therapist').val(data[i][3]);
							break;
						case 'request_not_filled':
							$('#request_not_filled').removeClass('sr-only');
							$('#request_patient').html(data[i][1]);
							break;
						case 'gas_not_filled':
							$('#gas_not_filled').removeClass('sr-only');
							$('#gas_patient').html(data[i][1]);
							break;
						case 'no_battery':
							$('#battery_error').removeClass('sr-only');
							break;
					}
				}
			} catch(ex){}
		});		
	}

	function validate(handle){
		$('#submit_start').addClass('disabled');
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
		var patientcode_data = $('#patientcode').val();
		var instance_data = $('#instance').val();
		var therapist_data = $('#therapist').val();
		var data = {patientcode_post: patientcode_data, instance_post: instance_data, therapist_post: therapist_data, csrf_test_name: csrf_token}
		$.ajax({
          data: data,
          type: 'POST',
          url: './ajax_validate_credentials', 
          success: function(data) {
						$('#submit_start').removeClass('disabled');
						handle(data);
					}, 
					error: function(){
						$('#submit_start').removeClass('disabled');
						$('#timeout_error').removeClass('sr-only');
					},
					timeout: 6000
      	});
	}
</script>