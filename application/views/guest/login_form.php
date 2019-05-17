<br />
<br />
<br />
<div class="row">
	<div class="col-sm-offset-4 col-sm-4">
		<div class="panel panel-default">

			<div class="panel-heading">
				<h3 class="panel-title">Login</h3>
			</div>
			
			<div class="panel-body">
				<?php if( isset( $error ) && $error ): ?>
					<div class="alert alert-danger">
						<?php if( $error_code === 403 ): ?>
							Authentifizierung fehlgeschlagen!
						<?php else: ?>
							Ein Fehler ist aufgetreten.
						<?php endif; ?>
					</div><!-- end:.error -->
				<?php endif; ?>
					
				<?php echo form_open(site_url('login/validate_credentials'), array('role' => 'form', 'accept-charset' => 'utf-8', 'method' => 'post'));?>
					<div class="form-group">
						<label for="username"><?php echo lang('username'); ?></label>
						<input type="text" class="form-control" id="username" name="username" placeholder="<?php echo lang('username'); ?>" autofocus>
					</div>
					
					<div class="form-group">
						<label for="password"><?php echo lang('password'); ?></label>
						<input type="password" class="form-control" id="password" name="password" placeholder="<?php echo lang('password'); ?>">
					</div>

					<button type="submit" class="btn btn-default">Login</button>
				</form>
			</div>
		</div>
	</div>
</div>