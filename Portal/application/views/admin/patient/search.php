
<div class="media bottom_spacer_50px place_headline">
	<a class="pull-left">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading">Patientenliste</h1>
	</div>
</div>

<nav class="menu">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="../dashboard">Dashboard</a></li>
		<li class="breadcrumb-item active">Liste</li>
	</ol>        
</nav><!-- end:.usermenu -->     
	

<div class="row">
	<div class="col-lg-3">
		<?php echo form_open( 'admin/patient/search', array( 'role' => 'form' ) ); ?>
			<div class="input-group">
				<input type="text" class="form-control" id="patientcode" name="patientcode" placeholder="Suche nach Patientencode..." <?php if( isset( $searched_patientcode ) ) { echo "value='$searched_patientcode'"; } ?> required autofocus >
				<span class="input-group-btn">
					<button type="submit" class="btn btn-outline-secondary">Los!</button>
				</span>
			</div><!-- /input-group -->
		</form>
	</div><!-- /.col-lg-6 -->
</div><!-- /.row -->


<div class="row">
	<div class="col-lg-12">
		<hr />
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<?php if( !is_null( $patient_data ) ):?>
			<script>
				/* Add basic pagination and ordering to the user table.
				   The search feature is disabled because it may confuse the user if there's more than one search field.
				*/
				$(document).ready(function(){
					$('#searchResultTable').DataTable( { 
						searching: false
					});
				});
			</script>

			<table id="searchResultTable" class="table">
				<thead>
					<tr>
						<th>Patientcode</th>
						<th>Login erstellt?</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($patient_data as $key => $patient):?>
					<tr>
						<?php $link = 'user/patient/list/' . $patient->code; ?>
                        <td><?php echo anchor( $link, $patient->code ); ?></td>
						<td><?php echo ( $patient_login_exists[$key] ) ? 'Ja' : 'Nein'; ?></td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
		<?php elseif( isset( $searched_patientcode ) ): ?>
			<div class="alert alert-info" role="alert">
				Den Patient mit dem Code <b><?php echo $patientcode; ?></b> gibt es nicht.
			</div>
		<?php endif; ?>
	</div>
</div>