<div class="media bottom_spacer_50px place_headline">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading">Nachrichten</h1> 	
	</div>
</div>

<nav class="menu">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('index.php/'.$userrole.'/dashboard');?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Nachrichten</li>
    </ol>        
</nav><!-- end:.usermenu -->

<div class="row">
	<div class="col">
		<a class="btn btn-outline-primary form-control clickable" id="linkMsgModal" data-toggle="modal" data-target="#msgModal">
			Neue Nachricht schreiben
		</a>

		<br><br>

		<div class="accordion" id="accordion">
			<div class="card">
				<a id="linkCollapseOne" data-toggle="collapse" href="#collapseOne" class="remove_text_decoration" role="button" aria-expanded="false" aria-controls="collapseOne">
					<div class="card-header" id="headingOne">
						<h4 class="card-title">
							<span class="fas fa-envelope"></span> Ungelesene Nachrichten <span class="badge pull-right"><?php echo $anzahlUnreadMsg; ?></span>
						</h4>
					</div>
				</a>
				
				<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body">
						<?php if( $anzahlUnreadMsg > 0 ): ?>
							<caption><strong>Ungelesene Nachrichten </strong><span class="badge"><?php echo $anzahlUnreadMsg; ?></span></caption>
							<br><br>
							<script>
								$(document).ready(function(){
									$('#unreadMessagesTable').DataTable( {
										"order": [],
										"columnDefs": [
											{
												//Disable the ability to order the first column (message date column) https://datatables.net/reference/option/columns.orderable
												//Ordering by message date isn't working correctly because of the supplied date format (10-03-2019 15:27 would get sorted before 12-08-2019 14:52).
												//So the order of the messages is correct by default (date descending as returned from the database) and further ordering of it is disabled so the user doesn't get confused.
												"orderable": false,
												"targets": 0
											}
										]
									} );
								});
							</script>
							<table id="unreadMessagesTable" class="table table-striped">
								<thead>
									<tr>
										<th style="width: 100px;">Datum</th>
										<th style="width: 100px;">Von</th>
										<th style="width: 150px;">Betreff</th>
										<th>Nachricht</th>
									</tr>
								</thead>
								<tbody>
									<?php $this->load->helper('text');
										foreach( $receivedMsgs as $msg ): ?>
											<?php if( $msg->status == 0 ): ?>
												<tr>
													<td><?php echo date( 'd-m-Y <\b\r> H:i', strtotime( $msg->datum ) ); ?></td>
													<td><?php echo ( !empty( $msg->sender ) )? $msg->sender : 'Kein Sender'; ?></td>
													<td><strong><?php echo ( !empty( $msg->betreff ) )? anchor( 'user/patient/showMessage/'.$msg->id, $msg->betreff ) : 'Kein Betreff'; ?></strong></td>
													<td><?php echo character_limiter( $msg->nachricht, 200).' '.anchor( 'user/patient/showMessage/'.$msg->id ,'weiterlesen'); ?></td>
												</tr>		
										<?php endif; ?>
									<?php endforeach; ?>
								<tbody>
							</table>
						<?php else: ?>
							<div class="alert alert-info">Sie haben zur Zeit keine ungelesenen Nachrichten.</div>
						<?php endif; ?>
					</div>
				</div><!-- collapseOne -->
			</div>

			<div class="card">
				<a id="linkCollapseTwo" data-toggle="collapse" href="#collapseTwo" class="remove_text_decoration" role="button" aria-expanded="false" aria-controls="collapseTwo">
					<div class="card-header" id="headingTwo">
						<h4 class="card-title">
							<span class="fas fa-cloud-download-alt"></span> Gelesene Nachrichten <span class="badge pull-right"><?php echo $anzahlReadMsg; ?></span>
						</h4>
					</div>
				</a>

				<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">	
					<div class="card-body">
						<?php if( $anzahlReadMsg > 0 ): ?>
							<caption><strong>Gelesene Nachrichten </strong><span class="badge"><?php echo $anzahlReadMsg; ?></span></caption>
							<br><br>
							<script>
								$(document).ready(function(){
									$('#readMessagesTable').DataTable( {
										"order": [],
										"columnDefs": [
											{
												//Disable the ability to order the first column (message date column) https://datatables.net/reference/option/columns.orderable
												//Ordering by message date isn't working correctly because of the supplied date format (10-03-2019 15:27 would get sorted before 12-08-2019 14:52).
												//So the order of the messages is correct by default (date descending as returned from the database) and further ordering of it is disabled so the user doesn't get confused.
												"orderable": false,
												"targets": 0
											}
										]
									} );
								});
							</script>
							<table id="readMessagesTable" class="table table-striped">
								<thead>
									<tr>
										<th style="width: 100px;">Datum</th>
										<th style="width: 100px;">Von</th>
										<th style="width: 150px;">Betreff</th>
										<th>Nachricht</th>
									</tr>
								</thead>
								
								<tbody>
									<?php $this->load->helper('text');
									foreach( $receivedMsgs as $msg ): ?>
										<?php if( $msg->status == 1 ): ?>
											<tr>
												<td><?php echo date( 'd-m-Y <\b\r> H:i', strtotime( $msg->datum ) ); ?></td>
												<td><?php echo ( !empty( $msg->sender ) )? $msg->sender : 'Kein Sender'; ?></td>
												<td><?php echo ( !empty( $msg->betreff ) )? anchor( 'user/patient/showMessage/'.$msg->id, $msg->betreff ) : 'Kein Betreff'; ?></td>
												<td><?php echo character_limiter( $msg->nachricht, 100).' '.anchor( 'user/patient/showMessage/'.$msg->id ,'weiterlesen'); ?></td>
											</tr>		
										<?php endif; ?>
									<?php endforeach; ?>
								<tbody>
							</table>
						<?php else: ?>
							<div class="alert alert-info">Sie haben zur Zeit keine gelesene Nachrichten.</div>
						<?php endif; ?>
					</div>
				</div><!-- collapseTwo -->
			</div>

			<div class="card">
				<a id="linkCollapseThree" data-toggle="collapse" href="#collapseThree" class="remove_text_decoration" role="button" aria-expanded="false" aria-controls="collapseThree">
					<div class="card-header" id="headingThree">
						<h4 class="card-title">
							<span class="fas fa-cloud-upload-alt"></span> Gesendete Nachrichten <span class="badge pull-right"><?php echo $anzahlSentMsg; ?></span>
						</h4>
					</div>
				</a>

				<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
					<div class="card-body">
						<?php if( $anzahlSentMsg > 0 ): ?>
							<caption><strong>Gesendete Nachrichten </strong><span class="badge"><?php echo $anzahlSentMsg; ?></span></caption>
							<br><br>
							<script>
								$(document).ready(function(){
									$('#sentMessagesTable').DataTable( {
										"order": [],
										"columnDefs": [
											{
												//Disable the ability to order the first column (message date column) https://datatables.net/reference/option/columns.orderable
												//Ordering by message date isn't working correctly because of the supplied date format (10-03-2019 15:27 would get sorted before 12-08-2019 14:52).
												//So the order of the messages is correct by default (date descending as returned from the database) and further ordering of it is disabled so the user doesn't get confused.
												"orderable": false,
												"targets": 0
											}
										]
									} );
								});
							</script>
							<table id="sentMessagesTable" class="table table-striped">
								<thead>
									<tr>
										<th style="width: 100px;">Datum</th>
										<th style="width: 100px;">Empfänger</th>
										<th style="width: 150px;">Betreff</th>
										<th>Nachricht</th>
									</tr>
								</thead>
								<tbody>
									<?php $this->load->helper('text');
									foreach( $sentMsgs as $msg ): ?>
											<tr>
												<td><?php echo date( 'd-m-Y <\b\r> H:i', strtotime( $msg->datum ) ); ?></td>
												<td><?php echo ( !empty( $msg->receiver ) )? $msg->receiver : 'Kein Empfänger'; ?></td>
												<td><?php echo ( !empty( $msg->betreff ) )? anchor( 'user/patient/showMessage/'.$msg->id, $msg->betreff ) : 'Kein Betreff'; ?></td>
												<td><?php echo character_limiter( $msg->nachricht, 100).' '.anchor( 'user/patient/showMessage/'.$msg->id ,'weiterlesen'); ?></td>
											</tr>		
									<?php endforeach; ?>
								<tbody>
							</table>
						<?php else: ?>
							<div class="alert alert-info">Sie haben noch keine Nachrichten gesendet.</div>
						<?php endif; ?>
					</div>
				</div><!-- collapseThree -->
			</div>
		</div><!--/.accordion -->
	</div><!--/.col -->			
</div><!--/.row -->

<!-- Modal -->
<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open( 'user/patient/send_msg/', array('role'=>'form') ); ?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="meinModalLabel">Neue Nachricht</h4>
				</div>
				<div class="modal-body">
				
					<div class="form-group">
						<label for="empfaenger">Empfänger</label>
						<select class="contact_select form-control" id="empfaenger" name="empfaenger">
							<?php foreach ($allowed_receivers as $allowed_receiver): ?>
								<option value="<?php echo $allowed_receiver -> CODE; ?>"><?php echo $allowed_receiver -> CODE; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					
					<div class="form-group">
						<label for="betreff">Betreff</label>
						<input type="text" class="form-control" id="betreff" name="betreff" placeholder="Betreff">
					</div>
					
					<div class="form-group">	
						<label for="nachricht">Nachricht</label>						
						<textarea class="form-control" id="nachricht" name="nachricht" placeholder="Ihre Nachricht" rows="12"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
					<?php echo form_submit(array('class' => 'btn btn-primary'), 'Nachricht abschicken'); ?>
				</div>
			</form>
		</div>
	</div>
</div>
