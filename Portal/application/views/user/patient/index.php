<div id="member_area" class="patient">
    <div class="media bottom_spacer_50px place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient-index.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Patienten</h1>
        </div>
    </div>
    <div class="menu">
        <ul class="breadcrumb">
            <li class="active">Patienten</li>
        </ul>        
    </div><!-- end:.usermenu -->
    <div class="dashrow status">
        <h2>Funktionsübersicht</h2>
        <a class="pull-left" href="<?php echo base_url(); ?>index.php/user/patient/list_all">
            <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
        </a> 
        
        <div class="media-body">
            <h3><?php echo anchor( "user/patient/list_all", 'Patientenliste' ); ?></h3>
        </div>
    </div><!-- end:.dashrow -->
</div><!-- end:#member_area -->