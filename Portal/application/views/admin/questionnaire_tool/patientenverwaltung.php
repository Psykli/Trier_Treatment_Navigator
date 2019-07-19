<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li><?php echo anchor( 'admin/questionnaire_tool', 'Startseite' ); ?></li>
				<li class="active"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung', array('class' => 'clickable') ); ?></li>
				<li><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung' ); ?></li>
                <li><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung' ); ?></li>
				
			</ul>
		</div>
	</div>
    <br/><br/><br/>
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Fragebogen für Patienten</h3>
			</div>
			<div class="panel-body">
				<p>In diesem Bereich können Sie nach einzelnen Patienten suchen oder nach Patienten, die zu einem Therapeuten gehören.</p>
				<div class="col-md-12">
					<?php echo form_open( 'admin/questionnaire_tool/patientenverwaltung', array( 'role' => 'form' ) ); ?>
						<input type="text" class="form-control" id="patientcode" name="patientcode" placeholder="Patientencode" value="<?php echo $searched_patientcode; ?>" autofocus>
						<br>
						<input type="text" class="form-control" id="therapist" name="therapist" placeholder="Therapist" value="<?php echo $searched_therapist; ?>">
						<br>
						<button type="submit" class="btn btn-primary">Suche</button>
					</form>
					<br/>
				</div>
			</div>
		</div><!-- /.panel panel-default -->
	</div><!-- /.col-md-6 -->
</div><!-- /.row -->


<div class="row">
	<div class="col-lg-12">
		<hr />

	</div>
</div>

<div class="row"> 
	<div class="col-lg-12">

		<?php 
			if( isset( $patients ) ):
		?>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th width="5%">Patientencode</th>
						<th width="5%">Therapeut</th>
						<th width="20%">Eingetragene Fragebögen</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach( $patients as $patient ): ?>
						<tr>
							<td>
								<a class="btn btn-default" href="<?php echo site_url();?>/admin/questionnaire_tool/show_questionnaire_list/<?php echo $patient -> CODE;?>"><?php echo $patient -> CODE; ?></a>
							</td>
							<td>
								<?php echo $patient -> THERPIST; ?>
							</td>
							<td>
								<?php 
									$questionnaire_released = $this -> Questionnaire_tool_model -> get_released_questionnaires( $patient -> CODE, 0 );
									$ordered_quests = array();
									if(isset($questionnaire_released)){
										foreach($questionnaire_released as $questionnaire){
											$ordered_quests[$questionnaire->tablename][] = $questionnaire;
										}
									}
								
								?>
								<?php if (isset($questionnaire_released)): ?>

									<?php foreach ($ordered_quests as $key => $quest): ?>
										<div class="dropdown">
											<button class="btn btn-default dropdown-toggle" type="button" id="questionnaire_<?php echo $key; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width: 100%;">
												<?php echo $key; ?>
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu list-group" aria-labelledby="questionnaire_<?php echo $key; ?>">
												<?php foreach ($quest as $value): ?>
													<li class="list-group-item"><span class="label label-<?php echo $value->finished ? 'success' : 'warning'; ?>"><?php echo $value->instance; ?></span><span> <?php echo $value->datum;?></span> <br/>Wird aktiviert am:<br/> <span><?php echo $value->activation;?></span><span> <a class="btn btn-xs btn-danger" href="<?php echo site_url();?>/admin/questionnaire_tool/delete_questionnaire_from_patient/<?php echo $patient -> CODE;?>/<?php echo $value->id;?>">
													<span class="glyphicon glyphicon-remove"></span></a></span>
														</li>
												<?php endforeach; ?>
											</ul>
										</div>
										<br/>
									<?php endforeach; ?>
								<?php else: ?>
									<p>Es sind keine unbearbeiteten Fragebögen bei diesem Patienten vorhanden. Bitte fügen Sie neue hinzu, falls dies erfoderlich ist. </p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>
<script>
	$('select option').each(function() {
		var thisAttr = $(this).attr('disabled');
		if(thisAttr == "disabled") {
			$(this).hide();
   		}
	});
 </script>
<script>
	//Setzt die Größe der Zellen der vorherigen Tabelleneinträge auf die gleiche Größe
	var tmp = 0;
	$('.pair').each(function (index){
		if($(this).outerHeight() > tmp){
			tmp = $(this).outerHeight();
		}
	});
	$('.pair').css('min-height',tmp);
</script>