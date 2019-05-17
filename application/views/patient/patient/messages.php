<div class="media bottom_spacer place_headline">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading">Nachrichten</h1> 	
	</div>
</div>

<ul class="breadcrumb">
	<li><a href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/dashboard">Startseite</a></li>
	<li class="active">Nachrichten</li>
</ul>  

<div class="row">

	<div class="col-sm-3">
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a id="linkMsgModal" data-toggle="modal" data-target="#msgModal">
							<span class="glyphicon glyphicon-pencil"></span> Neue Nachricht schreiben
						</a>
					</h4>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a id="linkCollapseOne" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
							<span class="glyphicon glyphicon-envelope"></span> Ungelesene Nachrichten <span class="badge pull-right"><?php echo $anzahlUnreadMsg; ?></span>
						</a>
					</h4>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a id="linkCollapseTwo" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
							<span class="glyphicon glyphicon-cloud-download"></span> Gelesene Nachrichten <span class="badge pull-right"><?php echo $anzahlReadMsg; ?></span>
						</a>
					</h4>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a id="linkCollapseThree" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
							<span class="glyphicon glyphicon-cloud-upload"></span> Gesendete Nachrichten <span class="badge pull-right"><?php echo $anzahlSentMsg; ?></span>
						</a>
					</h4>
				</div>
			</div>
		</div><!-- /.accordion -->
	</div>
	
	<div class="col-sm-9">
		<?php if( isset( $receivedMsgs ) || isset( $sentMsgs ) ): ?>
		
			<div class="panel-group" id="accordion">
			
				<div id="collapseOne" class="panel-collapse collapse in">
					<?php if( $anzahlUnreadMsg > 0 ): ?>
						<table class="table table-striped">
							<caption><strong>Ungelesene Nachrichten </strong><span class="badge"><?php echo $anzahlUnreadMsg; ?></span></caption>
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
												<td><strong><?php echo ( !empty( $msg->betreff ) )? anchor( 'patient/patient/showMessage/'.$msg->id, $msg->betreff ) : 'Kein Betreff'; ?></strong></td>
												<td><?php echo character_limiter( $msg->nachricht, 200).' '.anchor( 'patient/patient/showMessage/'.$msg->id ,'weiterlesen'); ?></td>
											</tr>		
									<?php endif; ?>
								<?php endforeach; ?>
							<tbody>
						</table>
					<?php else: ?>
						<div class="alert alert-info">Sie haben zur Zeit keine ungelesenen Nachrichten.</div>
					<?php endif; ?>
				</div><!-- collapseOne -->
			
				<div id="collapseTwo" class="panel-collapse collapse">	
					<?php if( $anzahlReadMsg > 0 ): ?>
						<table class="table table-striped">
							<caption><strong>Gelesene Nachrichten </strong><span class="badge"><?php echo $anzahlReadMsg; ?></span></caption>
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
											<td><?php echo ( !empty( $msg->betreff ) )? anchor( 'patient/patient/showMessage/'.$msg->id, $msg->betreff ) : 'Kein Betreff'; ?></td>
											<td><?php echo character_limiter( $msg->nachricht, 100).' '.anchor( 'patient/patient/showMessage/'.$msg->id ,'weiterlesen'); ?></td>
										</tr>		
									<?php endif; ?>
								<?php endforeach; ?>
							<tbody>
						</table>
					<?php else: ?>
						<div class="alert alert-info">Sie haben zur Zeit keine gelesene Nachrichten.</div>
					<?php endif; ?>
				</div>	<!-- collapseTwo -->
				
				<div id="collapseThree" class="panel-collapse collapse">	
					<?php if( $anzahlSentMsg > 0 ): ?>
						<table class="table table-striped">
							<caption><strong>Gesendete Nachrichten </strong><span class="badge"><?php echo $anzahlSentMsg; ?></span></caption>
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
											<td><?php echo ( !empty( $msg->betreff ) )? anchor( 'patient/patient/showMessage/'.$msg->id, $msg->betreff ) : 'Kein Betreff'; ?></td>
											<td><?php echo character_limiter( $msg->nachricht, 100).' '.anchor( 'patient/patient/showMessage/'.$msg->id ,'weiterlesen'); ?></td>
										</tr>		
								<?php endforeach; ?>
							<tbody>
						</table>
					<?php else: ?>
						<div class="alert alert-info">Sie haben noch keine Nachrichten gesendet.</div>
					<?php endif; ?>
				</div>	<!-- collapseTwo -->
				
			</div><!--/.accordion -->
			
		<?php else: ?>
			<div class="alert alert-info">Ihr Therapeut hat Ihnen noch keine Nachrichten gesendet.</div>
		<?php endif; ?>   
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open( 'patient/patient/send_msg/', array('role'=>'form')); ?>
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
					<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
					<?php echo form_submit(array('class' => 'btn btn-primary'), 'Nachricht abschicken'); ?>
				</div>
			</form>
		</div>
	</div>
</div>

	


