<script src="<?php echo base_url(); ?>js/charts/hsclProcess.js"></script>
<script src="<?php echo base_url(); ?>js/charts/compare.js"></script>

<div id="member_area" class="process">
    <div class="media bottom_spacer place_headline">
        <a class="pull-left" href="#">
            <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Verlauf</h1>
        </div>
    </div>

<ol class="breadcrumb">
	<li><a href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/dashboard">Meine Patientenübersicht</a></li>
	<li><?php $link = $userrole.'/patient/list_all' ?>
		<?php echo anchor( $link, 'Patientenliste' ); ?></li>
	<li><a href="<?php echo base_url( ); ?>index.php/user/patient/list/<?php echo $patientcode; ?>">Patientendetails</a></li>
	<li class="active">Verlauf</li>
</ol>  

<?php if( !isset( $patientcode ) ): //ERROR during transmit ?>
    <div class="alert alert-danger">
        Fehlerhafte übermittlung von Sitzung und Patientencode.
    </div>
<?php else: ?>
    <div class="dashrow status">	
		<div class="table_box feedback_info">
			<div class="panel panel-default">
				<div class="panel-body">
					<b>Code: </b> <?php echo $patientcode; ?> - 
					<b>Letzte Sitzung: </b>                         
						<?php if( isset( $last ) ): ?>
                            <?php echo $last->instance; ?> (<?php echo $last->status_name; ?>)
                        <?php else: /* keine Daten vorhanden*/?>
                            kein Eintrag
                        <?php endif; ?> - 
					<b>Datum der Sitzung: </b>                            
						<?php if( isset( $last ) ): ?>
                            <?php echo $last->date; ?>
                        <?php else: /* keine Daten vorhanden*/?>
                            kein Eintrag
                        <?php endif; ?> 			
				</div><!-- end:.panel-body -->
			</div><!-- end:.panel panel-default -->
			<!-- Der Counter wird auch als Index für den Graphen genutzt-->
			
			<?php $counter = 0; ?>
			<?php foreach ($graphs as $graph): ?>
				<div class="col-sm-12" id="graph<?php echo $counter; ?>">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse<?php echo $counter ?>"><?php echo $graph;?></a>
								</h4>
							</div>
							<div id="collapse<?php echo $counter ?>" class="panel-collapse collapse <?php if($counter === $graph_counter): echo "in"; endif; ?>">
								<div class="panel-body">
									<form name="change_process_form">
										Instanz: 
										<select onchange="change_process(this.value, <?php echo $counter; ?>)">
											<option value="0"><?php echo lang('therapy_all'); ?></option>
											<option value="1" <?php if($counter === $graph_counter AND $filtered_therapy_type === 1): echo "selected"; endif; ?>><?php echo lang('therapy_single_exclude'); ?></option>
											<option value="2" <?php if($counter === $graph_counter AND $filtered_therapy_type === 2): echo "selected"; endif; ?>><?php echo lang('therapy_group'); ?></option>
											<option value="3" <?php if($counter === $graph_counter AND $filtered_therapy_type === 3): echo "selected"; endif; ?>><?php echo lang('therapy_online'); ?></option>
											<option value="4" <?php if($counter === $graph_counter AND $filtered_therapy_type === 4): echo "selected"; endif; ?>><?php echo lang('therapy_seminar'); ?></option>
										</select>
									</form>

									<?php if ($graph === "HSCL"): ?>
										<?php if( sizeof( $hsclData['INSTANCES'] ) > 0 ): ?>
											<canvas id="<?php echo $graph;?><?php echo $counter;?>" width="680px" height="400px"></canvas>
											<script>
												createHsclChart("<?php echo $graph;?><?php echo $counter;?>", "TEST<?php echo $counter;?>", <?php echo json_encode($hsclData['MEANS']);?>,
												<?php echo json_encode($hsclData['INSTANCES']);?>,<?php echo json_encode($hsclData['BOUNDARIES']);?>,
												<?php echo json_encode($hsclData['EXPECTED']);?>);
											</script>
										<?php else: ?>
											<br>
											<?php echo lang('no_therapy_graph_data'); ?>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php $counter++; ?>
			<?php endforeach; ?>

			<?php foreach ($means as $name => $mean): ?>
				<?php 
					$tables = array_keys($mean);
				?>
				<div class="col-sm-12" id="graph<?php echo $counter; ?>">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse<?php echo $counter ?>"><?php echo $name;?></a>
								</h4>
							</div>
							<div id="collapse<?php echo $counter ?>" class="panel-collapse collapse <?php if($counter === $graph_counter): echo "in"; endif; ?>">
								<div class="panel-body">
									<form name="change_process_form">
										Instanz: 
										<select onchange="change_process(this.value, <?php echo $counter; ?>)">
											<option value="0"><?php echo lang('therapy_all'); ?></option>
											<option value="1" <?php if($counter === $graph_counter AND $filtered_therapy_type === 1): echo "selected"; endif; ?>><?php echo lang('therapy_single_exclude'); ?></option>
											<option value="2" <?php if($counter === $graph_counter AND $filtered_therapy_type === 2): echo "selected"; endif; ?>><?php echo lang('therapy_group'); ?></option>
											<option value="3" <?php if($counter === $graph_counter AND $filtered_therapy_type === 3): echo "selected"; endif; ?>><?php echo lang('therapy_online'); ?></option>
											<option value="4" <?php if($counter === $graph_counter AND $filtered_therapy_type === 4): echo "selected"; endif; ?>><?php echo lang('therapy_seminar'); ?></option>
										</select>
									</form>
									
									<?php if( sizeof( $mean[$tables[0]] ) > 0 ): ?>
										<canvas id="<?php echo $name;?><?php echo $counter;?>" width="680px" height="400px"></canvas>
										<script>
											createLineChart("<?php echo $name;?><?php echo $counter;?>","<?php echo $name;?>", <?php echo json_encode($mean[$tables[0]]);?>,
											<?php echo json_encode($mean[$tables[1]]);?>,<?php echo json_encode($info[$name]);?>);
										</script>
									<?php else: ?>
										<br>
										<?php echo lang('no_therapy_graph_data'); ?>
									<?php endif; ?>
									<?php $counter++; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
				
				</div>
	<!-- TEST ENDE -->
		</div>
	</div>
</div>
</div>
<?php endif; ?>

<script>
	function change_process(select_value, graph_number) {
		window.location = "<?php echo site_url('user/status/process/'.$patientcode);?>/"+(parseInt(select_value))+"/"+(parseInt(graph_number))+"#graph"+graph_number
	}
</script>