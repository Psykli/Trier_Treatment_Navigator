<div id="member_area" class="dashboard">
    <!-- <div class="usermenu">
        <ul class="breadcrumb">
            <li><a href="#">Home</a></li>
        </ul>        
    </div><!-- end:.usermenu -->
    <div class="media bottom_spacer place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/user-home.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Administratorfunktionen</h1>
        </div>
    </div>
                
	<div class=" col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Patientenübersicht</h4>
			</div>
			<div class="panel-body">
				<ul class="list-group">
					<li class="list-group-item"><?php echo anchor(base_url() . 'index.php/admin/patient/list_all','Patientenliste'); ?></li>
					<li class="list-group-item"><?php echo anchor(base_url() . 'index.php/admin/patient/search','Nach Patienten suchen'); ?></li>
					<li class="list-group-item"><?php echo anchor(base_url() . 'index.php/admin/patient/instance_count','Erhebungsstatistik'); ?></li>
				</ul>	
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Therapeutenübersicht</h4>
			</div>
			<div class="panel-body">
				<ul class="list-group">
					<li class="list-group-item"><?php echo anchor(base_url() . '/index.php/admin/dashboard/caseload','Übersicht der Therapeuten Caseloads','Title="Übersicht der Therapeuten Caseloads"');?></li>
					<li class="list-group-item"><?php echo anchor(base_url() . 'index.php/admin/dashboard/suchend','Suchende Therapeuten','Title="Suchende Therapeuten"'); ?></li>
					<li class="list-group-item"><?php echo anchor(base_url() . 'index.php/admin/patient/index/reminds','Erinnerungen','Title="Erinnerungen"'); ?></li>
				</ul>	
				<?php echo form_open( 'admin/dashboard/caseload_all' ); ?>
					<div class="col-md-8">
					<label for="number" class="form-control-label">Caseload für n Patienten:</label>
					<select class="form-control" id="number" name="number" class="input-small">
						<option value=""></option>
						<?php for ($i = 1; $i < 50; $i++): ?>
							<?php if ($i < 23): ?>
								<option class="bg-warning text-white" value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php elseif ($i > 35): ?>
								<option class="bg-danger text-white" value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php else: ?>
								<option class="bg-success  text-white" value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php endif; ?>
						<?php endfor; ?>
					</select>
					</div>
					<br>
					<button type="submit" class="btn btn-default">PDF</button>
				</form>
			</div>
		</div>
	</div>
	
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Tools</h4>
			</div>
			<div class="panel-body">
				<ul class="list-group">
					<li class="list-group-item"><?php echo anchor( base_url() . 'index.php/admin/questionnaire_tool/','Fragenbogen-Tool' ); ?></li>
					<li class="list-group-item"><?php echo anchor( base_url() . 'index.php/admin/meeting_tool/index/overview','Zuweisungs-Tool' ); ?></li>					
					<li class="list-group-item"><?php echo anchor(base_url() . 'index.php/admin/mail/index','Admin Mail'); ?></li>
				</ul>	
				<button id="purge_button" class="btn btn-info" onclick="purge_testpatients()">Testpatienten auf Sitzung 10 zurücksetzen</button>
				<div id="save_info" class="alert alert-success" style="display:none;"> 
					Testpatienten wurden zurückgesetzt
				</div>
			</div>
		</div>
	</div>		

<script>
function purge_testpatients(){
	$('#purge_button').addClass('disabled');
	var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
	$.ajax({
	data: {csrf_test_name: csrf_token},
	type: 'POST',
	url: '<?php echo site_url(); ?>/admin/dashboard/purge_testpatients', 
	success: function() {
		$('#purge_button').removeClass('disabled');
		$('#save_info').fadeIn(400).delay(1500).fadeOut(400);
	}         
	});
}
</script>
<script>
	_paq.push(['appendToTrackingUrl', 'new_visit=1']); // (1) forces a new visit 
	_paq.push(["deleteCookies"]); // (2) deletes existing tracking cookies to start the new visit
	_paq.push(['setCustomVariable',
    // Index, the number from 1 to 5 where this custom variable name is stored
    1,
    // Name, the name of the variable, for example: Gender, VisitorType
    "User",
    // Value, for example: "Male", "Female" or "new", "engaged", "customer"
    "<?php echo $username?>",
    // Scope of the custom variable, "visit" means the custom variable applies to the current visit
    "visit"
	]);

	_paq.push(['trackPageView']);
</script>
   
