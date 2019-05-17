</div>
<div class="container quest_container">
<div class="row" style="margin-top:10%;">
	<div class="col-sm-offset-4 col-sm-4">
		<?php echo form_open('./patient/sb_dynamic/overview', array('method' => 'post', 'role' => 'form'));?>	
			<input type="hidden" id="post_patientcode" class="form-control" name="patientcode" placeholder="Patientencode">
			<input type="hidden" id="post_instance" class="form-control" name="instance" placeholder="Sitzungsnummer">
			<input type="hidden" id="post_therapist" class="form-control"  name="therapist" placeholder="Therapeut">
			<input type="hidden" id="post_skipped" class="form-control"  name="skipped" placeholder="Therapeut" value="check_for_skip">
			<div id="not_subject" class="alert alert-warning hidden">
				Der Patient <span id="subject"></span> ist kein eingetragener Patient.
			</div>
			<div id="not_therapist" class="alert alert-warning hidden">
				Der zugewiesen Therapeut für diesen Patient ist <span id="correct_therapist"></span>. Sie haben jedoch <span id="wrong_therapist"></span> eingegeben. <button type="submit" class="btn btn-link btn-xs">Trotzdem fortfahren?</button>
			</div>
			<div id="not_valid_therapist" class="alert alert-danger hidden">
				Der eingegebene Therapeut <span id="invalid_therapist"></span> existiert nicht.
			</div>
			<div id="not_instance" class="alert alert-warning hidden">
				Ihre aktuelle Sizungsnummer ist <span id="correct_instance"></span>. Wenn Sie trotzdem mit der Sitzung <span id="wrong_instance"></span> fortfahren wollen klicken Sie <button type="submit" class="btn btn-link btn-xs">hier</button>.
				Falls die letzte Sitzung als Papierbogen eingeben worden ist, kann es sein, dass diese noch nicht in das System übertragen wurde. Sie können in diesem Fall auf weiter klicken.
				Bitte beachten Sie, dass Doppelsitzungen als Einzelsitzungen gezählt werden!  
			</div>
			<div id="low_instance" class="alert alert-danger hidden">
				Die nächste Sitzung ist <span id="current_instance"></span>. Die eingegebene Sitzungsnummer <span id="entered_instance"></span> sollte bereits stattgefunden haben. Bitte beachten Sie, dass Doppelsitzungen als Einzelsitzungen gezählt werden!
			</div>
			<div id="timeout_error" class="alert alert-danger hidden">
				Die Daten konnten nicht übertragen werden, bitte versuchen Sie es erneut oder wenden Sie sich an <a href="mailto:psyfeedback@uni-trier.de">psyfeedback@uni-trier.de</a>
			</div>
			<div id="request_not_filled" class="alert alert-danger hidden">
				<h2 class="alert-heading">Die Fallkonzpetion des Patienten <span id="request_patient"></span> fehlt!</h2>
				<p>Bitte reichen Sie diese ein bevor Sie fortfahren oder wenden Sie sich an <a href="mailto:psyfeedback@uni-trier.de">psyfeedback@uni-trier.de</a></p>
			</div>
			<div id="gas_not_filled" class="alert alert-danger hidden">
				<h2 class="alert-heading">Die GAS des Patienten <span id="gas_patient"></span> fehlt!</h2>
				<p>Bitte tragen Sie diese ein bevor Sie fortfahren oder wenden Sie sich an <a href="mailto:psyfeedback@uni-trier.de">psyfeedback@uni-trier.de</a></p>
			</div>
		</form>
		<div class="panel panel-default">

			<div class="panel-heading">
				<h3 class="panel-title"></h3>
			</div>
			
			<div class="panel-body">
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
<div class="row">
<a class="text-center" href="../../../index.php"><h1> Zurück zum Portal </h1></a>

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
		$('#not_subject').addClass('hidden');
		$('#not_therapist').addClass('hidden');
		$('#not_instance').addClass('hidden');
		$('#not_valid_therapist').addClass('hidden');
		$('#low_instance').addClass('hidden');
		$('#timeout_error').addClass('hidden');
		$('#request_not_filled').addClass('hidden');
		$('#gas_not_filled').addClass('hidden');
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
							$('#not_subject').removeClass('hidden');
							$('#subject').html(data[i][1]);
							break;
						case 'not_therapist':
							$('#not_therapist').removeClass('hidden');
							$('#wrong_therapist').html(data[i][1]);
							$('#correct_therapist').html(data[i][2]);
							break;
						case 'not_instance':
							$('#not_instance').removeClass('hidden');
							$('#wrong_instance').html(data[i][1]);
							$('#correct_instance').html(data[i][2]);
							break;
						case 'not_valid_therapist':
							$('#not_valid_therapist').removeClass('hidden');
							$('#invalid_therapist').html(data[i][1]);
							break;
						case 'low_instance':
							$('#low_instance').removeClass('hidden');
							$('#entered_instance').html(data[i][1]);
							$('#current_instance').html(data[i][2]);
						case 'post':
							$('#post_patientcode').val(data[i][1]);
							$('#post_instance').val(data[i][2]);
							$('#post_therapist').val(data[i][3]);
							break;
						case 'request_not_filled':
							$('#request_not_filled').removeClass('hidden');
							$('#request_patient').html(data[i][1]);
							break;
						case 'gas_not_filled':
							$('#gas_not_filled').removeClass('hidden');
							$('#gas_patient').html(data[i][1]);
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
						$('#timeout_error').removeClass('hidden');
					},
					timeout: 6000
      	});
	}

	
</script>