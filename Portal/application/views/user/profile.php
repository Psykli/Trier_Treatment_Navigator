
<div class="media bottom_spacer_50px">
    <a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/identity.png">
	</a>
    <div class="media-body">
        <h2 class="media-heading"><?php echo lang( 'user_profile' ); ?></h2>
    </div>
</div>

<?php if( isset( $error_message ) ): ?>
	<div class="alert alert-danger">
        <?php echo $error_message; ?>
	</div>
<?php endif; ?>

<?php if( isset( $success_message ) ): ?>
	<div class="alert alert-success">
        <?php echo $success_message; ?>
	</div>
<?php endif; ?>

<?php if( isset( $info_message ) ): ?>
	<div class="alert alert-info">
        <?php echo $info_message; ?>
	</div>
<?php endif; ?>

<?php if( isset( $old_email_address ) && isset( $new_email_address_unconfirmed ) && isset( $accept_confirmation_link ) ): ?>
	<div class="alert alert-info">
        <?php echo lang( 'email_address_change_confirm_part1' ) . $old_email_address . lang( 'email_address_change_confirm_part2' ) . $new_email_address_unconfirmed . lang( 'email_address_change_confirm_part3' ); ?>
        <br><br>
        <a href="<?php echo $accept_confirmation_link; ?>" class="btn btn-primary"><?php echo lang( 'email_address_change_confirm_button' ); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="<?php echo site_url('patient/profile'); ?>" class="btn btn-outline-primary"><?php echo lang( 'email_address_change_cancel_button' ); ?></a>
	</div>
<?php endif; ?>

<div class="row">
	<div class="col-md-6">
			<div class="form-group">
				<label for="initials" class="control-label"><?php echo lang( 'initials' ); ?> (Login)</label>
					<input type="text" class="form-control" id="initials" name="initials" value="<?php echo $profile -> initials; ?>" placeholder="<?php echo $profile -> initials; ?>" readonly>
					<span class="help-inline"><?php echo lang( 'initials_note' ); ?></span>
			</div>

			<div class="form-group">
				<label for="first_name" class="control-label"><?php echo lang( 'first_name' ); ?></label>
					<input type="text" class="form-control" id="first_name" name="first_name" placeholder="<?php echo $profile -> first_name; ?>" readonly>
                    <span class="help-inline"><?php echo lang( 'first_name_note' ); ?></span>
			</div>

			<div class="form-group">
				<label for="last_name" class="control-label"><?php echo lang( 'last_name' ); ?></label>
					<input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?php echo $profile -> last_name; ?>" readonly>
                    <span class="help-inline"><?php echo lang( 'last_name_note' ); ?></span>
			</div>

			<div class="form-group">
				<label for="rolle" class="control-label"><?php echo lang( 'role' ); ?></label>
					<input type="text" class="form-control" id="rolle" name="rolle" placeholder="<?php echo $profile -> role; ?>" readonly>
                    <span class="help-inline"><?php echo lang( 'role_note' ); ?></span>
			</div>

    </div>
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <?php echo form_open( 'user/profile/change_email',  array('class' => 'form-horizontal', 'role' => 'form', 'id' => 'emailForm' ) ); ?>
                    <div class="form-group">
                        <label for="current_password" class="col-sm-4 control-label"><?php echo lang( 'current_password' ); ?></label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label"><?php echo lang( 'email' ); ?></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php if( isset( $new_email_address ) ) { echo $new_email_address; } else { echo $profile -> email; } ?>" placeholder="<?php echo $profile -> email; ?>">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-outline-primary"><?php echo lang( 'change_email' ); ?></button>
                    </div>
        </form>

        <hr/>
		<?php echo form_open( 'user/profile/change_password',  array('class' => 'form-horizontal', 'role' => 'form', 'id' => 'passwordForm' ) ); ?>
                    <div class="form-group">
                        <label for="current_password" class="control-label"><?php echo lang( 'current_password' ); ?></label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="control-label"><?php echo lang( 'new_password' ); ?></label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>                   

                    <div class="form-group">
                        <label for="new_password_confirm" class="control-label"><?php echo lang( 'new_password_confirm' ); ?></label>
                        <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-outline-primary"><?php echo lang( 'change_password' ); ?></button>
                    </div>
		</form>
    </div>
</div>