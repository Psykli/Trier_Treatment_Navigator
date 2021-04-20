<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<h1>Fragebögen<br />
				<small>
					Sie können auf dieser Seite einen für Sie freigeschalteten Fragebogen auswählen und ausfüllen.
				</small>
			</h1>
		</div>
	</div>
</div>

<hr /> 

<div class="container">
	<div class="row">
		<div class="col-sm-8">
			<table class="table table-hover">
				<thead>
					<tr>
						<th width="5%">ID</th>
						<th width="10%">Fragebogen</th>
						<th width="20%">Wird freigeschaltet am: </th>
						<th>Beschreibung</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $questionnaires as $questionnaire ): ?>
						<?php 
						$today = strtotime('now');
						$activation = strtotime($questionnaire->activation);
						?>
							<?php $questionnaire_data = $this -> Questionnaire_tool_model -> get_questionnaire( $questionnaire->qid ); ?>
							<tr>
								<td><?php echo ($questionnaire->id ); ?></td>
								<?php if($today >= $activation): ?>
								<td><?php echo anchor( 'patient/questionnaire/show_questionnaire/'.$questionnaire->id.'/'.$questionnaire->instance, $questionnaire_data[0]->header_name[0] ); ?> (<?php echo $questionnaire->instance; ?>)</td>
								<?php else: ?>
								<td><?php echo $questionnaire_data[0]->header_name[0];?> (Noch nicht freigeschaltet)</td>
								<?php endif; ?>
								<td><?php echo ($questionnaire->activation ); ?></td>
								<td><?php echo $questionnaire_data[0]->description[0] ; ?></td>
							</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

	</div>
</div>
