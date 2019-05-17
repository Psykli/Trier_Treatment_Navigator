<div class="navbar navbar-inverse navbar-fixed-top">
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
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
						<?php //TODO move to property-file or admin-preferences ?>
                        <?php echo anchor( "patient/sb_dynamic/index", 'Stundenbögen' ) ?>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            Patientenfeedback
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <?php echo anchor('admin/dashboard', 'Funktionsübersicht'); ?>
                            </li>
                            <li class="dropdown-submenu">
                                <a tabindex="-1" href="<?php echo base_url(); ?>index.php/admin/user">Benutzer</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <?php echo anchor('admin/user/list_all', 'Benutzerliste'); ?>
                                    </li>
                                    <li>
                                        <?php echo anchor('admin/user/new_user', 'Neuer Benutzer'); ?>
                                    </li>
                                    <li>
                                        <?php echo anchor('admin/user/list_all_delete', 'Benutzer löschen'); ?>
                                    </li>
                                </ul>
                            </li><!-- end:.dropdown-submenu -->
                            <li class="dropdown-submenu">
                                <a tabindex="-1" href="<?php echo base_url(); ?>index.php/admin/patient">Patienten</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <?php echo anchor('admin/patient/list_all', 'Patientenliste'); ?>
                                    </li>
                                    <li>
                                        <?php echo anchor('admin/patient/index/list_all_set_therapeut', 'Patienten editieren'); ?>
                                    </li>
                                    <li>
                                        <?php echo anchor('admin/patient/index/instance_count', 'Erhebungsstatistik'); ?>
                                    </li>
								</ul>
							</li><!-- end:.dropdown-submenu -->
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <!--Einbinden der API zur Fehlermeldung-->
                    <?php include 'application/views/all/gogs_button.php';?>

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
                                <?php echo anchor('admin/profile', 'Profil'); ?>
                            </li>
                            <li>
                                <?php echo anchor('login/logout', 'Logout', array('onclick' => 'piwik_logout()')); ?>
                            </li>
                        </ul>
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
<div id="main_content_container">
	<div class="container">

<script>
	
	function piwik_logout(){
		_paq.push(['appendToTrackingUrl', 'new_visit=1']); // (1) forces a new visit 
		_paq.push(["deleteCookies"]); // (2) deletes existing tracking cookies to start the new visit
		
		_paq.push(['deleteCustomVariable', '1'])
	
		_paq.push(['trackPageView']);
	}
</script>

<!--Einbinden der API zur Fehlermeldung-->
<?php include 'application/views/all/gogs.php';?>