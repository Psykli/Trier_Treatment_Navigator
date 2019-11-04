$(function() {
			
	var icons = {
		header: "ui-icon-circle-arrow-e",
		activeHeader: "ui-icon-circle-arrow-s"
	};	
				
	$( "#all_patients" ).accordion({
		active: false,
		collapsible: true,
		heightStyle: "content",
		icons: icons,
		animate: {
			duration: 500
		}
	});
});

$(function() 
	$( "#tag" ).datepicker({
		dateFormat: "dd-mm-yy",
		showWeek: true,
		firstDay: 1,
		dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
		dayNamesShort: [ "Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam" ],
		dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
		monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
		monthNamesShort: [ "Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez" ],
		weekHeader: "Wo",
		//minDate: 0
	});
});

$(function() {
	$( "#tag_2" ).datepicker({
		dateFormat: "dd-mm-yy",
		showWeek: true,
		firstDay: 1,
		dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
		dayNamesShort: [ "Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam" ],
		dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
		monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
		monthNamesShort: [ "Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez" ],
		weekHeader: "Wo",
		//minDate: 0
	});
});




$(function() {
	$( "button[name = 'setDate']" ).click(function() {
		$( "#datum3" ).val( $( this ).val() );
		$( "#datum2" ).val( $( this ).val() );
		$( "#datum" ).val( $( this ).val() );
	});
});


/*
*	Die verschiedenen Slider für die "Konfrontationsübung"
*
*	@author Ruven Martin
*	@since 0.8.0
*/
$(function() {
	$( "#slider_1" ).slider({
		value: 0,
		animate: "fast",
		min: 0,
		max: 100,
		slide: function( event, ui ) {
			$( "#slider_1_amount" ).val( ui.value + " %" );
		}
	});
	$( "#slider_1_amount" ).val($( "#slider_1" ).slider( "value" ) + " %");
});

$(function() {
	$( "#slider_2" ).slider({
		value: 0,
		animate: "fast",
		min: 0,
		max: 100,
		slide: function( event, ui ) {
			$( "#slider_2_amount" ).val( ui.value + " %" );
		}
	});
	$( "#slider_2_amount" ).val( $( "#slider_2" ).slider( "value" ) + " %"  );
});

$(function() {
	$( "#slider_3" ).slider({
		value: 0,
		animate: "fast",
		min: 0,
		max: 100,
		slide: function( event, ui ) {
			$( "#slider_3_amount" ).val( ui.value + " %"  );
		}
	});
	$( "#slider_3_amount" ).val( $( "#slider_3" ).slider( "value" ) + " %"  );
});

$(function() {
	$( "#linkCollapseOne" ).click(function() {
		$( '#collapseTwo' ).hide();
		$( '#collapseThree' ).hide();
		$( '#collapseOne' ).show();
	});
	
	$( "#linkCollapseTwo" ).click(function() {
		$( '#collapseOne' ).hide();
		$( '#collapseThree' ).hide();
		$( '#collapseTwo' ).show();
	});
	
	$( "#linkCollapseThree" ).click(function() {
		$( '#collapseOne' ).hide();
		$( '#collapseTwo' ).hide();
		$( '#collapseThree' ).show();
	});
});


/*
*	Die verschiedenen Slider für die "Imaginationsübung"
*
*	@author Ruven Martin
*	@since 0.8.0
*/
$(function() {
	$( "#ex_imagin_slider_1" ).slider({
		value: 0,
		animate: "fast",
		min: 0,
		max: 100,
		slide: function( event, ui ) {
			$( "#ex_imagin_slider_1_amount" ).val( ui.value + " %" );
		}
	});
	$( "#ex_imagin_slider_1_amount" ).val($( "#ex_imagin_slider_1" ).slider( "value" ) + " %");
});

/*
* jPlayer Konfiguration für Video
*
*
* @author Ruven Martin
* @since 0.8.0
*/
$(document).ready(function(){
    $("[rel='tooltip']").tooltip({
		position: {
			my: "left top",
			at: "right+5 top-5"
		}
    });
});


function switch_language(lang){
	document.cookie = "language="+lang;
	location.reload();
}


// **** INFO Tooltip mit Bootstrap (anstatt mit tooltip standard plugin von jQuery)
// - <button type="button" class="btn btn-outline-secondary" data-toggle="tooltip" data-placement="left" title="Tooltip on left">Tooltip on left</button>