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
            <li><a href="./">Benutzer</a></li>
            <li class="active">Liste</li>
        </ul>        
    </div><!-- end:.usermenu -->
	
    <div class="dashrow status">
        <?php if( isset( $users ) ): ?>
          <!--
			<div class="well">
                <?php
                // generate the role-links
                $link_all = $userrole . '/user/list_all/all';
                $link_admins = $userrole . '/user/list_all/admins';
                $link_users = $userrole . '/user/list_all/users';
                $link_migradet = $userrole . '/user/list_all/migrated';
                ?>
                      
                <?php if( $users['list'] === 'admins' ): ?>
                    <?php echo anchor( $link_all, 'Alle' ); ?> <span class="badge"><?php echo $users['count']['all']; ?></span> | <strong>Administratoren</strong> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <?php echo anchor( $link_users, 'Benutzer' ); ?> <span class="badge"><?php echo $users['count']['users']; ?></span> | <?php echo anchor( $link_migradet, 'Script-Migriert' ); ?> <span class="badge"><?php echo $users['count']['migrated']; ?></span>
                <?php elseif ($users['list'] === 'users' ): ?>
                    <?php echo anchor( $link_all, 'Alle' ); ?> <span class="badge"><?php echo $users['count']['all']; ?></span> | <?php echo anchor( $link_admins, 'Administratoren' ); ?> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <strong>Benutzer</strong> <span class="badge"><?php echo $users['count']['users']; ?></span> | <?php echo anchor( $link_migradet, 'Script-Migriert' ); ?> <span class="badge"><?php echo $users['count']['migrated']; ?></span>
                <?php elseif ($users['list'] === 'migrated' ): ?>    
                    <?php echo anchor( $link_all, 'Alle' ); ?> <span class="badge"><?php echo $users['count']['all']; ?></span> | <?php echo anchor( $link_admins, 'Administratoren' ); ?> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <?php echo anchor( $link_users, 'Benutzer' ); ?> <span class="badge"><?php echo $users['count']['users']; ?></span> | <strong>Script-Migriert</strong> <span class="badge"><?php echo $users['count']['migrated']; ?></span>                
                <?php else: ?>
                    <strong>Alle</strong> <span class="badge"><?php echo $users['count']['all']; ?></span> | <?php echo anchor( $link_admins, 'Administratoren' ); ?> <span class="badge"><?php echo $users['count']['admins']; ?></span> | <?php echo anchor( $link_users, 'Benutzer' ); ?> <span class="badge"><?php echo $users['count']['users']; ?></span> | <?php echo anchor( $link_migradet, 'Script-Migriert' ); ?> <span class="badge"><?php echo $users['count']['migrated']; ?></span>
                <?php endif; ?> 
            </div>
          -->  
			
			<?php 
				$table_columns = array( 	'id' => 'ID', 
											'initials' => 'Initialien',
											'first_name' => 'Vorname', 
											'last_name' => 'Nachname', 
											'role' => 'Rolle', 
											'email' => 'E-Mail', 
											'rechte_feedback' => 'Rechte: Feedback', 
											'rechte_entscheidung' => 'Rechte: Entscheidung', 
                                            'rechte_zuweisung' => 'Rechte: Zuweisung', 
                                            'rechte_verlauf_normal' => 'Rechte: Einzeltherapie Verlauf',  
                                            'rechte_verlauf_online' => 'Rechte: Onlinetherapie Verlauf',
                                            'rechte_verlauf_gruppe' => 'Rechte: Gruppentherapie Verlauf',
                                            'rechte_verlauf_seminare' => 'Rechte: Seminartherapie Verlauf',
                                            'rechte_zw' => 'Rechte: Zwischenmessung',
                                                 
										); 
			?>
            <table class="table table-bordered table-striped" id="user" cellspacing="0" width="100%">
                <thead>
					<tr>
						<?php foreach( $table_columns as $key => $value ): ?>
							<th>
								<?php echo $value;?>
							</th>
						<?php endforeach; ?>
					</tr>
                </thead>
                <tbody>
                    <?php foreach( $users as $user ): ?>
                        <?php //check if object (class) ?>
                        <?php if( is_object( $user ) ): ?>
                            <tr>
                                <td><!-- usercode -->
                                    <?php $link = $userrole . '/user/edit_user/' . $user -> id; ?>
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
								<td <?php echo ( $user -> rechte_entscheidung == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
                                <td <?php echo ( $user -> rechte_zuweisung == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
                                <td <?php echo ( $user -> rechte_verlauf_normal == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
                                <td <?php echo ( $user -> rechte_verlauf_online == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
                                <td <?php echo ( $user -> rechte_verlauf_gruppe == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
                                <td <?php echo ( $user -> rechte_verlauf_seminare == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
                                <td <?php echo ( $user -> rechte_zw == 1 ) ? 'class="success"' : 'class="danger"'; ?>></td>
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

<script>
var user_filter = 0; 


$(document).ready(function() {
    var table = $('#user').DataTable( {
        pageLength: 50,
        scrollX: true,
        dom: 'Bfrtip',
        columnDefs: [
            {
                targets: 1,
                className: 'noVis',
            }
        ],
        buttons: [
            {
                extend: 'colvis',
                columns: ':not(.noVis)',
                text: 'Spalten auswählen'
            },
            {
                extend: 'colvisGroup',
                text: 'alle Benutzer',
                show: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 ],
                hide: [ ],
            },
            {
                extend: 'colvisGroup',
                text: 'Therapeuten',
                show: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, ],
                hide: [ 16 ],
            },
            {
                extend: 'colvisGroup',
                text: 'Patienten',
                show: [ 0, 1, 2, 3, 4, 5, 16   ],
                hide: [ 6, 7, 8 , 9, 10, 11, 12, 13, 14, 15 ],
            },
            {
                extend: 'colvisGroup',
                text: 'Andere (Indikation, Administrator)',
                show: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, ],
                hide: [  16  ],
            },
            {
                extend: 'colvisGroup',
                text: 'Supervisoren',
                show: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 ],
                hide: [  16 ],
            },
            
            
        ]
    } );
    // folgender Abschnitt funktioniert, keiner weiß warum... WTF
    var action1 = table.button(1).action();
    var action2 = table.button(2).action();
    var action3 = table.button(3).action();
    var action4 = table.button(4).action();
    var action5 = table.button(5).action();

    table.button(1).action( function ( e, dt, node, config ) {
                    action1(e, dt, node, config);
                    user_filter = 0;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });

    table.button(2).action( function ( e, dt, node, config ) {
                    action2(e, dt, node, config);
                    user_filter = 1;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });

    table.button(3).action( function ( e, dt, node, config ) {
                    action3(e, dt, node, config);
                    user_filter = 2;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });
    table.button(4).action( function ( e, dt, node, config ) {
                    action4(e, dt, node, config);
                    user_filter = 3;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });
    table.button(5).action( function ( e, dt, node, config ) {
                    action5(e, dt, node, config);
                    user_filter = 4;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });


    $.fn.dataTable.ext.search.push(
        function( settings, searchData, index, rowData, counter  ) {
            var user = searchData[4];
            switch(user_filter){
                case 0:
                    var beginUser = /.*/;
                    break;
                case 1:
                    var beginUser = /user/;
                    break;
                case 2:
                    var beginUser = /patient/;
                    break;
                case 3:
                    var beginUser = /(priviledged_user|admin)/;
                    break;
                case 4:
                    var beginUser = /supervisor/;
                    break;
            }
    
            if ( beginUser.test(user) == true )
            {
                return true;
            }
            return false;
        }
    );	
} );
</script>