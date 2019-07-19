<div class="media bottom_spacer place_headline">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/user-home.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading"><?php echo lang('dashboard_heading');?></h1>
	</div>
</div>

<?php $pstatus = array_pop($passwordStatus); ?>
<?php if ($pstatus['change_password'] != 1): ?> 
    <script type="text/javascript">
        $(window).load(function(){
            $('#meinModalPassword').modal('show');
        });
    </script>
<?php endif; ?> 


<div class="row">
	<div class="col-sm-4">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang('dashboard_patientoverview');?></h3>
			</div>	

			<table class="table">
				<tbody>
					<tr>
						<td><?php echo lang('dashboard_therapie');?></td>
						<td><?php echo $status['open']; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('dashboard_brake');?></td>
						<td><?php echo $status['temp_break']; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('dashboard_finish');?></td>
						<td><?php echo $status['closed']; ?></td>
					</tr>
				</tbody>
			</table>
			
			<div class="panel-footer">
				<?php echo anchor( "user/patient/list_all", lang('dashboard_patientlist'), array('class' => 'btn btn-link btn-block', 'role' => 'button') ); ?>
			</div>
		
		</div><!-- end:.panel -->
		
		
	</div><!-- end:.col -->

	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang('dashboard_reminds');?></h3>
			</div>
			<div class="panel-body">
				<div class="panel-group" id="accordion">
				
					<?php if(empty($reminds)):?>
						<div class="alert alert-success"><?php echo lang('dashboard_noreminds');?></div>
					<?php else: ?>
						<div class="panel panel-danger">
							<div class="panel-heading">
								<h4 class="panel-title" >
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
										<span class="glyphicon glyphicon-chevron-right"></span> <?php echo lang('dashboard_importent');?><span class="badge pull-right"><?php echo count($reminds); ?></span>
									</a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse">
							
								<div class="panel-body">
									<?php echo lang('dashboard_remindtext');?>
								</div>
						
								<table class="table">
									<tbody>
										<?php foreach($reminds as $remind): ?>
											<tr>
												<td>
													<?php $link = $userrole . '/patient/list/' .  $remind['code']; ?>
													<?php echo anchor( $link,  $remind['code'] ); ?>
													<?php echo anchor( 'user/dashboard/delete_therapy_remind/'.$remind['code'], '<span class="glyphicon glyphicon-remove"></span>', array( 'class' => 'btn btn-danger btn-xs pull-right' ) ); ?>
												</td>
											</tr>
										<?php endforeach;?>
									</tbody>					
								</table>
							</div>
						</div>
					<?php endif; ?>

					<?php if(empty($gasReminds)):?>
						<hr />
						<div class="alert alert-success">
							<?php echo lang('dashboard_nogas');?>
						</div>
					<?php else: ?>
						<hr />
						<div class="panel panel-danger">
							<div class="panel-heading">
								<h4 class="panel-title" >
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
										<span class="glyphicon glyphicon-chevron-right"></span> <?php echo lang('dashboard_gas');?><span class="badge pull-right"><?php echo count($gasReminds); ?></span>
									</a>
								</h4>
							</div>
							<div id="collapseTwo" class="panel-collapse collapse">	
							
								<div class="panel-body">
									<?php echo lang('dashboard_gasreminds');?>
								</div>
								
								<table class="table">
									<tbody>
										<?php foreach($gasReminds as $gasRemind): ?>
											<tr>
												<td>
													<?php $link = $userrole . '/patient/list/' .  $gasRemind['code']; ?>
													<?php echo anchor( $link,  $gasRemind['code'] ); ?>
													<?php echo anchor( 'user/dashboard/delete_gas_remind/'.$remind['code'], '<span class="glyphicon glyphicon-remove"></span>', array( 'class' => 'btn btn-danger btn-xs pull-right' ) ); ?>
												</td>
											</tr>
										<?php endforeach;?>
									</tbody>					
								</table>						
							</div>
						</div>
					<?php endif; ?>
					
					<?php if( empty( $zwReminds ) ):?>
						<hr />
						<div class="alert alert-success">
							<?php echo lang('dashboard_measurement_none');?>
						</div>
					<?php else: ?>
						<hr />
						<div class="panel panel-danger">
							<div class="panel-heading">
								<h4 class="panel-title" >
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
										<span class="glyphicon glyphicon-chevron-right"></span> <?php echo lang('dashboard_measurement');?><span class="badge pull-right"><?php echo count($zwReminds); ?></span>
									</a>
								</h4>
							</div>
							<div id="collapseThree" class="panel-collapse collapse">
							
								<div class="panel-body">
									<?php echo lang('dashboard_measurementreminds');?>
								</div>
						
                                
								<table class="table">
									<tbody>
										<?php foreach($zwReminds as $remind): ?>
											<tr>
												<td>
													<?php $link = $userrole . '/patient/list/' .  $remind['code']; ?>
													<?php echo anchor( $link,  $remind['code'] ); ?>
													( <?php echo $remind['instance']; ?> )
													<?php echo anchor( 'user/dashboard/delete_zw_remind/'.$remind['code'].'/'.$remind['instance'], '<span class="glyphicon glyphicon-remove"></span>', array( 'class' => 'btn btn-danger btn-xs pull-right' ) ); ?>
												</td>
											</tr>
										<?php endforeach;?>
									</tbody>					
								</table>
                                
							</div>
						</div>
					<?php endif; ?>
					<?php if(empty($inactiveMessages)): ?>
						<?php if(!$noReleasedQuestionnaires): ?>
								<hr />
							<div class="alert alert-success">
								<?php echo lang('dashboard_inactive_none');?>
							</div>
						<?php endif; ?>

					<?php else: ?>
					<div class="panel panel-danger">
							<div class="panel-heading">
								<h4 class="panel-title" >
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
										<span class="glyphicon glyphicon-chevron-right"> </span><?php echo lang('dashboard_inactive');?><span class="badge pull-right"><?php echo count($inactiveMessages); ?></span>
									</a>
								</h4>
							</div>
							<div id="collapseFour" class="panel-collapse collapse">
							
								<div class="panel-body">
									<?php echo lang('dashboard_inactivereminds');?>
								</div>
						
								<table class="table">
									<tbody>
										<?php foreach($inactiveMessages as $msg): ?>
											<tr>
												<td>
													<?php $link = $userrole . '/patient/list/' .  $msg['code'] ?>
													<?php echo anchor( $link,  $msg['code'] . ' ' . $msg['instance'] . ' ' . $msg['name'] ); ?>
													<?php echo anchor( 'user/dashboard/delete_quest_remind/'.$msg['code'].'/'.$msg['instance'].'/'.$msg['name'], '<span class="glyphicon glyphicon-remove"></span>', array( 'class' => 'btn btn-danger btn-xs pull-right' ) ); ?>
												</td>
											</tr>
										<?php endforeach;?>
									</tbody>					
								</table>
							</div>
						</div>
					<?php endif; ?>
					
				</div><!-- end:.panel-group -->
			</div>
		</div><!-- end:.panel -->
	</div><!-- end:.col -->	
	
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang('dashboard_message');?></h3>
			</div>	
			<div class="panel-body">
				<?php if( $anzahlUnreadMsg == 0 ): ?>
					<div class="alert alert-info">
						<?php echo lang('dashboard_message_none');?>
					</div>
				<?php else: ?>
					<?php echo anchor( "user/patient/messages/", lang('dashboard_message_new') ); ?> <span class="badge "><?php echo $anzahlUnreadMsg; ?> neu</span>
				<?php endif; ?>
			</div>
			<div class="panel-footer">
				<?php echo anchor( "user/patient/messages", lang('dashboard_message_button'), array('class' => 'btn btn-link btn-block', 'role' => 'button') ); ?>
			</div>
		</div>	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo lang('dashboard_tutorial');?></h3>
			</div>	
			<div class="panel-body">
				<?php echo anchor( "user/feedback/index/feedback_tutorial/".$username, lang('dashboard_tutorial_link') ); ?>
			</div>
		</div>
	</div>

</div><!-- end:.row -->

<!--Modal-->
<div class="modal fade" id="meinModalPassword" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="meinModalLabel"><?php echo lang('dashboard_password');?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger" role="alert">
                <p><?php echo lang('dashboard_password_msg');?></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('dashboard_password_close');?></button>
        </div>
        </div>
    </div>
</div>

<script>
	_paq.push(['appendToTrackingUrl', 'new_visit=1']); // (1) forces a new visit 
	_paq.push(["deleteCookies"]); // (2) deletes existing tracking cookies to start the new visit
	_paq.push(['setCustomVariable',
    // Index, the number from 1 to 5 where this custom variable name is stored
    1,
    // Name, the name of the variable, for example: Gender, VisitorType
    "User",
    // Value, for example: "Male", "Female" or "new", "engaged", "customer"
    "<?php echo $username?>",
    // Scope of the custom variable, "visit" means the custom variable applies to the current visit
    "visit"
	]);

	_paq.push(['trackPageView']);
</script>
   


<?php //include('ppia_fragebogen.php'); ?>