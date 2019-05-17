<div id="member_area" class="patient">
    <div class="media bottom_spacer place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Patientenliste</h1>
        </div>
    </div>
    <div class="menu">
        <ul class="breadcrumb">
            <li><a href="../dashboard">Dashboard</a></li>
            <li class="active">Liste</li>
        </ul>        
    </div><!-- end:.usermenu -->     
	
    <div class="dashrow status">      
        <?php if( isset( $patients ) ): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Zustand</th>
                        <th>Erstsichtung</th>
                        <th>Therapeut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    <?php foreach( $patients as $patient ): ?>
                    <tr>
                        <td><!-- patientcodde -->
                            <?php $link = 'user/patient/list/' . $patient->code; ?>
                            <?php echo anchor( $link, $patient->code ); ?>
                        </td>
                        <td><!-- patientzustand -->
                            <?php
                                        //TODO @see application->views->user->patient->list_all.php
                                        $zustand_to_print;
                                    switch( $patient->zustand )
                                    {
                                        case 0:
                                            $zustand_to_print = 'Wartezeit';
                                            break;
                                        case 1:
                                            $zustand_to_print = 'Laufend';
                                            break;
                                        case 2:
                                            $zustand_to_print = 'Regulärer Abschluss';
                                            break;
                                        case 3:
                                            $zustand_to_print = 'Abbruch mit bewilligten Sitzungen';
                                            break;
                                        case 4:
                                            $zustand_to_print = 'Abbruch in Probatorik';
                                            break;
                                        case 5:
                                            $zustand_to_print = 'Unterbrechung';
                                            break;
                                        case 6:
                                            $zustand_to_print = 'Therapie nicht zustandegekommen';
                                            break;
                                        case 7:
                                            $zustand_to_print = 'Abbruch in Probatorik durch Therapeut';
                                            break;
                                        case 8:
                                            $zustand_to_print = 'Abbruch in Probatorik durch Patient';
                                            break;
                                        case 9:
                                            $zustand_to_print = 'Abbruch mit bewilligten Sitzungen durch Therapeut';
                                            break;
                                        case 10:
                                            $zustand_to_print = 'Abbruch mit bewilligten Sitzungen durch Patient';
                                            break;
                                        case 11:
                                            $zustand_to_print = 'Abbruch aus formalen Gründen';
                                            break;
                                        default:
                                            // for example: -1 -> not in database
                                            $zustand_to_print = 'Kein Eintrag vorhanden';
                                    }
                                    
                                    echo $zustand_to_print;
                                    ?>
                        </td>     
                        
                        <td><!-- Erstsichtung -->
                            <?php echo $patient->erstsich; ?>
                        </td>
                        <td><!--Therapeut -->
                            <?php echo $patient->therpist; ?>
                        </td>
                    </tr>
                    <?php $i++; ?>
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
