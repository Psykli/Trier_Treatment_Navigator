<div class="container">
	<div class="row">
		<div class="col-sm12">
			<h1>Willkommen im Portal<br />
				<small>
					Hier finden Sie eine Übersicht zu den verschiedenen Bereichen des Portals. <br />
					Unter <i>Nachrichten</i> könnnen Sie Nachrichten empfangen und an Ihren Therapeuten schicken. <br /> 
					Die Funktion <i>Erinnerungen</i> zeigt Ihnen an, welche Fragebögen und Übungen Sie vergessen haben.<br />
				</small>
			</h1>
		</div>
	</div>
</div>

<hr /> 

<?php if (	$datenschutz_status -> ot_studie_ziel == 0 AND
			$datenschutz_status -> ot_studie_info == 0 AND
			$datenschutz_status -> ot_studie_anonym == 0 AND
			$datenschutz_status -> ot_studie_notfall == 0 AND
			$datenschutz_status -> ot_studie_risiken == 0): ?>
    <script type="text/javascript">
        $(window).load(function(){
            $('#meinModal').modal('show');
        });
    </script>
	<script>
		$(document).ready(function(){
			$("#meinModal").modal({backdrop: 'static', keyboard: false});
		});
	</script>
<?php endif; ?>

<?php
	$qid = $this->Questionnaire_tool_model->get_questionnaire_id_by_table('ziel-fragebogen-internetinterventionen');
 	$zfi = $this->Questionnaire_tool_model->get_single_released_questionnaire($username, $qid, null, 0);
 	if(isset($zfi) AND strtotime($zfi->activation)<=time() AND $zfi->instance != 'OT01'):?>
		<script type="text/javascript">
			$(window).load(function(){
				$('#zfi_modal').modal('show');
			});
		</script>
		<script>
			$(document).ready(function(){
				$("#zfi_modal").modal({backdrop: 'static', keyboard: false});
			});
		</script>
<?php endif;?>
<div class="row">

	
	<?php $questionnaire_list = $this -> Questionnaire_tool_model -> get_released_not_finished_questionnaires( $username ); ?>   
	<?php if(isset($questionnaire_list)): ?>
		<div class="col-sm-2" id="quest_button">
			<a href="<?php echo base_url(); ?>index.php/patient/patient/index/questionnaire" type="button" class="btn btn-default btn-lg" style="width:170px;">
				<span class="glyphicon glyphicon-list-alt" style="font-size: 36px; color: #31708f"></span><br />
				<br />Fragebögen
			</a>
		</div>
	<?php endif; ?>
	
	<div class="col-sm-2">
		<a href="<?php echo base_url(); ?>index.php/patient/patient/messages/" type="button" class="btn btn-default btn-lg"  style="width:170px;">
			<span class="glyphicon glyphicon-envelope" style="font-size: 36px; color: #31708f"></span><br />
			Nachrichten <br />
			<span class="badge "><?php echo $anzahlMsg; ?> neue Nachrichte<?php echo ( $anzahlMsg != 1 ) ? 'n' : '';?> </span>
		</a>
	</div>
	
	<div class="col-sm-2">
		<a href="" type="button" class="btn btn-default btn-lg" style="width:170px;">
			<span class="glyphicon glyphicon-time" style="font-size: 36px; color: #31708f"></span><br />
			Erinnerungen <br />
			<span class="badge"><?php echo ( !isset( $remindsPlaner ) OR ( is_null( $remindsPlaner[0] ) && is_null( $remindsPlaner[1] ) && is_null( $remindsPlaner[2] ) ) ) ? 'Keine Erinnerungen' : 'Erinnerungen' ?></span>
		</a>
	</div>
		
</div> <!-- /.row -->


<!--Modal-->
<div class="modal fade" id="meinModal" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="meinModalLabel">
				Datenschutzhinweis:
            </h4>
        </div>
        <div class="modal-body">
			<p>An dieser Stelle möchten wir Sie noch kurz über das Überbrückungsangebot informieren, zu dem Sie im Rahmen einer Studie der Universität Trier Zugang erhalten. </p>	
			<br/>
			<p><u>Was habe ich von der Onlineterapie?</u></p>

			<p> Bei unserem internetbasierten Behandlungsansatz handelt es sich um ein Selbsthilfeprogramm: 
				<ul>
					<li>dessen Hauptkomponente Selbsthilfesitzungen sind, die Sie zu Hause vor Ihrem Computer selbstständig durcharbeiten können.</li> 
					<li>begleitend werden Veränderungen in Ihren Beschwerden durch Fragebögen erfasst</li>
				</ul>
			</p>
			<br/>
			<p><u>Was sind mögliche Risiken und Nebenwirkungen?</u></p>
			
			<p>Bei der Bearbeitung der Übungen kann es sein, dass Sie mit belastenden Inhalten konfrontiert werden. Dies kann bei einigen Teilnehmern zu einer vorübergehenden erhöhten Belastung, bis hin zu Dekompensationen (psychische Überlastung) führen. Innerhalb der Eingangsdiagnostik wird dieses Risiko erhoben. Teilnehmer mit einem erhöhten Risiko der Dekompensation (psychische Überlastung) werden von dieser Studie ausgeschlossen. Trotzdem bleibt ein Restrisiko, das nicht ausgeschlossen werden kann. Jede Teilnehmerin und jeder Teilnehmer muss vor Teilnahmebeginn separat bestätigen, dass er über dieses Risiko informiert wurde.</p>
			<br/>
			<p><u>Was muss ich beachten?</u></p>

			<p>Es gibt keine Aufwandsentschädigung. Die Teilnahme ist freiwillig. Die Teilnahme kann jederzeit von Ihnen ohne Angabe von Gründen und ohne dass Ihnen daraus Nachteile entstehen widerrufen werden.</p>
			<br/>
			<p><u>Was passiert mit meinen Daten?</u></p>
			<p>Die Datenschutzerklärung finden Sie unter folgendem <a href="<?php echo base_url().'pdf/Datenschutzerklaerung.pdf';?>">Link</a></p>


			<?php echo form_open( 'patient/dashboard/set_datenschutz',  array('role' => 'form' ) ); ?>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="ot_studie_ziel" value="1">
						Hiermit bestätige ich, dass ich ausreichend über das Ziel der Studie, den damit verbundenen Zeitaufwand und die Teilnahmebedingungen informiert wurde. Mir ist bekannt, dass ich jederzeit das Recht habe, ohne Angaben von Gründen meine Teilnahme an der Studie zu beenden, ohne dass mir dadurch Nachteile entstehen. 
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="ot_studie_info" value="1">
						Ich habe alle Informationen über die Studie verstanden. Ich bestätige zudem, dass ich aus freiem Willen an dieser Studie teilnehme. 
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="ot_studie_anonym" value="1">
						Mir ist auch bekannt, dass die von mir erhobenen Daten pseudonymisiert werden und dass ich jederzeit das Recht habe, die Löschung dieser Daten zu verlangen. 
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="ot_studie_notfall" value="1">
						Auch bestätige ich, dass ich mir im Fall eines Notfalles (z. B. akute Suizidalität) sofort Hilfe holen werde. 
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="ot_studie_risiken" value="1">
						Hiermit bestätige ich, dass ich über eventuelle Risiken und Nebenwirkungen, die mit der Teilnahme an dieser Studie verbunden sind, informiert wurde. Ich bin mir dieser Risiken bewusst und wurde informiert, dass die Studienleiter nicht für eventuelle Schäden haftbar gemacht werden können. 
					</label>
				</div>
				<button type="submit" class="btn btn-default">Bestätigen</button>
		</form>

        </div>
        <div class="modal-footer">
			<?php echo anchor('login/logout', 'Schließen', array('onclick' => 'piwik_logout()', 'class' => 'btn btn-default')); ?>
        </div>
        </div>
    </div>
</div>

<!--Modal-->
<div class="modal fade" id="zfi_modal" tabindex="-1" role="dialog" aria-labelledby="zfiModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="zfiModalLabel">
				Fragebogen entfernen?
            </h4>
        </div>
        <div class="modal-body">
			Wollen Sie keinen weiteren Fragebogen ausfüllen um mehr Übungen zu erhalten?
        </div>
        <div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Nein, weiterhin nutzen</button>
			<button class="btn btn-primary" data-dismiss="modal" onclick="delete_zfi()">Ja, Fragebogen entfernen</button>
        </div>
        </div>
    </div>
</div>

<script>
	function delete_zfi(){
		tryDelete(function(quest_avl){
			if(quest_avl == 'no_quests'){
				$('#ex_button').removeClass('disabled');
				$('#quest_info').addClass('hidden');
				$('#quest_button').addClass('hidden');
			}
		});
		
	}

	function tryDelete(handle){
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
		$.ajax({
			data: {csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo site_url(); ?>/patient/dashboard/delete_ot_quest/',
			success: function(data){
				handle(data)
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