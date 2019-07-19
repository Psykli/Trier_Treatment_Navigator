<div class="media bottom_spacer">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h2 class="media-heading"><?php echo lang('details_details');?>	<?php  echo "Code: ";  echo $patientcode; ?></h2>
	</div>
</div>

<ol class="breadcrumb">
	<li><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard"><?php echo lang('list_overview');?></a></li>
	<li><?php $link = $userrole.'/patient/list_all' ?>
		<?php echo anchor( $link, lang('list_list1') ); ?></li>
	<li class="active"><?php echo lang('details_details2');?></li>
</ol> 

<?php 
$rechte_nn = $this -> membership_model -> is_rechte_set( $username, 'rechte_nn' );
$rechte_uebungen = $this -> membership_model -> is_rechte_set( $username, 'rechte_uebungen' ); 
?>

<div class="panel panel-default">
	<div class="panel-body">
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
			
		<?php //if($rechte_nn): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo lang('details_diagnostik');?></h3>
				</div>
				<div class="panel-body">
					<div class="media">
					<?php 
					$recommendation_status = $this-> User_Model ->get_status_recommendation( $patientcode, $username );
					if (  $recommendation_status[0] -> status == 1 ){
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

					<div class="media-body">
						<div class="col-sm-6">
							<?php
							$text = "Behandlungsempfehlung";							
							echo "<br />$text";
							?>
						</div>
						<div class="col-sm-6">
							<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-info">Zur Behandlungsempfehlung</a>
						</div>
					</div>
				</div>
					
					
				</div>
			</div>
		<?php //endif;?>
			
<br/><br/><br/>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo lang('details_statusreport');?></h3>
				</div>
				<div class="panel-body">				
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

	</div><!-- end:#feedbackOQ -->

	
	

	<div class="col-sm-6">	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang('details_feedback');?></h3>
			</div>
			<div class="panel-body">

				<!--Nun wird das Bild geladen und der Text angezeigt. -->
				<div class="media">
					<?php 
					$link = site_url().'/user/feedback/overview/'.$patientcode; 
					if($color == 'red'):?>
						<a href="<?php echo $link;?>"><img class="media-object pull-left" src="<?php echo base_url();?>/img/feedback/<?php echo $color; ?>.png"></a>
					<?php else:?>
						<img class="media-object pull-left" src="<?php echo base_url();?>/img/feedback/<?php echo $color; ?>.png">
					<?php endif;?>

					<div class="media-body">
						<div class="col-sm-6">
							<?php
							if($color == 'white')
								$text = "Für die aktuelle Sitzung wird kein Feedback berechnet";
							elseif ($color == 'missing')
								$text = "Berechnung nicht erfolgreich abgeschlossen (Details in Übersicht)";									
							else
								$text = "Behandlungsanpassung";							
							echo "<br />$text";
							
							?>
						</div>
						<div class="col-sm-6">
							<a href="<?php echo $link ; ?>" role="button" class="btn btn-info">Zur Behandlungsanpassung</a>
						</div>
					</div>
				</div>

				<div style="clear: both;"></div>
			</div>
		</div>

		<br/><br/><br/>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang('details_verlaufsreport');?></h3>
			</div>
			<div class="panel-body">				
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

		<?php $has_gas = $this->SB_Model->has_gas($patientcode); $has_request = $this->SB_Model->has_filled_request($patientcode); ?>
		<?php if (( $userrole === 'admin' OR $userrole === 'priviledged_user' ) AND (!$has_gas OR !$has_request)): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">SB-Freigabe</h3>
				</div>
				<div class="panel-body">
					<div class="btn-group" style="width:100%;">
						<p>Das Stundenbogensystem ist nur erreichbar, wenn die Fallkonzeption bis zur 10. Sitzung und die Gas bis zur 15. Sitzung angelegt wurden. Soll es weiterhin möglich sein das Stundenbogensystem zu benutzen, so muss hier angegeben werden, bis zu welcher Sitzung diese Regel ignoriert werden kann.</p> 
						<?php if(!$has_gas):?>
							<p class="alert alert-warning"> Gas wurde nicht ausgefüllt</p>
						<?php endif;?>
						<?php if(!$has_request):?>
							<p class="alert alert-warning"> Fallkonzeption wurde nicht ausgefüllt</p>
						<?php endif;?>
						Letze beendete Sitzung: <b><?php echo $this->SB_Model->getLastInstance($patientcode);?></b> - Erlaubt bis einschließlich Sitzung: <b><span id="allowed_until"><?php echo $this->Patient_Model->get_sb_allowed($patientcode) !== null ? $this->Patient_Model->get_sb_allowed($patientcode)->allowed_until_instance : 'Nicht gesetzt';?></span></b>
						<br/>
						<label for="allowed_instance">Neue erlaubte Sitzung:</label>
						<input type="number" class="form-control" name="allowed_instance" id="allowed_instance" value="<?php echo ($this->SB_Model->getLastInstance($patientcode)+3);?>" min="<?php echo ($this->SB_Model->getLastInstance($patientcode)+1);?>">
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
					<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
					<?php echo form_submit(array('class' => 'btn btn-primary'), 'Nachricht abschicken'); ?>
				</div>
			</form>
		</div>
	</div>
</div>
