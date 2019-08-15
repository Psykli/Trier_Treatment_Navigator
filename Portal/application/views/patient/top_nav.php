<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar_1collapse_1">
				<span class="sr-only">Navigation ein-/ausblenden</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span> 
			</button>
			
			<a class="navbar-brand" href="<?php echo base_url(); ?>">
				<span style="color:orange;"> TTN</span>
			</a>
		</div>
		
		<div class="collapse navbar-collapse" id="navbar_1collapse_1">
		
			<ul class="nav navbar-nav">
				<li class="dropdown">
				
					<a class="dropdown-toggle" data-toggle="dropdown">
						Übersicht
						<b class="caret"></b>
					</a>
					
				
					
				</li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Sprache<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><button class="btn btn-link" onclick="switch_language('de');">Deutsch</button></li>
						<li><button class="btn btn-link" onclick="switch_language('en');">English</button></li>
					</ul>
				</li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown">
						Willkommen <?php echo $username; ?>.
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li>
							<?php echo anchor('patient/profile/index', 'Passwort ändern'); ?>
						</li>
						<hr/>
						<li>
							<?php echo anchor('login/logout', 'Logout'); ?>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div><!-- /.container-->
</div><!-- /.navbar-->

<header>
	<div class="logo_container">
		<div class="container">
			<img src="<?php echo base_url(); ?>/img/header_logo.png" alt="Logo">
		</div>
	</div>
</header>


<div class="container">

