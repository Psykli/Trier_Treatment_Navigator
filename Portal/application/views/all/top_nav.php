
<?php $userrole = $this -> membership_model -> get_role( $this->data[TOP_NAV_STRING]['username']);?>
<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
		<div class="navbar-header">
			<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar_1collapse_1">
				<span class="navbar-toggler-icon"></span>
			</button>
		
			<a class="navbar-brand" href="<?php echo base_url(); ?>">Feedbacksystem</a>
		</div>
		
		<div class="collapse navbar-collapse" id="navbar_1collapse_1">
			<ul class="navbar-nav mr-auto">
				<?php echo $this->membership_model->get_navbar_content($userrole);?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown nav-item">
					<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Sprache<span class="caret"></span></a>
					<div class="dropdown-menu">
						<button class="btn btn-link dropdown-item" onclick="switch_language("de");">Deutsch</button>
						<button class="btn btn-link dropdown-item" onclick="switch_language("en");">English</button>
					</div>
            	</li>
				<?php if($userrole === 'guest'):?>
					<li>
						<?php echo anchor('login', 'Login', array('class' => 'nav-link')); ?>
					</li>
				<?php else: ?>
					<li class="dropdown nav-item">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            Willkommen <?php echo $this->data[TOP_NAV_STRING]['username']; ?>
                            <b class="caret"></b>
                        </a>
                        <div class="dropdown-menu">
							<?php echo anchor($userrole.'/profile', lang('profile'), array('class' => 'dropdown-item')); ?>
							<?php echo anchor('login/logout', 'Logout', array('class' => 'dropdown-item')); ?>
                        </div>
                    </li>
				<?php endif;?>			
			</ul>
		</div>
</nav>
<div class="container">
	<div id="header_container"></div>
</div>
<div class="parallax">
	<div class="container">


<!--Einbinden der API zur Fehlermeldung-->
<?php include 'application/views/all/gogs.php';?>

<script>
	
	function piwik_logout(){
		_paq.push(['appendToTrackingUrl', 'new_visit=1']); // (1) forces a new visit 
		_paq.push(["deleteCookies"]); // (2) deletes existing tracking cookies to start the new visit
		
		_paq.push(['deleteCustomVariable', '1'])
	
		_paq.push(['trackPageView']);
	}
</script>