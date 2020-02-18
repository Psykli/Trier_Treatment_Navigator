<div class="media bottom_spacer_50px">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h2 class="media-heading"><?php echo lang('details_details');?>	<?php  echo "Code: ";  echo $patientcode; ?></h2>
	</div>
</div>

<nav class="menu">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard"><?php echo lang('list_overview');?></a></li>
		<li class="breadcrumb-item"><?php $link = $userrole.'/patient/list_all' ?>
			<?php echo anchor( $link, lang('list_list1') ); ?></li>
		<li class="breadcrumb-item active"><?php echo lang('details_details2');?></li>
	</ol> 
</nav>

<div class="card ">
	<div class="card-body">
		<ul class="nav nav-pills">		



			<!--GAS-->
			<li>
				<?php $link = 'index.php/' . 'user/Gas_Tool/create_gas/' . $patientcode; ?>
				<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-link" style="width:100%;">GAS</a>
			</li>
		</ul>
	</div>
</div>

<br/>


<div class="row">	
	<div class="col-sm-6">	
			<div class="card-column">
			<div class="card ">
				<div class="card-header">
					<h4 class="card-title"><?php echo lang('details_diagnostik');?></h4>
				</div>
				<div class="card-body">
					<?php 
					if (  $recommendation_status == 1 ){
						$viewColor = "green";	
					}
					else{
						$viewColor = "red";
					}
					
					?>

					<?php $link = 'index.php/' . 'user/patient/diagnostiktool/' . $patientcode; ?>
					<?php 
					if($viewColor == 'red'):?>
						<a href="<?php echo $link;?>"><img class="media-object pull-left" src="<?php echo base_url();?>/img/feedback/red.png"></a>
						<?php else:?>
							<img class="media-object pull-left" src="<?php echo base_url();?>/img/feedback/green.png">
					<?php endif;?>
							<span>Behandlungsempfehlung</span>
							<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-outline-info form-control">Zur Behandlungsempfehlung</a>								
				</div>
			</div>
			<br/>
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title"><?php echo lang('details_statusreport');?></h3>
				</div>
				<div class="card-body">				
					<?php if( !isset( $status ) ): ?>
						<div class="alert alert-info">
							<?php echo lang('details_nodata');?>
						</div>
					<?php else: /* Daten vorhanden */ ?>
						<table class="table table-striped">
							<thead>
								<th style="text-align: center"><?php echo lang('details_erhebung');?></th>
								<th style="text-align: center"><?php echo lang('details_date');?></th>       
							</thead>
							<tbody>
								<?php foreach( $status as $entry ): ?>
									<tr>
										<td style="text-align: center;">
											<?php $link = 'user/status/status/' . $entry->instance . '/' . $patientcode;
												echo anchor( $link, $entry->instance );
												?>
										</td>
										<td style="text-align: center;">
											<?php echo $entry->date; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Patientenzuweisung</h3>
				</div>
				<div class="card-body">
					<p>
						Zugewiesener Therapeut: <b><?php echo empty($assigned_therapist) ? 'Keiner' : $assigned_therapist;?></b>
					</p>
					<?php echo form_open( 'user/patient/assign_therapist/'.$patientcode,  array('role' => 'form', 'id' => 'assignTherapist' ) ); ?>
						<select id="assignment" name="assignment" class="form-control">
							<?php foreach($therapists as $therapist):?>
								<option value="<?php echo $therapist->initials;?>"><?php echo $therapist->initials;?></option>
							<?php endforeach;?>
						</select>
						<br/>
						<input type="submit" name="submit" class="btn btn-primary">
					</form>
					<hr/>
					<?php 
						switch($patient_state){
							case 0: 
								$s = 'Wartezeit';
								break;
							case 1:
								$s = 'Laufend';
								break;
							case 2:
								$s = 'Beendet';
								break;
							default:
								$s = 'Unterbrochen';
								break;
						}
					?>
					<p>Zustand: <b id="current_state"><?php echo $s;?></b></p>
					<button id="therapy_resume" class="btn btn-success" onclick="therapyStateChange(1)">Therapie wieder aufnehmen</button>
					<button id="therapy_break" class="btn btn-warning" onclick="therapyStateChange(5)">Therapie unterbrechen</button>
					<button id="therapy_end" class="btn btn-danger" onclick="therapyStateChange(2)">Therapie beenden</button>
				</div>
			</div>
			<script>
				function therapyStateChange(state){
					var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';

					$.ajax({
						data: {patientcode: '<?php echo $patientcode;?>', state: state, csrf_test_name: csrf_token},
						type: 'POST',
						url: '../therapy_state_change',
						success: function(){
							$('#current_state').html(getPatientStateString(state));
						}
					});
				}
				function getPatientStateString(state){
					switch(state){
						case 0: 
							return 'Wartezeit';
							break;
						case 1:
							return 'Laufend';
							break;
						case 2:
							return 'Beendet';
							break;
						default:
							return 'Unterbrochen';
							break;
					}
				}
			</script>

			</div>

	</div><!-- end:#feedbackOQ -->

	
	

	<div class="col-sm-6">	
	<div class="card-column">
		<div class="card ">
			<div class="card-header">
				<h4 class="card-title"><?php echo lang('details_feedback');?></h4>
			</div>
			<div class="card-body">

				<!--Nun wird das Bild geladen und der Text angezeigt. -->
					<?php 
					$link = site_url().'/user/feedback/overview/'.$patientcode; 
					if($color == 'red'):?>
						<a href="<?php echo $link;?>"><img class="media-object pull-left" src="<?php echo base_url();?>/img/feedback/<?php echo $color; ?>.png"></a>
					<?php else:?>
						<img class="media-object pull-left" src="<?php echo base_url();?>/img/feedback/<?php echo $color; ?>.png">
					<?php endif;?>

							<span><?php
							if($color == 'white')
								$text = "Für die aktuelle Sitzung wird kein Feedback berechnet";
							elseif ($color == 'missing')
								$text = "Berechnung nicht erfolgreich abgeschlossen (Details in Übersicht)";									
							else
								$text = "Behandlungsanpassung";							
							echo "$text";
					
							?></span>
							<a href="<?php echo $link ; ?>" role="button" class="btn btn-outline-info form-control">Zur Behandlungsanpassung</a>

				<div style="clear: both;"></div>
			</div>
		</div>

		<br/>
		
		<div class="card ">
			<div class="card-header">
				<h3 class="card-title"><?php echo lang('details_verlaufsreport');?></h3>
			</div>
			<div class="card-body">				
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td style="width: 50%;"><?php echo lang('details_last');?></td>
							<td>
								<?php if( isset( $last ) ): ?>
									<?php echo $last->instance; ?> (<?php echo $last->status_name; ?>)
								<?php else: /* keine Daten vorhanden*/?>
									<?php echo lang('details_nodata');?>
								<?php endif; ?>        
							</td>
						</tr>
						<tr>
							<td><?php echo lang('details_date');?></td>
							<td>
								<?php if( isset( $last ) ): ?>
									<?php echo $last->date; ?>
								<?php else: /* keine Daten vorhanden*/?>
									<?php echo lang('details_nodata');?>
								<?php endif; ?> 
							</td>
						</tr>
					</tbody>
				</table>
				<!-- <h3>Gesamt</h3> -->
				<?php $link = 'index.php/user/status/process/' . $patientcode ; ?>
				<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-info"><?php echo lang('details_verlauf');?></a>				

				<!--Funktioniert nur, wenn es höchstens eine instance für PO gibt-->
				<?php foreach( $status as $entry ): ?>
					<?php if($entry->instance === "PO"): ?>
						<?php $link = 'index.php/user/status/process/' . $patientcode . '/pr_po_comparison'; ?>
						<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-info"><?php echo lang('details_pr_po');?></a>							
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>	

		<?php if (( $userrole === 'admin' OR $userrole === 'privileged_user' ) AND (!$has_gas OR !$has_request)): ?>
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">SB-Freigabe</h3>
				</div>
				<div class="card-body">
					<div class="btn-group" style="width:100%;">
						<p>Das Stundenbogensystem ist nur erreichbar, wenn die Fallkonzeption bis zur 10. Sitzung und die Gas bis zur 15. Sitzung angelegt wurden. Soll es weiterhin möglich sein das Stundenbogensystem zu benutzen, so muss hier angegeben werden, bis zu welcher Sitzung diese Regel ignoriert werden kann.</p> 
						<?php if(!$has_gas):?>
							<p class="alert alert-warning"> Gas wurde nicht ausgefüllt</p>
						<?php endif;?>
						<?php if(!$has_request):?>
							<p class="alert alert-warning"> Fallkonzeption wurde nicht ausgefüllt</p>
						<?php endif;?>
						Letze beendete Sitzung: <b><?php echo $last_instance; ?></b> - Erlaubt bis einschließlich Sitzung: <b><span id="allowed_until"><?php echo $sb_allowed !== null ? $sb_allowed -> allowed_until_instance : 'Nicht gesetzt';?></span></b>
						<br/>
						<label for="allowed_instance">Neue erlaubte Sitzung:</label>
						<input type="number" class="form-control" name="allowed_instance" id="allowed_instance" value="<?php echo $last_instance + 3; ?>" min="<?php echo $last_instance + 1; ?>">
						<br/>
						<button type="button" class="btn btn-info form-control" id="set_allowed_instance" onclick="set_allowed()">Erlaubte Sitzung setzen</button>
						<br/>
						<button type="button" class="btn btn-info form-control" id="delete_allowed_instance" onclick="delete_allowed()">Erlaubte Sitzung löschen</button>
						<br/><br/><br/>
						<p id="save_info" class="alert alert-success" style="display:none;">
							Änderung gespeichert
						</p>
						<p id="error_info" class="alert alert-danger" style="display:none;">
							Aktion konnte nicht durchgeführt werden
						</p>
					</div>
				</div>
			</div>
		<?php endif;?>

	</div>
	<div style="clear: both;"></div>
	</div>
</div>	


<script>
	function set_allowed(){
		<?php $link = site_url('user/patient/set_sb_allowed'); ?>
		var allowed_instance = $('#allowed_instance').val();
		$('#set_allowed_instance').addClass('disabled');
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
		$.ajax({
			data: {patientcode: '<?php echo $patientcode;?>', allowed_instance: allowed_instance, csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo $link;?>', 
			success: function() {
				$('#set_allowed_instance').removeClass('disabled');
				$('#save_info').fadeIn(400).delay(1500).fadeOut(400);
				$('#allowed_until').text(allowed_instance);
			},
			error: function(){
				$('#set_allowed_instance').removeClass('disabled');
				$('#error_info').fadeIn(400).delay(1500).fadeOut(400);
			}         
		});
	}

	function delete_allowed(){
		<?php $link = site_url('user/patient/delete_sb_allowed'); ?>
		$('#delete_allowed_instance').addClass('disabled');
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
		$.ajax({
			data: {patientcode: '<?php echo $patientcode;?>', csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo $link;?>', 
			success: function() {
				$('#delete_allowed_instance').removeClass('disabled');
				$('#save_info').fadeIn(400).delay(1500).fadeOut(400);
				$('#allowed_until').text('Nicht gesetzt');
			},
			error: function(){
				$('#delete_allowed_instance').removeClass('disabled');
				$('#error_info').fadeIn(400).delay(1500).fadeOut(400);
			}         
		});
	}
</script>

<!-- Modal -->
<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open('user/patient/send_msg/'.$patientcode, array('role'=>'form')); ?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="meinModalLabel">Neue Nachricht</h4>
				</div>
				<div class="modal-body">
				
					<div class="form-group">
						<label for="empfaenger">Empfänger</label>
						<input type="text" class="form-control" id="empfaenger" name="empfaenger" placeholder="<?php echo $patientcode; ?>" disabled>
					</div>
					
					<div class="form-group">
						<label for="betreff">Betreff</label>
						<input type="text" class="form-control" id="betreff" name="betreff" placeholder="Betreff">
					</div>
					
					<div class="form-group">	
						<label for="nachricht">Nachricht</label>						
						<textarea class="form-control" id="nachricht" name="nachricht" placeholder="Ihre Nachricht" rows="12"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
					<?php echo form_submit(array('class' => 'btn btn-primary'), 'Nachricht abschicken'); ?>
				</div>
			</form>
		</div>
	</div>
</div>
