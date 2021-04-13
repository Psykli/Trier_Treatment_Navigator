<div class="container">
	<div class="row">
		<div class="col-sm12">
			<h1>Willkommen im Portal<br />
				<small>
					Hier finden Sie eine Übersicht zu den verschiedenen Bereichen des Portals. <br />
					Unter <i>Nachrichten</i> könnnen Sie Nachrichten empfangen und an Ihren Therapeuten schicken. <br />
					Unter <i>Fragebögen</i> sehen Sie die zu absolvierenden Fragebögen.<br />
				</small>
			</h1>
		</div>
	</div>
</div>

<hr /> 
<div class="row">
		<div class="col-sm-2" id="quest_button">
			<a href="<?php echo base_url(); ?>index.php/patient/patient/questionnaire/<?php echo $username;?>" type="button" class="btn btn-outline-secondary btn-lg" style="width:170px;">
				<span class="fas fa-list-alt" style="font-size: 36px; color: #31708f"></span><br />
				<br />Fragebögen
			</a>
		</div>
	
	<div class="col-sm-2">
		<a href="<?php echo base_url(); ?>index.php/patient/patient/messages/" type="button" class="btn btn-outline-secondary btn-lg"  style="width:170px;">
			<span class="fas fa-envelope" style="font-size: 36px; color: #31708f"></span><br />
			Nachrichten <br />
			<span class="badge "><?php echo $anzahlMsg; ?> neue Nachrichte<?php echo ( $anzahlMsg != 1 ) ? 'n' : '';?> </span>
		</a>
	</div>
</div> <!-- /.row -->

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