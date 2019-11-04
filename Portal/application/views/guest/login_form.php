<br />
<br />
<br />
<div class="row">
	<div class="col-sm-4 mx-auto">
		<div class="card ">

			<div class="card-header">
				<h3 class="card-title">Login</h3>
			</div>
			
			<div class="card-body">
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

					<?php if( !empty($redirect_page) && !empty($redirect_param_1) ) : ?>
						<input type="hidden" name="redirect_page" value="<?php echo $redirect_page; ?>" />
						<input type="hidden" name="redirect_param_1" value="<?php echo $redirect_param_1; ?>" />
					<?php endif; ?>

					<button type="submit" class="btn btn-outline-secondary">Login</button>
				</form>
			</div>
		</div>
	</div>
</div>