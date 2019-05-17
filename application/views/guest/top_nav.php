<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar_1collapse_1">
				<span class="sr-only">Navigation ein-/ausblenden</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span> 
			</button>
		
			<a class="navbar-brand" href="<?php echo base_url(); ?>">Feedbacksystem</a>
		</div>
		
		<div class="collapse navbar-collapse" id="navbar_1collapse_1">
			<ul class="nav navbar-nav">
				<li>
					<?php //TODO move to property-file or admin-preferences ?>
					<?php echo anchor( "patient/sb_dynamic/index", 'Stundenbögen' ) ?>
				</li>
				<li>
					<?php echo anchor( 'login', 'Portal' ); ?>
				</li>
				<li>
				</li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Sprache<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><button class="btn btn-link" onclick="switch_language('de');">Deutsch</button></li>
						<li><button class="btn btn-link" onclick="switch_language('en');">English</button></li>
					</ul>
				</li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<!--Einbinden der API zur Fehlermeldung-->
                <?php include 'application/views/all/gogs_button.php';?>
				
				<li>
					<?php echo anchor('login', 'Login'); ?>
				</li>
			</ul>
		</div>
	</div>
</div>
<header id="header_container">
	<div class="logo_container">
		<div class="container">
			<img src="<?php echo base_url(); ?>/img/header_logo.png" alt="Logo">
		</div>
	</div>
</div>

<div class="container">

<!--Einbinden der API zur Fehlermeldung-->
<?php include 'application/views/all/gogs.php';?>