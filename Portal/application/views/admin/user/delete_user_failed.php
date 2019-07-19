<div class="media bottom_spacer">
    <a class="pull-left" href="#"> <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/identity.png"> </a>
    <div class="media-body">
        <h2 class="media-heading">Benutzer löschen</h2>
    </div>
</div>
<div class="menu">
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>/index.php/<?php echo $userrole; ?>/user">Benutzer</a> <span class="divider">/</span></li>
            <li><a href="<?php echo base_url(); ?>/index.php/<?php echo $userrole; ?>/user/list_all_delete">Liste</a> <span class="divider">/</span></li>
            <li class="active">Benutzer löschen</li>
        </ul>        
    </div><!-- end:.usermenu -->
<div class="wrapper profile_person">
    <div class="alert alert-error">
        <h4>Fehler</h4>
        <?php if( isset( $last_admin_error ) AND $last_admin_error ): ?>
            Bei dem Benutzer <strong><?php echo $del_username; ?></strong> handelt es sich um den letzten Administrator und dieser kann nicht gelöscht werden.
        <?php elseif( isset( $user_input_to_db_error ) AND $user_input_to_db_error ): ?>
            Übermittelter Benutzername <strong><?php echo $del_username; ?></strong> und ID <strong><?php echo $del_user_id; ?></strong> stimmen nicht überein.
        <?php else: ?>
            Es ist ein interner Fehler aufgetreten.<br />
            Bitte versuchen Sie es noch einmal und/oder überprüfen Sie die Logfiles.
        <?php endif; ?>
    </div>
</div>