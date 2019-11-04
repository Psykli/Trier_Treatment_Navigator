

<!-- Haupt-Jumbotron für die Begrüßung zur Seite -->
<div class="jumbotron">
	<div class="container">
		<h2>Herzlich Willkommen!</h2>
	</div>
</div><!--/.jumbotron -->

<div class="container">
	<div class="row">
		<div class="col-sm-offset-3 col-md-3">
			<blockquote>
				<?php $link =$this->session_model->is_logged_in( $this->session->all_userdata()) ? "$userrole/dashboard" : "login" ;?>
				<h2><?php echo anchor( $link, 'Portal' ); ?></h2>
			</blockquote>
		</div>
		<div class="col-sm-offset-3 col-md-3">
			<blockquote>
				<h2><?php echo anchor( "patient/sb_dynamic/index", 'Stundenbogen' ); ?></h2>
			</blockquote>
		</div>
	</div><!--/.row -->
</div><!--/.container -->