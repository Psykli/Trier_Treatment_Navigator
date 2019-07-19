<div id="member_area" class="patient">
    <div class="media bottom_spacer place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/user-edit.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Benutzerliste</h1>
        </div>
    </div>
    <div class="menu">
        <ul class="breadcrumb">
            <li><a href="./">Benutzer</a> <span class="divider">/</span></li>
            <li class="active">Liste</li>
        </ul>        
    </div><!-- end:.usermenu -->
    <div class="dashrow status">
        <?php if( isset( $users ) ): ?>
            <div class="well">
                <?php
                // generate the role-links
                $link_controller = '/user/list_all_delete/';
                $link_all = $userrole . $link_controller . 'all';
                $link_admins = $userrole . $link_controller . 'admins';
                $link_users = $userrole . $link_controller . 'users';
                $link_migradet = $userrole . $link_controller . 'migrated';
                ?>
                      
                <?php if( $users['list'] == 'admins' ): ?>
                    <?php echo anchor( $link_all, 'Alle' ); ?> <span class="badge"><?php echo $users['count']['all']; ?></span> | <strong>Administratoren</strong> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <?php echo anchor( $link_users, 'Benutzer' ); ?> <span class="badge"><?php echo $users['count']['users']; ?></span> | <?php echo anchor( $link_migradet, 'Script-Migriert' ); ?> <span class="badge"><?php echo $users['count']['migrated']; ?></span>
                <?php elseif ($users['list'] == 'users' ): ?>
                    <?php echo anchor( $link_all, 'Alle' ); ?> <span class="badge"><?php echo $users['count']['all']; ?></span> | <?php echo anchor( $link_admins, 'Administratoren' ); ?> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <strong>Benutzer</strong> <span class="badge"><?php echo $users['count']['users']; ?></span> | <?php echo anchor( $link_migradet, 'Script-Migriert' ); ?> <span class="badge"><?php echo $users['count']['migrated']; ?></span>
                <?php elseif ($users['list'] == 'migrated' ): ?>    
                    <?php echo anchor( $link_all, 'Alle' ); ?> <span class="badge"><?php echo $users['count']['all']; ?></span> | <?php echo anchor( $link_admins, 'Administratoren' ); ?> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <?php echo anchor( $link_users, 'Benutzer' ); ?> <span class="badge"><?php echo $users['count']['users']; ?></span> | <strong>Script-Migriert</strong> <span class="badge"><?php echo $users['count']['migrated']; ?></span>                
                <?php else: ?>
                    <strong>Alle</strong> <span class="badge"><?php echo $users['count']['all']; ?></span> | <?php echo anchor( $link_admins, 'Administratoren' ); ?> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <?php echo anchor( $link_users, 'Benutzer' ); ?> <span class="badge"><?php echo $users['count']['users']; ?></span> | <?php echo anchor( $link_migradet, 'Script-Migriert' ); ?> <span class="badge"><?php echo $users['count']['migrated']; ?></span>
                <?php endif; ?> 
            </div>
            
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Benutzername</th>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>Rolle</th>
                        <th>EMail</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $users as $user ): ?>
                        <?php //check if object (class) ?>
                        <?php if( is_object( $user ) ): ?>
                            <tr>
                                <td><!-- usercode -->
                                    <?php $link = $userrole . '/user/list/admin/' . $user -> id; ?>
                                    <?php echo anchor( $link, $user -> id ); ?>
                                </td>
                                <td><!-- userlogin -->
                                    <?php echo $user -> initials; ?>
                                </td>
                                <td><!-- uservorname -->
                                    <?php echo $user -> first_name; ?>
                                </td>
                                <td><!-- usernachname -->
                                    <?php echo $user -> last_name; ?>
                                </td>
                                <td><!-- userrolle -->
                                    <?php echo $user -> role; ?>
                                </td>
                                <td><!-- useremail -->
                                    <?php echo $user -> email; ?>
                                </td>
                                <td>
                                    <?php $link = base_url() . 'index.php/' . $userrole . '/user/delete_user_validation/' . $user -> id . '/' . $user->initials; ?>
                                     <a href="<?php echo $link; ?>" class="btn btn-danger btn-mini" type="button"><i class=" icon-remove icon-white"></i> löschen</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                Keine Datensätze vorhanden.
            </div>
        <?php endif; ?>
    </div><!-- end:.dashrow -->
</div>