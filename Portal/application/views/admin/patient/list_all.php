<div id="member_area" class="patient">
    <div class="media bottom_spacer_50px place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading"><?php echo lang('list_list1');?></h1>
        </div>
    </div>
    <nav class="menu">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Liste</li>
        </ol>        
    </nav><!-- end:.usermenu -->     
	
        <?php if( isset( $patients ) ): ?>
            <div id="list_info" class="alert alert-info" role="alert">
                Tabelle wird geladen...
            </div>
            <table id="patient_list" style="display:none;" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th><?php echo lang('list_code');?></th>
                        <th><?php echo lang('list_state');?></th>
                        <th><?php echo lang('list_erstsichtung');?></th>
                        <th><?php echo lang('list_therapist');?></th>
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
                                    $zustand_to_print;
                                    switch( $patient->zustand )
                                    {
                                        case 0:
                                            $zustand_to_print = lang('list_waiting_time');;
                                            break;
                                        case 1:
                                            $zustand_to_print = lang('list_run');
                                            break;
                                        case 2:
                                            $zustand_to_print = lang('list_regular_completion');
                                            break;
                                        case 3:
                                            $zustand_to_print = lang('list_case1');
                                            break;
                                        case 4:
                                            $zustand_to_print = lang('list_case2');
                                            break;
                                        case 5:
                                            $zustand_to_print = lang('list_stop');
                                            break;
                                        case 6:
                                            $zustand_to_print = lang('list_therapy_fall_through');
                                            break;
                                        case 7:
                                            $zustand_to_print = lang('list_case3');
                                            break;
                                        case 8:
                                            $zustand_to_print = lang('list_case4');
                                            break;
                                        case 9:
                                            $zustand_to_print = lang('list_case5');
                                            break;
                                        case 10:
                                            $zustand_to_print = lang('list_case6');
                                            break;
                                        case 11:
                                            $zustand_to_print = lang('list_case7');
                                            break;
                                        default:
                                            // for example: -1 -> not in database
                                            $zustand_to_print = lang('list_no_entry');
                                    }
                                    
                                    echo $zustand_to_print;
                                    ?>
                        </td>     
                        
                        <td><!-- Erstsichtung -->
                            <?php echo $patient->erstsich; ?>
                        </td>
                        <td><!--Therapeut -->
                            <?php echo $patient->therapist; ?>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <?php echo lang('list_nodata'); ?>
            </div>
        <?php endif; ?> 
</div>

<script>
$(document).ready(function () {
    $('#patient_list').DataTable();
    $('#list_info').hide();
    $('#patient_list').show();
});

</script>
