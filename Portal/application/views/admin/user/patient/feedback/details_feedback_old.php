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
				<?php $link = 'index.php/' . 'user/Gas_Tool/index/create_gas/' . $patientcode; ?>
				<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-link" style="width:100%;">GAS</a>
			</li>

			<?php if( $userrole === 'admin' ):?>
				<li>
					<?php $link = 'index.php/' . 'user/patient/index/create_modul/' . $patientcode; ?>
					<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-link" style="width:100%;">Übungen</a>
				</li>
			<?php endif;?>
		</ul>
	</div>
</div>

<br/>


<div class="row">	
	<div class="col-sm-6">	
			
		<?php if( $this -> membership_model -> is_rechte_set( $username, 'rechte_feedback' ) ): ?>
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
											<?php $link = $userrole . '/status/index/' . $entry->instance . '/' . $patientcode;
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
		<?php endif; ?>

	</div><!-- end:#feedbackOQ -->

	
	

	<div class="col-sm-6">
	
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
				<?php $link = 'index.php/' . $userrole . '/status/index/NULL/' . $patientcode . '/process'; ?>
				<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-info"><?php echo lang('details_verlauf');?></a>				

				<!--Funktioniert nur, wenn es höchstens eine instance für PO gibt-->
				<?php foreach( $status as $entry ): ?>
					<?php if($entry->instance === "PO"): ?>
						<?php $link = 'index.php/' . $userrole . '/status/index/NULL/' . $patientcode . '/pr_po_comparison'; ?>
						<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-info"><?php echo lang('details_pr_po');?></a>							
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>	
	</div>



	<div style="clear: both;"></div>
</div>	


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
