<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool', 'Dashboard', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung', array("class" => 'nav-link active') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung', array("class" => 'nav-link') ); ?></li>
			</ul>
		</div>
	</div> 

<div class="row">
	<div class="col-sm-6"> 
		<h2> Einzelnen Fragebögen freischalten: </h2>
		<?php echo form_open( 'admin/questionnaire_tool/quest_release/'.$patientcode , array('role' => 'form', 'id' => 'quest_form', )); ?>
			<div class="form-group">
				<select class="form-control" name="quest_select" id="quest_select">
					<?php foreach($questionnaire_list as $quest):?>
						<option value="<?php echo $quest->tablename;?>"><?php echo $quest->tablename;?></option>
					<?php endforeach;?>
				</select>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Los!</button>
			</div>
		</form>

	</div>
	<div class="col-sm-6"> 
		<h2> Batterien freischalten: </h2>
		<?php echo form_open( 'admin/questionnaire_tool/battery_release/'.$patientcode , array('role' => 'form', 'id' => 'battery_form', )); ?>
			<div class="form-group">
				<select class="form-control" name="battery_select" id="battery_select">
					<?php foreach($batteries as $battery):?>
						<option value="<?php echo $battery->id;?>"><?php echo $battery->name;?></option>
					<?php endforeach;?>
				</select>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Los!</button>
			</div>
		</form>

	</div>
</div>	
									
<div class="row">
	<div class="col-md-12">
		<div class="card ">
			<div class="card-header">
				<h3 class="card-title">Fragebogen für Patienten</h3>
			</div>
			<div class="card-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Fragebogen</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($questionnaires)):?>
							<?php foreach ($sorted_quests as $table => $quest): ?>
							<tr>
								<td>
									<button class="btn btn-primary form-control" type="button" data-toggle="collapse" data-target="#table_<?php echo str_replace(' ','_',$table);?>" aria-expanded="false" aria-controls="collapseExample">
										<?php echo $table; ?>
									</button>
									<div class="collapse" id="table_<?php echo str_replace(' ','_',$table);?>">
										<div class="well">
											<?php foreach($quest as $entry):?>
												<?php if(boolval($entry->finished)):?>
													<a class="btn btn-success" href="<?php echo site_url();?>/admin/questionnaire_tool/show_questionnaire/<?php echo $patientcode;?>/<?php echo $entry->filename;?>/<?php echo $entry->instance;?>"><?php echo $entry->instance; ?></span></a>
												<?php else:?>
												<a class="btn btn-alert" disabled><?php echo $entry->instance; ?></span></a>
												<?php endif;?>
											<?php endforeach;?>
										</div>
									</div>
								
								</td>
							</tr>
							<?php endforeach; ?>
						<?php endif;?>
					</tbody>
				</table>
			</div>
		</div><!-- /.card  -->
	</div><!-- /.col-md-6 -->
