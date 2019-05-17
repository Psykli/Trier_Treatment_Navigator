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
	<li class="active">Verlauf <?php if($ot) echo "OT";?></li>
</ol>  

<?php if($ot): ?>
<!-- Navigation-->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Titel und Schalter werden für eine bessere mobile Ansicht zusammengefasst -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Navigation ein-/ausblenden</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <!-- Alle Navigationslinks, Formulare und anderer Inhalt werden hier zusammengefasst und können dann ein- und ausgeblendet werden -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <!--<li><?php //echo anchor($userrole.'/patient/index/homework_introduction/'.$patient[0]->code, 'Einführung <br/> in das Übungsportal'); ?></li>-->
        <li><?php echo anchor( 'user/patient/create_modul/'.$patientcode, 'Modul <br/> erstellen' ); ?></li>
        <li><?php echo anchor('user/patient/homework/'.$patientcode, 'vorhandene <br/> Module'); ?></li>
        <?php $link = 'user/status/index/NULL/' . $patientcode . '/process_ot'; ?>
		<li class="active"><?php echo anchor($link, 'Verlauf <br/> Online Therapie' ); ?></li>
        <!--<li><?php //echo anchor( 'user/patient/index/closed_exercises/'.$patient[0]->code, 'Abgeschlossene <br/> Übungen' ); ?></li>-->
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav><!-- /.Navigation -->    
<?php endif; ?>

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
					<hr />
					<?php if(!$ot){
						$link = 'user/status/index/NULL/' . $patientcode . '/process_all';
						echo anchor( $link, 'Zur Ansicht: Fragebögen im Überblick' );
					} else {
						$link = 'user/status/index/NULL/' . $patientcode . '/process_all_ot';
						echo anchor( $link, 'Zur Ansicht: Fragebögen im Überblick' );
					} ?>						
				</div><!-- end:.panel-body -->
			</div><!-- end:.panel panel-default -->
			<!-- Der Counter wird auch als Index für den Graphen genutzt-->
			
			<?php
				//TODO finish
				//var_dump($fepData);
				//die();
			?>
			<?php $counter = 0; ?>
			<?php foreach ($graphs as $graph): ?>
				<div class="col-sm-12">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse<?php echo $counter ?>"><?php echo $graph;?></a>
								</h4>
							</div>
							<div id="collapse<?php echo $counter ?>" class="panel-collapse collapse">
								<div class="panel-body">
									<?php if ($graph === "HSCL"): ?>
									<canvas id="<?php echo $graph;?><?php echo $counter;?>" width="680px" height="400px"></canvas>
									<script>
										createHsclChart("<?php echo $graph;?><?php echo $counter;?>", "TEST<?php echo $counter;?>", <?php echo json_encode($hsclData['MEANS']);?>,
										<?php echo json_encode($hsclData['INSTANCES']);?>,<?php echo json_encode($hsclData['BOUNDARIES']);?>,
										<?php echo json_encode($hsclData['EXPECTED']);?>);
									</script>
									<?php endif; ?>
									<?php if($graph === "FEP"): ?>
										<canvas id="<?php echo $graph;?><?php echo $counter;?>" width="680px" height="400px"></canvas>
										<script>
											createFepChart("<?php echo $graph;?><?php echo $counter;?>", "<?php echo $graph;?>", <?php echo json_encode($fepData['MEANS']);?>, <?php echo json_encode($fepData['INSTANCES']);?> );
										</script>
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
				<div class="col-sm-12">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse<?php echo $counter ?>"><?php echo $name;?></a>
								</h4>
							</div>
							<div id="collapse<?php echo $counter ?>" class="panel-collapse collapse">
								<div class="panel-body">
									<canvas id="<?php echo $name;?><?php echo $counter;?>" width="680px" height="400px"></canvas>
									<script>
										createLineChart("<?php echo $name;?><?php echo $counter;?>","TEST<?php echo $counter;?>", <?php echo json_encode($mean[$tables[0]]);?>,
										 <?php echo json_encode($mean[$tables[1]]);?>,<?php echo json_encode($info[$name]);?>);
									</script>
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
	function change_process(select){
		var index = select.value;
		window.location = "<?php echo site_url('user/status/process/'.$patientcode);?>/"+(parseInt(index)+1)
	}
</script>