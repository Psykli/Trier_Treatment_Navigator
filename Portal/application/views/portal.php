
<?php if(ENVIRONMENT == 'development'):?>
<!-- Haupt-Jumbotron für die Begrüßung zur Seite -->
<div class="jumbotron">
	<div class="container">
		<h2>Herzlich Willkommen!</h2>
	</div>
</div><!--/.jumbotron -->

<div class="container">
	<div class="row">
		<div class="col-md-3">
			<blockquote>
				<h2><?php echo anchor( 'patient/sb_dynamic/index', 'Stundenbogen'); ?></h2>
			</blockquote>	
			<hr />
			<blockquote>
				<h2><?php echo anchor( 'patient/sb/index/test', 'Testpatient'); ?></h2>				
			</blockquote>
			<blockquote>
				<h2><?php echo anchor( 'patient/sb_dynamic/index/test', 'Testpatient (Neu)'); ?></h2>				
			</blockquote>
		</div>
		<?php if ( $is_priviledged_user ): ?>
			<div class="col-md-2">
				<blockquote>
					<h2><?php echo anchor( "admin/meeting_tool/index/overview", lang('portal_admin') ); ?></h2>
				</blockquote>
			</div>
			<div class="col-md-2">
				<blockquote>
					<h2><?php echo anchor( "admin/meeting_tool/index/overview", lang('portal_therapeut') ); ?></h2>
				</blockquote>
			</div>
			<div class="col-md-2">
				<blockquote>
					<h2><?php echo anchor( "admin/meeting_tool/index/overview", lang('portal_patient') ); ?></h2>
				</blockquote>
			</div>
		<?php else: ?>
		<?php $link = $userrole != 'guest' ? "$userrole/dashboard" : "login";?>
			<div class="col-md-2">
				<blockquote>
					<h2><?php echo anchor( $link, lang('portal_admin') ); ?></h2>
				</blockquote>
			</div>
			<div class="col-md-2">
				<blockquote>
					<h2><?php echo anchor( $link, lang('portal_therapeut') ); ?></h2>
				</blockquote>
			</div>
			<div class="col-md-2">
				<blockquote>
					<h2><?php echo anchor( $link, lang('portal_patient') ); ?></h2>
				</blockquote>
			</div>
		<?php endif; ?>
	</div><!--/.row -->
</div><!--/.container -->
<?php endif;?>


<?php if(ENVIRONMENT == 'production'):?>
<!-- Haupt-Jumbotron für die Begrüßung zur Seite -->
<div class="jumbotron">
	<div class="container">
		<h2>Herzlich Willkommen!</h2>
	</div>
</div><!--/.jumbotron -->

<div class="container">
	<div class="row">


		<?php if ( $is_priviledged_user ): ?>
			<div class="col-sm-offset-3 col-md-3">
				<blockquote>
					<h2><?php echo anchor( "admin/meeting_tool/index/overview", 'Portal' ); ?></h2>
				</blockquote>
			</div>
		<?php else: ?>
			<div class="col-sm-offset-3 col-md-3">
				<blockquote>
					<h2><?php echo anchor( "$userrole/dashboard", 'Portal' ); ?></h2>
				</blockquote>
			</div>
		<?php endif; ?>
	</div><!--/.row -->
</div><!--/.container -->
<?php endif;?>
