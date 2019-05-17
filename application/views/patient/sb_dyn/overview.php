</div>
<div class="container quest_container">
<?php if(ENVIRONMENT == 'development'): ?>
    <span>
        <span class="flag-icon flag-icon-de" ><button class="btn btn-link" onclick="switch_language('de');"></button></span>
        /<span class="flag-icon flag-icon-gb"><button class="btn btn-link" onclick="switch_language('en');"></button></span>
    </span>
<?php endif;?>
<h1> <?php echo lang('patientcode');?> <?php echo $patientcode;?></h1>
<h1> <?php echo lang('instance');?> <?php echo $instance;?></h1>
<a href="<?php echo site_url('patient/sb_dynamic/index');?>"><h1> <?php echo lang('to_start');?> </h1></a>
<?php if($new_quartal):?>
<h1><?php echo lang('insurance');?></h1>
<?php endif;?>
<?php if($instance % 5 == 0):?>
<!--<h1>[Es wurden zusätzliche Zwischenmessungsfragebögen freigeschaltet]</h1>-->
<h1><?php echo lang('Z_out');?></h1>
<?php endif;?>
<?php if(($instance - 1) % 5 == 0):?>
<!--<h1>[Es wurden zusätzliche Zwischenmessungsfragebögen freigeschaltet]</h1>-->
<h1><?php echo lang('Z_in');?></h1>
<?php endif;?>
<?php if($instance == 7):?>
<!--<h1>[Es wurden zusätzliche Zwichenmessungsfragebögen freigeschaltet]</h1>-->
<h1><?php echo lang('diagnose');?></h1>
<?php endif;?>

<?php 
$lastHscl = $this->Patient_Model->get_last_hscl( $patientcode);
$over = -1;
// Wir wollen hier herausfinden wann die Boundary das letzte mal überschritten war. 
// In BOUNDARY_UEBERSCHRITTEN steht nur wann sie das erste mal überschritten wurde
for($i = $lastHscl->instance; $i >= $lastHscl->instance-3; $i--){
    $boundary = $this->Patient_Model->get_boundary($patientcode, $i);
    if($boundary->BOUNDARY_UEBERSCHRITTEN > 0){
        $over = $i;
        break;
    }
}

if($over > 0):?>
<h3>[Bitte beachten Sie, dass die klinischen Unterstützungstools derzeit im Feedbackportal für Patient <?php echo $patientcode;?> freigeschaltet sind]</h3>
<?php endif;?>

<?php if($instance % 5 == 0 ):?>
<!-- Dieser Button ermöglicht es die Zwischenmessungen im Portal des Patienten freizuschalten, alle Funktionen sind implementiert, es muss nur noch das Go gegeben werden -->
<?php if(ENVIRONMENT == 'development'): ?>
    <?php 
    $z_instance = ($instance - ($instance % 5)); 
    $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);
    $has_zwischen = $this -> Questionnaire_tool_model -> has_zwischen($patientcode, $z_instance);
    if ($has_zwischen == FALSE): 
    ?>
        <button id="releaseZ" class="btn btn-primary"><?php echo lang('Z_unlock');?></button> 
    <?php else: ?>
        <button id="releaseZ" class="btn btn-primary" disabled><?php echo lang('Z_unlock');?></button> 
    <?php endif; ?>
<?php endif; ?>
<?php endif;?>

<?php $is_immutable = $this-> Gas_Model ->is_immutable($patientcode, $username);
    $has_gas = $this-> SB_Model ->has_filled_questionnaire($patientcode, 'gas');
    if($has_gas AND $instance > 10 AND ($instance-1) % 5 == 0 AND !$is_immutable):?>
        <div class="alert alert-warning"> GAS wurde nicht endgültig eingetragen und kann daher nicht ausgefüllt werden </div>
    <?php endif;?>


<div id="releaseZ_success" class="alert alert-success" style="display:none;"><?php echo lang('Z_success');?></div>
<div id="releaseZ_error" class="alert alert-danger" style="display:none;"><?php echo lang('Z_error');?></div>
<hr/>
<div class="row col-md-6 col-md-offset-3">
<div class="list-group">
    <?php $count_section = 0;
     $z_instance = ($instance - ($instance % 5)); 
     $z_instance = intval($z_instance) < 10 ? 'Z0'.intval($z_instance) : 'Z'.intval($z_instance);
     $section_names = $batterie[0]->section_names; 
     $section_name_array = explode(';',$section_names);
    ?>
    <?php $active_section = $step < sizeof($batterie) ? intval($batterie[$step]->section) : -1;?>
    <?php foreach ($batterie as $key => $quest): ?>   
        <!--<?php var_dump($key, $quest);?>-->
        <?php $section_order = intval($batterie[$key]->section_order);
            $section = intval($batterie[$key]->section);
            $gas_filled = $this-> session ->userdata('gas');
            $view_status = $this-> Patient_model->get_view_status( $patientcode );
            ?>
        <?php if(($section_order == 0 AND $section != $active_section) OR ($section == $active_section AND $step == $key)):?>
            <a role="button" class="list-group-item list-group-item-action text-center <?php echo ($section == $active_section)?'active':'disabled';?>" href="../../patient/questionnaire/show_questionnaire/<?php echo $batterie[$key]->qid;?>/<?php echo $instance;?>"><?php echo empty($section_name_array[$count_section]) ? 'Sektion '.($count_section+1) : $section_name_array[$count_section];?></a>
            <?php if($is_immutable AND $quest->gas_section == $count_section AND ($instance-1) % 5 == 0 AND ($instance-1) != 5 AND $this-> Gas_Model ->does_pr_exist($patientcode, $username)):?>
                <a role="button" class="list-group-item list-group-item-action text-center <?php echo ($section == $active_section AND !$gas_filled)?'':'disabled';?>" href="../../user/Gas_Tool/fill_gas_sb/<?php echo $patientcode;?>/<?php echo $z_instance;?>">GAS</a>
                <?php if( $gas_filled AND $view_status == 2 ):?>
                    <a role="button" class="list-group-item list-group-item-action text-center <?php echo ($gas_filled)?'active':'disabled';?>" href="../../patient/sb_dynamic/gas_feedback/<?php echo $patientcode;?>/<?php echo $instance;?>"><?php echo lang('GAS_proc');?></a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif;?>
        <?php if(array_key_exists($key+1, $batterie)):?>
            <?php if($batterie[$key+1]->section != $count_section):?>
                </div>
                <div class="list-group">
                <?php $count_section = $batterie[$key+1]->section;
                
            endif;?>
        <?php endif;?>
    <?php endforeach; ?>
</div> 


<?php $view_status = $this->Patient_model->get_view_status( $patientcode );?>
<?php if($step > $key OR sizeof($batterie) == 0):?>
    <?php if ( $view_status == 2 ): ?>
    <a class="btn btn-primary" style="width:100%;" href="<?php echo site_url('patient/sb_dynamic/process');?>"> <?php echo lang('proc');?> </a>
    <?php else: ?>
    <a class="btn btn-primary" style="width:100%;" href="<?php echo site_url();?>"> <?php echo lang('exit');?> </a>
    <?php endif;?>
<?php endif;?>
</div>

<div class="modal fade" data-backdrop="static" data-keyboard="false" id="missing_data" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
  <div class="modal-header">
    <h3><?php echo lang('missing_head');?></h3>
  </div>
  <div class="modal-body">
    <?php if(!$has_request):?>
    <div class="alert alert-danger">
        <h3 class="alert-heading"><?php echo lang('missing_1');?></h3>
        <p><?php echo lang('missing_2');?></p>
    </div>
    <?php endif;?>
    <?php if(!$has_gas):?>
    <div class="alert alert-danger">
        <p><?php echo lang('missing_3');?></p>
    </div>
    <?php endif;?>
    <?php if(!$has_pers_dis):?>
    <div class="alert alert-warning">
        <p><?php echo lang('missing_4');?></p>
    </div>
    <?php endif;?>
    <?php if(!$has_psypharm):?>
    <div class="alert alert-warning">
        <p><?php echo lang('missing_5');?></p>
    </div>
    <?php endif;?>
    <?php if(!empty($zwReminds)):
        $message = 'Es fehlen Zwischenmessungen(Patient) zu: ';
        foreach($zwReminds as $remind){
            $message .= $remind['instance'].', ';
        };?>
    <div class="alert alert-warning">
        <p><?php echo $message;?></p>
    </div>
    <?php endif;?>
    <?php if(!empty($haqReminds)):
        $message = 'Es fehlen Zwischenmessungen(Therapeut) zu: ';
        foreach($haqReminds as $remind){
            $message .= $remind['instance'].', ';
        }?>
    <div class="alert alert-warning">
        <p><?php echo $message;?></p>
    </div>
    <?php endif;?>
  </div>
  <div class="modal-footer">
    <button type="button" id="modal_close" class="btn btn-default" data-dismiss="modal"><?php echo lang('close');?></button>
  </div>
</div>
</div>
</div>

<!-- Anchor Tags sind selbst mit der Klasse 'disabled' noch klickbar, dieses Skript verhindert die Weiterleitung -->
<script>
    $(document).ready(function(){
        $('.disabled').on('click', function(e){e.preventDefault();});

        <?php if((!$has_request OR !$has_pers_dis OR !$has_psypharm OR !$has_gas OR !empty($zwReminds) OR !empty($haqReminds)) AND $instance >= 7 AND !$this->session->userdata('acknowledged_missing_data')):?>
            $('#missing_data').modal('show');
        <?php endif;?>

        $('#modal_close').on('click',function(e){
            $.ajax({
                type: "GET",
                url: '../../patient/sb_dynamic/ajax_acknowledge_missing_data/<?php echo $therapist;?>/<?php echo $patientcode;?>/<?php echo $instance;?>'
            });
        });

        $('#releaseZ:enabled').on('click', function(e){
            $(this).addClass("disabled");
            $(this).attr("disabled", true);
            $.ajax({
                type: "GET",
                url: '../../patient/sb_dynamic/ajax_release_z_battery/<?php echo $therapist;?>/<?php echo $patientcode;?>/<?php echo $instance;?>',
                success: function(){
                    $('#releaseZ_success').fadeIn(400).delay(2500).fadeOut(400);
                },
                error: function(){
                    $('#releaseZ').removeClass("disabled");
                    $('#releaseZ').attr("disabled", false);
                    $('#releaseZ_error').fadeIn(400).delay(2500).fadeOut(400);
                },
                timeout: 5000
            });
        });
    });
</script>