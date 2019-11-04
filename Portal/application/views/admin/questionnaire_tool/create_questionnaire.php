<div class="row">
	<div class="col-sm-12">
		<h3>Fragebogen-Tool</h3>
		<ul class="nav nav-tabs" role="tablist">
			<li><?php echo anchor( 'admin/questionnaire_tool', 'Dashboard' ); ?></li>
			<li><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung' ); ?></li>
			<li class="active"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung' ); ?></li>
			<li><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung' ); ?></li>
		</ul>
	</div>
</div>

<div>
<?php if( isset($creation_error) ): ?>
<br>
<b>
	<?php echo lang('questionnnaire_tool_missing_data'); ?>
</b>
<?php endif; ?>
<br><br>
<b>
	<?php echo lang('questionnnaire_tool_creation_note'); ?>
</b>
<br><br><br>
</div>

<select name="lang" onchange="setData(this.value);">
	<?php foreach($languages as $key => $lang):?>
		<option value="<?php echo $key;?>"><?php echo $lang;?></option>
	<?php endforeach;?>
</select>
<div id="formbuilder"></div>

<script>

String.prototype.nl2br = function()
{
	/*
		Converts all occurrences of \n in a String to <br />
		Example:
		var myString = "line1\nline2";
		myString = myString.nl2br();
	*/
  return this.replace(/\n/g, "<br />");
}

var fields = [{
  label: 'Slider',
  attrs: {
    type: 'slider'
  },
  icon: '<span class="fas fa-arrows-alt-h"></span>'
}];

var templates = {
  slider: function(fieldData) {
    return {
      field: '<input id="'+fieldData.name+'" type="range">'
    };
  },
  'checkbox-group': function(fieldData) {
	  var fieldString = '';
	  fieldData.values.forEach(function(element) {
		  if(!element.selected){
			fieldString += '<span>'+element.label+'</span> <input id="'+fieldData.name+'_'+element.value+'" name="'+element.value+'" type="text"></br>';
		  } else {
			fieldString += '<span>'+element.label+'</span> <input id="'+fieldData.name+'_'+element.value+'" name="'+element.value+'" type="checkbox"></br>';
		  }
	  });
	return {
		field: fieldString
	}
  }
};

function fixCheckedPropForField (fld, fieldName) {
    // Retrieve the Checkbox as a jQuery object
    $checkbox = $(".fld-"+ fieldName, fld);
    
    // According to the value of the attribute "value", check or uncheck
    if($checkbox.val() == "true"){
        $checkbox.attr("checked", true);
    }else{
        $checkbox.attr("checked", false);
    }
};

var json_array = <?php echo json_encode($fields);?>;
var json = json_array[0];
var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
var newAttr = {
	parentName:{
		label: 'Parent Name',
		placeholder: 'Column Name der Parent Frage',
		description: 'Falls die Frage bedingt durch eine andere eingeblendet werden soll'	
	},
	parentTriggers:{
		label: 'Triggers',
		value: '',
		placeholder: 'Bitte durch Komma getrennt die Trigger Werte eingeben (z.B. 1,2,3)'
	}
};
var options = {
	dataType: 'json',
	formData: JSON.stringify(json),
	fields: fields,
	templates: templates,
	actionButtons: [{
		id:'saveCreatedXml',
		className: 'btn btn-success',
		label: '<span class="fas fa-file"></span>',
		type: 'button',
		events:{
			click: function(){
				json_array = JSON.parse(formBuilder.actions.getData('json', true));
				$.ajax({
					//TODO process the redirect to edit_questionnaire page
					data: { form: json_array , table: '<?php echo $table;?>', languages: '<?php echo json_encode($languages);?>', csrf_test_name: csrf_token},
					type: 'POST',
					url: '<?php echo site_url(); ?>/admin/Questionnaire_tool/save_created_xml',
					complete: function(response) {
						window.location.replace(response.responseText); 
					}
				});
			}
		}

	}],
	typeUserAttrs: {
		text: {
			popover: {
				label: 'Popover',
				type: 'checkbox'
			},
			size:{
				label: 'Size',
				type: 'number'
			},
			parentName:{
				label: 'Parent Name',
				placeholder: 'Column Name der Parent Frage',
				description: 'Falls die Frage bedingt durch eine andere eingeblendet werden soll'	
			},
			parentTriggers:{
				label: 'Triggers',
				value: '',
				placeholder: 'Bitte durch Komma getrennt die Trigger Werte eingeben (z.B. 1,2,3)'
			}
		},
		'radio-group': newAttr,
		'checkbox-group': {
			parentName:{
				label: 'Parent Name',
				placeholder: 'Column Name der Parent Frage',
				description: 'Falls die Frage bedingt durch eine andere eingeblendet werden soll'	
			},
			parentTriggers:{
				label: 'Triggers',
				value: '',
				placeholder: 'Bitte durch Komma getrennt die Trigger Werte eingeben (z.B. 1,2,3)'
			},
			optional: {
				label: 'Optional',
				type: 'checkbox'
			}
		},
		slider: {
			minText: {
				label: 'Minimum Text',
				type: 'text'
			},
			maxText: {
				label: 'Maximum Text',
				type: 'text'
			},
			minVal: {
				label: 'Minimum Value',
				type: 'number'
			},
			maxVal: {
				label: 'Maximum Value',
				type: 'number'
			},
			parentName:{
				label: 'Parent Name',
				placeholder: 'Column Name der Parent Frage',
				description: 'Falls die Frage bedingt durch eine andere eingeblendet werden soll'	
			},
			parentTriggers:{
				label: 'Triggers',
				value: '',
				placeholder: 'Bitte durch Komma getrennt die Trigger Werte eingeben (z.B. 1,2,3)'
			}
		},
		textarea: {
			cols:{
				label: 'Columns',
				type: 'number'
			},
			optional: {
				label: 'Optional',
				type: 'checkbox'
			},
			parentName:{
				label: 'Parent Name',
				placeholder: 'Column Name der Parent Frage',
				description: 'Falls die Frage bedingt durch eine andere eingeblendet werden soll'	
			},
			parentTriggers:{
				label: 'Triggers',
				value: '',
				placeholder: 'Bitte durch Komma getrennt die Trigger Werte eingeben (z.B. 1,2,3)'
			}
		},
		number: newAttr
	},
	typeUserEvents: {
        text: {
            onadd: function (fld) {
                fixCheckedPropForField(fld, "popover");
            }
        },
		textarea: {
			onadd: function(fld) {
				fixCheckedPropForField(fld, "optional");
			}
		},
		'checkbox-group': {
			onadd: function(fld) {
				fixCheckedPropForField(fld, "optional");
			}
		}
    }
};
var fbTemplate = document.getElementById('formbuilder');
var formBuilder = $(fbTemplate).formBuilder(options);
var langIndex = 0;


function setData(index){
	json_array[langIndex] = JSON.parse(formBuilder.actions.getData('json', true));

	formBuilder.actions.setData(JSON.stringify(json_array[index]));
	langIndex = index;
}

</script>