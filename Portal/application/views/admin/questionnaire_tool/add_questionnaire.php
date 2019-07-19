<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li><?php echo anchor( 'admin/questionnaire_tool', 'Startseite' ); ?></li>
				<li><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung' ); ?></li>
				<li class="active"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung', array('class' => 'clickable') ); ?></li>
				<li><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung' ); ?></li>
				
			</ul>
		</div>
	</div>
	<br/><br/><br/>

<?php if(isset($already_exists)): ?>
	<div class="row">
		<div class="col-sm-12">
		<?php if($already_exists == 'insert')
				$link = 'admin/questionnaire_tool/insert_new_questionnaire/1';
			  else
			  	$link = 'admin/questionnaire_tool/update_questionnaire/'.$id.'/1';
			 echo form_open( $link, array('role' => 'form', 'method' => 'post' ) ); ?>
					<input type="hidden" name="tablename" value="<?php echo $post_data[0];?>">
					<input type="hidden" name="filename" value="<?php echo $post_data[1];?>">
					<input type="hidden" name="name" value="<?php echo $post_data[2];?>">
					<input type="hidden" name="description" value="<?php echo $post_data[3];?>">		
					<input type="hidden" name="nameEngl" value="<?php echo $post_data[4];?>">	
					<input type="hidden" name="descriptionEngl" value="<?php echo $post_data[5];?>">	

				<div class="alert alert-warning"> Es existiert bereits ein Fragebogen, der diese Datei benutzt. 
				<?php if($already_exists == 'insert'):?>Soll er trotzdem erstellt werden? 
				<?php else: ?> Soll der Fragebogen trotzdem aktualisiert werden? <?php endif;?>
				<button type="submit" class="btn btn-danger"><?php if($already_exists == 'insert'):?>Fragebogen hinzufügen<?php else: ?>Fragebogen ändern <?php endif;?></button></div>
			</form>
		</div>
	</div>
<?php endif;?>
	<div class="row">	
        <div class="col-sm-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Vorhandenen Fragebögen</h3>
				</div>
				<div class="panel-body">
					<table class="table table-hover" id="questionnaires">
						<thead>
							<tr>
								<th width="3%">ID</th>
								<th width="15%">Tabellenname</th>
								<th width="20%">Dateiname</th>
								<th width="15%">Name</th>
								<th>Beschreibung</th>
								<?php if(ENVIRONMENT == 'development'):?>
								<th>Ändern</th>
								<?php endif;?>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $all_questionnaire as $questionnaire ): ?>
								<?php 
									$name = NULL; $description = NULL;
									if ( isset($lang) AND $lang === 'english' ){  //TODO Cookie-Abfrage implemenieren
										$name = $questionnaire->header_name[1]; 
										$description = $questionnaire->description[1] ; 
									}
									else{ 
										// default is 'german'
										$name = $questionnaire->header_name[0] ; 
										$description = $questionnaire->description[0] ; 
									}
								?>
								<tr>
									<td><?php echo ( $questionnaire->id ); ?></td>
									<td><?php echo ( $questionnaire->tablename ); ?></td>
									<td><?php echo ( $questionnaire->filename ); ?></td>
									
									<td><?php echo ( $name ); ?></td>
									<?php 
										$max = 10;
										$str_array = explode(" ",$description,$max+1);
										unset($str_array[$max]);
										$short_str = implode(" ",$str_array);
										if(count(explode(" ",$description)) > $max){
											$short_str .= "[...]";
										}
									?>
									<td><?php echo ( $short_str ); ?></td>
									<?php if(ENVIRONMENT == 'development'):?>
									<td>
										<a href="<?php echo site_url('admin/questionnaire_tool/edit_questionnaire/'.$questionnaire->id);?>" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
									</td>
									<?php endif;?>
								</tr>								
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">XML hinzufügen</h3>
				</div>
				<div class="panel-body">
					<?php echo form_open_multipart('admin/questionnaire_tool/do_upload' ); ?>
						<input type="file" name="userfile" size="15" />
						<br><br>
						<input type="submit" value="XML einfügen" />
					</form>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">XML erstellen</h3>
				</div>
				<div class="panel-body">
					<form method="GET" action="<?php echo site_url('admin/questionnaire_tool/create_questionnaire/'); ?>">
						<input type="submit" value="XML erstellen">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(function() { 
		$('#questionnaires').DataTable({
			scrollX: true,
			dom: 'frtip'
		});   
	});
	
</script>