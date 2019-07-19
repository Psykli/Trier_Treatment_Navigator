<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li><?php echo anchor( 'admin/questionnaire_tool', 'Startseite' ); ?></li>
				<li class="active"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung' ); ?></li>
				<li><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung' ); ?></li>
				<li><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung' ); ?></li>
				
			</ul>
		</div>
	</div>

    <h4>Auswahl der Instanz</h4>
    <?php echo form_open( 'admin/questionnaire_tool/insert_questionnaire_batterie_patient/'.$patient -> CODE.'/'.$battery.'/'.$patient -> THERPIST , array('role' => 'form', 'class' => 'instance_input', 'id' => 'battery_form' ) ); ?>
    <div class="form-group">

        <label for="instance_battery_prefix"> Instanz des Fragebogens: </label>
        <select id="instance_battery_prefix" name="instance_prefix" class="form-control" 
        onchange="changeInstance(<?php echo $instanceOT; ?>,<?php echo $instanceZ; ?>,<?php echo $instanceSB; ?>,'#battery_form',true);">	
            <option value="WZ" <?php if($disableWZ OR $disablePR OR $disablePO){ echo 'disabled'; }?>>WZ</option>
            <option value="OT" <?php if($disablePR OR $disablePO){ echo 'disabled'; }else{echo 'selected';}?>>OT</option>
            <option value="PR" <?php if($disablePR OR $disablePO){ echo 'disabled'; }?>>PR</option>
            <option value="Z"  <?php if($disablePO){ echo 'disabled'; }elseif($disablePR AND $disableWZ){echo 'selected';}?>>Z</option>
            <option value="PO" <?php if($disablePO){ echo 'disabled'; }?>>PO</option>
            <option value="" > Stundenbogen</option>
        </select>
    </div>
    <div class="form-group">
        <label for="instance_battery"> Instanz Nummer: </label>
        <input id="instance_battery" name="instance" type="text" class="form-control" placeholder="Instanz" value="<?php echo $instanceOT; ?>">
    </div>
    <?php foreach ($questionnaires as $questKey => $quest): ?>
        <div class="form-group">
            <label for="start_battery_<?php echo $questKey;?>"> Startdatum des Fragebogen (<?php echo $quest->tablename;?>): </label>
            <input id="start_battery_<?php echo $questKey;?>" required="required" name="start_<?php echo $questKey;?>" type="date" class="form-control" placeholder="Startdatum" value="<?php echo date('Y-m-d');?>">
        </div>
        <div class="form-group">
            <label for="interval_battery_<?php echo $questKey;?>"> Anzahl der Tage bis zum nächsten Fragebogen (<?php echo $quest->tablename;?>): </label>
            <input id="interval_battery_<?php echo $questKey;?>"  name="interval_<?php echo $questKey;?>" type="number" class="form-control" placeholder="Tage" value="7">
        </div>
    <?php endforeach; ?>														
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Fragebögen speichern</button>
        <a href="<?php echo site_url('admin/questionnaire_tool/show_questionnaire_list/'.$patient -> CODE);?>" class="btn btn-default">Abbrechen</a>
    </div>
    </form>

	<script>   $('#battery_form')
					.on('init.field.fv', function(e, data) {
						// data.fv      --> The FormValidation instance
						// data.field   --> The field name
						// data.element --> The field element
			
						var icon      = data.element.data('fv.icon'),
							options    = data.fv.getOptions(),                      // Entire options
							validators = data.fv.getOptions(data.field).validators; // The field validators
			
						if (validators.notEmpty && options.icon && options.icon.required) {
							// The field uses notEmpty validator
							// Add required icon
							icon.addClass(options.icon.required).show();
						}
					})      
					.formValidation({
						framework: 'bootstrap',
						
						//Feedback icons
						icon: {
							required: 'glyphicon glyphicon-asterisk',
							valid: 'glyphicon glyphicon-ok',
							invalid: 'glyphicon glyphicon-remove',
							validating: 'glyphicon glyphicon-refresh'
						},
						
						//List of fields and their validation rules
						fields: {
							instance: {
								validators: {
									notEmpty: {
										message: 'Es wird eine Instanz benötigt!'
									},
									digits: {
										message: 'Instanz muss eine Nummer sein!'
									},
									greaterThan:{
										value: <?php echo intval($instanceOT); ?>,
										message: 'Zahl muss größer oder gleich '+<?php echo $instanceOT; ?> +' sein!'
									},
									step: {
										baseValue: 1,
										message: 'Nur 1er Schritte erlaubt!',
										step: 1
									}
								}
							},
							<?php foreach ($battery_questionnaires as $key => $value): ?>
							interval_<?php echo $key;?>: {
								validators: {
									notEmpty: {
										message: 'Es wird ein Interval benötigt!'
									},
									digits: {
										message: 'Instanz muss eine Nummer sein!'
									},
									greaterThan:{
										value: 0,
										message: 'Zahl muss größer oder gleich 0 sein!'
									},
									step: {
										baseValue: 1,
										message: 'Nur 1er Schritte erlaubt!',
										step: 1
									}
								}
							},
							<?php endforeach; ?> 
						}
					})
					.on('status.field.fv', function(e, data) {
						// Remove the required icon when the field updates its status
						var icon      = data.element.data('fv.icon'),
							options    = data.fv.getOptions(),                      // Entire options
							validators = data.fv.getOptions(data.field).validators; // The field validators
			
						if (validators.notEmpty && options.icon && options.icon.required) {
							icon.removeClass(options.icon.required).addClass('glyphicon');
						}
					});
	</script>
    <script type="text/javascript">
	function changeInstance(ot,z,sb,formID,battery = false){
		var select = null;
		if(!battery)
			select = $('#instance_prefix')[0];
		else
			select = $('#instance_battery_prefix')[0];
		var option = select[select.options.selectedIndex];

		var input = null;
		if(!battery)
			input = $('#instance')[0];
		else
			input = $('#instance_battery')[0];
		var form = $(formID);

		switch (option.value) {
			case 'Z':
				input.disabled = false;
				if(z < 10){
					input.value = '0' + z;
				} else {
					input.value = z;
				}
				form.formValidation('updateOption','instance','greaterThan','value', z);
				form.formValidation('updateOption','instance','greaterThan','message', 'Zahl muss größer oder gleich '+z+' sein!');
				form.formValidation('updateOption','instance','step','step', 5);
				form.formValidation('updateOption','instance','step','baseValue', 5);
				form.formValidation('updateOption','instance','step','message', 'Nur 5er Schritte erlaubt!');
				break;
			case 'OT':
				input.disabled = false;
				if(ot < 10){
					input.value = '0' + ot;
				} else {
					input.value = ot;
				}
				form.formValidation('updateOption','instance','greaterThan','value', ot);
				form.formValidation('updateOption','instance','greaterThan','message', 'Zahl muss größer oder gleich '+ot+' sein!');
				form.formValidation('updateOption','instance','step','step', 1);
				form.formValidation('updateOption','instance','step','baseValue', 1);
				form.formValidation('updateOption','instance','step','message', 'Nur 1er Schritte erlaubt!');
				break;
			case '':
				input.disabled = false;
				if(sb < 10){
					input.value = '0' + sb;
				} else {
					input.value = sb;
				}
				form.formValidation('updateOption','instance','greaterThan','value', sb);
				form.formValidation('updateOption','instance','greaterThan','message', 'Zahl muss größer oder gleich '+sb+' sein!');
				form.formValidation('updateOption','instance','step','step', 1);
				form.formValidation('updateOption','instance','step','baseValue', 1);
				form.formValidation('updateOption','instance','step','message', 'Nur 1er Schritte erlaubt!');
				break;
			case 'PR':
			case 'WZ':
			case 'PO':
			default:
				input.value = '';
				input.disabled = true;
				break;

		}

		form.formValidation('revalidateField','instance');

		
	}
</script>
