<div class="media bottom_spacer">
    <a class="pull-left" href="#"> <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/identity.png"> </a>
    <div class="media-body">
        <h2 class="media-heading">Benutzer löschen</h2>
    </div>
</div>
<?php if( $userrole === 'admin' ): ?>
<div class="menu">
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>/index.php/<?php echo $userrole; ?>/user">Benutzer</a> <span class="divider">/</span></li>
            <li><a href="<?php echo base_url(); ?>/index.php/<?php echo $userrole; ?>/user/list_all_delete">Liste</a> <span class="divider">/</span></li>
            <li class="active">Benutzer löschen</li>
        </ul>        
    </div><!-- end:.usermenu -->
<?php endif; ?>
<div class="wrapper profile_person">
    <div class="alert alert-success">
        <h4>Löschen erfolgreich</h4>
        Der Benutzer <strong><?php echo $del_username; ?></strong> mit der ID <strong><?php echo $del_user_id; ?></strong> wurde gelöscht.
    </div>
</div>