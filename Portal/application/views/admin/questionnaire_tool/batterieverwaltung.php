<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool', 'Dashboard', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung', array("class" => 'nav-link active') ); ?></li>
			</ul>
		</div>
	</div>
	<br/><br/><br/>
	<div class="row">	
        <div class="col-sm-8">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">Vorhandene Fragebogenbatterien</h3>
				</div>
				<div class="card-body">
					<table class="table table-hover">
						<thead>
							<tr>
                                <th>Löschen</th>
								<th width="3%">ID</th>
								<th width="20%">Name</th>
								<th>Fragebögen</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $all_batteries as $batterie ): ?>
								<tr>
                                    <th>
                                        <?php $link = base_url() . 'index.php/admin/questionnaire_tool/delete_batterie/'.$batterie->id; ?>
										<a href="<?php echo $link; ?>" class="btn btn-danger btn-sm" type="button"><i class="fas fa-trash-alt"></i></a>
                                    </th>
									<td><?php echo ( $batterie->id ); ?></td>
									<td><?php echo ( $batterie->name ); ?><br/><span id="sb-label_<?php echo $batterie->id;?>" class="badge badge-success sb-label" <?php echo $batterie->is_standard ? '' : 'style="display:none;"';?>>SB Standard</span></td>
									<td>
									<input type="radio" id="gas_<?php echo $batterie->id; ?>_-1" name="gas<?php echo $batterie->id;?>" onchange="set_gas(this,<?php echo $batterie->id;?>,-1 )" <?php if($batterie->gas_section == -1) echo 'checked';?>>Kein GAS
                                    
									<?php $questionnaires_batterie = $questionnaires_batteries[$batterie -> id]; ?>   
									<?php $z_batterie = $z_batteries[$batterie -> id]; ?>   
                                        <?php $index=0;
											  $section_names = $section_names_collection[$batterie -> id];
											  $section_name_array = explode(';',$section_names);
										?>
										<?php for ($i=0; $i < $batterie->sections; $i++):?>
											<input class="form-control section_name_<?php echo $batterie->id;?>" type="text" id="section_<?php echo $batterie->id;?>_<?php echo $i+1;?>" name="section_<?php echo $i+1;?>" placeholder="Sektion <?php echo $i+1;?>" value="<?php if($i < sizeof($section_name_array)){echo $section_name_array[$i];}?>">
											<input type="radio" id="gas_<?php echo $batterie->id; ?>_<?php echo $i;?>" name="gas<?php echo $batterie->id;?>" onchange="set_gas(this,<?php echo $batterie->id;?>,<?php echo $i;?> )" <?php if($batterie->gas_section == $i) echo 'checked';?>>GAS
											<ul style="min-height:10px;" class="list-group sortable_<?php echo $batterie->id;?> sortable_sections<?php echo $batterie->id;?>" id="section<?php echo $batterie->id.'_'.$i;?>">
												<?php if (isset($questionnaires_batterie)){
													 for ($j=$index; $j < sizeof($questionnaires_batterie); $j++):?>
													<?php if($questionnaires_batterie[$j]->section == $i AND intval($questionnaires_batterie[$j]->is_Z) != 1):?>
													<li class="list-group-item" id="<?php echo 'item_'.$questionnaires_batterie[$j]->id;?>">
														<?php $link = base_url() . 'index.php/admin/questionnaire_tool/delete_questionnaire_from_battery/'.$batterie->id.'/'.$questionnaires_batterie[$j]->id; ?>
														<a href="<?php echo $link; ?>" class="btn btn-danger btn-sm" type="button"><i class="fas fa-trash-alt"></i></a>
														<?php echo ( $questionnaires_batterie[$j]->tablename ); ?>
													</li>
													<?php $index++;?>
													<?php endif;?>												
												<?php endfor;} ?>
											</ul>
											<br/>
										<?php endfor;?>
										<p>Zwischenmessungen</p>
										<ul style="min-height:10px;" class="list-group sortable_<?php echo $batterie->id;?>  sortable_Z<?php echo $batterie->id;?>" id="section<?php echo $batterie->id.'_Z';?>">
										<?php if(isset($z_batterie)){
                                            for($i=0; $i < sizeof($z_batterie); $i++):?>
											<li class="list-group-item" id="<?php echo 'item_'.$z_batterie[$i]->id;?>">
														<?php $link = base_url() . 'index.php/admin/questionnaire_tool/delete_questionnaire_from_batterie/'.$batterie->id.'/'.$z_batterie[$i]->id; ?>
														<a href="<?php echo $link; ?>" class="btn btn-danger btn-sm" type="button"><i class="fas fa-trash-alt"></i></a>
														<?php echo ( $z_batterie[$i]->tablename ); ?>
											</li>
										<?php endfor;} ?>
										</ul>
										<br/>
										<a class="btn btn-outline-secondary" href="<?php echo site_url();?>/admin/questionnaire_tool/add_section/<?php echo $batterie->id;?>">Sektion hinzufügen</a>
										<a class="btn btn-outline-secondary" href="<?php echo site_url();?>/admin/questionnaire_tool/delete_section/<?php echo $batterie->id;?>">Sektion entfernen</a>
										
										<button id="save_changes<?php echo $batterie->id; ?>" class="btn btn-outline-secondary" onclick="save_changes(<?php echo $batterie->id;?>)"> Änderungen Speichern </button>
										<div id="save_info<?php echo $batterie->id; ?>" class="alert alert-success" style="display:none;">
											Änderungen gespeichert!
										</div>
                                            <hr/>
                                            <?php echo form_open( 'admin/questionnaire_tool/add_questionnaire_to_battery', array('role' => 'form' ) ); ?>
                                                <input type="hidden" name="bid" value="<?php echo ( $batterie->id ); ?>" />
                                                <select class="form-control" id="qid" name="qid">
                                                    <option default>Bitte Fragebogen auswählen</option>
                                                    <?php foreach( $questionnaire_list as $questionnaire ): ?>
                                                        <option value="<?php echo ( $questionnaire->id ); ?>"><?php echo ( $questionnaire->tablename ); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <br/>
                                                <button type="submit" class="btn btn-outline-secondary">Fragebogen hinzufügen</button>
                                            </form>
											<a class="btn btn-outline-secondary" href="<?php echo site_url('admin/questionnaire_tool/batterie_feedback/'.$batterie->id);?>"> Feedbackseite gestalten </a>
											<br/>
											<button type="button" class="btn btn-info" onclick="make_standard(<?php echo $batterie->id;?>);"> Als SB Standard setzen </button>
                                        
                                    </td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">Fragebogenbatterien hinzufügen</h3>
				</div>
				<div class="card-body">
					<p>Hier sehen Sie neue Fragebogenbatterien einfügen.</p>
					<?php echo form_open( 'admin/questionnaire_tool/insert_new_batterie', array('role' => 'form', 'id' => 'batterieverwaltung' ) ); ?>
						<div class="input-group">
							<span class="input-group-addon"><i class="fas fa-edit"></i></span>
							<input type="text" class="form-control" id="name" name="name" placeholder="Name">
						</div>
						<br/>
						<button type="submit" class="btn btn-outline-secondary">Fragebogenbatterien hinzufügen</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Form Validation-->
<script>
	function save_changes(bid){
		$('#save_order'+bid).addClass('disabled');
		var data = "";
		$('.sortable_sections'+bid).each(function(){
			var tmp = $('#'+this.id);
			if(data.length != 0)
				data += '?';
			data += tmp.sortable('serialize', {key: 'order'});
		});

		var dataZ = $('.sortable_Z'+bid).sortable('serialize', {key: 'order'});
		
		var section_names = "";
		var counter = 1;
		$('.section_name_'+bid).each(function(){
			var tmp = $('#'+this.id);
			if(tmp.val().length == 0){
				section_names += 'Sektion '+counter;
			} else {
				section_names += tmp.val()+';';
			}
			counter++;
		})
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';

		// POST to server using $.post or $.ajax
		$.ajax({
			data: {order: data, orderZ: dataZ, section_names: section_names, csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo site_url(); ?>/admin/questionnaire_tool/save_changes/'+bid, 
			success: function() {
			$('#save_changes'+bid).removeClass('disabled');
			$('#save_info'+bid).fadeIn(400).delay(1500).fadeOut(400);
			}         
		});
	}

	function set_gas(gas,bid,sid){
		
		var checked = gas.checked ? sid : -1;
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';

		// POST to server using $.post or $.ajax
		$.ajax({
			data: {checked: checked, csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo site_url(); ?>/admin/questionnaire_tool/set_gas/'+bid, 
		});
	}

	function set_quest_type(isZ,hid){
		
		var checked = isZ.checked ? 1 : 0;
		if(isZ.checked){
			$("label[for='"+isZ.id+"']").addClass("active");
		} else {
			$("label[for='"+isZ.id+"']").removeClass("active");
		}
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';

		// POST to server using $.post or $.ajax
		$.ajax({
			data: {checked: checked, csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo site_url(); ?>/admin/questionnaire_tool/set_quest_type/'+hid, 
		});
	}

	function make_standard(bid){
		var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';

		// POST to server using $.post or $.ajax
		$.ajax({
			data: {csrf_test_name: csrf_token},
			type: 'POST',
			url: '<?php echo site_url(); ?>/admin/questionnaire_tool/set_as_standard_battery/'+bid, 
			success: function() {
				$('.sb-label').hide();
				$('#sb-label_'+bid).show();
			}
		});
	}

    $(document).ready(function() {     

		<?php foreach ($all_batteries as $batterie):?>
		 $('.sortable_<?php echo $batterie->id;?>').sortable({
			axis: 'y',
			connectWith:'.sortable_<?php echo $batterie->id;?>'
		});
		<?php endforeach;?>
    });
</script>