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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-arrow-left"></span> <?php echo anchor( 'patient/patient/messages', 'Zurück zu den Nachrichten' ); ?>
				</h4>
			</div>
		</div>
	</div>
	<div class="col-sm-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a id="linkMsgModal" data-toggle="modal" data-target="#msgModal">
						<span class="glyphicon glyphicon-pencil"></span> Antworten
					</a>
				</h4>
			</div>
		</div>
	</div>
</div>

<?php if( isset( $msg ) ): ?>
	<div class="panel panel-default">
		<div class="panel-body">
			<h4>Nachricht <?php echo ( !empty( $msg[0]->datum ) ) ? 'vom '.date( 'd-m-Y H:i', strtotime( $msg[0]->datum ) ) : ''; ?> </h4>
			<p><strong>Absender</strong></p>
			<p><?php echo $msg[0]->sender; ?></p>
			<p><strong>Betreff</strong></p>
			<p><?php echo ( !empty( $msg[0]->betreff ) ) ? $msg[0]->betreff : 'Kein Betreff'; ?></p>
			<p><strong>Nachricht</strong></p>
			<p><?php echo ( !empty( $msg[0]->nachricht ) ) ? $msg[0]->nachricht : 'Kein Nachricht enthalten.'; ?></p>
		</div>
	</div>
<?php endif; ?>   

<p><?php echo anchor( 'patient/patient/messages', 'Zurück zu den Nachrichten' ); ?></p>


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
						<input type="text" class="form-control" id="empfaenger" name="empfaenger" value="<?php echo $msg[0] -> sender; ?>" readonly>
					</div>
					
					<div class="form-group">
						<label for="betreff">Betreff</label>
						<input type="text" class="form-control" id="betreff" name="betreff" value="RE: <?php echo $msg[0] -> betreff; ?>">
					</div>
					
					<div class="form-group">	
						<label for="nachricht">Nachricht</label>						
						<textarea class="form-control" id="nachricht" name="nachricht" placeholder="Ihre Nachricht" rows="4"></textarea>
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