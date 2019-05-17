<div class="media-body">
	<h1 class="media-heading">Patienten</h1>
</div>
	
<div class="menu">
	<ul class="breadcrumb">
		<li class="active">Patienten</li>
	</ul>        
</div><!-- end:.usermenu -->

<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<h2>Funktionsübersicht</h2>
		</div>
		
		<div class="col-sm-12">
			
			<div class="function_container">
				<div class="icon">
					<a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/patient/list_all">
						<img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
					</a> 
				</div>
				
				<div class="media-body head">
					<h3><?php echo anchor( 'admin/patient/new_patientlogin', 'Neuer Patientenlogin' ); ?></h3>
				</div>
				<div class="desc">
					Einen neuen Patientlogin anlegen
				</div>
			</div>
        
			<div class="function_container">
				<div class="icon">
					<a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/patient/list_all">
						<img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
					</a> 
				</div>
				
				<div class="media-body head">
					<h3><?php echo anchor( "$userrole/patient/list_all", 'Patientenliste' ); ?></h3>
				</div>
				<div class="desc">
					Liste aller Patienten der Ambulanz.
				</div>
			</div>
			
			<!--
			-----------------------------------
			| Es war mal angedacht, die Therapeutenzuweisung zu Patienten über das Portal zu regeln.
			| Derzeit soll es aber nicht umgesetzt werden.
			-----------------------------------------------
			
			<div class="function_container">
				<div class="icon">
					<a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/patient/index/list_all_set_therapeut">
						<img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
					</a>
				</div>
				
				<div class="media-body head">
					<h3><?php echo anchor( "$userrole/patient/list_all_set_therapeut", 'Patienten editieren' ); ?></h3>
				</div>
				<div class="desc">
					Eine Liste aller Datensätze mit Bearbeitungsmöglichkeiten.
				</div>
			</div>
			-->

			<div class="function_container">
				<div class="icon">
					<a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/patient/list_all">
						<img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/gnumeric.png" data-src="holder.js/32x32">
					</a>
				</div>
				
				<div class="media-body head">
					<h3><?php echo anchor( "$userrole/patient/instance_count", 'Erhebungsstatistik' ); ?></h3>
				</div>
				<div class="desc">
					Aktuelle Erhebungsstatistik.
				</div>
			</div>
		</div>
	</div>
</div>