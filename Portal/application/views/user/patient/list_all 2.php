<div class="media bottom_spacer_50px place_headline">
	<a class="pull-left">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading"><?php echo lang('list_list1');?></h1>
	</div>
</div>

<nav class="menu">
	<ol class="breadcrumb">
		<li class="breadcrumb-item">
			<?php echo anchor( 'user/dashboard', lang('list_overview') ); ?>
		</li>
		<li class="breadcrumb-item active"><?php echo lang('list_list1');?></li>
	</ol> 
</nav>


<script>
	/* Add basic pagination to the user tables. The fifth table is handled at the bottom of this file. */
	$(document).ready(function(){
		$('#userTable1').DataTable( { } );
		$('#userTable2').DataTable( { } );
		$('#userTable3').DataTable( { } );
		$('#userTable4').DataTable( { } );
	});
</script>

<!--
	Liste der Zustände
 0: Wartezeit
 1: Laufend
 2: Regulärer Abschluss
 3: Abbruch mit bewilligten Sitzungen
 4: Abbruch in Probatorik
 5: Unterbrechung
 6: Therapie nicht Zustande gekommen
 7: Abbruch in Probatorik durch Therapeut
 8: Abbruch in Probatorik durch Patient
 9: Abbruch mit bewilligten Sitzungen durch Therapeut
10: Abbruch mit bewilligten Sitzungen durch Patient
11: Abbruch aus formalen Gründen
-->
<div class="container">

<?php
if( $userrole === 'privileged_user' || $userrole === 'admin' ) {
	if($show_all) {
		echo anchor( 'user/patient/list_all', 'Nur eigene Patienten anzeigen', array('class' => 'btn btn-primary'))."<br><br>"; 
	} else {
 		echo anchor( 'user/patient/list_all/all', 'Alle Patienten anzeigen', array('class' => 'btn btn-primary'))."<br><br>";
	}
}
?>
	<div class="row">
		<div class="col-sm-12">
			<?php if( isset( $patients ) ): ?>
				<p>
					<i>
						<?php
							echo lang('list_instruction_part1');
							if( !$ausblenden ) {
								echo lang('list_instruction_part2');
							}
						?>
					</i>
				</p>
				
				<div id="accordion" role="tablist" aria-multiselectable="true">
					<div class="card ">
						<div class="card-header" role="tab" id="überschriftEins">
							<h4 class="card-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseEins" aria-expanded="true" aria-controls="collapseEins">
									<?php echo lang('list_run');?> (<?php echo $status['open']; ?>)
								</a>
							</h4>
						</div>
						<div id="collapseEins" class="card-collapse collapse in" role="tabcard" aria-labelledby="überschriftEins">
							<div class="card-body">
								<table id="userTable1" class="table table-bordered table-striped">
							
									<!-- Alle Patienten mit Status "Laufend" -->
									<thead>
										<tr>
											<th><?php echo lang('list_code');?></th>
											<th><?php echo lang('list_status');?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach( $patients as $patient ): if ($patient->zustand == 1) : ?>
										<tr>
											<td><!-- patientcode -->
												<?php $link = 'user/patient/list/' . $patient->code; ?>
												<?php echo anchor( $link, $patient->code ); ?>
											</td>
											<td><!-- patientzustand -->
												<?php echo lang('list_run');?>
											</td>
										</tr>
										<?php endif; endforeach;?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="card ">
						<div class="card-header" role="tab" id="überschriftZwei">
							<h4 class="card-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseZwei" aria-expanded="true" aria-controls="collapseZwei">
									<?php echo lang('list_quit');?> (<?php echo $status['abort']; ?>)
								</a>
							</h4>
						</div>
						<div id="collapseZwei" class="card-collapse collapse" role="tabcard" aria-labelledby="collapseZwei">
							<div class="card-body">
								<table id="userTable2" class="table table-bordered table-striped">					
									<tr>
										<th><?php echo lang('list_code');?></th>
										<th><?php echo lang('list_status');?></th>
									</tr>
									<?php foreach( $patients as $patient ): if ($patient->zustand == 3 || $patient->zustand == 4 || ($patient->zustand >= 7 && $patient->zustand <= 11)) : ?>
									<tr>
										<td>
											<?php $link = 'user/patient/list/' . $patient->code; ?>
											<?php echo anchor( $link, $patient->code ); ?>
										</td>
										<td>
											<?php switch ($patient->zustand){
												case 3:
													echo lang('list_case1');
													break;
													
												case 4:
													echo lang('list_case2');
													break;	

													case 7:
														echo lang('list_case3');
														break;	
														
													case 8:
														echo lang('list_case4');
														break;
														
													case 9:
														echo lang('list_case5');
														break;
														
													case 10:
														echo lang('list_case6');
														break;
														
													case 11:
														echo lang('list_case7');
														break;
														
													default:
														echo  $patient->zustand; 
												}         ?>
											</td>
										</tr>
										<?php endif; endforeach;?>
									</tbody>
								</table>
							</div>
						</div>
					</div>	

					<div class="card ">
						<div class="card-header" role="tab" id="überschriftDrei">
							<h4 class="card-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseDrei" aria-expanded="true" aria-controls="collapseDrei">
									<?php echo lang('list_normal');?> (<?php echo $status['closed']; ?>)
								</a>
							</h4>
						</div>
						<div id="collapseDrei" class="card-collapse collapse" role="tabcard" aria-labelledby="collapseDrei">
							<div class="card-body">
								<table id="userTable3" class="table table-bordered table-striped">							
									<!-- Alle Patienten mit Status "Regulär beendet" -->
									<thead>
										<tr>
											<th><?php echo lang('list_code');?></th>
											<th><?php echo lang('list_status');?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach( $patients as $patient ): if ($patient->zustand == 2) : ?>
										<tr>
											<td><!-- patientcode -->
												<?php $link = 'user/patient/list/' . $patient->code; ?>
												<?php echo anchor( $link, $patient->code ); ?>
											</td>
											<td><!-- patientzustand -->
												<?php echo lang('list_normal');?>
											</td>
										</tr>
										<?php endif; endforeach;?>
									</tbody>
								</table>
							</div>
						</div>
					</div>	

					<div class="card ">
						<div class="card-header" role="tab" id="überschriftVier">
							<h4 class="card-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseVier" aria-expanded="true" aria-controls="collapseVier">
									<?php echo lang('list_stop');?> (<?php echo $status['temp_break']; ?>)
								</a>
							</h4>
						</div>
						<div id="collapseVier" class="card-collapse collapse" role="tabcard" aria-labelledby="collapseVier">
							<div class="card-body">
								<table id="userTable4" class="table table-bordered table-striped">							
									
									<!-- Alle Patienten mit Status "Unterbrechung" -->
									<thead>
										<tr>
											<th><?php echo lang('list_code');?></th>
											<th><?php echo lang('list_status');?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach( $patients as $patient ): if ($patient->zustand == 5) : ?>
										<tr>
											<td><!-- patientcode -->
												<?php $link = 'user/patient/list/' . $patient->code; ?>
												<?php echo anchor( $link, $patient->code ); ?>
											</td>
											<td><!-- patientzustand -->
												<?php echo lang('list_stop');?>
											</td>
										</tr>
										<?php endif; endforeach;?>
									</tbody>
								</table>
							</div>
						</div>
					</div>	

					<?php if( ($userrole === 'privileged_user' || $userrole === 'admin' ) && $show_all): ?>
						<div class="card ">
							<div class="card-header" role="tab" id="überschriftFünf">
								<h4 class="card-title">				
									<script>
										$(document).ready(function(){
											$('#userTable5').DataTable( { } );
										});
									</script>
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseFünf" aria-expanded="true" aria-controls="collapseFünf">
										Wartezeit (<?php echo $status['waiting']; ?>)
									</a>
								</h4>
							</div>
							<div id="collapseFünf" class="card-collapse collapse" role="tabcard" aria-labelledby="collapseFünf">
								<div class="card-body">
									<table id="userTable5" class="table table-bordered table-striped">							
										
										<!-- Alle Patienten mit Status "Unterbrechung" -->
										<thead>
											<tr>
												<th><?php echo lang('list_code');?></th>
												<th><?php echo lang('list_status');?></th>
												<th><?php echo lang('list_date');?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach( $patients as $patient ): if ($patient->zustand == 0) : ?>
											<tr>
												<td><!-- patientcode -->
													<?php $link = 'user/patient/list/' . $patient->code; ?>
													<?php echo anchor( $link, $patient->code ); ?>
												</td>
												<td><!-- patientzustand -->
													Wartezeit
												</td>
												<td><!-- Erstsichtung -->
													<?php echo $patient->erstsich;?>
												</td>
											</tr>
											<?php endif; endforeach;?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					<?php endif;?>				
				</div>	
			<?php else: ?>
				<div class="alert alert-info">
					<?php echo lang('list_nodata');?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
