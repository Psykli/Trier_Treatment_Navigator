<div class="media bottom_spacer">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h2 class="media-heading"><?php echo lang('details_details');?>	<?php  echo "Code: ";  echo $patientcode; ?></h2>
	</div>
</div>

<ol class="breadcrumb">
	<li><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard"><?php echo lang('list_overview');?></a></li>
	<li><?php $link = $userrole.'/patient/list_all' ?>
		<?php echo anchor( $link, lang('list_list1') ); ?></li>
	<li class="active"><?php echo lang('details_details2');?></li>
</ol>

<?php 
$rechte_nn = $this -> membership_model -> is_rechte_set( $username, 'rechte_nn' );
$rechte_uebungen = $this -> membership_model -> is_rechte_set( $username, 'rechte_uebungen' ); 
?>

<div class="panel panel-default">
	<div class="panel-body">
		<ul class="nav nav-pills">		



			<!--GAS-->
			<li>
				<?php $link = 'index.php/' . 'user/Gas_Tool/index/create_gas/' . $patientcode; ?>
				<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-link" style="width:100%;">GAS</a>
			</li>

			<?php if( $userrole === 'admin' ):?>
				<li>
					<?php $link = 'index.php/' . 'user/patient/index/create_modul/' . $patientcode; ?>
					<a href="<?php echo base_url( $link ); ?>" role="button" class="btn btn-link" style="width:100%;">Übungen</a>
				</li>
			<?php endif;?>				

		</ul>
	</div>
</div>

<br/>

<div class="col-sm-8">	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang('details_notice'); ?></h3>
		</div>
		<div class="panel-body">				
			<p>
				Sie sind nicht berechtigt Feedback zu diesem Patienten zu erhalten. 
			</p>
			<p>
				<?php $link = $userrole.'/patient/list_all' ?><?php echo anchor( $link, "Zurück zur Patientenliste" ); ?>
			</p>
		</div>
	</div>

	<?php $has_gas = $this->SB_Model->has_gas($patientcode); $has_request = $this->SB_Model->has_filled_request($patientcode); ?>
	<?php if (( $userrole === 'admin' OR $userrole === 'priviledged_user' ) AND (!$has_gas OR !$has_request)): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">SB-Freigabe</h3>
			</div>
			<div class="panel-body">
				<div class="btn-group" style="width:100%;">
					<p>Das Stundenbogensystem ist nur erreichbar, wenn die Fallkonzeption bis zur 10. Sitzung und die Gas bis zur 15. Sitzung angelegt wurden. Soll es weiterhin möglich sein das Stundenbogensystem zu benutzen, so muss hier angegeben werden, bis zu welcher Sitzung diese Regel ignoriert werden kann.</p> 
					<?php if(!$has_gas):?>
						<p class="alert alert-warning"> Gas wurde nicht ausgefüllt</p>
					<?php endif;?>
					<?php if(!$has_request):?>
						<p class="alert alert-warning"> Fallkonzeption wurde nicht ausgefüllt</p>
					<?php endif;?>
					Letze beendete Sitzung: <b><?php echo $this->SB_Model->getLastInstance($patientcode);?></b> - Erlaubt bis einschließlich Sitzung: <b><span id="allowed_until"><?php echo $this->Patient_Model->get_sb_allowed($patientcode) !== null ? $this->Patient_Model->get_sb_allowed($patientcode)->allowed_until_instance : 'Nicht gesetzt';?></span></b>
					<br/>
					<label for="allowed_instance">Neue erlaubte Sitzung:</label>
					<input type="number" class="form-control" name="allowed_instance" id="allowed_instance" value="<?php echo ($this->SB_Model->getLastInstance($patientcode)+3);?>" min="<?php echo ($this->SB_Model->getLastInstance($patientcode)+1);?>">
					<br/>
					<button type="button" class="btn btn-info form-control" id="set_allowed_instance" onclick="set_allowed()">Erlaubte Sitzung setzen</button>
					<br/>
					<button type="button" class="btn btn-info form-control" id="delete_allowed_instance" onclick="delete_allowed()">Erlaubte Sitzung löschen</button>
					<br/><br/><br/>
					<p id="save_info" class="alert alert-success" style="display:none;">
						Änderung gespeichert
					</p>
					<p id="error_info" class="alert alert-danger" style="display:none;">
						Aktion konnte nicht durchgeführt werden
					</p>
				</div>
			</div>
		</div>
	<?php endif;?>
</div>

<script>
function set_allowed(){
	<?php $link = site_url('user/patient/index/set_sb_allowed'); ?>
	var allowed_instance = $('#allowed_instance').val();
	$('#set_allowed_instance').addClass('disabled');
	var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
	$.ajax({
		data: {patientcode: '<?php echo $patientcode;?>', allowed_instance: allowed_instance, csrf_test_name: csrf_token},
		type: 'POST',
		url: '<?php echo $link;?>', 
		success: function() {
			$('#set_allowed_instance').removeClass('disabled');
			$('#save_info').fadeIn(400).delay(1500).fadeOut(400);
			$('#allowed_until').text(allowed_instance);
		},
		error: function(){
			$('#set_allowed_instance').removeClass('disabled');
			$('#error_info').fadeIn(400).delay(1500).fadeOut(400);
		}         
	});
}

function delete_allowed(){
	<?php $link = site_url('user/patient/index/delete_sb_allowed'); ?>
	$('#delete_allowed_instance').addClass('disabled');
	var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
	$.ajax({
		data: {patientcode: '<?php echo $patientcode;?>', csrf_test_name: csrf_token},
		type: 'POST',
		url: '<?php echo $link;?>', 
		success: function() {
			$('#delete_allowed_instance').removeClass('disabled');
			$('#save_info').fadeIn(400).delay(1500).fadeOut(400);
			$('#allowed_until').text('Nicht gesetzt');
		},
		error: function(){
			$('#delete_allowed_instance').removeClass('disabled');
			$('#error_info').fadeIn(400).delay(1500).fadeOut(400);
		}         
	});
}
</script>
