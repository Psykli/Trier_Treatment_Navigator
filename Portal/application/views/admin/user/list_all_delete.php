<div id="member_area" class="patient">
    <div class="media bottom_spacer_50px place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/user-edit.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Benutzerliste</h1>
        </div>
    </div>
    <div class="menu">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Benutzer</a></li>
            <li class="breadcrumb-item active">Liste</li>
        </ul>        
    </div><!-- end:.usermenu -->
        <?php if( isset( $users ) ): ?>
            
            <table id="delete-users" class="table table-bordered table-striped">
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
</div>

<script>
    $(document).ready(function () {
        var table = $('#delete-users').DataTable(
            {
            pageLength: 10,
        scrollX: true,
        dom: 'fBrtp',
        columnDefs: [
            {
                targets: 1,
                className: 'noVis',
            }
        ],
        buttons: [
            {
                extend: 'colvisGroup',
                text: 'alle Benutzer'           
            },
            {
                extend: 'colvisGroup',
                text: 'Therapeuten'
            },
            {
                extend: 'colvisGroup',
                text: 'Patienten'
            },
            {
                extend: 'colvisGroup',
                text: 'Andere (Indikation, Administrator)'
            },
            {
                extend: 'colvisGroup',
                text: 'Supervisoren'
            }       
        ]
    } );

    var action1 = table.button(0).action();
    var action2 = table.button(1).action();
    var action3 = table.button(2).action();
    var action4 = table.button(3).action();
    var action5 = table.button(4).action();

    table.button(0).action( function ( e, dt, node, config ) {
                    action1(e, dt, node, config);
                    user_filter = 0;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });

    table.button(1).action( function ( e, dt, node, config ) {
                    action2(e, dt, node, config);
                    user_filter = 1;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });

    table.button(2).action( function ( e, dt, node, config ) {
                    action3(e, dt, node, config);
                    user_filter = 2;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });
    table.button(3).action( function ( e, dt, node, config ) {
                    action4(e, dt, node, config);
                    user_filter = 3;
                    table.column(4)
                    .data()
                    .filter(function(value, index){
                        
                    }).draw();
                    });
    table.button(4).action( function ( e, dt, node, config ) {
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
                    var beginUser = /(privileged_user|admin)/;
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