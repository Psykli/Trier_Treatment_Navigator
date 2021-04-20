<div id="member_area" class="patient">
    <div class="media bottom_spacer_50px place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/user-menu.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Benutzer</h1>
        </div>
    </div>
    <div class="menu">
        <ul class="breadcrumb">
            <li class="active">Benutzer</li>
        </ul>        
    </div><!-- end:.usermenu -->
    <div class="dashrow status">
        <h2>Funktionsübersicht</h2>
        
        <div class="function_container">
            <div class="icon">
                <a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/user/list_all">
                    <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/user-edit.png" data-src="holder.js/32x32">
                </a> 
            </div>
            
            <div class="media-body head">
                <h3><?php echo anchor( "$userrole/user/list_all", 'Benutzerliste' ); ?></h3>
            </div>
            <div class="desc">
                Liste von allen vorhanden System-Benutzern
            </div>
        </div>
        
        <div class="function_container">
            <div class="icon">
                <a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/user/new_user">
                    <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/user-new.png" data-src="holder.js/32x32">
                </a> 
            </div>
            
            <div class="media-body head">
                <h3><?php echo anchor( "$userrole/user/new_user", 'Benutzer anlegen' ); ?></h3>
            </div>
            <div class="desc">
                Anlegen von neuen Systembenutzern.
            </div>
        </div>
        
        <div class="function_container">
            <div class="icon">
                <a class="pull-left" href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/user/list_all_delete">
                    <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/user-delete.png" data-src="holder.js/32x32">
                </a> 
            </div>
            
            <div class="media-body head">
                <h3><?php echo anchor( "$userrole/user/list_all_delete", 'Benutzer löschen' ); ?></h3>
            </div>
            <div class="desc">
                Löschen von bestehenden Systembenutzern.
            </div>
        </div>
        
    </div><!-- end:.dashrow -->